#!/bin/bash

# MySQL-Firebase Integration Setup Script for HausTap Service Booking Platform
# This script sets up the connection between MySQL and Firebase APIs

set -e

echo "🚀 HausTap MySQL-Firebase Integration Setup"
echo "=========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    print_error "Please run this script from the Laravel backend directory (backend/api)"
    exit 1
fi

# Function to check if MySQL is running
check_mysql_status() {
    print_status "Checking MySQL status..."
    
    if command -v mysql &> /dev/null; then
        if mysql -u haustap_user -phaustap_password -e "SELECT 1;" &> /dev/null; then
            print_success "MySQL is running and accessible"
            return 0
        else
            print_warning "MySQL is installed but not accessible with current credentials"
            return 1
        fi
    else
        print_warning "MySQL client not found. Please install MySQL or use Docker."
        return 1
    fi
}

# Function to setup MySQL via Docker if not running
setup_mysql_docker() {
    print_status "Setting up MySQL via Docker..."
    
    if command -v docker &> /dev/null; then
        print_status "Starting MySQL Docker container..."
        docker-compose -f docker-compose-mysql-only.yml up -d
        
        # Wait for MySQL to be ready
        print_status "Waiting for MySQL to be ready..."
        sleep 30
        
        # Test connection
        if docker exec haustap_mysql mysql -u haustap_user -phaustap_password -e "SELECT 1;" &> /dev/null; then
            print_success "MySQL Docker container is ready"
            return 0
        else
            print_error "MySQL Docker container failed to start properly"
            return 1
        fi
    else
        print_error "Docker is not installed. Please install Docker or set up MySQL manually."
        return 1
    fi
}

# Function to install PHP dependencies
install_dependencies() {
    print_status "Installing PHP dependencies..."
    
    if command -v composer &> /dev/null; then
        composer install --no-dev --optimize-autoloader
        print_success "PHP dependencies installed"
    else
        print_error "Composer is not installed. Please install Composer first."
        exit 1
    fi
}

# Function to setup environment file
setup_environment() {
    print_status "Setting up environment configuration..."
    
    if [ ! -f ".env" ]; then
        cp .env.example .env
        print_status "Created .env file from example"
    fi
    
    # Update database configuration for MySQL
    sed -i.bak 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
    sed -i.bak 's/DB_HOST=.*/DB_HOST=127.0.0.1/' .env
    sed -i.bak 's/DB_PORT=.*/DB_PORT=3306/' .env
    sed -i.bak 's/DB_DATABASE=.*/DB_DATABASE=haustap_db/' .env
    sed -i.bak 's/DB_USERNAME=.*/DB_USERNAME=haustap_user/' .env
    sed -i.bak 's/DB_PASSWORD=.*/DB_PASSWORD=haustap_password/' .env
    
    # Set Firebase configuration
    sed -i.bak 's/FIREBASE_PROJECT_ID=.*/FIREBASE_PROJECT_ID=haustap-booking-system/' .env
    
    print_success "Environment configuration updated"
}

# Function to generate application key
generate_app_key() {
    print_status "Generating application key..."
    php artisan key:generate
    print_success "Application key generated"
}

# Function to run database migrations
run_migrations() {
    print_status "Running database migrations..."
    
    # Create database if it doesn't exist
    mysql -u root -proot_password -e "CREATE DATABASE IF NOT EXISTS haustap_db;" 2>/dev/null || true
    
    # Run migrations
    php artisan migrate --force
    print_success "Database migrations completed"
}

# Function to setup Firebase credentials
setup_firebase_credentials() {
    print_status "Setting up Firebase credentials..."
    
    # Create config directory if it doesn't exist
    mkdir -p config
    
    # Note: User needs to provide their own service account file
    if [ ! -f "config/firebase-service-account.json" ]; then
        print_warning "Please place your Firebase service account JSON file at: config/firebase-service-account.json"
        print_warning "You can download this from Firebase Console > Project Settings > Service Accounts"
    else
        print_success "Firebase service account file found"
    fi
}

