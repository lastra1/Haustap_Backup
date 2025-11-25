# Mobile App API Configuration
# This file contains the API configuration for your Android app

export const API_CONFIG = {
  // Development URLs
  DEV_API_URL: 'http://localhost:8000/api',
  DEV_WEB_URL: 'http://localhost:3000',
  
  // Production URLs - Update these with your actual Render URLs
  PROD_API_URL: 'https://haustap-api.onrender.com/api',
  PROD_WEB_URL: 'https://haustap-web.onrender.com',
  
  // API Endpoints
  ENDPOINTS: {
    // Authentication
    LOGIN: '/v2/auth/login',
    REGISTER: '/v2/auth/register',
    PROFILE: '/v2/auth/profile',
    LOGOUT: '/v2/auth/logout',
    
    // Bookings
    BOOKINGS: '/bookings',
    BOOKING_DETAIL: '/bookings/',
    CANCEL_BOOKING: '/bookings/{id}/cancel',
    RATE_BOOKING: '/bookings/{id}/rate',
    
    // Services
    SERVICES: '/firebase/services',
    CATEGORIES: '/firebase/categories',
    PROVIDERS: '/firebase/providers',
    
    // Chat
    CHAT_MESSAGES: '/chat/{booking_id}/messages',
    SEND_MESSAGE: '/chat/{booking_id}/messages',
    
    // Notifications
    NOTIFICATIONS: '/v2/notifications',
    NOTIFICATION_DETAIL: '/v2/notifications/',
    MARK_READ: '/v2/notifications/{id}/read',
    
    // Location
    LOCATION_PINS: '/v2/location-pins',
    NEARBY_PINS: '/v2/location-pins/nearby',
    
    // Health Check
    HEALTH: '/health'
  },
  
  // Headers
  HEADERS: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
};

// Environment detection
export const getApiUrl = () => {
  if (__DEV__) {
    return API_CONFIG.DEV_API_URL;
  }
  return API_CONFIG.PROD_API_URL;
};

export const getWebUrl = () => {
  if (__DEV__) {
    return API_CONFIG.DEV_WEB_URL;
  }
  return API_CONFIG.PROD_WEB_URL;
};