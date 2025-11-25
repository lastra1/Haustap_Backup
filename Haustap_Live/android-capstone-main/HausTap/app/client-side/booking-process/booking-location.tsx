import { Ionicons } from "@expo/vector-icons"
import { useRouter } from "expo-router"
import React from "react"
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native"

export default function BookingLocation() {
  const router = useRouter()
  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Booking Location</Text>
      </View>
      <View style={styles.placeholder}>
        <Text style={styles.placeholderText}>
          Map view is not available on web. Open this screen in Expo Go on Android/iOS.
        </Text>
      </View>
    </ScrollView>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
    paddingHorizontal: 16,
    paddingTop: 40,
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 12,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "600",
    marginLeft: 10,
  },
  placeholder: {
    height: 300,
    borderRadius: 12,
    overflow: "hidden",
    marginBottom: 20,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#f0f0f0",
  },
  placeholderText: {
    fontSize: 16,
    textAlign: "center",
  },
})
