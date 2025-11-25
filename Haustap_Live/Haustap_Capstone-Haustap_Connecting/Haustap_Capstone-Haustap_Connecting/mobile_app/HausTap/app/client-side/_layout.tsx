
import { Slot } from "expo-router";
import React from "react";
import { View } from "react-native";
import FooterNav from "../../components/FooterNav";
import { AuthProvider } from "../context/AuthContext";


export default function ClientLayout() {
  const FOOTER_HEIGHT = 64;
  return (
    <AuthProvider>
      <View style={{ flex: 1 }}>
        <View style={{ flex: 1, paddingBottom: FOOTER_HEIGHT }}>
          <Slot />
        </View>
        <FooterNav section="client" />
      </View>
    </AuthProvider>
  );
}


