param(
  [int]$LegacyPort = 5001,
  [int]$ApiPort = 8001,
  [int]$ExpoPort = 8082,
  [string]$RadminIp
)

# One-click to start Legacy PHP, Laravel API, and Expo Web concurrently
$Root = $PSScriptRoot
Write-Host "Starting all dev services (Radmin-ready)..." -ForegroundColor Cyan

# Detect Radmin VPN IPv4 (26.x.x.x) if not provided
if (-not $RadminIp -or $RadminIp -eq '') {
  try {
    $RadminIp = (Get-NetIPAddress | Where-Object { $_.IPAddress -like '26.*' -and $_.AddressFamily -eq 'IPv4' }).IPAddress | Select-Object -First 1
  } catch {}
}
if (-not $RadminIp -or $RadminIp -eq '') { Write-Host "Warning: Radmin IP not detected automatically. Pass -RadminIp 26.x.x.x if needed." -ForegroundColor Yellow }

# Legacy PHP server (friendly router) - bind to 0.0.0.0
$legacyCmd = "php -S 0.0.0.0:$LegacyPort router.php"
Write-Host "Legacy: $legacyCmd" -ForegroundColor DarkGray
Start-Process -FilePath powershell -ArgumentList $legacyCmd -WorkingDirectory $Root

# Laravel API server - bind to 0.0.0.0
$apiCmd = "php artisan serve --host 0.0.0.0 --port $ApiPort"
Write-Host "API:    $apiCmd" -ForegroundColor DarkGray
Start-Process -FilePath powershell -ArgumentList $apiCmd -WorkingDirectory (Join-Path $Root 'backend\api')

# Expo Web - bind to 0.0.0.0 and point API base to Laravel on Radmin IP
$expoCmd = "`$env:EXPO_PUBLIC_API_BASE='http://$RadminIp:$ApiPort'; `$env:HOST='0.0.0.0'; npx expo start --web --port $ExpoPort --clear"
Write-Host "Expo:   $expoCmd" -ForegroundColor DarkGray
Start-Process -FilePath powershell -ArgumentList "-NoProfile", "-Command", $expoCmd -WorkingDirectory (Join-Path $Root 'android-capstone-main\HausTap')

$hostHint = if ($RadminIp) { $RadminIp } else { 'your-Radmin-IP' }
Write-Host "Open: Legacy http://$hostHint:$LegacyPort/ | API http://$hostHint:$ApiPort/ | Expo http://$hostHint:$ExpoPort/" -ForegroundColor Green