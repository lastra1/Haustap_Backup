import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../components/MultiSelectList";

const services = [
  { title: "Full Body Massage (Swedish)", price: "₱800 / 60 mins", desc: "Relaxing massage to relieve tension and improve circulation." },
  { title: "Full Body Massage (Deep Tissue)", price: "₱1,000 / 60 mins", desc: "Firm pressure to target deep muscle knots and chronic tension." },
  { title: "Aromatherapy Massage", price: "₱1,200 / 60 mins", desc: "Relaxing massage with essential oils for stress relief" },
  { title: "Reflexology (Foot Massage)", price: "₱600 / 45 mins", desc: "Focused massage on pressure points in the feet." },
  { title: "Scalp & Head Massage", price: "₱500 / 30 mins", desc: "Relieves headaches and promotes blood circulation." },
  { title: "Back & Shoulder Massage", price: "₱500 / 30 mins", desc: "Quick relief for upper body tension." },
];

export default function MassageScreen() {
  return <MultiSelectList items={services} mainCategory="Wellness Services" serviceName="Massage" />;
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