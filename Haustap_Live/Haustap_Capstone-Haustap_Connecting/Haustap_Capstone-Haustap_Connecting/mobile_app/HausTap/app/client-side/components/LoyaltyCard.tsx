import React from "react";
import { View, Text, StyleSheet } from "react-native";

export default function LoyaltyCard({
  title,
  subtitle,
  description,
  condition,
}: {
  title: string;
  subtitle: string;
  description: string;
  condition: string;
}) {
  return (
    <View style={styles.card}>
      <Text style={styles.title}>{title}</Text>
      <Text style={styles.subtitle}>{subtitle}</Text>
      <Text style={styles.description}>{description}</Text>
      <Text style={styles.condition}>{condition}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: "#fff",
    borderRadius: 15,
    width: "48%",
    aspectRatio: 1, // makes it square
    padding: 15,
    elevation: 4,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    justifyContent: "center",
  },
  title: {
    color: "#3DC1C6",
    fontWeight: "bold",
    fontSize: 12,
    textTransform: "uppercase",
    marginBottom: 5,
  },
  subtitle: {
    fontSize: 11,
    color: "#00A1A7",
    fontWeight: "600",
    marginBottom: 6,
  },
  description: {
    fontSize: 10.5,
    color: "#444",
    lineHeight: 15,
    marginBottom: 6,
  },
  condition: {
    fontSize: 10,
    color: "#666",
    fontStyle: "italic",
  },
});
