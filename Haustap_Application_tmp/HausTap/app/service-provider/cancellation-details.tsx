import React from "react";
import { StyleSheet, Text, View } from "react-native";

export default function CancellationDetailsScreen() {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Cancellation Details</Text>
      <Text style={styles.text}>Details about the cancellation will appear here.</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#F9F9F9",
    padding: 20,
  },
  title: {
    fontSize: 22,
    fontWeight: "700",
    marginBottom: 12,
  },
  text: {
    fontSize: 16,
    color: "#555",
    textAlign: "center",
  },
});
