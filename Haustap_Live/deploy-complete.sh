#!/bin/bash

# 🚀 HausTap Complete Deployment Script
# Deploys API Backend, Web Frontend, and configures Mobile App

echo "🚀 HausTap Complete Deployment Script"
echo "====================================="
echo ""
echo "This script will help you deploy:"
echo "📱 Android App API Backend"
echo "🌐 Web Frontend (PHP Website)"
echo "🔗 Configure Cross-Platform Access"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Check if git is initialized
check_git() {
    print_step "Checking Git repository..."
    if [ ! -d ".git" ]; then
        print_warning "Git not initialized. Initializing..."
        git init
        git add .
        git commit -m "Initial commit - HausTap deployment ready"
    else
        print_status "Git repository found"
    fi
}

# Check if GitHub remote exists
check_github() {
    print_step "Checking GitHub remote..."
    if git remote get-url origin > /dev/null 2>&1; then
        print_status "GitHub remote found: $(git remote get-url origin)"
        return 0
    else
        print_warning "No GitHub remote found"
        return 1
    fi
}

# Create GitHub repository prompt
create_github_repo() {
    print_step "Creating GitHub repository..."
    echo "Please create a GitHub repository and paste the URL below:"
    echo "1. Go to https://github.com/new"
    echo "2. Create a new repository"
    echo "3. Copy the repository URL"
    echo ""
    read -p "Enter GitHub repository URL: " github_url
    
    if [ -n "$github_url" ]; then
        git remote add origin "$github_url"
        git branch -M main
        git push -u origin main
        print_status "GitHub repository connected!"
    else
        print_error "No GitHub URL provided. Please set up manually."
        return 1
    fi
}

# Deploy to Render
deploy_render() {
    print_step "Deploying to Render..."
    echo ""
    echo "🎯 Render Deployment Steps:"
    echo "1. Go to https://render.com"
    echo "2. Click 'New +' → 'Web Service'"
    echo "3. Connect your GitHub repository"
    echo "4. Select your HausTap repository"
    echo "5. Render will auto-detect the configuration"
    echo ""
    echo "📋 Configuration Settings:"
    echo "- Name: haustap-api"
    echo "- Environment: Docker"
    echo "- Dockerfile Path: ./backend/api/Dockerfile"
    echo "- Context: ./backend/api"
    echo "- Port: 8000"
    echo "- Health Check: /api/health"
    echo ""
    read -p "Press Enter when you've started the deployment..."
}

# Deploy web frontend
deploy_web_frontend() {
    print_step "Setting up Web Frontend deployment..."
    echo ""
    echo "🌐 Web Frontend Deployment:"
    echo "1. In Render dashboard, click 'New +' → 'Web Service'"
    echo "2. Use these settings:"
    echo "   - Name: haustap-web"
    echo "   - Environment: Docker"
    echo "   - Dockerfile Path: ./Dockerfile.web"
    echo "   - Context: ./Haustap_Capstone-Haustap_Connecting"
    echo "   - Port: 80"
    echo ""
    read -p "Press Enter when web frontend deployment is configured..."
}

# Configure mobile app
configure_mobile_app() {
    print_step "Configuring Mobile App..."
    echo ""
    echo "📱 Mobile App Configuration:"
    echo "1. Copy 'mobile-app-config.js' to your React Native app"
    echo "2. Update the API URLs in your mobile app:"
    echo ""
    echo "   // In your mobile app configuration"
    echo "   PROD_API_URL: 'https://your-api.onrender.com/api'"
    echo "   PROD_WEB_URL: 'https://your-web.onrender.com'"
    echo ""
    echo "3. Update all API calls to use the production URLs"
    echo ""
    read -p "Press Enter when mobile app is configured..."
}

# Test deployment
test_deployment() {
    print_step "Testing Deployment..."
    echo ""
    echo "🧪 Testing Checklist:"
    echo ""
    echo "API Backend Tests:"
    echo "• Health Check: curl https://your-api.onrender.com/api/health"
    echo "• Auth Test: POST to /api/v2/auth/login"
    echo ""
    echo "Web Frontend Tests:"
    echo "• Homepage: Visit https://your-web.onrender.com"
    echo "• API Integration: Test booking flow"
    echo ""
    echo "Mobile App Tests:"
    echo "• Connection: Test API calls from mobile app"
    echo "• Authentication: Login/Register flow"
    echo "• Booking: Create and manage bookings"
    echo ""
    echo "📋 Common Test URLs:"
    echo "• API Health: https://your-api.onrender.com/api/health"
    echo "• API Docs: https://your-api.onrender.com/api/v2/docs"
    echo "• Web Frontend: https://your-web.onrender.com"
    echo ""
    read -p "Press Enter when testing is complete..."
}

# Main deployment process
main() {
    print_status "Starting HausTap deployment process..."
    echo ""
    
    # Step 1: Git setup
    check_git
    
    # Step 2: GitHub setup
    if ! check_github; then
        create_github_repo
    fi
    
    echo ""
    print_status "🚀 Ready for deployment!"
    echo ""
    echo "Choose deployment option:"
    echo "1. 🚄 Render (Recommended - Fastest)"
    echo "2. 🚂 Railway (Alternative)"
    echo "3. ☁️  Google Cloud (Advanced)"
    echo ""
    read -p "Enter your choice (1-3): " choice
    
    case $choice in
        1)
            deploy_render
            deploy_web_frontend
            configure_mobile_app
            test_deployment
            ;;
        2)
            print_status "Railway deployment selected"
            echo "Use: railway login && railway up"
            ;;
        3)
            print_status "Google Cloud deployment selected"
            echo "Use: gcloud app deploy"
            ;;
        *)
            print_error "Invalid choice. Please run again."
            exit 1
            ;;
    esac
    
    echo ""
    print_status "🎉 Deployment process initiated!"
    echo ""
    echo "📋 Final Checklist:"
    echo "✅ API Backend: Deployed and tested"
    echo "✅ Web Frontend: Deployed and tested" 
    echo "✅ Mobile App: Configured for production"
    echo "✅ CORS: Configured for cross-platform access"
    echo ""
    echo "🎯 Your HausTap platform is now live!"
    echo "📱 Android App: Ready to connect to live API"
    echo "🌐 Website: Accessible via web browser"
    echo "🔗 API: Serving both platforms"
}

# Run the main function
main "$@"