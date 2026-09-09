<#
.SYNOPSIS
    Automated Controller Setup Script for REIAC Community Laravel Project.
.DESCRIPTION
    Verifies project root, checks Artisan availability, creates required 
    controller subdirectories, and generates all Community & Admin controllers safely.
#>

$ErrorActionPreference = "Stop"

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host " REIAC Community - Controller Setup Automation" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

# 1. Verify project root & artisan
if (-not (Test-Path "artisan")) {
    Write-Host "[ERROR] artisan not found. Please run this script from the Laravel project root directory." -ForegroundColor Red
    exit 1
}

Write-Host "[OK] Project root verified." -ForegroundColor Green

# 2. Create directories if needed
$communityDir = "app/Http/Controllers/Community"
$adminDir = "app/Http/Controllers/Admin"

foreach ($dir in @($communityDir, $adminDir)) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir | Out-Null
        Write-Host "[CREATED] Directory: $dir" -ForegroundColor Yellow
    } else {
        Write-Host "[EXISTS] Directory: $dir" -ForegroundColor Gray
    }
}

# 3. Define controllers to generate
$communityControllers = @(
    "CommunityController", "PostController", "CommentController", "LikeController",
    "ShareController", "SavedPostController", "FollowController", "ProfileController",
    "ActivityController", "NotificationController", "SearchController", "TrendingController", "ReportController"
)

$adminControllers = @(
    "DashboardController", "UserController", "PostController", "CommentController",
    "ReportController", "CategoryController", "TagController", "CountryController",
    "VisaServiceController", "VisaServiceItemController"
)

Write-Host "`n--- Generating Community Controllers ---" -ForegroundColor Cyan
foreach ($controller in $communityControllers) {
    $fullName = "Community/$controller"
    $path = "app/Http/Controllers/Community/$controller.php"
    if (Test-Path $path) {
        Write-Host "[SKIPPED] $fullName already exists." -ForegroundColor DarkGray
    } else {
        php artisan make:controller $fullName
        Write-Host "[CREATED] $fullName" -ForegroundColor Green
    }
}

Write-Host "`n--- Generating Admin Controllers ---" -ForegroundColor Cyan
foreach ($controller in $adminControllers) {
    $fullName = "Admin/$controller"
    $path = "app/Http/Controllers/Admin/$controller.php"
    if (Test-Path $path) {
        Write-Host "[SKIPPED] $fullName already exists." -ForegroundColor DarkGray
    } else {
        php artisan make:controller $fullName
        Write-Host "[CREATED] $fullName" -ForegroundColor Green
    }
}

Write-Host "`n==================================================" -ForegroundColor Cyan
Write-Host " Controller Setup Completed Successfully!" -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Cyan