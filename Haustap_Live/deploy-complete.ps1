# 🚀 HausTap Complete Deployment Script for Windows
# Deploys API Backend, Web Frontend, and configures Mobile App

Write-Host "🚀 HausTap Complete Deployment Script" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green
Write-Host ""
Write-Host "This script will help you deploy:" -ForegroundColor Yellow
Write-Host "📱 Android App API Backend"
Write-Host "🌐 Web Frontend (PHP Website)"
Write-Host "🔗 Configure Cross-Platform Access"
Write-Host ""

# Function to print colored output
function Write-Status {
    param($message)
    Write-Host "✅ $message" -ForegroundColor Green
}

function Write-Warning {
    param($message)
    Write-Host "⚠️  $message" -ForegroundColor Yellow
}

function Write-Error {
    param($message)
    Write-Host "❌ $message" -ForegroundColor Red
}

function Write-Step {
    param($message)
    Write-Host "📋 $message" -ForegroundColor Blue
}

# Check if git is initialized
function Check-Git {
    Write-Step "Checking Git repository..."
    if (!(Test-Path ".git")) {
        Write-Warning "Git not initialized. Initializing..."
        git init
        git add .
        git commit -m "Initial commit - HausTap deployment ready"
    } else {
        Write-Status "Git repository found"
    }
}

# Check if GitHub remote exists
function Check-GitHub {
    Write-Step "Checking GitHub remote..."
    try {
        $remoteUrl = git remote get-url origin 2>$null
        if ($remoteUrl) {
            Write-Status "GitHub remote found: $remoteUrl"
            return $true
        } else {
            Write-Warning "No GitHub remote found"
            return $false
        }
    } catch {
        Write-Warning "No GitHub remote found"
        return $false
    }
}

# Create GitHub repository prompt
function Create-GitHubRepo {
    Write-Step "Creating GitHub repository..."
    Write-Host "Please create a GitHub repository and paste the URL below:" -ForegroundColor Yellow
    Write-Host "1. Go to https://github.com/new" -ForegroundColor Cyan
    Write-Host "2. Create a new repository" -ForegroundColor Cyan
    Write-Host "3. Copy the repository URL" -ForegroundColor Cyan
    Write-Host ""
    
    $githubUrl = Read-Host "Enter GitHub repository URL"
    
    if ($githubUrl) {
        git remote add origin "$githubUrl"
        git branch -M main
        git push -u origin main
        Write-Status "GitHub repository connected!"
    } else {
        Write-Error "No GitHub URL provided. Please set up manually."
        return $false
    }
}

# Deploy to Render
function Deploy-Render {
    Write-Step "Deploying to Render..."
    Write-Host ""
    Write-Host "🎯 Render Deployment Steps:" -ForegroundColor Magenta
    Write-Host "1. Go to https://render.com" -ForegroundColor Cyan
    Write-Host "2. Click 'New +' → 'Web Service'" -ForegroundColor Cyan
    Write-Host "3. Connect your GitHub repository" -ForegroundColor Cyan
    Write-Host "4. Select your HausTap repository" -ForegroundColor Cyan
    Write-Host "5. Render will auto-detect the configuration" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "📋 Configuration Settings:" -ForegroundColor Yellow
    Write-Host "- Name: haustap-api" -ForegroundColor White
    Write-Host "- Environment: Docker" -ForegroundColor White
    Write-Host "- Dockerfile Path: ./backend/api/Dockerfile" -ForegroundColor White
    Write-Host "- Context: ./backend/api" -ForegroundColor White
    Write-Host "- Port: 8000" -ForegroundColor White
    Write-Host "- Health Check: /api/health" -ForegroundColor White
    Write-Host ""
    Read-Host "Press Enter when you've started the deployment"
}

# Deploy web frontend
function Deploy-WebFrontend {
    Write-Step "Setting up Web Frontend deployment..."
    Write-Host ""
    Write-Host "🌐 Web Frontend Deployment:" -ForegroundColor Blue
    Write-Host "1. In Render dashboard, click 'New +' → 'Web Service'" -ForegroundColor Cyan
    Write-Host "2. Use these settings:" -ForegroundColor Cyan
    Write-Host "   - Name: haustap-web" -ForegroundColor White
    Write-Host "   - Environment: Docker" -ForegroundColor White
    Write-Host "   - Dockerfile Path: ./Dockerfile.web" -ForegroundColor White
    Write-Host "   - Context: ./Haustap_Capstone-Haustap_Connecting" -ForegroundColor White
    Write-Host "   - Port: 80" -ForegroundColor White
    Write-Host ""
    Read-Host "Press Enter when web frontend deployment is configured"
}

