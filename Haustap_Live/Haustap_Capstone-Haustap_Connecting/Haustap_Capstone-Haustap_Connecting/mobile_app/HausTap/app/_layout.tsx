import { Stack } from "expo-router";
import React from "react";
import { BookingSelectionProvider } from "@/app/context/BookingSelectionContext";

export default function Layout() {
  return (
    <BookingSelectionProvider>
      <Stack
        screenOptions={{
          headerShown: false,
          contentStyle: { flex: 1 },
        }}
      >
        <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
      </Stack>
    </BookingSelectionProvider>
  );
}
