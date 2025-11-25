import { Stack, useRouter } from 'expo-router';
import React, { useEffect } from 'react';

export default function PartnerScreen() {
  const router = useRouter();

  useEffect(() => {
    // redirect to the form route which lives in app/(partner)/partner-form.tsx
    router.replace('/partner-form');
  }, []);

  return (
    <>
      <Stack.Screen
        options={{
          title: 'Become a Partner',
          headerShown: true,
        }}
      />
    </>
  );
}