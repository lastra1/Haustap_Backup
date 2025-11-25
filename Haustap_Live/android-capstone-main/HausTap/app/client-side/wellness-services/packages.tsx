import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../components/MultiSelectList";

const packages = [
  {
    title: "Total Relaxation Package",
    price: "₱1,200",
    desc: "Relaxing massage to relieve tension and improve circulation.\nSwedish Full Body Massage (60 mins) + Reflexology (45 mins)",
  },
  {
    title: "Stress Relief Duo",
    price: "₱900",
    desc: "Back & Shoulder Massage (30 mins) + Scalp & Head Massage (30 mins)",
  },
];

export default function WellnessPackagesScreen() {
  return (
    <MultiSelectList items={packages} mainCategory="Wellness Services" serviceName="Wellness Packages" />
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
  categoriesContainer: {
    padding: 16,
  },
  categoryBox: {
    backgroundColor: "#DEE1E0",
    borderRadius: 10,
    padding: 15,
    marginBottom: 15,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 3,
    elevation: 3,
  },
  categoryContent: {
    flex: 1,
  },
  categoryTitle: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#000",
    marginBottom: 4,
  },
  categoryPrice: {
    fontSize: 14,
    color: "#444",
    marginBottom: 4,
  },
  categoryDesc: {
    fontSize: 14,
    color: "#444",
    lineHeight: 20,
  },
});
