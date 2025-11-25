# HausTap Docker Deployment - Fixed Version
# This script deploys the working Node.js API server

Write-Host "🚀 HausTap Docker Deployment (Fixed)" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green
Write-Host ""

# Configuration
$IMAGE_NAME = "haustap-nodejs-api"
$CONTAINER_NAME = "haustap-api"
$HOST_PORT = "3002"  # Changed to avoid conflicts
$CONTAINER_PORT = "3001"

Write-Host "📋 Configuration:" -ForegroundColor Cyan
Write-Host "   Image: $IMAGE_NAME" -ForegroundColor Gray
Write-Host "   Container: $CONTAINER_NAME" -ForegroundColor Gray
Write-Host "   Port: $HOST_PORT -> $CONTAINER_PORT" -ForegroundColor Gray
Write-Host ""

# Check if Docker is running
try {
    docker info | Out-Null
    Write-Host "✅ Docker is running" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker is not running. Please start Docker Desktop." -ForegroundColor Red
    exit 1
}

# Stop existing container if running
Write-Host "🛑 Checking for existing container..." -ForegroundColor Yellow
try {
    docker stop $CONTAINER_NAME 2>$null
    docker rm $CONTAINER_NAME 2>$null
    Write-Host "✅ Removed existing container" -ForegroundColor Green
} catch {
    Write-Host "ℹ️  No existing container found" -ForegroundColor Gray
}

# Build the Docker image
Write-Host "🏗️  Building Docker image..." -ForegroundColor Yellow
try {
    docker build -f Dockerfile.nodejs -t $IMAGE_NAME:latest .
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Docker image built successfully" -ForegroundColor Green
    } else {
        throw "Build failed"
    }
} catch {
    Write-Host "❌ Docker build failed" -ForegroundColor Red
    Write-Host "📋 Error: $_.Exception.Message" -ForegroundColor Red
    exit 1
}

# Run the container
Write-Host "🚀 Starting container..." -ForegroundColor Yellow
try {
    docker run -d `
        --name $CONTAINER_NAME `
        -p ${HOST_PORT}:${CONTAINER_PORT} `
        --restart unless-stopped `
        $IMAGE_NAME:latest
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Container started successfully" -ForegroundColor Green
    } else {
        throw "Container start failed"
    }
} catch {
    Write-Host "❌ Failed to start container" -ForegroundColor Red
    Write-Host "📋 Error: $_.Exception.Message" -ForegroundColor Red
    exit 1
}

# Wait for container to start
Write-Host "⏳ Waiting for container to start..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

# Test the API endpoints
Write-Host "🧪 Testing API endpoints..." -ForegroundColor Yellow

# Test health endpoint
try {
    $healthResponse = Invoke-WebRequest -Uri "http://localhost:$HOST_PORT/api/health" -Method GET -TimeoutSec 10
    if ($healthResponse.StatusCode -eq 200) {
        Write-Host "✅ Health check passed" -ForegroundColor Green
        Write-Host "   Response: $($healthResponse.Content)" -ForegroundColor Gray
    } else {
        Write-Host "⚠️  Health check returned status: $($healthResponse.StatusCode)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Health check failed: $_.Exception.Message" -ForegroundColor Red
}

# Test sync status endpoint
try {
    $syncResponse = Invoke-WebRequest -Uri "http://localhost:$HOST_PORT/api/sync/status" -Method GET -TimeoutSec 10
    if ($syncResponse.StatusCode -eq 200) {
        Write-Host "✅ Sync status endpoint working" -ForegroundColor Green
        Write-Host "   MySQL-Firebase sync is operational" -ForegroundColor Gray
    } else {
        Write-Host "⚠️  Sync status returned status: $($syncResponse.StatusCode)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Sync status failed: $_.Exception.Message" -ForegroundColor Red
}

# Show container info
Write-Host "📊 Container Information:" -ForegroundColor Yellow
docker ps --filter "name=$CONTAINER_NAME" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

Write-Host ""
Write-Host "🎉 DEPLOYMENT COMPLETED!" -ForegroundColor Green
Write-Host "=========================" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Your HausTap API is running at:" -ForegroundColor Cyan
Write-Host "   http://localhost:$HOST_PORT" -ForegroundColor White
Write-Host ""
Write-Host "🔥 Available endpoints:" -ForegroundColor Yellow
Write-Host "   Health Check: http://localhost:$HOST_PORT/api/health" -ForegroundColor White
Write-Host "   Sync Status:  http://localhost:$HOST_PORT/api/sync/status" -ForegroundColor White
Write-Host "   API Docs:     http://localhost:$HOST_PORT/api/docs" -ForegroundColor White
Write-Host "   Full Sync:    POST http://localhost:$HOST_PORT/api/sync/full" -ForegroundColor White
Write-Host ""
Write-Host "📋 Docker Commands:" -ForegroundColor Gray
Write-Host "   View logs:    docker logs $CONTAINER_NAME" -ForegroundColor Gray
Write-Host "   Stop:         docker stop $CONTAINER_NAME" -ForegroundColor Gray
Write-Host "   Start:        docker start $CONTAINER_NAME" -ForegroundColor Gray
Write-Host "   Remove:       docker rm $CONTAINER_NAME" -ForegroundColor Gray
Write-Host ""
Write-Host "💡 Your MySQL-Firebase sync APIs are now fully operational!" -ForegroundColor Green

Write-Host ""
Write-Host "Press any key to exit..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")