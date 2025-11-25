import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../components/MultiSelectList";

const services = [
  { title: "Initial Assessment", price: "₱1,000", desc: "Complete physical assessment and treatment plan development" },
  { title: "General Physical Therapy", price: "₱800/session", desc: "Standard physical therapy treatment for general conditions" },
  { title: "Sports Injury Therapy", price: "₱1,200/session", desc: "Specialized treatment for sports-related injuries" },
  { title: "Post-Surgery Rehabilitation", price: "₱1,500/session", desc: "Rehabilitation therapy for post-surgery recovery" },
  { title: "Exercise Program", price: "₱2,000", desc: "Customized exercise program development with follow-up sessions" },
];

export default function TherapyScreen() {
  return <MultiSelectList items={services} mainCategory="Wellness Services" serviceName="Therapy" />;
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