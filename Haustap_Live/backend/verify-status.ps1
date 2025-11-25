# Quick verification script for HausTap
Write-Host "🔍 HausTap Status Check" -ForegroundColor Green
Write-Host "========================" -ForegroundColor Green
Write-Host ""

# Check if Node.js server is running
Write-Host "📡 Checking Node.js server..." -ForegroundColor Yellow
try {
    $nodeProcess = Get-Process node -ErrorAction Stop
    Write-Host "✅ Node.js is running (PID: $($nodeProcess.Id))" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Node.js server not detected" -ForegroundColor Yellow
}

# Test API endpoints
Write-Host "🧪 Testing API endpoints..." -ForegroundColor Yellow

$endpoints = @(
    @{Name="Health Check"; Url="http://localhost:3001/api/health"},
    @{Name="Sync Status"; Url="http://localhost:3001/api/sync/status"},
    @{Name="API Docs"; Url="http://localhost:3001/api/docs"}
)

$allWorking = $true

foreach ($endpoint in $endpoints) {
    try {
        $response = Invoke-WebRequest -Uri $endpoint.Url -Method GET -TimeoutSec 5
        if ($response.StatusCode -eq 200) {
            Write-Host "✅ $($endpoint.Name): WORKING" -ForegroundColor Green
            
            # Show key info for sync status
            if ($endpoint.Name -eq "Sync Status") {
                $data = $response.Content | ConvertFrom-Json
                Write-Host "   📊 MySQL Users: $($data.data.mysql_users)" -ForegroundColor Gray
                Write-Host "   📊 MySQL Bookings: $($data.data.mysql_bookings)" -ForegroundColor Gray
                Write-Host "   📊 Firebase Synced Users: $($data.data.users_with_firebase_id)" -ForegroundColor Gray
            }
        } else {
            Write-Host "⚠️  $($endpoint.Name): Status $($response.StatusCode)" -ForegroundColor Yellow
            $allWorking = $false
        }
    } catch {
        Write-Host "❌ $($endpoint.Name): FAILED" -ForegroundColor Red
        $allWorking = $false
    }
}

# Check port usage
Write-Host "🔌 Checking port usage..." -ForegroundColor Yellow
try {
    $portCheck = netstat -ano | findstr :3001
    if ($portCheck) {
        Write-Host "✅ Port 3001 is in use" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Port 3001 not detected" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  Could not check ports" -ForegroundColor Yellow
}

# Final status
Write-Host ""
if ($allWorking) {
    Write-Host "🎉 ALL SYSTEMS OPERATIONAL!" -ForegroundColor Green
    Write-Host "💡 Your HausTap MySQL-Firebase integration is working perfectly!" -ForegroundColor Green
} else {
    Write-Host "⚠️  Some issues detected - check the details above" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "📋 Next Steps:" -ForegroundColor Cyan
Write-Host "   • Test sync: POST http://localhost:3001/api/sync/full" -ForegroundColor White
Write-Host "   • View docs: http://localhost:3001/api/docs" -ForegroundColor White
Write-Host "   • Deploy to cloud: Use deploy-docker-fixed.ps1" -ForegroundColor White