# Function to test Firebase connection
test_firebase_connection() {
    print_status "Testing Firebase connection..."
    
    php artisan tinker --execute="
    try {
        \$fs = app(\App\Services\Firebase\FirestoreClient::class);
        \$result = \$fs->list('users', 1);
        echo '✅ Firebase connection successful' . PHP_EOL;
    } catch (Exception \$e) {
        echo '❌ Firebase connection failed: ' . \$e->getMessage() . PHP_EOL;
    }
    "
}

# Function to test MySQL connection
test_mysql_connection() {
    print_status "Testing MySQL connection..."
    
    php artisan tinker --execute="
    try {
        DB::connection()->getPdo();
        echo '✅ MySQL connection successful' . PHP_EOL;
        echo 'Database: ' . DB::connection()->getDatabaseName() . PHP_EOL;
    } catch (Exception \$e) {
        echo '❌ MySQL connection failed: ' . \$e->getMessage() . PHP_EOL;
    }
    "
}

# Function to run initial sync
run_initial_sync() {
    print_status "Running initial data synchronization..."
    
    php artisan sync:firebase --direction=both --type=all
    print_success "Initial sync completed"
}

# Function to create systemd service (optional)
create_systemd_service() {
    print_status "Creating systemd service for automatic sync..."
    
    sudo tee /etc/systemd/system/haustap-sync.service > /dev/null <<EOF
[Unit]
Description=HausTap MySQL-Firebase Sync Service
After=network.target mysql.service

[Service]
Type=oneshot
User=www-data
WorkingDirectory=$(pwd)
ExecStart=/usr/bin/php artisan sync:firebase --direction=both --type=all
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
EOF

    sudo tee /etc/systemd/system/haustap-sync.timer > /dev/null <<EOF
[Unit]
Description=HausTap MySQL-Firebase Sync Timer
Requires=haustap-sync.service

[Timer]
OnBootSec=5min
OnUnitActiveSec=30min

[Install]
WantedBy=timers.target
EOF

    sudo systemctl daemon-reload
    sudo systemctl enable haustap-sync.timer
    
    print_success "Systemd service created and enabled"
}

# Function to display final instructions
show_final_instructions() {
    echo ""
    echo "🎉 MySQL-Firebase Integration Setup Complete!"
    echo "=============================================="
    echo ""
    echo "📋 Next Steps:"
    echo "1. Ensure your Firebase service account file is placed at: config/firebase-service-account.json"
    echo "2. Test the API endpoints:"
    echo "   - GET  /api/sync/status           - Check sync status"
    echo "   - POST /api/sync/users/to-firebase - Sync users to Firebase"
    echo "   - POST /api/sync/bookings         - Sync bookings"
    echo "   - POST /api/sync/full            - Full sync"
    echo ""
    echo "🔄 Manual Sync Commands:"
    echo "   php artisan sync:firebase --direction=both --type=all"
    echo "   php artisan sync:firebase --direction=to-firebase --type=users"
    echo "   php artisan sync:firebase --direction=from-firebase --type=bookings"
    echo ""
    echo "📊 Monitoring:"
    echo "   tail -f storage/logs/laravel.log"
    echo ""
    echo "🌐 API Testing:"
    echo "   curl -X GET http://localhost:8000/api/sync/status"
    echo ""
}

# Main execution
main() {
    print_status "Starting MySQL-Firebase integration setup..."
    
    # Check prerequisites
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed. Please install PHP 8.1+ first."
        exit 1
    fi
    
    # Setup process
    install_dependencies
    setup_environment
    generate_app_key
    
    # Database setup
    if check_mysql_status; then
        print_success "MySQL is already running"
    else
        print_warning "MySQL is not accessible. Attempting Docker setup..."
        if setup_mysql_docker; then
            print_success "MySQL Docker setup completed"
        else
            print_error "Failed to setup MySQL. Please set up MySQL manually."
            exit 1
        fi
    fi
    
    run_migrations
    setup_firebase_credentials
    
    # Testing
    test_mysql_connection
    test_firebase_connection
    
    # Initial sync
    read -p "Would you like to run initial data synchronization? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        run_initial_sync
    fi
    
    # Systemd service (optional)
    read -p "Would you like to create a systemd service for automatic sync? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        create_systemd_service
    fi
    
    show_final_instructions
}

# Run main function
main "$@"