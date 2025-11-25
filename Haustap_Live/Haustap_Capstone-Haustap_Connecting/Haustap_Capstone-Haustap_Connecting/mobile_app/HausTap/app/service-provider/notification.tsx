import { MaterialCommunityIcons } from "@expo/vector-icons";
import { useRouter } from 'expo-router';
import React from "react";
import { StyleSheet, Text, TouchableOpacity, View } from "react-native";

export default function App() {
  const router = useRouter();
  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.title}>Notification</Text>
        <TouchableOpacity>
          <Text style={styles.markAll}>Mark All as read</Text>
        </TouchableOpacity>
      </View>

      {/* Notification List */}
      <View style={styles.notificationCard}>
        <View style={styles.row}>
          <MaterialCommunityIcons name="email-outline" size={24} color="#333" />
          <View style={{ marginLeft: 10 }}>
            <Text style={styles.notificationTitle}>Promotions</Text>
            <Text style={styles.notificationDesc}>
              Get ₱50 OFF for First-Time Bookings
            </Text>
          </View>
        </View>
      </View>

      <View style={styles.divider} />

      <View style={styles.notificationCard}>
        <View style={styles.row}>
          <MaterialCommunityIcons name="cloud-outline" size={24} color="#00B0FF" />
          <View style={{ marginLeft: 10 }}>
            <Text style={styles.notificationTitle}>Haustap Update</Text>
            <Text style={styles.notificationDesc}>
              Welcome to Haustap Ms. Jenn Bornilla...
            </Text>
          </View>
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#F9F9F9",
    paddingHorizontal: 20,
    paddingTop: 60,
  },
  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 25,
  },
  title: {
    fontSize: 22,
    fontWeight: "700",
  },
  markAll: {
    fontSize: 14,
    color: "#666",
  },
  notificationCard: {
    marginBottom: 15,
  },
  row: {
    flexDirection: "row",
    alignItems: "center",
  },
  notificationTitle: {
    fontSize: 16,
    fontWeight: "600",
    color: "#000",
  },
  notificationDesc: {
    fontSize: 14,
    color: "#555",
  },
  divider: {
    height: 1,
    backgroundColor: "#CCC",
    marginVertical: 10,
  },
  navItem: {
    alignItems: "center",
  },
  navText: {
    fontSize: 12,
    color: "#000",
    marginTop: 4,
  },
});
