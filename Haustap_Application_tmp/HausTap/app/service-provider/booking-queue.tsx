import { Ionicons } from "@expo/vector-icons";
import * as Haptics from 'expo-haptics';
import { router } from "expo-router";
import React, { useRef, useState } from "react";
import { Animated, PanResponder, ScrollView, StyleSheet, Text, TextInput, TouchableOpacity, View } from "react-native";

export default function App() {
  const [isAccepted, setIsAccepted] = useState(false);
  const pan = useRef(new Animated.Value(0)).current;

  const panResponder = PanResponder.create({
    onStartShouldSetPanResponder: () => true,
    onPanResponderMove: (_, gesture) => {
      const maxSlide = 240; // Maximum slide distance
      const newValue = Math.max(0, Math.min(gesture.dx, maxSlide));
      pan.setValue(newValue);
    },
    onPanResponderRelease: (_, gesture) => {
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
            // Navigate to the before-pending-function screen after successful slide
            navigateToBeforePending();
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

  const handleBack = () => {
    router.push("/service-provider");
  };

  // Navigate to the screen defined in app/service-provider/before-pending-function.tsx
  const navigateToBeforePending = () => {
    try {
      // Give a small haptic confirmation, then replace the route so user can't go back
      Haptics.selectionAsync();
      router.replace('/service-provider/before-pending');
    } catch (e) {
      // eslint-disable-next-line no-console
      console.error('Navigation to before-pending failed', e);
    }
  };

  return (
    <ScrollView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={handleBack}>
          <Ionicons name="arrow-back" size={26} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>SP ACCEPT?</Text>
      </View>


      {/* Booking Card */}
      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <View>
            <Text style={styles.cardTitle}>Home Cleaning</Text>
            <Text style={styles.cardSubtitle}>Bungalow - Basic Cleaning</Text>
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
            B1 L50 Mango st. Phase 1 Saint Joseph Village 10 barangay Langgam, City of San Pedro, Laguna 4023
          </Text>
        </View>


        {/* Selected Service */}
        <View style={styles.section}>
          <Text style={styles.label}>Selected:</Text>
          <Text style={styles.valueBold}>Bungalow 80–150 sqm</Text>
          <Text style={styles.value}>Basic Cleaning – 1 Cleaner</Text>


          <Text style={[styles.label, { marginTop: 8 }]}>Inclusions:</Text>
          <Text style={styles.value}>
            Living Room: walls, mop, dusting furniture, trash removal,{"\n"}
            Bedrooms: bed making, sweeping, dusting, trash removal,{"\n"}
            Hallways: mop & sweep, remove cobwebs, Windows & Mirrors: quick wipe
          </Text>
        </View>


        {/* Notes */}
        <View style={styles.section}>
          <Text style={styles.label}>Notes:</Text>
          <TextInput style={styles.notesBox} placeholder=" " />
        </View>


        {/* Voucher */}
        <View style={styles.voucherBox}>
          <Ionicons name="pricetag-outline" size={20} color="#666" />
          <Text style={styles.voucherText}>No voucher added</Text>
        </View>


        {/* Total */}
        <View style={styles.totals}>
          <View style={styles.rowBetween}>
            <Text style={styles.label}>Sub Total</Text>
            <Text style={styles.value}>₱1,000.00</Text>
          </View>
          <View style={styles.rowBetween}>
            <Text style={styles.label}>Voucher Discount</Text>
            <Text style={styles.value}>₱0</Text>
          </View>
          <View style={styles.rowBetween}>
            <Text style={styles.totalText}>TOTAL</Text>
            <Text style={styles.totalText}>₱1,000.00</Text>
          </View>
          <Text style={styles.noteSmall}>
            Full payment will be collected directly by the service provider upon completion of the service.
          </Text>
        </View>


        {/* Accept Booking */}
        <View style={styles.acceptSection}>
          <Text style={styles.acceptTitle}>Accept Booking?</Text>
          <Text style={styles.acceptSub}>Be the first one to accept!</Text>


          <View style={styles.slideContainer}>
            <View style={styles.slideTrack}>
              <Text style={styles.slideText}>
                {isAccepted ? "Booking Accepted!" : "Slide to Accept Booking"}
              </Text>
            </View>
            <Animated.View
              {...panResponder.panHandlers}
              style={[
                styles.slideButton,
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
        </View>
      </View>
    </ScrollView>
  );
}


const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#F8F8F8",
    padding: 20,
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 15,
  },
  headerTitle: {
    fontSize: 12,
    color: "#999",
    marginLeft: 10,
  },
  card: {
    backgroundColor: "#F5F5F5",
    borderRadius: 12,
    padding: 16,
    elevation: 2,
    shadowColor: "#000",
    shadowOpacity: 0.1,
    shadowOffset: { width: 0, height: 1 },
    shadowRadius: 2,
  },
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 12,
  },
  cardTitle: {
    fontSize: 16,
    fontWeight: "700",
  },
  cardSubtitle: {
    color: "#444",
    fontSize: 13,
  },
  row: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginVertical: 6,
  },
  half: {
    width: "48%",
  },
  label: {
    fontSize: 13,
    color: "#666",
  },
  value: {
    fontSize: 14,
    color: "#000",
  },
  valueBold: {
    fontSize: 14,
    color: "#000",
    fontWeight: "600",
  },
  section: {
    marginVertical: 8,
  },
  notesBox: {
    borderWidth: 1,
    borderColor: "#DDD",
    borderRadius: 6,
    height: 50,
    marginTop: 4,
    padding: 8,
  },
  voucherBox: {
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#DDD",
    borderRadius: 8,
    padding: 10,
    marginTop: 10,
  },
  voucherText: {
    marginLeft: 8,
    color: "#444",
  },
  totals: {
    marginTop: 16,
  },
  rowBetween: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginVertical: 2,
  },
  totalText: {
    fontSize: 15,
    fontWeight: "700",
  },
  noteSmall: {
    fontSize: 11,
    color: "#777",
    marginTop: 4,
  },
  acceptSection: {
    marginTop: 20,
    alignItems: "center",
  },
  acceptTitle: {
    fontSize: 16,
    fontWeight: "700",
  },
  acceptSub: {
    color: "#777",
    fontSize: 13,
    marginBottom: 12,
  },
  slideContainer: {
    width: '100%',
    height: 60,
    backgroundColor: '#f0f0f0',
    borderRadius: 30,
    overflow: 'hidden',
    position: 'relative',
    marginVertical: 20,
  },
  slideButton: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: '#00B0B9',
    position: 'absolute',
    left: 0,
    justifyContent: 'center',
    alignItems: 'center',
  },
  slideTrack: {
    width: '100%',
    height: '100%',
    alignItems: 'center',
    justifyContent: 'center',
  },
  slideText: {
    color: '#666',
    fontWeight: '600',
    fontSize: 16,
  },
  screenCenter: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
});
