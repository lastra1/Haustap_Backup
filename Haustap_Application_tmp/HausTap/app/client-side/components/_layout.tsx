import { Ionicons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import React, { useState } from "react";
import {
    SafeAreaView,
    ScrollView,
    StyleSheet,
    Text,
    TouchableOpacity,
    View,
} from "react-native";

export default function BookingScreen() {
  const router = useRouter();
  const [selectedCategory, setSelectedCategory] = useState("Home Cleaning");

  const categories = [
    "Home Cleaning",
    "AC Cleaning",
    "AC Deep Cleaning (Chemical Cleaning)",
  ];

  const services = [
    {
      title: "Bungalow",
      subtitle: "(80–150 sqm)",
      desc: "A single-story house with wider living spaces, ideal for families.",
    },
    {
      title: "Condominium Studio / 1BR",
      subtitle: "Studio / 1BR (25–60 sqm)",
      desc: "A compact unit with one bedroom or open layout, ideal for singles or couples.",
    },
    {
      title: "Condominium 2BR",
      subtitle: "(60–100 sqm)",
      desc: "A medium-sized condo unit with two bedrooms, suited for small families.",
    },
    {
      title: "Condominium Penthouse",
      subtitle: "~150 sqm",
      desc: "A large, luxury unit at the top floor with spacious rooms and premium finishes.",
    },
    {
      title: "Duplex",
      subtitle: "Larger (150–200 sqm)",
      desc: "A bigger duplex unit with wider rooms and more functional areas for families.",
    },
    {
      title: "Container House",
      subtitle: "Single (10–20 sqm)",
      desc: "A compact home built from one container, often studio-type.",
    },
    {
      title: "Container House",
      subtitle: "Multiple (30–50 sqm)",
      desc: "A larger house made from combined containers with more functional spaces.",
    },
    {
      title: "Stilt House",
      subtitle: "Small (30–50 sqm)",
      desc: "A raised house on stilts, typically simple and compact.",
    },
    {
      title: "Stilt House",
      subtitle: "Large (80–120 sqm)",
      desc: "A bigger elevated house with wider living and bedroom areas.",
    },
    {
      title: "Mansion",
      subtitle: "Smaller (300–500 sqm)",
      desc: "A large, multi-room residence with luxury features and wide spaces.",
    },
    {
      title: "Mansion",
      subtitle: "Larger (600–1000 sqm)",
      desc: "A very spacious, high-end residence with multiple floors and amenities.",
    },
    {
      title: "Villa",
      subtitle: "Smaller (100–250 sqm)",
      desc: "A private, medium-sized luxury house often with a garden or outdoor space.",
    },
    {
      title: "Villa",
      subtitle: "Larger (300–1000 sqm)",
      desc: "A grand villa with expansive living areas, outdoor features, and multiple rooms.",
    },
  ];

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={22} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Cleaning Services</Text>
      </View>

      {/* Top Text Navigation */}
      <View style={styles.textNav}>
        {categories.map((cat, index) => (
          <React.Fragment key={cat}>
            <TouchableOpacity onPress={() => setSelectedCategory(cat)}>
              <Text
                style={[
                  styles.navText,
                  selectedCategory === cat && styles.navTextActive,
                ]}
              >
                {cat}
              </Text>
            </TouchableOpacity>
            {index < categories.length - 1 && (
              <Text style={styles.navDivider}>|</Text>
            )}
          </React.Fragment>
        ))}
      </View>

      {/* Service List */}
      <ScrollView style={styles.scrollView} showsVerticalScrollIndicator={false}>
        {services.map((item, index) => (
          <View key={index} style={styles.card}>
            <View style={{ flexDirection: "row", alignItems: "center" }}>
              <Ionicons
                name="home-outline"
                size={20}
                color="#3DC1C6"
                style={{ marginRight: 10 }}
              />
              <View style={{ flex: 1 }}>
                <Text style={styles.cardTitle}>{item.title}</Text>
                <Text style={styles.cardSubtitle}>{item.subtitle}</Text>
                <Text style={styles.cardDesc}>{item.desc}</Text>
              </View>
            </View>
          </View>
        ))}
      </ScrollView>

      {/* Next Button */}
      <TouchableOpacity
        style={styles.nextButton}
        onPress={() => router.push("/(client)/nextStep" as any)}
      >
        <Text style={styles.nextText}>Next</Text>
      </TouchableOpacity>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: "#fff" },
  header: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 15,
    paddingVertical: 10,
  },
  headerTitle: { fontSize: 18, fontWeight: "600", marginLeft: 8 },
  textNav: {
    flexDirection: "row",
    justifyContent: "center",
    flexWrap: "wrap",
    marginBottom: 10,
    marginTop: 5,
  },
  navText: {
    fontSize: 13,
    color: "#555",
    marginHorizontal: 5,
  },
  navTextActive: {
    color: "#3DC1C6",
    fontWeight: "600",
  },
  navDivider: {
    color: "#999",
    fontSize: 13,
  },
  scrollView: {
    paddingHorizontal: 15,
  },
  card: {
    backgroundColor: "#F9F9F9",
    borderRadius: 10,
    padding: 15,
    marginBottom: 10,
    borderWidth: 1,
    borderColor: "#E0E0E0",
  },
  cardTitle: { fontWeight: "600", color: "#000", fontSize: 15 },
  cardSubtitle: {
    fontSize: 13,
    color: "#666",
    marginTop: 2,
    marginBottom: 5,
  },
  cardDesc: { fontSize: 12, color: "#444", lineHeight: 18 },
  nextButton: {
    backgroundColor: "#3DC1C6",
    margin: 15,
    borderRadius: 6,
    paddingVertical: 12,
    alignItems: "center",
  },
  nextText: { color: "#fff", fontWeight: "600", fontSize: 16 },
});
