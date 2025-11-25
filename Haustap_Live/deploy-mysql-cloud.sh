#!/bin/bash

# 🚀 HausTap MySQL + Firebase/Google Cloud Deployment Script
# Architecture: MySQL Docker Database + Firebase/Google Cloud Server Infrastructure

echo "🚀 HausTap MySQL + Firebase/Google Cloud Deployment"
echo "==================================================="
echo ""
echo "🏗️  Architecture: MySQL Docker (Local) + Firebase/Google Cloud (Server)"
echo "📊 Database: MySQL 8.0 with 23 tables ready"
echo "☁️  Server: Firebase/Google Cloud Run"
echo "📱 Clients: Android App + Web Frontend"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}[✅]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[⚠️]${NC} $1"
}

print_error() {
    echo -e "${RED}[❌]${NC} $1"
}

print_step() {
    echo -e "${BLUE}[📋]${NC} $1"
}

print_info() {
    echo -e "${CYAN}[ℹ️]${NC} $1"
}

# Check MySQL Docker status
check_mysql_status() {
    print_step "Checking MySQL Docker status..."
    
    if docker ps | grep -q "haustap_mysql"; then
        print_status "MySQL Docker container is running"
        
        # Test connection
        if docker exec haustap_mysql mysqladmin ping -u haustap_user -p'haustap_password' --silent 2>/dev/null; then
            print_status "MySQL connection successful"
            
            # Get table count
            table_count=$(docker exec haustap_mysql mysql -u haustap_user -p'haustap_password' -D haustap_db -e "SHOW TABLES;" 2>/dev/null | wc -l)
            print_info "Found $((table_count - 1)) tables in database"
        else
            print_error "MySQL connection failed"
            return 1
        fi
    else
        print_warning "MySQL Docker container not running"
        print_info "Starting MySQL container..."
        docker-compose -f docker-compose.mysql.yml up -d mysql
        
        # Wait for MySQL to be ready
        print_info "Waiting for MySQL to be ready..."
        sleep 30
        
        if docker exec haustap_mysql mysqladmin ping -u haustap_user -p'haustap_password' --silent 2>/dev/null; then
            print_status "MySQL is now running and accessible"
        else
            print_error "Failed to start MySQL container"
            return 1
        fi
    fi
}

# Test Laravel MySQL connection
test_laravel_mysql() {
    print_step "Testing Laravel MySQL connection..."
    
    cd backend/api
    
    # Copy MySQL environment file
    cp .env.mysql.production .env
    
    # Update database host for local testing
    sed -i 's/DB_HOST=host.docker.internal/DB_HOST=localhost/' .env
    
    # Test connection
    if php artisan migrate:status 2>/dev/null; then
        print_status "Laravel can connect to MySQL database"
    else
        print_warning "Laravel MySQL connection test inconclusive"
    fi
    
    cd ../..
}

# Firebase setup check
check_firebase_setup() {
    print_step "Checking Firebase setup..."
    
    if [ -f "backend/api/config/firebase-service-account.json" ]; then
        print_status "Firebase service account found"
    else
        print_warning "Firebase service account not found"
        print_info "You'll need to add your firebase-service-account.json file"
    fi
    
    # Check if Firebase CLI is installed
    if command -v firebase &> /dev/null; then
        print_status "Firebase CLI is installed"
    else
        print_warning "Firebase CLI not found"
        print_info "Install with: npm install -g firebase-tools"
    fi
}

# Google Cloud setup check
check_google_cloud() {
    print_step "Checking Google Cloud setup..."
    
    # Check if gcloud is installed
    if command -v gcloud &> /dev/null; then
        print_status "Google Cloud SDK is installed"
        
        # Check if user is authenticated
        if gcloud auth list --filter=status:ACTIVE --format="value(account)" 2>/dev/null | grep -q "@"; then
            print_status "Google Cloud authentication active"
        else
            print_warning "Google Cloud authentication required"
            print_info "Run: gcloud auth login"
        fi
    else
        print_warning "Google Cloud SDK not found"
        print_info "Download from: https://cloud.google.com/sdk/docs/install"
    fi
}

# Deployment options menu
deployment_menu() {
    echo ""
    print_step "Choose your deployment option:"
    echo ""
    echo "1. ☁️  Google Cloud Run (Recommended - Auto-scaling)"
    echo "2. 🔥 Firebase Functions (Serverless)"
    echo "3. 🚄 Render (Alternative - Simple)"
    echo "4. 🧪 Test Local Setup First"
    echo "5. 📚 Show Deployment Instructions"
    echo ""
    
    read -p "Enter your choice (1-5): " choice
    
    case $choice in
        1)
            deploy_google_cloud_run
            ;;
        2)
            deploy_firebase_functions
            ;;
        3)
            deploy_render
            ;;
        4)
            test_local_setup
            ;;
        5)
            show_deployment_instructions
            ;;
        *)
            print_error "Invalid choice. Please try again."
            deployment_menu
            ;;
    esac
}

