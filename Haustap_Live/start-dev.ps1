param([int]$Port = 8001)

# One-click dev server for HausTap (Windows/PowerShell)
# - Serves the entire monorepo as docroot so /mock-api is available
# - Uses the friendly router at /public/index.php for clean routes (e.g., /login, /signup)

$Root   = $PSScriptRoot
$Router = Join-Path $Root 'public\index.php'

Write-Host "Starting HausTap dev server on http://127.0.0.1:$Port" -ForegroundColor Cyan
Write-Host "Docroot: $Root" -ForegroundColor DarkGray
Write-Host "Router:  $Router" -ForegroundColor DarkGray

# Helpful environment flags
$env:APP_ENV   = $env:APP_ENV   -as [string]
if (-not $env:APP_ENV)   { $env:APP_ENV = 'development' }
$env:APP_DEBUG = $env:APP_DEBUG -as [string]
if (-not $env:APP_DEBUG) { $env:APP_DEBUG = 'true' }

# Launch PHP built-in server
php -S 127.0.0.1:$Port -t $Root $Router

