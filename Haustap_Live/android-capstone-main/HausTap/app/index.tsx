import * as Linking from 'expo-linking';
import { router } from "expo-router";
import { useEffect } from "react";
import { getUserRole, loadBaseUrl } from "../src/config/apiConfig";

export default function App() {
  useEffect(() => {
    const initializeApp = async () => {
      await loadBaseUrl();
      // If the app was opened via a URL (e.g. QR / deep link), always route to guest first
      try {
        const initialUrl = await Linking.getInitialURL();
        if (initialUrl) {
          router.replace('/guess-account');
          return;
        }
      } catch (e) {
        // ignore linking errors and fall back to role-based routing
      }

      const role = await getUserRole();

      if (!role) {
        router.replace('/guess-account');
      } else if (role === 'client') {
        router.replace('/client-side');
      } else if (role === 'service-provider' || role === 'provider') {
        router.replace('/service-provider');
      } else {
        router.replace('/guess-account'); // Fallback to login if role is unrecognized
      }

    };

    initializeApp();
  }, []);

  return null;
}
