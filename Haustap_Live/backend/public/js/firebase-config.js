// Firebase Configuration for HausTap Web Application
// This configuration connects to the haustap-booking-system Firebase project

// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAuth, connectAuthEmulator } from "firebase/auth";
import { getFirestore, connectFirestoreEmulator } from "firebase/firestore";
import { getStorage, connectStorageEmulator } from "firebase/storage";
import { getDatabase, connectDatabaseEmulator } from "firebase/database";
import { getAnalytics } from "firebase/analytics";

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyCfhR1vIh8_z4TAmdaQRESHB459CsVqJ9M",
  authDomain: "haustap-booking-system.firebaseapp.com",
  databaseURL: "https://haustap-booking-system-default-rtdb.asia-southeast1.firebasedatabase.app",
  projectId: "haustap-booking-system",
  storageBucket: "haustap-booking-system.firebasestorage.app",
  messagingSenderId: "515769404711",
  appId: "1:515769404711:web:ddf0b32df0498eb18aad02",
  measurementId: "G-DLBL1HFP27"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize services
const auth = getAuth(app);
const db = getFirestore(app);
const storage = getStorage(app);
const database = getDatabase(app);
const analytics = getAnalytics(app);

// Export for use in other files
export { app, auth, db, storage, database, analytics };

// Optional: Connect to emulators for development
// Uncomment these lines when developing locally
// if (window.location.hostname === 'localhost') {
//   connectAuthEmulator(auth, "http://localhost:9099");
//   connectFirestoreEmulator(db, 'localhost', 8080);
//   connectStorageEmulator(storage, "localhost", 9199);
//   connectDatabaseEmulator(database, "localhost", 9000);
// }