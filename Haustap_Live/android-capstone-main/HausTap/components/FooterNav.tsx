import { Ionicons } from "@expo/vector-icons";
import { useRouter, useSegments } from "expo-router";
import React, { useEffect, useState } from "react";
import { Platform, StyleSheet, Text, TouchableOpacity, View } from "react-native";
import { getUserRole } from "../src/config/apiConfig";

export type FooterNavSection = "client" | "provider";

interface FooterNavProps {
  section: FooterNavSection;
}

export default function FooterNav({ section }: FooterNavProps) {
  const router = useRouter();
  const segments = useSegments();
  const [role, setRole] = useState<string | null>(null);
  const isProvider = section === "provider";

  useEffect(() => {
    let mounted = true;
    (async () => {
      try {
        const r = await getUserRole();
        if (mounted) setRole(r);
      } catch (e) {
        if (mounted) setRole(null);
      }
    })();
    return () => { mounted = false; };
  }, []);

  // Determine which tab is active using segments
  const path = '/' + segments.join('/');
  const tabs = isProvider
    ? [
        {
          label: "Home",
          icon: "home",
          route: "/service-provider",
          active: path === "/service-provider",
        },
        {
          label: "Bookings",
          icon: "calendar-outline",
          route: "/service-provider/before-pending",
          active: path.startsWith("/service-provider/before-pending") || path.startsWith("/service-provider/booking-queue") || path.startsWith("/service-provider/booking-process-pending") || path.startsWith("/service-provider/booking-process-ongoing") || path.startsWith("/service-provider/booking-process-completed") || path.startsWith("/service-provider/booking-process-cancelled"),
        },
        {
          label: "Chat",
          icon: "chatbubble-outline",
          route: "/service-provider/chatbox",
          active: path.startsWith("/service-provider/chatbox"),
        },
        {
          label: "Profile",
          icon: "person-outline",
          route: "/service-provider/my-account",
          active: path.startsWith("/service-provider/my-account") || path.startsWith("/service-provider/profile"),
        },
      ]
    : [
        {
          label: "Home",
          icon: "home",
          route: "/client-side",
          active: path === "/client-side",
        },
        {
          label: "Bookings",
          icon: "calendar-outline",
          route: "/client-side/client-booking-summary/booking-pending",
          active: path.startsWith("/client-side/client-booking-summary"),
        },
        {
          label: "Chat",
          icon: "chatbubble-outline",
          route: "/client-side/chat",
          active: path.startsWith("/client-side/chat"),
        },
        {
          label: "My Account",
          icon: "person-outline",
          route: "/client-side/client-profile",
          active: path.startsWith("/client-side/client-profile"),
        },
      ];

  return (
    <View style={styles.footerNav}>
      {tabs.map((tab) => (
        <TouchableOpacity
          key={tab.label}
          style={styles.navItem}
          onPress={() => {
            const guestBlocked = !role && (tab.label === "Bookings" || tab.label === "Chat" || tab.label === "My Account");
            if (guestBlocked) {
              router.push('/signup');
            } else {
              router.push(tab.route as any);
            }
          }}
        >
          <Ionicons
            name={tab.icon as any}
            size={22}
            color={tab.active ? "#3DC1C6" : "#888"}
          />
          <Text style={[styles.navText, tab.active && { color: "#3DC1C6" }]}>{tab.label}</Text>
        </TouchableOpacity>
      ))}
    </View>
  );
}

const FOOTER_HEIGHT = 64;
const styles = StyleSheet.create({
  footerNav: {
    position: "absolute",
    left: 0,
    right: 0,
    bottom: 0,
    flexDirection: "row",
    justifyContent: "space-around",
    backgroundColor: "#E0F7F9",
    paddingVertical: Platform.OS === "ios" ? 14 : 10,
    borderTopWidth: 1,
    borderTopColor: "#ccc",
    zIndex: 20,
    height: FOOTER_HEIGHT,
  },
  navItem: {
    alignItems: "center",
    flex: 1,
  },
  navText: {
    fontSize: 12,
    color: "#888",
    marginTop: 4,
  },
});