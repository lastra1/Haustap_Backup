import { useRouter } from "expo-router";
import React from "react";
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";

export default function CleaningServicesScreen() {
  const router = useRouter();

  const services = [
    { title: "Home Cleaning", route: "/client-side/cleaning-services/homeCleaning" },
    { title: "AC Cleaning", route: "/client-side/cleaning-services/ACcleaning" },
    { title: "AC Deep Cleaning", route: "/client-side/cleaning-services/ACdeepCleaning" },
  ] as const;

  return (
    <View style={styles.container}>
      <ScrollView>
        <Text style={styles.header}>Cleaning Services</Text>
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
