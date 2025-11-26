import { initializeApp } from 'firebase/app'
import { getAuth } from 'firebase/auth'

const firebaseConfig = {
  apiKey: 'AIzaSyCfhR1vIh8_z4TAmdaQRESHB459CsVqJ9M',
  authDomain: 'haustap-booking-system.firebaseapp.com',
  projectId: 'haustap-booking-system',
  storageBucket: 'haustap-booking-system.firebasestorage.app',
  messagingSenderId: '515769404711',
  appId: '1:515769404711:web:ddf0b32df0498eb18aad02',
}

const app = initializeApp(firebaseConfig)
export const auth = getAuth(app)
export default app