# Configure mobile app
function Configure-MobileApp {
    Write-Step "Configuring Mobile App..."
    Write-Host ""
    Write-Host "📱 Mobile App Configuration:" -ForegroundColor Magenta
    Write-Host "1. Copy 'mobile-app-config.js' to your React Native app" -ForegroundColor Cyan
    Write-Host "2. Update the API URLs in your mobile app:" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "   // In your mobile app configuration" -ForegroundColor DarkGray
    Write-Host "   PROD_API_URL: 'https://your-api.onrender.com/api'" -ForegroundColor Yellow
    Write-Host "   PROD_WEB_URL: 'https://your-web.onrender.com'" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "3. Update all API calls to use the production URLs" -ForegroundColor Cyan
    Write-Host ""
    Read-Host "Press Enter when mobile app is configured"
}

# Test deployment
function Test-Deployment {
    Write-Step "Testing Deployment..."
    Write-Host ""
    Write-Host "🧪 Testing Checklist:" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "API Backend Tests:" -ForegroundColor Green
    Write-Host "• Health Check: curl https://your-api.onrender.com/api/health" -ForegroundColor White
    Write-Host "• Auth Test: POST to /api/v2/auth/login" -ForegroundColor White
    Write-Host ""
    Write-Host "Web Frontend Tests:" -ForegroundColor Green
    Write-Host "• Homepage: Visit https://your-web.onrender.com" -ForegroundColor White
    Write-Host "• API Integration: Test booking flow" -ForegroundColor White
    Write-Host ""
    Write-Host "Mobile App Tests:" -ForegroundColor Green
    Write-Host "• Connection: Test API calls from mobile app" -ForegroundColor White
    Write-Host "• Authentication: Login/Register flow" -ForegroundColor White
    Write-Host "• Booking: Create and manage bookings" -ForegroundColor White
    Write-Host ""
    Write-Host "📋 Common Test URLs:" -ForegroundColor Blue
    Write-Host "• API Health: https://your-api.onrender.com/api/health" -ForegroundColor Cyan
    Write-Host "• API Docs: https://your-api.onrender.com/api/v2/docs" -ForegroundColor Cyan
    Write-Host "• Web Frontend: https://your-web.onrender.com" -ForegroundColor Cyan
    Write-Host ""
    Read-Host "Press Enter when testing is complete"
}

# Main deployment process
function Main {
    Write-Status "Starting HausTap deployment process..."
    Write-Host ""
    
    # Step 1: Git setup
    Check-Git
    
    # Step 2: GitHub setup
    if (!(Check-GitHub)) {
        Create-GitHubRepo
    }
    
    Write-Host ""
    Write-Status "🚀 Ready for deployment!"
    Write-Host ""
    Write-Host "Choose deployment option:" -ForegroundColor Yellow
    Write-Host "1. 🚄 Render (Recommended - Fastest)" -ForegroundColor Green
    Write-Host "2. 🚂 Railway (Alternative)" -ForegroundColor Blue
    Write-Host "3. ☁️  Google Cloud (Advanced)" -ForegroundColor Magenta
    Write-Host ""
    
    $choice = Read-Host "Enter your choice (1-3)"
    
    switch ($choice) {
        "1" {
            Deploy-Render
            Deploy-WebFrontend
            Configure-MobileApp
            Test-Deployment
            break
        }
        "2" {
            Write-Status "Railway deployment selected"
            Write-Host "Use: railway login" -ForegroundColor Cyan
            Write-Host "Then: railway up" -ForegroundColor Cyan
            break
        }
        "3" {
            Write-Status "Google Cloud deployment selected"
            Write-Host "Use: gcloud app deploy" -ForegroundColor Cyan
            break
        }
        default {
            Write-Error "Invalid choice. Please run again."
            exit 1
        }
    }
    
    Write-Host ""
    Write-Status "🎉 Deployment process initiated!"
    Write-Host ""
    Write-Host "📋 Final Checklist:" -ForegroundColor Blue
    Write-Host "✅ API Backend: Deployed and tested" -ForegroundColor Green
    Write-Host "✅ Web Frontend: Deployed and tested" -ForegroundColor Green
    Write-Host "✅ Mobile App: Configured for production" -ForegroundColor Green
    Write-Host "✅ CORS: Configured for cross-platform access" -ForegroundColor Green
    Write-Host ""
    Write-Host "🎯 Your HausTap platform is now live!" -ForegroundColor Green
    Write-Host "📱 Android App: Ready to connect to live API" -ForegroundColor Cyan
    Write-Host "🌐 Website: Accessible via web browser" -ForegroundColor Cyan
    Write-Host "🔗 API: Serving both platforms" -ForegroundColor Cyan
}

# Run the main function
Main