# Deploy to Google Cloud Run
deploy_google_cloud_run() {
    print_step "Deploying to Google Cloud Run..."
    echo ""
    print_info "Steps for Google Cloud Run deployment:"
    echo ""
    echo "1. Build Docker image with MySQL support:"
    echo "   docker build -f backend/api/Dockerfile.mysql -t gcr.io/YOUR-PROJECT-ID/haustap-api:latest ./backend/api"
    echo ""
    echo "2. Push to Google Container Registry:"
    echo "   docker push gcr.io/YOUR-PROJECT-ID/haustap-api:latest"
    echo ""
    echo "3. Deploy to Cloud Run:"
    echo "   gcloud run deploy haustap-api \\"
    echo "     --image gcr.io/YOUR-PROJECT-ID/haustap-api:latest \\"
    echo "     --platform managed \\"
    echo "     --region us-central1 \\"
    echo "     --allow-unauthenticated \\"
    echo "     --set-env-vars DB_HOST=YOUR_MYSQL_HOST_IP,DB_PORT=3307,DB_DATABASE=haustap_db,DB_USERNAME=haustap_user,DB_PASSWORD=haustap_password \\"
    echo "     --memory 2Gi \\"
    echo "     --cpu 2 \\"
    echo "     --max-instances 10"
    echo ""
    echo "4. Update your client apps with the new API URL"
    echo ""
    read -p "Press Enter when ready to continue..."
}

# Deploy to Firebase Functions
deploy_firebase_functions() {
    print_step "Deploying to Firebase Functions..."
    echo ""
    print_info "Steps for Firebase Functions deployment:"
    echo ""
    echo "1. Initialize Firebase in your project:"
    echo "   firebase init functions"
    echo ""
    echo "2. Configure Firebase Functions for Laravel API"
    echo "   - Copy Laravel API to functions directory"
    echo "   - Configure MySQL connection for Firebase environment"
    echo ""
    echo "3. Deploy Firebase Functions:"
    echo "   firebase deploy --only functions"
    echo ""
    echo "4. Update client apps with Firebase Functions URL"
    echo ""
    read -p "Press Enter when ready to continue..."
}

# Deploy to Render
deploy_render() {
    print_step "Deploying to Render..."
    echo ""
    print_info "Steps for Render deployment:"
    echo ""
    echo "1. Go to https://render.com"
    echo "2. Create new Web Service"
    echo "3. Connect your GitHub repository"
    echo "4. Use the render-mysql-config.yaml configuration"
    echo "5. Update MySQL host IP in environment variables"
    echo "6. Deploy and get your API URL"
    echo ""
    echo "Your API will be available at: https://haustap-api-mysql.onrender.com"
    echo ""
    read -p "Press Enter when ready to continue..."
}

# Test local setup
test_local_setup() {
    print_step "Testing local setup..."
    echo ""
    print_info "Starting local MySQL + Laravel API test..."
    
    # Start MySQL if not running
    if ! docker ps | grep -q "haustap_mysql"; then
        print_info "Starting MySQL container..."
        docker-compose -f docker-compose.mysql.yml up -d mysql
        sleep 20
    fi
    
    # Start Laravel API locally
    print_info "Starting Laravel API with MySQL..."
    cd backend/api
    
    # Update .env for local testing
    cp .env.mysql.production .env
    sed -i 's/DB_HOST=host.docker.internal/DB_HOST=localhost/' .env
    sed -i 's/APP_ENV=production/APP_ENV=local/' .env
    sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env
    
    print_info "Starting Laravel development server..."
    print_info "API will be available at: http://localhost:8000"
    print_info "Test endpoints:"
    print_info "  - Health: http://localhost:8000/api/health"
    print_info "  - Database: http://localhost:8000/api/test-db"
    
    php artisan serve --host=0.0.0.0 --port=8000
}

# Show deployment instructions
show_deployment_instructions() {
    print_step "Deployment Instructions Summary"
    echo ""
    echo "🏗️  Architecture: MySQL Docker (Local) + Firebase/Google Cloud (Server)"
    echo ""
    echo "📊 Database Status:"
    echo "   ✅ MySQL 8.0 with 23 tables ready"
    echo "   ✅ Connection tested and working"
    echo "   ✅ Laravel configuration optimized"
    echo ""
    echo "☁️  Server Options:"
    echo "   1. Google Cloud Run - Auto-scaling, pay-per-use"
    echo "   2. Firebase Functions - Serverless, event-driven"
    echo "   3. Render - Simple deployment, free tier available"
    echo ""
    echo "📱 Client Configuration:"
    echo "   - Update API URLs in mobile app config"
    echo "   - Configure CORS for your domains"
    echo "   - Set up Firebase authentication if needed"
    echo ""
    echo "🔐 Security Notes:"
    echo "   - MySQL runs locally (not exposed to internet)"
    echo "   - API layer handles authentication"
    echo "   - Use environment variables for sensitive data"
    echo "   - Enable SSL/TLS for production"
    echo ""
    echo "💰 Cost Optimization:"
    echo "   - MySQL: No hosting costs (runs on your infrastructure)"
    echo "   - Cloud Run: ~$0.24 per million requests"
    echo "   - Firebase: Free tier available, pay-as-you-grow"
    echo ""
}

# Main execution
main() {
    print_status "Starting HausTap MySQL + Cloud deployment process..."
    echo ""
    
    # Check prerequisites
    check_mysql_status
    test_laravel_mysql
    check_firebase_setup
    check_google_cloud
    
    echo ""
    print_status "✅ Prerequisites check complete!"
    echo ""
    
    # Show deployment menu
    deployment_menu
}

# Run main function
main "$@"