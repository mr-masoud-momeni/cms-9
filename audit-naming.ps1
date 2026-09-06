param(
    [string]$Root = (Get-Location).Path
)

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host " CMS-9 FULL NAMING AUDIT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Project: $Root" -ForegroundColor DarkGray
Write-Host ""

# =========================================================
# Configuration
# =========================================================

$ExcludedDirectories = @(
    "vendor",
    "node_modules",
    "storage",
    ".git",
    "bootstrap/cache"
)

$SourceExtensions = @(
    ".php",
    ".blade.php"
)

# =========================================================
# Helpers
# =========================================================

function Is-ExcludedPath {
    param(
        [string]$Path
    )

    $Normalized = $Path.Replace("\", "/")

    foreach ($Directory in $ExcludedDirectories) {

        $D = $Directory.Replace("\", "/")

        if ($Normalized -match "(^|/)$([regex]::Escape($D))(/|$)") {
            return $true
        }
    }

    return $false
}

function Relative-Path {
    param(
        [string]$Path
    )

    return $Path.Substring($Root.Length + 1)
}

function Get-PhpFiles {

    return Get-ChildItem `
        -LiteralPath $Root `
        -Recurse `
        -File `
        -ErrorAction SilentlyContinue |
        Where-Object {

            ($_.Extension -eq ".php" -or
             $_.Name -like "*.blade.php") -and
            -not (Is-ExcludedPath $_.FullName)

        }
}

# =========================================================
# Counters
# =========================================================

$IssueCount = 0

function Issue {
    param(
        [string]$Type,
        [string]$Path,
        [string]$Message
    )

    $script:IssueCount++

    Write-Host ""
    Write-Host "[$Type]" -ForegroundColor Yellow
    Write-Host "  $Path" -ForegroundColor White
    Write-Host "  $Message" -ForegroundColor Gray
}

# =========================================================
# 1. DIRECTORY AUDIT
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "1. DIRECTORY AUDIT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

$Directories = Get-ChildItem `
    -LiteralPath $Root `
    -Recurse `
    -Directory `
    -ErrorAction SilentlyContinue |
    Where-Object {
        -not (Is-ExcludedPath $_.FullName)
    }

foreach ($Directory in $Directories) {

    $Name = $Directory.Name

    # lowercase directory names
    if ($Name -cmatch '^[a-z0-9_-]+$') {

        # Ignore common Laravel lowercase directories
        $Allowed = @(
            "app",
            "bootstrap",
            "config",
            "database",
            "lang",
            "public",
            "resources",
            "routes",
            "tests"
        )

        if ($Allowed -notcontains $Name) {

            Issue `
                "LOWERCASE DIRECTORY" `
                (Relative-Path $Directory.FullName) `
                "Directory name is completely lowercase."
        }
    }

    # underscores
    if ($Name.Contains("_")) {

        Issue `
            "UNDERSCORE DIRECTORY" `
            (Relative-Path $Directory.FullName) `
            "Directory contains underscore."
    }
}

# =========================================================
# 2. CONTROLLER DIRECTORY AUDIT
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "2. CONTROLLER DIRECTORY AUDIT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

$ControllerRoot = Join-Path `
    $Root `
    "app\Http\Controllers"

if (Test-Path $ControllerRoot) {

    $ControllerDirectories = Get-ChildItem `
        -LiteralPath $ControllerRoot `
        -Directory `
        -Recurse `
        -ErrorAction SilentlyContinue

    foreach ($Directory in $ControllerDirectories) {

        $Name = $Directory.Name

        if ($Name.Length -gt 0 -and
            $Name.Substring(0,1) -cne
            $Name.Substring(0,1).ToUpper()) {

            Issue `
                "CONTROLLER DIRECTORY" `
                (Relative-Path $Directory.FullName) `
                "Controller directory should start with uppercase."
        }
    }
}

# =========================================================
# 3. PHP FILE NAMING
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "3. PHP FILE NAMING AUDIT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

$PhpFiles = Get-PhpFiles

foreach ($File in $PhpFiles) {

    $Name = $File.Name

    # Skip migrations
    if ($File.FullName -match "/database/migrations/") {
        continue
    }

    # Skip composer files
    if ($Name -in @(
        "index.php",
        "server.php"
    )) {
        continue
    }

    # PHP class-like files
    if ($Name -match '\.php$') {

        $BaseName = $Name -replace '\.php$', ''

        if ($BaseName -match '^[a-z]') {

            Issue `
                "LOWERCASE PHP FILE" `
                (Relative-Path $File.FullName) `
                "PHP class/file name should start with uppercase."
        }

        if ($BaseName -match '_') {

            Issue `
                "UNDERSCORE PHP FILE" `
                (Relative-Path $File.FullName) `
                "PHP class/file name contains underscore."
        }
    }
}

# =========================================================
# 4. CONTROLLER FILE NAMING
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "4. CONTROLLER FILE NAMING" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

if (Test-Path $ControllerRoot) {

    $ControllerFiles = Get-ChildItem `
        -LiteralPath $ControllerRoot `
        -Recurse `
        -File `
        -Filter "*.php" `
        -ErrorAction SilentlyContinue

    foreach ($File in $ControllerFiles) {

        if (Is-ExcludedPath $File.FullName) {
            continue
        }

        $Name = $File.BaseName

        # Auth controllers are allowed without Controller suffix
        if ($File.FullName -match "/Auth/") {
            continue
        }

        # Standard controller convention
        if (
            $Name -notmatch 'Controller$' -and
            $Name -notmatch '^index$' -and
            $Name -notmatch '^blog$'
        ) {

            Issue `
                "CONTROLLER NAME" `
                (Relative-Path $File.FullName) `
                "Controller file does not end with 'Controller'."
        }

        # lowercase first character
        if ($Name -match '^[a-z]') {

            Issue `
                "CONTROLLER CASE" `
                (Relative-Path $File.FullName) `
                "Controller class/file starts with lowercase."
        }
    }
}

# =========================================================
# 5. CLASS DECLARATION AUDIT
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "5. PHP CLASS AUDIT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

foreach ($File in $PhpFiles) {

    $Content = Get-Content `
        -LiteralPath $File.FullName `
        -Raw `
        -ErrorAction SilentlyContinue

    if ($null -eq $Content) {
        continue
    }

    # class declarations
    $Matches = [regex]::Matches(
        $Content,
        '(?m)\bclass\s+([A-Za-z_][A-Za-z0-9_]*)'
    )

    foreach ($Match in $Matches) {

        $ClassName = $Match.Groups[1].Value

        if ($ClassName -cmatch '^[a-z]') {

            Issue `
                "LOWERCASE CLASS" `
                (Relative-Path $File.FullName) `
                "Class '$ClassName' starts with lowercase."
        }

        if ($File.FullName -match "/Controllers/") {

            if (
                $ClassName -notmatch 'Controller$' -and
                $ClassName -notmatch '^ForgotPasswordController$' -and
                $ClassName -notmatch '^LoginController$' -and
                $ClassName -notmatch '^RegisterController$' -and
                $ClassName -notmatch '^ResetPasswordController$' -and
                $ClassName -notmatch '^UserRegisterController$'
            ) {

                Issue `
                    "CONTROLLER CLASS" `
                    (Relative-Path $File.FullName) `
                    "Class '$ClassName' does not follow Controller naming convention."
            }
        }
    }
}

# =========================================================
# 6. NAMESPACE AUDIT
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "6. NAMESPACE AUDIT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

$NamespacePatterns = @(
    'App\\Http\\Controllers\\front',
    'App\\Http\\Controllers\\customer',
    'App\\Http\\Controllers\\admin'
)

foreach ($File in $PhpFiles) {

    $Content = Get-Content `
        -LiteralPath $File.FullName `
        -Raw `
        -ErrorAction SilentlyContinue

    if ($null -eq $Content) {
        continue
    }

    foreach ($Pattern in $NamespacePatterns) {

        if ($Content.Contains($Pattern)) {

            $Suggested = switch ($Pattern) {

                'App\Http\Controllers\front' {
                    'App\Http\Controllers\Front'
                }

                'App\Http\Controllers\customer' {
                    'App\Http\Controllers\Customer'
                }

                'App\Http\Controllers\admin' {
                    'App\Http\Controllers\Admin'
                }
            }

            Issue `
                "OLD NAMESPACE" `
                (Relative-Path $File.FullName) `
                "$Pattern -> $Suggested"
        }
    }
}

# =========================================================
# 7. BLADE VIEW DIRECTORY AUDIT
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "7. BLADE VIEW DIRECTORY AUDIT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

$ViewsRoot = Join-Path `
    $Root `
    "resources\views"

if (Test-Path $ViewsRoot) {

    $ViewDirectories = Get-ChildItem `
        -LiteralPath $ViewsRoot `
        -Recurse `
        -Directory `
        -ErrorAction SilentlyContinue

    foreach ($Directory in $ViewDirectories) {

        $Name = $Directory.Name

        # Blade directories should use PascalCase
        if ($Name -match '^[a-z]') {

            Issue `
                "VIEW DIRECTORY CASE" `
                (Relative-Path $Directory.FullName) `
                "View directory starts with lowercase."
        }

        if ($Name.Contains("_")) {

            Issue `
                "VIEW DIRECTORY FORMAT" `
                (Relative-Path $Directory.FullName) `
                "View directory contains underscore."
        }
    }
}

# =========================================================
# 8. BLADE FILE AUDIT
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "8. BLADE FILE AUDIT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

if (Test-Path $ViewsRoot) {

    $BladeFiles = Get-ChildItem `
        -LiteralPath $ViewsRoot `
        -Recurse `
        -File `
        -Filter "*.blade.php" `
        -ErrorAction SilentlyContinue

    foreach ($File in $BladeFiles) {

        $Name = $File.Name -replace '\.blade\.php$', ''

        if ($Name -match '^[a-z]') {

            Issue `
                "BLADE FILE CASE" `
                (Relative-Path $File.FullName) `
                "Blade file starts with lowercase."
        }

        if ($Name.Contains("_")) {

            Issue `
                "BLADE FILE FORMAT" `
                (Relative-Path $File.FullName) `
                "Blade file contains underscore."
        }
    }
}

# =========================================================
# 9. VIEW REFERENCES
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "9. VIEW REFERENCE AUDIT" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

$ViewReferenceRegex = @(
    "view\(\s*['""]([^'""]+)",
    "include\(\s*['""]([^'""]+)",
    "extends\(\s*['""]([^'""]+)",
    "component\(\s*['""]([^'""]+)"
)

foreach ($File in $PhpFiles) {

    $Content = Get-Content `
        -LiteralPath $File.FullName `
        -Raw `
        -ErrorAction SilentlyContinue

    if ($null -eq $Content) {
        continue
    }

    foreach ($Regex in $ViewReferenceRegex) {

        $Matches = [regex]::Matches(
            $Content,
            $Regex
        )

        foreach ($Match in $Matches) {

            $ViewName = $Match.Groups[1].Value

            # lowercase namespace segments
            if ($ViewName -match '(^|\.)(front|customer|admin|shop|pay)(\.|$)') {

                Issue `
                    "VIEW REFERENCE" `
                    (Relative-Path $File.FullName) `
                    "Review view reference: $ViewName"
            }
        }
    }
}

# =========================================================
# 10. ROUTE CONTROLLER REFERENCES
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "10. ROUTE CONTROLLER REFERENCES" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

$RouteFiles = @()

$WebRoutes = Join-Path $Root "routes\web.php"
$ApiRoutes = Join-Path $Root "routes\api.php"

if (Test-Path $WebRoutes) {
    $RouteFiles += $WebRoutes
}

if (Test-Path $ApiRoutes) {
    $RouteFiles += $ApiRoutes
}

foreach ($RouteFile in $RouteFiles) {

    $Content = Get-Content `
        -LiteralPath $RouteFile `
        -Raw `
        -ErrorAction SilentlyContinue

    foreach ($Pattern in $NamespacePatterns) {

        if ($Content.Contains($Pattern)) {

            $Suggested = switch ($Pattern) {

                'App\Http\Controllers\front' {
                    'App\Http\Controllers\Front'
                }

                'App\Http\Controllers\customer' {
                    'App\Http\Controllers\Customer'
                }

                'App\Http\Controllers\admin' {
                    'App\Http\Controllers\Admin'
                }
            }

            Issue `
                "ROUTE NAMESPACE" `
                (Relative-Path $RouteFile) `
                "$Pattern -> $Suggested"
        }
    }
}

# =========================================================
# 11. MODEL CLASS / FILE CONSISTENCY
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "11. MODEL FILE / CLASS CONSISTENCY" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

$ModelsRoot = Join-Path `
    $Root `
    "app\Models"

if (Test-Path $ModelsRoot) {

    $ModelFiles = Get-ChildItem `
        -LiteralPath $ModelsRoot `
        -File `
        -Filter "*.php" `
        -ErrorAction SilentlyContinue

    foreach ($File in $ModelFiles) {

        $ExpectedClass = $File.BaseName

        $Content = Get-Content `
            -LiteralPath $File.FullName `
            -Raw `
            -ErrorAction SilentlyContinue

        if ($null -eq $Content) {
            continue
        }

        $Matches = [regex]::Matches(
            $Content,
            '(?m)\bclass\s+([A-Za-z_][A-Za-z0-9_]*)'
        )

        foreach ($Match in $Matches) {

            $ClassName = $Match.Groups[1].Value

            if ($ClassName -cne $ExpectedClass) {

                Issue `
                    "MODEL MISMATCH" `
                    (Relative-Path $File.FullName) `
                    "File: $ExpectedClass.php / Class: $ClassName"
            }
        }
    }
}

# =========================================================
# 12. GIT CASE TRACKING
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "12. GIT CASE TRACKING" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

Write-Host ""

git status --short

Write-Host ""

$GitIgnoreCase = git config core.ignorecase

Write-Host "git core.ignorecase = $GitIgnoreCase" `
    -ForegroundColor DarkGray

if ($GitIgnoreCase -eq "true") {

    Write-Host ""
    Write-Host "NOTE:" -ForegroundColor Yellow

    Write-Host `
        "Git is configured to ignore filename case changes on this Windows machine." `
        -ForegroundColor Gray

    Write-Host `
        "Case-only renames must therefore use a temporary filename." `
        -ForegroundColor Gray
}

# =========================================================
# FINAL RESULT
# =========================================================

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan

if ($IssueCount -eq 0) {

    Write-Host `
        "AUDIT PASSED - No naming issues detected." `
        -ForegroundColor Green

} else {

    Write-Host `
        "AUDIT FOUND $IssueCount POTENTIAL ISSUES" `
        -ForegroundColor Yellow
}

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

Write-Host `
    "IMPORTANT: This script only audits. No files were modified." `
    -ForegroundColor Cyan

Write-Host ""