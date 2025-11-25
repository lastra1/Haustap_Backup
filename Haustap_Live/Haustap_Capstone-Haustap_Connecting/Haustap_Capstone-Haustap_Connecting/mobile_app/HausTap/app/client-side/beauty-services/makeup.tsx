import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../components/MultiSelectList";
import { makeupCategories } from "../data/makeup";

export default function MakeupScreen() {
  const services = makeupCategories;
  return <MultiSelectList items={services} mainCategory="Beauty Services" serviceName="Makeup" />;
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
  sectionHeader: {
    marginVertical: 12,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: "700",
    color: "#222",
    marginBottom: 4,
  },
  sectionDesc: {
    fontSize: 14,
    color: "#666",
    marginBottom: 8,
  },
});