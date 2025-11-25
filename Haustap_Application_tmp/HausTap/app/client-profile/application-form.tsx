import { useRouter } from 'expo-router';
import { useEffect } from 'react';

export default function ClientApplicationFormRedirect() {
  const router = useRouter();

  useEffect(() => {
    // Redirect to the partner application form that already exists in the project
    router.replace('/partner');
  }, []);

  return null;
}
