param(
    [ValidateSet("Scan", "Apply", "Verify")]
    [string]$Mode = "Scan"
)

$ErrorActionPreference = "Stop"

$Root = (Get-Location).Path

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host " CMS-9 Naming Refactor" -ForegroundColor Cyan
Write-Host " Mode: $Mode" -ForegroundColor Cyan
Write-Host " Root: $Root" -ForegroundColor DarkGray
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# =========================================================
# Rename Map
# =========================================================
#
# نکته:
# این Renameها با git mv انجام می‌شوند.
# برای Case-only rename ابتدا به اسم موقت می‌روند.
#

$RenameMap = @(

    # -------------------------
    # Controller directories
    # -------------------------

    @{
        Type = "Directory"
        From = "app/Http/Controllers/front"
        To   = "app/Http/Controllers/Front"
    },

    @{
        Type = "Directory"
        From = "app/Http/Controllers/customer"
        To   = "app/Http/Controllers/Customer"
    },

    @{
        Type = "Directory"
        From = "app/Http/Controllers/admin"
        To   = "app/Http/Controllers/Admin"
    }

)

# =========================================================
# Namespace replacements
# =========================================================

$ReferenceMap = @(

    @{
        From = 'App\Http\Controllers\front'
        To   = 'App\Http\Controllers\Front'
    },

    @{
        From = 'App\Http\Controllers\customer'
        To   = 'App\Http\Controllers\Customer'
    },

    @{
        From = 'App\Http\Controllers\admin'
        To   = 'App\Http\Controllers\Admin'
    }

)

# =========================================================
# Files that are safe to scan
# =========================================================

$Extensions = @(
    ".php",
    ".blade.php"
)

$ExcludedDirectories = @(
    "\vendor\",
    "\storage\",
    "\bootstrap\cache\",
    "\node_modules\"
)

# =========================================================
# Helpers
# =========================================================

