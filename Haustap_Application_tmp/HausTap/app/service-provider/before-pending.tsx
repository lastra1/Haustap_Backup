import { router } from "expo-router";
import React, { useEffect, useState } from "react";
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";
import BookingCardFull from "../../components/BookingCardFull";
import CancelBookingModal from "../../components/CancelBookingModal";
import { Booking, bookingStore } from "../../src/services/bookingStore";

export default function BookingsScreen() {
  const [pendingBookings, setPendingBookings] = useState<Booking[]>([]);
  const [showMoreMenu, setShowMoreMenu] = useState<string | null>(null);

  // Cancel booking modal states
  const [cancelModalVisible, setCancelModalVisible] = useState(false);
  const [selectedBookingForCancel, setSelectedBookingForCancel] = useState<Booking | null>(null);

  // Fetch pending bookings and subscribe to updates
  useEffect(() => {
    setPendingBookings(bookingStore.getPendingBookings());
    const unsubscribe = bookingStore.subscribe((updatedBookings: Booking[]) => {
      const pending = updatedBookings.filter(
        (booking: Booking) => booking.status === "pending"
      );
      setPendingBookings(pending);
    });
    return () => unsubscribe();
  }, []);

  const handleTabPress = (tab: string) => {
    try {
      if (tab === "Pending") {
        router.push('/service-provider/before-pending');
      } else if (tab === "Ongoing") {
        router.push('/service-provider/before-ongoing');
      } else if (tab === "Completed") {
        router.push('/service-provider/booking-process-completed');
      } else if (tab === "Cancelled") {
        router.push('/service-provider/booking-process-cancelled');
      } else if (tab === "Return") {
        router.push('/service-provider/booking-process-return');
      }
    } catch (e) {
      console.error('Tab navigation failed', e);
    }
  };

  return (
    <View style={styles.page}>
      <ScrollView 
        style={styles.container}
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        {/* Header */}
        <Text style={styles.header}>Bookings</Text>

        {/* Tabs */}
        <View style={styles.tabs}>
          {["Pending", "Ongoing", "Completed", "Cancelled", "Return"].map(
            (tab, i) => (
              <TouchableOpacity
                key={i}
                onPress={() => handleTabPress(tab)}
                style={[styles.tab, tab === "Pending" && styles.tabActive]}
              >
                <Text
                  style={[styles.tabText, tab === "Pending" && styles.tabTextActive]}
                >
                  {tab}
                </Text>
              </TouchableOpacity>
            )
          )}
        </View>

        {/* Pending Bookings List */}
        {pendingBookings.length === 0 ? (
          <View style={styles.emptyState}>
            <Text style={styles.emptyText}>No pending bookings</Text>
          </View>
        ) : (
          pendingBookings.map((booking) => (
            <BookingCardFull
              key={booking.id}
              booking={booking}
              showMoreMenu={showMoreMenu === booking.id}
              onToggleMoreMenu={() =>
                setShowMoreMenu(showMoreMenu === booking.id ? null : booking.id)
              }
              showFooterButtons={true}
              onMarkArrived={() => {
                console.log(`[BeforePending] Mark Arrived clicked for booking ${booking.id}`);
                bookingStore.markAsOngoing(booking.id);
              }}
              onContactClient={() => {
                router.push({
                  pathname: '/service-provider/chatbox-individual',
                  params: {
                    bookingId: booking.id,
                    clientName: booking.clientName,
                    clientPhone: booking.clientPhone,
                  },
                });
              }}
              onCancelBooking={() => {
                setSelectedBookingForCancel(booking);
                setCancelModalVisible(true);
              }}
            />
          ))
        )}

        {/* Cancel Booking Modal */}
        <CancelBookingModal
          visible={cancelModalVisible}
          onClose={() => setCancelModalVisible(false)}
          onSubmit={(reason: string, description: string) => {
            if (selectedBookingForCancel) {
              console.log(`Cancellation submitted for booking ${selectedBookingForCancel.id}`);
              console.log(`Reason: ${reason}`);
              console.log(`Description: ${description}`);
              // Mark booking as cancelled in the store
              bookingStore.cancelBooking(selectedBookingForCancel.id);
            }
          }}
          clientName={selectedBookingForCancel?.clientName}
        />
      </ScrollView>
    </View>
  );
}

const handleTabPress = (tab: string) => {
  try {
    if (tab === "Pending") {
      router.push('/service-provider/before-pending');
    } else if (tab === "Ongoing") {
      router.push('/service-provider/before-ongoing');
    } else if (tab === "Completed") {
      router.push('/service-provider/booking-process-completed');
    } else if (tab === "Cancelled") {
      router.push('/service-provider/booking-process-cancelled');
    } else if (tab === "Return") {
      router.push('/service-provider/booking-process-return');
    }
  } catch (e) {
    console.error('Tab navigation failed', e);
  }
};

const styles = StyleSheet.create({
  page: {
    flex: 1,
    backgroundColor: "#F9F9F9",
  },
  container: {
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
    paddingHorizontal: 16,
    paddingTop: 60,
    paddingBottom: 100,
  },
  header: {
    fontSize: 22,
    fontWeight: "700",
    marginBottom: 15,
  },
  tabs: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 20,
  },
  tab: {
    paddingVertical: 6,
    paddingHorizontal: 10,
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
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 40,
  },
  emptyText: {
    fontSize: 16,
    color: "#999",
  },
});
