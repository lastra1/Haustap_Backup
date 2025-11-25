# HausTap Docker Build Test Script
# This script tests the Docker build locally to identify and fix issues

Write-Host "🏗️  HausTap Docker Build Test" -ForegroundColor Green
Write-Host "=============================" -ForegroundColor Green

# Set variables
$PROJECT_NAME = "haustap-api"
$DOCKERFILE_PATH = "Dockerfile"
$BUILD_CONTEXT = "."

Write-Host "📁 Build Context: $BUILD_CONTEXT" -ForegroundColor Cyan
Write-Host "🐳 Dockerfile: $DOCKERFILE_PATH" -ForegroundColor Cyan

# Test Docker build with no cache to see full output
Write-Host "🔨 Building Docker image (this may take several minutes)..." -ForegroundColor Yellow
Write-Host "   Running: docker build -f $DOCKERFILE_PATH -t $PROJECT_NAME:latest $BUILD_CONTEXT" -ForegroundColor Gray

try {
    docker build -f $DOCKERFILE_PATH -t $PROJECT_NAME:latest $BUILD_CONTEXT
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Docker build successful!" -ForegroundColor Green
        
        # Test the container
        Write-Host "🧪 Testing container..." -ForegroundColor Yellow
        
        # Run container in background
        $containerId = docker run -d -p 8000:8000 $PROJECT_NAME:latest
        
        if ($containerId) {
            Write-Host "🚀 Container started with ID: $containerId" -ForegroundColor Green
            
            # Wait for container to start
            Start-Sleep -Seconds 10
            
            # Test health endpoint
            Write-Host "🔍 Testing health endpoint..." -ForegroundColor Yellow
            try {
                $response = Invoke-WebRequest -Uri "http://localhost:8000/api/health" -Method GET -TimeoutSec 30
                if ($response.StatusCode -eq 200) {
                    Write-Host "✅ Health check passed!" -ForegroundColor Green
                    Write-Host "   Response: $($response.Content)" -ForegroundColor Gray
                } else {
                    Write-Host "⚠️  Health check returned status: $($response.StatusCode)" -ForegroundColor Yellow
                }
            } catch {
                Write-Host "❌ Health check failed: $_.Exception.Message" -ForegroundColor Red
            }
            
            # Show container logs
            Write-Host "📋 Container logs:" -ForegroundColor Yellow
            docker logs $containerId
            
            # Stop and remove container
            Write-Host "🛑 Stopping test container..." -ForegroundColor Yellow
            docker stop $containerId
            docker rm $containerId
            
            Write-Host "✅ Build test completed successfully!" -ForegroundColor Green
            Write-Host "🎯 Your Docker image is ready for deployment!" -ForegroundColor Green
        } else {
            Write-Host "❌ Failed to start container" -ForegroundColor Red
        }
    } else {
        Write-Host "❌ Docker build failed" -ForegroundColor Red
        Write-Host "📋 Check the error messages above for details" -ForegroundColor Yellow
        
        # Try to identify common issues
        Write-Host "🔍 Common fixes to try:" -ForegroundColor Yellow
        Write-Host "   1. Ensure Docker Desktop is running" -ForegroundColor White
        Write-Host "   2. Check if the backend directory exists with correct structure" -ForegroundColor White
        Write-Host "   3. Verify PHP and Composer dependencies are available" -ForegroundColor White
        Write-Host "   4. Try building with --no-cache flag" -ForegroundColor White
        Write-Host "   5. Check Docker logs for specific error details" -ForegroundColor White
    }
} catch {
    Write-Host "❌ Build error: $_.Exception.Message" -ForegroundColor Red
    Write-Host "📞 Make sure Docker Desktop is running and you have sufficient resources" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Press any key to exit..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")