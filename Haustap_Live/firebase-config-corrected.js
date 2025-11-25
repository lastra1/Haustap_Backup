// Firebase Web Configuration - Updated for HausTap Booking System
// This file contains the corrected Firebase configuration

// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAuth, connectAuthEmulator } from "firebase/auth";
import { getFirestore, connectFirestoreEmulator } from "firebase/firestore";
import { getStorage, connectStorageEmulator } from "firebase/storage";

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyCfhR1vIh8_z4TAmdaQRESHB459CsVqJ9M",
  authDomain: "haustap-booking-system.firebaseapp.com",
  projectId: "haustap-booking-system",
  storageBucket: "haustap-booking-system.firebasestorage.app",
  messagingSenderId: "515769404711",
  appId: "1:515769404711:web:ddf0b32df0498eb18aad02",
  measurementId: "G-DLBL1HFP27"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Firebase services
const auth = getAuth(app);
const db = getFirestore(app);
const storage = getStorage(app);

// Export for use in other modules
export { app, auth, db, storage };
export default app;