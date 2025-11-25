import { Ionicons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import React, { useEffect, useState } from "react";
import { StyleSheet, Switch, Text, TouchableOpacity, View } from "react-native";
import { settingsStore } from '../../src/services/settingsStore';


export default function SoundSettingScreen() {
  const router = useRouter();
  const [isSoundEnabled, setIsSoundEnabled] = useState(true);
  const [currentRingtone, setCurrentRingtone] = useState(settingsStore.getRingtone());


  const toggleSwitch = () => {
    setIsSoundEnabled((previousState) => !previousState);
    console.log("Notification Sound Enabled:", !isSoundEnabled);
  };

  useEffect(() => {
    // refresh displayed ringtone from store when this screen mounts
    setCurrentRingtone(settingsStore.getRingtone());
  }, []);


  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Sound</Text>
      </View>


      {/* Notification Sound Section */}
      <View style={styles.section}>
        <View style={styles.soundRow}>
          <View>
            <Text style={styles.optionTitle}>Enable Notification Sound</Text>
            <Text style={styles.optionDescription}>
              Plays for incoming new orders and push notifications.
            </Text>
          </View>
          <Switch
            trackColor={{ false: "#ccc", true: "#00B6C7" }}
            thumbColor="#fff"
            onValueChange={toggleSwitch}
            value={isSoundEnabled}
          />
        </View>
      </View>


      {/* General Sound Option */}
      <View style={styles.section}>
        <TouchableOpacity style={styles.generalRow} onPress={() => router.push('/service-provider/select-ringtone')} accessibilityLabel="Open ringtone selector">
          <View>
            <Text style={styles.generalTitle}>General</Text>
            <Text style={styles.generalSubtitle}>{currentRingtone}</Text>
          </View>
          <Ionicons name="chevron-forward" size={20} color="#aaa" />
        </TouchableOpacity>
      </View>
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
    marginBottom: 20,
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: "bold",
    marginLeft: 10,
  },
  section: {
    borderTopWidth: 1,
    borderBottomWidth: 1,
    borderColor: "#eee",
    marginTop: 10,
  },
  soundRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 20,
    paddingVertical: 15,
  },
  optionTitle: {
    fontSize: 15,
    fontWeight: "bold",
  },
  optionDescription: {
    fontSize: 13,
    color: "#666",
    marginTop: 3,
    width: 250,
  },
  generalRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 20,
    paddingVertical: 15,
  },
  generalTitle: {
    fontSize: 15,
    fontWeight: "bold",
  },
  generalSubtitle: {
    fontSize: 13,
    color: "#555",
    marginTop: 3,
  },
});
