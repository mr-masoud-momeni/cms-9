$ErrorActionPreference = "SilentlyContinue"

$Root = Get-Location

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host " Laravel Naming / Linux Safety Audit" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# --------------------------------------------------
# Helper
# --------------------------------------------------

function Get-ProjectFiles {
    param(
        [string]$Path,
        [string]$Filter = "*"
    )

    if (!(Test-Path $Path)) {
        return @()
    }

    Get-ChildItem $Path -Recurse -File -Filter $Filter |
        Where-Object {
            $_.FullName -notmatch "\\vendor\\" -and
            $_.FullName -notmatch "\\node_modules\\" -and
            $_.FullName -notmatch "\\storage\\" -and
            $_.FullName -notmatch "\\bootstrap\\cache\\"
        }
}

# ==================================================
# 1. Controller directories
# ==================================================

Write-Host "[1] Controller directories" -ForegroundColor Yellow
Write-Host ""

$controllerRoot = Join-Path $Root "app\Http\Controllers"

$controllerNames = @(
    "admin",
    "customer",
    "front"
)

if (Test-Path $controllerRoot) {

    foreach ($name in $controllerNames) {

        $path = Join-Path $controllerRoot $name

        if (Test-Path $path) {

            $correctName = $name.Substring(0,1).ToUpper() + $name.Substring(1)

            Write-Host "[FIX]" -ForegroundColor Red
            Write-Host "  $path"
            Write-Host "  Should be: $correctName"
            Write-Host ""
        }
    }
}

# ==================================================
# 2. PHP class / file consistency
# ==================================================

Write-Host "[2] PHP class/file consistency" -ForegroundColor Yellow
Write-Host ""

$appRoot = Join-Path $Root "app"

$phpFiles = Get-ProjectFiles $appRoot "*.php"

foreach ($file in $phpFiles) {

    $content = Get-Content $file.FullName -Raw

    $classMatches = [regex]::Matches(
        $content,
        '(?m)^\s*class\s+([A-Za-z_][A-Za-z0-9_]*)'
    )

    foreach ($classMatch in $classMatches) {

        $className = $classMatch.Groups[1].Value

        $fileName = [System.IO.Path]::GetFileNameWithoutExtension(
            $file.Name
        )

        if ($className -cne $fileName) {

            Write-Host "[FIX]" -ForegroundColor Red
            Write-Host "  File  : $($file.FullName)"
            Write-Host "  Name  : $fileName"
            Write-Host "  Class : $className"
            Write-Host ""
        }
    }
}

# ==================================================
# 3. Lowercase PHP namespaces
# ==================================================

Write-Host "[3] Lowercase PHP namespaces" -ForegroundColor Yellow
Write-Host ""

foreach ($file in $phpFiles) {

    $content = Get-Content $file.FullName -Raw

    $namespaceMatches = [regex]::Matches(
        $content,
        '(?m)^\s*namespace\s+([^;]+);'
    )

    foreach ($namespaceMatch in $namespaceMatches) {

        $namespace = $namespaceMatch.Groups[1].Value

        if (
            $namespace -match '\\front(\\|$)' -or
            $namespace -match '\\customer(\\|$)' -or
            $namespace -match '\\admin(\\|$)'
        ) {

            Write-Host "[FIX]" -ForegroundColor Red
            Write-Host "  File      : $($file.FullName)"
            Write-Host "  Namespace : $namespace"
            Write-Host ""
        }
    }
}

# ==================================================
# 4. Lowercase Controller references
# ==================================================

Write-Host "[4] Lowercase Controller references" -ForegroundColor Yellow
Write-Host ""

$allLaravelFiles = @()

$allLaravelFiles += Get-ProjectFiles (Join-Path $Root "app") "*.php"
$allLaravelFiles += Get-ProjectFiles (Join-Path $Root "routes") "*.php"
$allLaravelFiles += Get-ProjectFiles (Join-Path $Root "resources\views") "*.php"

foreach ($file in $allLaravelFiles) {

    $content = Get-Content $file.FullName -Raw

    $controllerMatches = [regex]::Matches(
        $content,
        'App\\Http\\Controllers\\(front|customer|admin)(\\|)'
    )

    if ($controllerMatches.Count -gt 0) {

        Write-Host "[FIX]" -ForegroundColor Red
        Write-Host "  File: $($file.FullName)"

        foreach ($match in $controllerMatches) {

            Write-Host "  -> $($match.Groups[0].Value)"
        }

        Write-Host ""
    }
}

