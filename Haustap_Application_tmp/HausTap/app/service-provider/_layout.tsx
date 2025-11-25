

import { Stack } from "expo-router";
import React from "react";
import { View } from "react-native";
import FooterNav from "../../components/FooterNav";
import { AuthProvider } from "../context/AuthContext";

export default function ServiceProviderLayout() {
  return (
    <AuthProvider>
      <View style={{ flex: 1 }}>
        <Stack
          screenOptions={{
            headerShown: false,
            contentStyle: { paddingBottom: 64 },
          }}
        />
        <FooterNav section="provider" />
      </View>
    </AuthProvider>
  );
}