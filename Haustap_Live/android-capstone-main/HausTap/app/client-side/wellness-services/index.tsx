import { useRouter } from "expo-router";
import React from "react";
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";

export default function WellnessServicesScreen() {
  const router = useRouter();

  const services = [
    { title: "Massage", route: "/client-side/wellness-services/massage" },
    { title: "Packages", route: "/client-side/wellness-services/packages" },
  ] as const;

  return (
    <View style={styles.container}>
      <ScrollView>
        <Text style={styles.header}>Wellness Services</Text>
        <View style={styles.servicesContainer}>
          {services.map((service, index) => (
            <TouchableOpacity
              key={index}
              style={styles.serviceButton}
              onPress={() => router.push(service.route)}
            >
              <Text style={styles.serviceText}>{service.title}</Text>
            </TouchableOpacity>
          ))}
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
  },
  header: {
    fontSize: 24,
    fontWeight: "bold",
    padding: 16,
    color: "#000",
  },
  servicesContainer: {
    padding: 16,
  },
  serviceButton: {
    backgroundColor: "#3DC1C6",
    padding: 16,
    borderRadius: 8,
    marginBottom: 12,
  },
  serviceText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "bold",
    textAlign: "center",
  },
});