# ==================================================
# 5. Blade view directories
# ==================================================

Write-Host "[5] Blade view directories" -ForegroundColor Yellow
Write-Host ""

$viewsRoot = Join-Path $Root "resources\views"

if (Test-Path $viewsRoot) {

    $bladeFiles = Get-ProjectFiles $viewsRoot "*.blade.php"

    $checkedDirectories = @{}

    foreach ($file in $bladeFiles) {

        $relativePath = $file.FullName.Substring(
            $viewsRoot.Length + 1
        )

        $parts = $relativePath -split '[\\/]'

        if ($parts.Count -gt 1) {

            for ($i = 0; $i -lt ($parts.Count - 1); $i++) {

                $directory = $parts[$i]

                if (
                    $directory -cmatch '^[a-z][a-zA-Z0-9_-]*$' -and
                    !$checkedDirectories.ContainsKey($directory)
                ) {

                    $checkedDirectories[$directory] = $true

                    Write-Host "[CHECK]" -ForegroundColor Yellow
                    Write-Host "  Directory: $directory"
                    Write-Host ""
                }
            }
        }
    }
}

# ==================================================
# 6. View references
# ==================================================

Write-Host "[6] View references" -ForegroundColor Yellow
Write-Host ""

$viewPatterns = @(
    'view\(["'']([^"'']+)',
    '@extends\(["'']([^"'']+)',
    '@include\(["'']([^"'']+)',
    '@component\(["'']([^"'']+)'
)

foreach ($file in $allLaravelFiles) {

    $content = Get-Content $file.FullName -Raw

    foreach ($pattern in $viewPatterns) {

        $matches = [regex]::Matches(
            $content,
            $pattern
        )

        foreach ($match in $matches) {

            $viewName = $match.Groups[1].Value

            if (
                $viewName -match '(^|\.)frontend(\.|$)' -or
                $viewName -match '(^|\.)backend(\.|$)' -or
                $viewName -match '(^|\.)customer(\.|$)' -or
                $viewName -match '(^|\.)front(\.|$)' -or
                $viewName -match '(^|\.)admin(\.|$)'
            ) {

                Write-Host "[CHECK]" -ForegroundColor Yellow
                Write-Host "  File : $($file.FullName)"
                Write-Host "  View : $viewName"
                Write-Host ""
            }
        }
    }
}

# ==================================================
# 7. Models
# ==================================================

Write-Host "[7] Models" -ForegroundColor Yellow
Write-Host ""

$modelsRoot = Join-Path $Root "app\Models"

if (Test-Path $modelsRoot) {

    $modelFiles = Get-ChildItem $modelsRoot -File -Filter "*.php"

    foreach ($file in $modelFiles) {

        $content = Get-Content $file.FullName -Raw

        $classMatches = [regex]::Matches(
            $content,
            '(?m)^\s*class\s+([A-Za-z_][A-Za-z0-9_]*)'
        )

        foreach ($classMatch in $classMatches) {

            $className = $classMatch.Groups[1].Value

            $fileName = [System.IO.Path]::GetFileNameWithoutExtension(
                $file.Name
            )

            if ($className -cne $fileName) {

                Write-Host "[FIX]" -ForegroundColor Red
                Write-Host "  File  : $($file.FullName)"
                Write-Host "  File  : $fileName"
                Write-Host "  Class : $className"
                Write-Host ""
            }
        }

        if ($file.Name -cmatch '^[a-z]') {

            Write-Host "[FIX]" -ForegroundColor Red
            Write-Host "  Lowercase model file:"
            Write-Host "  $($file.FullName)"
            Write-Host ""
        }
    }
}

# ==================================================
# 8. Summary
# ==================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host " Audit finished" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "This script ONLY audits." -ForegroundColor Green
Write-Host "It does NOT rename or modify any file."
Write-Host ""

Write-Host "[FIX]   = likely needs correction" -ForegroundColor Red
Write-Host "[CHECK] = review manually" -ForegroundColor Yellow
Write-Host ""
