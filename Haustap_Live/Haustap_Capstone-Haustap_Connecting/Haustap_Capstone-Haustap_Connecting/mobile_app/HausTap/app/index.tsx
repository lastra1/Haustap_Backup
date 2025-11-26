import * as Linking from 'expo-linking';
import { router } from "expo-router";
import { useEffect } from "react";
import { getUserRole, loadBaseUrl } from "../src/config/apiConfig";

export default function Index() {
  useEffect(() => {
    const initializeApp = async () => {
      await loadBaseUrl();
      try {
        const initialUrl = await Linking.getInitialURL();
        if (initialUrl) {
          router.replace('/guess-account');
          return;
        }
      } catch (e) {}

      const role = await getUserRole();
      if (!role) {
        router.replace('/guess-account');
      } else if (role === 'client') {
        router.replace('/client-side');
      } else if (role === 'service-provider' || role === 'provider') {
        router.replace('/service-provider');
      } else {
        router.replace('/guess-account');
      }
    };

    initializeApp();
  }, []);

  return null;
}