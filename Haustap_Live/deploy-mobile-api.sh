#!/bin/bash

echo "🚀 Deploying HausTap Mobile API to Firebase Functions..."

# Navigate to functions directory
cd functions

# Install dependencies with force to handle space issues
echo "📦 Installing dependencies..."
npm install --force --no-audit --no-fund

# Deploy to Firebase
echo "☁️ Deploying to Firebase Functions..."
firebase deploy --only functions

# Get the deployed URL
API_URL="https://us-central1-haustap-booking-system.cloudfunctions.net/api"

echo "✅ Deployment Complete!"
echo "📱 Mobile App API URL: $API_URL"
echo ""
echo "🔧 Test Endpoints:"
echo "   Health Check: $API_URL/health"
echo "   Database Test: $API_URL/test-db"
echo "   Services: $API_URL/services"
echo "   Users: $API_URL/users"
echo "   Bookings: $API_URL/bookings"
echo ""
echo "💡 Update your mobile app to use this API URL!"