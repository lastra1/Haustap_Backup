// OngoingScreen.js
import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
import React, { useEffect, useState } from "react";
import {
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from "react-native";
import BookingCardFull from "../../components/BookingCardFull";
import { Booking, bookingStore } from "../../src/services/bookingStore";

export default function OngoingScreen() {
  const [selectedTab, setSelectedTab] = useState("Ongoing");
  const [ongoingBookings, setOngoingBookings] = useState<Booking[]>([]);
  const [showMoreMenu, setShowMoreMenu] = useState<string | null>(null);

  // Subscribe to ongoing bookings when component mounts
  useEffect(() => {
    const unsubscribe = bookingStore.subscribe((updatedBookings: Booking[]) => {
      console.log(`[OngoingScreen] Received ${updatedBookings.length} total bookings`);
      const ongoing = updatedBookings.filter(
        (booking: Booking) => booking.status === "ongoing"
      );
      console.log(`[OngoingScreen] Found ${ongoing.length} ongoing bookings:`, ongoing);
      setOngoingBookings(ongoing);
    });

    return () => {
      unsubscribe();
    };
  }, []);

  const handleTabPress = (tab: string) => {
    setSelectedTab(tab);
    console.log("handleTabPress called with tab=", tab);

    try {
      switch (tab) {
        case "Pending":
          router.push("/service-provider/before-pending");
          break;
        case "Ongoing":
          router.push("/service-provider/before-ongoing");
          break;
        case "Completed":
          router.push("/service-provider/booking-process-completed");
          break;
        case "Cancelled":
          router.push("/service-provider/booking-process-cancelled");
          break;
        case "Return":
          router.push("/service-provider/booking-process-return");
          break;
        default:
          break;
      }
    } catch (e) {
      console.error("Tab navigation failed", e);
    }
  };

  const handleMarkComplete = (bookingId: string, beforePhoto: string, afterPhoto: string) => {
    console.log(`[OngoingScreen] Marking booking ${bookingId} as complete with photos`);
    bookingStore.completeBooking(bookingId, beforePhoto, afterPhoto);
    // Navigate to completed page
    router.push("/service-provider/booking-process-completed");
  };

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={{
        flexGrow: 1,
        paddingHorizontal: 16,
        paddingTop: 60,
        paddingBottom: 100,
      }}
    >
      {/* Header */}
      <Text style={styles.headerTitle}>Bookings</Text>

      {/* Tabs */}
      <View style={styles.tabs}>
        {["Pending", "Ongoing", "Completed", "Cancelled", "Return"].map(
          (tab) => (
            <TouchableOpacity
              key={tab}
              onPress={() => handleTabPress(tab)}
              style={[styles.tabButton, tab === selectedTab && styles.tabActive]}
            >
              <Text
                style={[
                  styles.tabText,
                  tab === selectedTab && styles.tabTextActive,
                ]}
              >
                {tab}
              </Text>
            </TouchableOpacity>
          )
        )}
      </View>

      {/* Ongoing Bookings List */}
      {ongoingBookings.length > 0 ? (
        ongoingBookings.map((booking) => (
          <BookingCardFull
            key={booking.id}
            booking={booking}
            showMoreMenu={showMoreMenu === booking.id}
            onToggleMoreMenu={() =>
              setShowMoreMenu(
                showMoreMenu === booking.id ? null : booking.id
              )
            }
            showFooterButtons={false}
            showPhotoUpload={true}
            showProceedButton={true}
            onProceed={(beforePhoto, afterPhoto) => handleMarkComplete(booking.id, beforePhoto, afterPhoto)}
          />
        ))
      ) : (
        <View style={styles.emptyState}>
          <Ionicons name="briefcase-outline" size={64} color="#CCC" />
          <Text style={styles.emptyText}>No ongoing bookings</Text>
        </View>
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#F9F9F9",
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: "700",
    marginBottom: 15,
  },
  tabs: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 20,
    borderBottomWidth: 1,
    borderBottomColor: "#E0E0E0",
  },
  tabButton: {
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderBottomWidth: 2,
    borderColor: "transparent",
  },
  tabActive: {
    borderColor: "#00B0B9",
  },
  tabText: {
    fontSize: 13,
    color: "#666",
  },
  tabTextActive: {
    color: "#00B0B9",
    fontWeight: "600",
  },
  emptyState: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    paddingVertical: 80,
  },
  emptyText: {
    marginTop: 12,
    fontSize: 16,
    color: "#999",
    fontWeight: "500",
  },
});
