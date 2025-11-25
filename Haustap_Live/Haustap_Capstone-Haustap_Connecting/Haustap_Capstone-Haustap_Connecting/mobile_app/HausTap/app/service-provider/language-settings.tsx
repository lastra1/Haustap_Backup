import React, { useState } from "react";
import { View, Text, TouchableOpacity, StyleSheet } from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { useRouter } from "expo-router";


export default function LanguageSettingScreen() {
  const [selectedLanguage, setSelectedLanguage] = useState("English");
  const router = useRouter();


  const handleConfirm = () => {
    console.log("Selected Language:", selectedLanguage);
    router.back(); // navigate back to previous screen
  };


  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Language Setting</Text>
      </View>


      {/* Language Options */}
      <View style={styles.languageList}>
        {["English", "Tagalog"].map((language) => (
          <TouchableOpacity
            key={language}
            style={styles.languageItem}
            onPress={() => setSelectedLanguage(language)}
          >
            <Text style={styles.languageText}>{language}</Text>
            <Ionicons
              name={
                selectedLanguage === language
                  ? "radio-button-on"
                  : "radio-button-off"
              }
              size={22}
              color={selectedLanguage === language ? "#000" : "#aaa"}
            />
          </TouchableOpacity>
        ))}
      </View>


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
    marginBottom: 20,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: "600",
    marginLeft: 10,
  },
  languageList: {
    borderTopWidth: 1,
    borderBottomWidth: 1,
    borderColor: "#eee",
  },
  languageItem: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 15,
    paddingHorizontal: 20,
    borderBottomWidth: 1,
    borderColor: "#f2f2f2",
  },
  languageText: {
    fontSize: 16,
    color: "#000",
  },
  confirmButton: {
    backgroundColor: "#00B6C7",
    borderRadius: 8,
    marginHorizontal: 20,
    marginTop: 30,
    paddingVertical: 14,
    alignItems: "center",
  },
  confirmText: {
    color: "#000",
    fontWeight: "bold",
    fontSize: 16,
  },
})
