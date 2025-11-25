import { useRouter } from "expo-router";
import React from "react";
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";

export default function DuplexIndexScreen() {
  const router = useRouter();

  const options = [
    { title: "Duplex Smaller", route: "/client-side/cleaning-services/homeCleaning/duplex-smaller" },
    { title: "Duplex Larger", route: "/client-side/cleaning-services/homeCleaning/duplex-larger" },
  ];

  return (
    <View style={styles.container}>
      <ScrollView>
        <Text style={styles.header}>Duplex Options</Text>
        <View style={styles.optionsContainer}>
          {options.map((o, i) => (
            <TouchableOpacity
              key={i}
              style={styles.optionButton}
              onPress={() => router.push(o.route as any)}
            >
              <Text style={styles.optionText}>{o.title}</Text>
            </TouchableOpacity>
          ))}
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: "#fff" },
  header: { fontSize: 24, fontWeight: "bold", padding: 16, color: "#000" },
  optionsContainer: { padding: 16 },
  optionButton: { backgroundColor: "#3DC1C6", padding: 16, borderRadius: 8, marginBottom: 12 },
  optionText: { color: "#fff", fontSize: 16, fontWeight: "bold", textAlign: "center" },
});
