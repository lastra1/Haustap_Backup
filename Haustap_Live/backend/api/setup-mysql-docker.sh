#!/bin/bash

# HausTap MySQL Docker Setup Script
# This script helps set up MySQL for the HausTap Service Booking Platform

echo "🚀 Setting up MySQL for HausTap Service Booking Platform..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker Desktop first."
    echo "💡 On Windows: Start Docker Desktop from Start Menu"
    echo "💡 On Mac: Start Docker from Applications"
    exit 1
fi

echo "✅ Docker is running"

# Stop and remove existing MySQL container if it exists
echo "🧹 Cleaning up existing MySQL container..."
docker stop haustap_mysql > /dev/null 2>&1
docker rm haustap_mysql > /dev/null 2>&1

# Start MySQL container
echo "🐳 Starting MySQL container..."
docker run -d \
  --name haustap_mysql \
  -e MYSQL_DATABASE=haustap_db \
  -e MYSQL_ROOT_PASSWORD=root_password \
  -e MYSQL_USER=haustap_user \
  -e MYSQL_PASSWORD=haustap_password \
  -p 3306:3306 \
  -v haustap_mysql_data:/var/lib/mysql \
  mysql:8.0 \
  --default-authentication-plugin=mysql_native_password

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 30

# Check if MySQL is running
if docker ps | grep -q haustap_mysql; then
    echo "✅ MySQL container is running"
    
    # Test connection
    echo "🧪 Testing MySQL connection..."
    if docker exec haustap_mysql mysql -u haustap_user -phaustap_password -e "SELECT 1;" haustap_db > /dev/null 2>&1; then
        echo "✅ MySQL connection successful"
        echo "📊 Database 'haustap_db' is ready"
        
        # Show connection details
        echo ""
        echo "🔗 MySQL Connection Details:"
        echo "   Host: localhost"
        echo "   Port: 3306"
        echo "   Database: haustap_db"
        echo "   Username: haustap_user"
        echo "   Password: haustap_password"
        echo ""
        echo "💡 Next steps:"
        echo "   1. Switch Laravel to MySQL: php artisan db:switch mysql"
        echo "   2. Run migrations: php artisan migrate"
        echo "   3. Access phpMyAdmin: http://localhost:8081"
        
    else
        echo "❌ MySQL connection failed"
        echo "📝 Check logs: docker logs haustap_mysql"
    fi
else
    echo "❌ MySQL container failed to start"
    echo "📝 Check logs: docker logs haustap_mysql"
fi

echo ""
echo "🎯 MySQL setup complete!"