function FullPath($RelativePath) {

    return Join-Path `
        $Root `
        ($RelativePath -replace "/", "\")
}


function IsExcluded($Path) {

    foreach ($Directory in $ExcludedDirectories) {

        if ($Path -like "*$Directory*") {
            return $true
        }
    }

    return $false
}


function Get-SourcePath($RelativePath) {

    return FullPath $RelativePath
}


function Get-TempPath($RelativePath) {

    $directory = Split-Path `
        (FullPath $RelativePath) `
        -Parent

    $name = Split-Path `
        (FullPath $RelativePath) `
        -Leaf

    return Join-Path `
        $directory `
        "__rename_temp_$name"
}


# =========================================================
# SCAN
# =========================================================

function Scan {

    Write-Host "Checking rename targets..." `
        -ForegroundColor Yellow

    Write-Host ""

    foreach ($Item in $RenameMap) {

        $Source = Get-SourcePath $Item.From
        $Target = Get-SourcePath $Item.To

        #
        # Windows case-insensitive filesystem
        #
        # بنابراین اگر فقط Case فرق داشته باشد،
        # Test-Path هر دو را موجود می‌بیند.
        #

        $SourceExists = Test-Path -LiteralPath $Source

        if (-not $SourceExists) {

            Write-Host "[NOT FOUND]" `
                -ForegroundColor DarkYellow

            Write-Host "  $($Item.From)"

            continue
        }

        $SourceItem = Get-Item -LiteralPath $Source

        Write-Host "[RENAME]" `
            -ForegroundColor Green

        Write-Host "  $($Item.From)"
        Write-Host "  -> $($Item.To)"

        if ($SourceItem.PSIsContainer) {

            Write-Host "  Type: Directory" `
                -ForegroundColor DarkGray

        } else {

            Write-Host "  Type: File" `
                -ForegroundColor DarkGray
        }

        Write-Host ""
    }


    # =====================================================
    # Namespace references
    # =====================================================

    Write-Host "Checking namespace references..." `
        -ForegroundColor Yellow

    Write-Host ""

    $Files = Get-ChildItem `
        -LiteralPath $Root `
        -Recurse `
        -File `
        -ErrorAction SilentlyContinue |
        Where-Object {

            $_.Extension -eq ".php" -and
            -not (IsExcluded $_.FullName)

        }


    foreach ($Map in $ReferenceMap) {

        Write-Host ""
        Write-Host "$($Map.From)" `
            -ForegroundColor Cyan

        Write-Host "    -> $($Map.To)" `
            -ForegroundColor Green

        $FoundCount = 0

        foreach ($File in $Files) {

            $Content = Get-Content `
                -LiteralPath $File.FullName `
                -Raw `
                -ErrorAction SilentlyContinue

            if ($null -eq $Content) {
                continue
            }

            if ($Content.Contains($Map.From)) {

                $Relative = $File.FullName.Substring(
                    $Root.Length + 1
                )

                Write-Host "    FOUND: $Relative" `
                    -ForegroundColor Green

                $FoundCount++
            }
        }

        if ($FoundCount -eq 0) {

            Write-Host "    No references found." `
                -ForegroundColor DarkGray
        }
    }


    Write-Host ""
    Write-Host "============================================" `
        -ForegroundColor Cyan

    Write-Host "SCAN ONLY - NO FILES WERE CHANGED" `
        -ForegroundColor Cyan

    Write-Host "============================================" `
        -ForegroundColor Cyan
}


# =========================================================
# APPLY
# =========================================================

function Apply {

    Write-Host ""
    Write-Host "WARNING: APPLY WILL MODIFY THE WORKING TREE" `
        -ForegroundColor Yellow

    Write-Host ""

    $answer = Read-Host `
        "Type APPLY to continue"

    if ($answer -ne "APPLY") {

        Write-Host ""
        Write-Host "Cancelled." `
            -ForegroundColor Yellow

        return
    }


    # =====================================================
    # Rename directories
    # =====================================================

    Write-Host ""
    Write-Host "Renaming directories..." `
        -ForegroundColor Yellow

    foreach ($Item in $RenameMap) {

        $Source = Get-SourcePath $Item.From
        $Target = Get-SourcePath $Item.To

        if (-not (Test-Path -LiteralPath $Source)) {

            Write-Host "[SKIP] Source not found:" `
                -ForegroundColor DarkYellow

            Write-Host "  $($Item.From)"

            continue
        }


        #
        # Case-only rename:
        #
        # front -> Front
        #
        # Git/Windows ممکن است این را rename محسوب نکند.
        #
        # پس:
        #
        # front
        #   ↓
        # __rename_temp_front
        #   ↓
        # Front
        #


        $Temp = Get-TempPath $Item.From

        if (Test-Path -LiteralPath $Temp) {

            Remove-Item `
                -LiteralPath $Temp `
                -Recurse `
                -Force
        }


        Write-Host ""
        Write-Host "[RENAME]" `
            -ForegroundColor Green

        Write-Host "  $($Item.From)"
        Write-Host "  -> $($Item.To)"


        git mv `
            -- `
            $Source `
            $Temp

        if ($LASTEXITCODE -ne 0) {

            throw `
                "Failed temporary rename: $($Item.From)"
        }


        git mv `
            -- `
            $Temp `
            $Target

        if ($LASTEXITCODE -ne 0) {

            throw `
                "Failed final rename: $($Item.To)"
        }
    }


    # =====================================================
    # Update namespaces
    # =====================================================

    Write-Host ""
    Write-Host "Updating namespace references..." `
        -ForegroundColor Yellow

    $Files = Get-ChildItem `
        -LiteralPath $Root `
        -Recurse `
        -File `
        -ErrorAction SilentlyContinue |
        Where-Object {

            $_.Extension -eq ".php" -and
            -not (IsExcluded $_.FullName)

        }


    foreach ($File in $Files) {

        $Content = Get-Content `
            -LiteralPath $File.FullName `
            -Raw `
            -ErrorAction SilentlyContinue

        if ($null -eq $Content) {
            continue
        }

        $Original = $Content


        foreach ($Map in $ReferenceMap) {

            $Content = $Content.Replace(
                $Map.From,
                $Map.To
            )
        }


        if ($Content -ne $Original) {

            Set-Content `
                -LiteralPath $File.FullName `
                -Value $Content `
                -Encoding UTF8


            $Relative = $File.FullName.Substring(
                $Root.Length + 1
            )

            Write-Host "[UPDATED] $Relative" `
                -ForegroundColor Green
        }
    }


    Write-Host ""
    Write-Host "============================================" `
        -ForegroundColor Green

    Write-Host "APPLY COMPLETE" `
        -ForegroundColor Green

    Write-Host "============================================" `
        -ForegroundColor Green
}


# =========================================================
# VERIFY
# =========================================================

function Verify {

    Write-Host ""
    Write-Host "Running verification..." `
        -ForegroundColor Yellow

    Write-Host ""


    $Failed = $false


    # =====================================================
    # Check directories
    # =====================================================

    foreach ($Item in $RenameMap) {

        $Target = Get-SourcePath $Item.To

        if (Test-Path -LiteralPath $Target) {

            Write-Host "[OK] $($Item.To)" `
                -ForegroundColor Green

        } else {

            Write-Host "[FAIL] Missing:" `
                -ForegroundColor Red

            Write-Host "  $($Item.To)"

            $Failed = $true
        }
    }


    # =====================================================
    # Check old namespaces
    # =====================================================

    Write-Host ""
    Write-Host "Checking old namespaces..." `
        -ForegroundColor Yellow


    $Files = Get-ChildItem `
        -LiteralPath $Root `
        -Recurse `
        -File `
        -ErrorAction SilentlyContinue |
        Where-Object {

            $_.Extension -eq ".php" -and
            -not (IsExcluded $_.FullName)

        }


    foreach ($Map in $ReferenceMap) {

        foreach ($File in $Files) {

            $Content = Get-Content `
                -LiteralPath $File.FullName `
                -Raw `
                -ErrorAction SilentlyContinue

            if ($null -eq $Content) {
                continue
            }


            if ($Content.Contains($Map.From)) {

                $Relative = $File.FullName.Substring(
                    $Root.Length + 1
                )

                Write-Host "[FAIL] Old namespace found:" `
                    -ForegroundColor Red

                Write-Host "  $Relative"
                Write-Host "  $($Map.From)"

                $Failed = $true
            }
        }
    }


    # =====================================================
    # Git status
    # =====================================================

    Write-Host ""
    Write-Host "Git status:" `
        -ForegroundColor Yellow

    git status --short


    # =====================================================
    # Composer
    # =====================================================

    Write-Host ""
    Write-Host "Composer autoload..." `
        -ForegroundColor Yellow

    composer dump-autoload

    if ($LASTEXITCODE -ne 0) {

        Write-Host "[FAIL] composer dump-autoload" `
            -ForegroundColor Red

        $Failed = $true

    } else {

        Write-Host "[OK] composer dump-autoload" `
            -ForegroundColor Green
    }


    # =====================================================
    # Laravel
    # =====================================================

    Write-Host ""
    Write-Host "Laravel route check..." `
        -ForegroundColor Yellow

    php artisan route:list

    if ($LASTEXITCODE -ne 0) {

        Write-Host "[FAIL] route:list" `
            -ForegroundColor Red

        $Failed = $true

    } else {

        Write-Host "[OK] route:list" `
            -ForegroundColor Green
    }


    # =====================================================
    # Result
    # =====================================================

    Write-Host ""

    if ($Failed) {

        Write-Host "============================================" `
            -ForegroundColor Red

        Write-Host "VERIFICATION FAILED" `
            -ForegroundColor Red

        Write-Host "============================================" `
            -ForegroundColor Red

        exit 1
    }


    Write-Host "============================================" `
        -ForegroundColor Green

    Write-Host "VERIFICATION PASSED" `
        -ForegroundColor Green

    Write-Host "============================================" `
        -ForegroundColor Green
}


# =========================================================
# MAIN
# =========================================================

switch ($Mode) {

    "Scan" {
        Scan
    }

    "Apply" {
        Apply
    }

    "Verify" {
        Verify
    }

}