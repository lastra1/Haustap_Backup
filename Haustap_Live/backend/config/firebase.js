const admin = require('firebase-admin');
const logger = require('../utils/logger');

let firebaseApp;

const initializeFirebase = async () => {
  try {
    // Check if Firebase is already initialized
    if (admin.apps.length > 0) {
      firebaseApp = admin.apps[0];
      logger.info('Firebase already initialized');
      return;
    }

    // Try to get service account from environment variable first
    let serviceAccount;
    try {
      const serviceAccountJson = process.env.FIREBASE_SERVICE_ACCOUNT_JSON;
      if (serviceAccountJson) {
        serviceAccount = JSON.parse(serviceAccountJson);
        logger.info('Using Firebase service account from environment variable');
      } else {
        throw new Error('FIREBASE_SERVICE_ACCOUNT_JSON not found');
      }
    } catch (error) {
      logger.warn('FIREBASE_SERVICE_ACCOUNT_JSON not found, trying file path...');
      
      // Fallback to file path
      try {
        const path = require('path');
        const serviceAccountPath = process.env.FIREBASE_CREDENTIALS_PATH || path.resolve(__dirname, '../storage/app/firebase/service-account.json');
        serviceAccount = require(serviceAccountPath);
        logger.info('Using Firebase service account from file');
      } catch (fileError) {
        logger.warn('Firebase service account file not found, using emulator mode');
        // Fallback to emulator mode for development
        firebaseApp = admin.initializeApp({
          projectId: process.env.FIREBASE_PROJECT_ID || 'haustap-booking-system',
          databaseURL: process.env.FIREBASE_DATABASE_URL || 'https://haustap-booking-system-default-rtdb.asia-southeast1.firebasedatabase.app'
        });
        return;
      }
    }

    // Initialize Firebase Admin SDK with service account
    firebaseApp = admin.initializeApp({
      credential: admin.credential.cert(serviceAccount),
      databaseURL: process.env.FIREBASE_DATABASE_URL || 'https://haustap-booking-system-default-rtdb.asia-southeast1.firebasedatabase.app'
    });
    
    logger.info('Firebase Admin SDK initialized successfully');
    
  } catch (error) {
    logger.error('Failed to initialize Firebase:', error);
    // Don't throw error, let the app continue without Firebase
    logger.warn('Continuing without Firebase connection');
  }
};

const getFirebaseApp = () => firebaseApp;
const getFirestore = () => {
  if (!firebaseApp) {
    throw new Error('Firebase not initialized');
  }
  return admin.firestore();
};
const getAuth = () => {
  if (!firebaseApp) {
    throw new Error('Firebase not initialized');
  }
  return admin.auth();
};
const getRealtimeDatabase = () => {
  if (!firebaseApp) {
    throw new Error('Firebase not initialized');
  }
  return admin.database();
};

module.exports = {
  initializeFirebase,
  getFirebaseApp,
  getFirestore,
  getAuth,
  getRealtimeDatabase
};
