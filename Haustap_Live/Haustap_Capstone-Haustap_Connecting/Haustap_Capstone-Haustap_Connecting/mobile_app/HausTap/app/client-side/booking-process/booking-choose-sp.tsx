import { Ionicons } from "@expo/vector-icons";
import { useRouter } from 'expo-router';
import React, { useEffect, useState } from "react";
import {
  ActivityIndicator,
  Animated,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from "react-native";

const BookingStatusCard = () => {
  const [isAccepted, setIsAccepted] = useState(false);
  const [fadeAnim] = useState(new Animated.Value(0));
  const router = useRouter();

  useEffect(() => {
    Animated.timing(fadeAnim, {
      toValue: 1,
      duration: 400,
      useNativeDriver: true,
    }).start();
  }, [isAccepted]);

  const handleCancel = () => {
    router.back();
  };

  // Simulate accepted booking (for demo)
  // Replace with your actual API or socket event
  useEffect(() => {
    const timer = setTimeout(() => setIsAccepted(true), 4000);
    return () => clearTimeout(timer);
  }, []);

  // when booking is accepted, redirect back to client-side homepage after a short delay
  useEffect(() => {
    if (!isAccepted) return;
    const t = setTimeout(() => {
      router.replace('/client-side');
    }, 2000);
    return () => clearTimeout(t);
  }, [isAccepted, router]);

  return (
    <Animated.View style={[styles.cardContainer, { opacity: fadeAnim }]}>
      <View style={styles.card}>
        <Ionicons
          name="globe-outline"
          size={50}
          color="#00ABB1"
          style={{ marginBottom: 10 }}
        />
        <Text style={styles.brand}>Haustap</Text>

        {!isAccepted ? (
          <>
            <ActivityIndicator size="large" color="#00ABB1" style={{ marginVertical: 16 }} />
            <Text style={styles.message}>
              Looking for a service provider to accept your booking...
            </Text>

            <TouchableOpacity style={styles.cancelButton} onPress={handleCancel}>
              <Text style={styles.cancelButtonText}>Cancel Booking</Text>
            </TouchableOpacity>
          </>
        ) : (
          <>
            <View style={styles.checkIconWrapper}>
              <Ionicons name="checkmark-circle" size={50} color="#00C851" />
            </View>
            <Text style={styles.acceptedMessage}>
              Your booking has been accepted!
            </Text>

            <View style={styles.bookingInfoBox}>
              <Text style={styles.clientName}>Ana Santos</Text>
              <Text style={styles.bookingId}>Booking ID: 12345</Text>
            </View>
          </>
        )}
      </View>
    </Animated.View>
  );
};

const styles = StyleSheet.create({
  cardContainer: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#f6f6f6",
    paddingVertical: 20,
  },
  card: {
    width: "85%",
    backgroundColor: "#fff",
    borderRadius: 12,
    paddingVertical: 30,
    alignItems: "center",
    elevation: 3,
    shadowColor: "#000",
    shadowOpacity: 0.1,
    shadowRadius: 6,
  },
  brand: {
    color: "#00ABB1",
    fontWeight: "700",
    fontSize: 16,
    marginBottom: 10,
  },
  message: {
    fontSize: 14,
    color: "#333",
    textAlign: "center",
    marginBottom: 20,
    paddingHorizontal: 20,
  },
  cancelButton: {
    backgroundColor: "#00ABB1",
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: 8,
  },
  cancelButtonText: {
    color: "#fff",
    fontWeight: "600",
  },
  checkIconWrapper: {
    marginVertical: 10,
  },
  acceptedMessage: {
    fontSize: 15,
    color: "#333",
    marginVertical: 10,
  },
  bookingInfoBox: {
    backgroundColor: "#E6F8FA",
    padding: 10,
    borderRadius: 8,
    alignItems: "center",
    width: "80%",
  },
  clientName: {
    color: "#00ABB1",
    fontWeight: "700",
  },
  bookingId: {
    color: "#333",
    fontSize: 13,
  },
});

export default BookingStatusCard;
