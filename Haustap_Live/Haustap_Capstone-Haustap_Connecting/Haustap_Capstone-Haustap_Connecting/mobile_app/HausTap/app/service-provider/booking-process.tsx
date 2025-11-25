import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
import React, { useRef, useState } from "react";
import {
    Animated,
    PanResponder,
    PanResponderGestureState,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View
} from "react-native";

export default function BookingsScreen() {
  const [isAccepted, setIsAccepted] = useState(false);
  const pan = useRef(new Animated.Value(0)).current;

  const panResponder = PanResponder.create({
    onStartShouldSetPanResponder: () => true,
    onPanResponderMove: (_, gesture: PanResponderGestureState) => {
      const maxSlide = 240;
      const newValue = Math.max(0, Math.min(gesture.dx, maxSlide));
      pan.setValue(newValue);
    },
    onPanResponderRelease: (_, gesture: PanResponderGestureState) => {
      const maxSlide = 240;
      if (gesture.dx >= maxSlide * 0.8) {
        Animated.spring(pan, {
          toValue: maxSlide,
          useNativeDriver: true,
          friction: 8,
          tension: 40,
        }).start(() => {
          if (!isAccepted) {
            setIsAccepted(true);
          }
        });
      } else {
        Animated.spring(pan, {
          toValue: 0,
          useNativeDriver: true,
          friction: 8,
          tension: 40,
        }).start();
      }
    },
  });

  return (
    <View style={styles.page}>
      <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
        {/* Header */}
        <Text style={styles.header}>Bookings</Text>

        {/* Tabs */}
        <View style={styles.tabs}>
          <TouchableOpacity
            style={[styles.tab, styles.tabActive]}
            onPress={() => router.push("/service-provider/before-pending")}
          >
            <Text style={[styles.tabText, styles.tabTextActive]}>Pending</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.tab, styles.tabActive]}
            onPress={() => router.push("/service-provider/before-ongoing")}
          >
            <Text style={[styles.tabText, styles.tabTextActive]}>Ongoing</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.tab}
            onPress={() => router.push("/service-provider/booking-process-completed")}
          >
            <Text style={styles.tabText}>Completed</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.tab} onPress={() => router.push("/service-provider/booking-process-cancelled")}>
            <Text style={styles.tabText}>Cancelled</Text>
          </TouchableOpacity>

          <TouchableOpacity style={styles.tab} onPress={() => router.push("/service-provider/booking-process-return")}>
            <Text style={styles.tabText}>Return</Text>
          </TouchableOpacity>
        </View>

        {/* Booking Card */}
        <View style={styles.card}>
          {/* Header */}
          <View style={styles.cardHeader}>
            <View style={{ flex: 1 }}>
              <Text style={styles.clientName}>
                <Text style={{ fontWeight: "700" }}>Client:</Text> Jenn Bornilla
              </Text>
              <Text style={styles.serviceTitle}>Home Cleaning</Text>
              <Text style={styles.serviceSubtitle}>Bungalow - Basic Cleaning</Text>
            </View>
            <Ionicons name="chevron-down" size={20} color="#000" />
          </View>

          {/* Date & Time */}
          <View style={styles.row}>
            <View style={styles.half}>
              <Text style={styles.label}>Date</Text>
              <Text style={styles.value}>May 21, 2025</Text>
            </View>
            <View style={styles.half}>
              <Text style={styles.label}>Time</Text>
              <Text style={styles.value}>8:00 AM</Text>
            </View>
          </View>

          {/* Address */}
          <View style={styles.section}>
            <Text style={styles.label}>Address</Text>
            <Text style={styles.value}>
              B1 L50 Mango st. Phase 1 Saint Joseph Village 10{"\n"}
              Barangay Langgam, City of San Pedro, Laguna 4023
            </Text>
          </View>

          {/* Selected Section */}
          <View style={styles.section}>
            <Text style={styles.label}>Selected:</Text>
            <Text style={styles.valueBold}>Bungalow 80–150 sqm</Text>
            <Text style={styles.value}>Basic Cleaning – 1 Cleaner</Text>
          </View>

          {/* Inclusions */}
          <View style={styles.section}>
            <Text style={styles.label}>Inclusions:</Text>
            <Text style={styles.value}>
              Living Room: walls, mop, dusting furniture, trash removal,{"\n"}
              Bedrooms: bed making, sweeping, dusting, trash removal,{"\n"}
              Hallways: mop & sweep, remove cobwebs,{"\n"}
              Windows & Mirrors: quick wipe
            </Text>
          </View>

          {/* Notes */}
          <View style={styles.section}>
            <Text style={styles.label}>Notes:</Text>
            <TextInput style={styles.textArea} placeholder=" " multiline />
          </View>

          {/* Voucher */}
          <View style={styles.voucherBox}>
            <Ionicons name="ticket-outline" size={18} color="#666" />
            <Text style={styles.voucherText}>No voucher added</Text>
          </View>

          {/* Subtotal & Total */}
          <View style={styles.priceSection}>
            <View style={styles.rowBetween}>
              <Text style={styles.subLabel}>Sub Total</Text>
              <Text style={styles.subAmount}>₱1,000.00</Text>
            </View>
            <View style={styles.rowBetween}>
              <Text style={styles.subLabel}>Voucher Discount</Text>
              <Text style={styles.subAmount}>₱0</Text>
            </View>
            <View style={styles.divider} />
            <View style={styles.rowBetween}>
              <Text style={styles.totalLabel}>TOTAL</Text>
              <Text style={styles.totalAmount}>₱1,000.00</Text>
            </View>
          </View>

          {/* Note text */}
          <Text style={styles.noteInfo}>
            Full payment will be collected directly by the service provider upon completion of the service.
          </Text>

          {/* Accept Booking */}
          <View style={styles.acceptBox}>
            <Text style={styles.acceptTitle}>Accept Booking?</Text>
            <Text style={styles.acceptSubtitle}>Be the first one to accept</Text>

            {/* Slide Button (UI only) */}
            <View style={styles.sliderContainer}>
              <View style={styles.sliderTrack}>
                <Text style={styles.sliderText}>
                  {isAccepted ? "Booking Arrived!" : "Slide to Mark as Arrived"}
                </Text>
              </View>
              <Animated.View
                {...panResponder.panHandlers}
                style={[
                  styles.slider,
                  {
                    transform: [{
                      translateX: pan
                    }]
                  }
                ]}
              >
                <Ionicons name="arrow-forward" size={24} color="#fff" />
              </Animated.View>
            </View>

            {/* Cancel Button */}
            <TouchableOpacity style={styles.cancelButton}>
              <Text style={styles.cancelText}>Cancel</Text>
            </TouchableOpacity>
          </View>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  page: {
    flex: 1,
    backgroundColor: "#F9F9F9",
  },
  container: {
    paddingHorizontal: 16,
    paddingTop: 60,
    marginBottom: 90,
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
  card: {
    backgroundColor: "#F5F5F5",
    borderRadius: 12,
    padding: 16,
    shadowColor: "#000",
    shadowOpacity: 0.08,
    shadowRadius: 3,
    shadowOffset: { width: 0, height: 2 },
    elevation: 2,
  },
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 10,
  },
  clientName: {
    fontSize: 14,
    color: "#000",
  },
  serviceTitle: {
    fontSize: 14,
    fontWeight: "600",
    color: "#000",
  },
  serviceSubtitle: {
    fontSize: 13,
    color: "#555",
  },
  row: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 10,
  },
  half: {
    width: "48%",
  },
  label: {
    fontSize: 12,
    color: "#666",
    marginBottom: 3,
  },
  value: {
    fontSize: 14,
    color: "#000",
    lineHeight: 20,
  },
  valueBold: {
    fontSize: 14,
    fontWeight: "600",
    color: "#000",
  },
  section: {
    marginTop: 10,
  },
  textArea: {
    borderWidth: 1,
    borderColor: "#DDD",
    borderRadius: 6,
    height: 50,
    marginTop: 4,
    padding: 8,
    textAlignVertical: "top",
  },
  voucherBox: {
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#DDD",
    borderRadius: 6,
    padding: 10,
    marginTop: 10,
  },
  voucherText: {
    marginLeft: 8,
    fontSize: 13,
    color: "#555",
  },
  priceSection: {
    marginTop: 15,
  },
  subLabel: {
    fontSize: 13,
    color: "#666",
  },
  subAmount: {
    fontSize: 13,
    color: "#000",
  },
  divider: {
    borderBottomWidth: 1,
    borderColor: "#EEE",
    marginVertical: 6,
  },
  totalLabel: {
    fontWeight: "700",
    fontSize: 15,
  },
  totalAmount: {
    fontWeight: "700",
    fontSize: 15,
  },
  rowBetween: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  noteInfo: {
    fontSize: 11,
    color: "#777",
    marginTop: 8,
    marginBottom: 12,
    lineHeight: 16,
  },
  acceptBox: {
    borderTopWidth: 1,
    borderColor: "#DDD",
    paddingTop: 10,
  },
  acceptTitle: {
    fontWeight: "700",
    fontSize: 14,
    color: "#000",
  },
  acceptSubtitle: {
    fontSize: 12,
    color: "#666",
    marginBottom: 8,
  },
  sliderContainer: {
    width: '100%',
    height: 60,
    backgroundColor: '#f0f0f0',
    borderRadius: 30,
    overflow: 'hidden',
    position: 'relative',
    marginVertical: 20,
  },
  slider: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: '#00B0B9',
    position: 'absolute',
    left: 0,
    justifyContent: 'center',
    alignItems: 'center',
  },
  sliderTrack: {
    width: '100%',
    height: '100%',
    alignItems: 'center',
    justifyContent: 'center',
  },
  sliderText: {
    color: '#666',
    fontWeight: '600',
    fontSize: 16,
  },
  cancelButton: {
    borderWidth: 1,
    borderColor: "#999",
    borderRadius: 8,
    paddingVertical: 10,
    alignItems: "center",
  },
  cancelText: {
    fontWeight: "600",
    color: "#333",
  },
  bottomNav: { display: "none" },
  navItem: { display: "none" },
  navText: { display: "none" },
});
