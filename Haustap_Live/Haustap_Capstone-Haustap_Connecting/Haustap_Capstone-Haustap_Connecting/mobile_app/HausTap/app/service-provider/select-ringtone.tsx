import { Ionicons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import React, { useState } from "react";
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";
import { settingsStore } from '../../src/services/settingsStore';

export default function SelectRingtoneScreen() {
  const router = useRouter();
  const [selectedTone, setSelectedTone] = useState(settingsStore.getRingtone());

  // subscribe to store updates (e.g. when persisted value loads)
  React.useEffect(() => {
    const unsub = settingsStore.subscribe(() => {
      const t = settingsStore.getRingtone();
      setSelectedTone(t);
    });
    return unsub;
  }, []);

  const ringtones = [
    "Honk (Default)",
    "Bottle",
    "Bubble",
    "Bullfrog",
    "Burst",
    "Chirp",
    "Clank",
    "Crystal",
    "Fadelin",
  ];

  const handleConfirm = () => {
    console.log("Selected Ringtone:", selectedTone);
    settingsStore.setRingtone(selectedTone);
    router.back();
  };

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Select Ringtone</Text>
      </View>

      {/* Ringtone List */}
      <ScrollView showsVerticalScrollIndicator={false}>
        {ringtones.map((tone, index) => (
          <TouchableOpacity
            key={index}
            style={styles.toneItem}
            onPress={() => setSelectedTone(tone)}
          >
            <Text style={styles.toneText}>{tone}</Text>
            <Ionicons
              name={
                selectedTone === tone
                  ? "radio-button-on"
                  : "radio-button-off"
              }
              size={22}
              color={selectedTone === tone ? "#00B6C7" : "#aaa"}
            />
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Confirm Button */}
      <TouchableOpacity style={styles.confirmButton} onPress={handleConfirm}>
        <Text style={styles.confirmText}>Confirm</Text>
      </TouchableOpacity>
    </View>
  );
}


const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
    paddingTop: 50,
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 20,
    marginBottom: 10,
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: "bold",
    marginLeft: 10,
  },
  toneItem: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 15,
    paddingHorizontal: 20,
    borderBottomWidth: 1,
    borderColor: "#f2f2f2",
  },
  toneText: {
    fontSize: 16,
    color: "#000",
  },
  confirmButton: {
    backgroundColor: "#00B6C7",
    borderRadius: 8,
    marginHorizontal: 20,
    marginVertical: 25,
    paddingVertical: 14,
    alignItems: "center",
  },
  confirmText: {
    color: "#000",
    fontWeight: "bold",
    fontSize: 16,
  },
});
