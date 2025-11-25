import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../components/MultiSelectList";

const packages = [
  {
    title: "Basic Care Package",
    price: "₱1,000",
    desc: "Inclusions:\n1:1 lash application for a natural look\nLightweight and comfortable for daily wear\nEnhances length and curl without heavy volume",
  },
  {
    title: "Glam Essentials Package",
    price: "₱2,200",
    desc: "Inclusions:\nCombination of classic and volume lash techniques\nFuller and more textured effect\nBalanced style for both natural and glam looks",
  },
  {
    title: "Event Ready Package",
    price: "₱3,500",
    desc: "Inclusions:\n3D–6D lash fans applied for dramatic volume\nCreates a glamorous, eye-catching effect\nIdeal for clients who prefer bold lashes",
  },
  {
    title: "Bridal Radiance Package",
    price: "₱8,000",
    desc: "Inclusions:\nMultiple ultra-fine lash fans for extra density\nIntense, dramatic lash look\nBest for special occasions or high-glam styles",
  },
  {
    title: "Mani + Pedi Combo",
    price: "₱500",
    desc: "Inclusions:\nLifts and curls natural lashes from the root\nTint adds depth and mascara-like effect\nLasts several weeks with low maintenance",
  },
  {
    title: "Gel Mani + Pedi Combo",
    price: "₱1,300",
    desc: "Inclusions:\nExtensions applied to bottom lashes\nEnhances definition and balance to eye look\nComplements upper lash extensions",
  },
];

export default function PackagesScreen() {
  return <MultiSelectList items={packages} mainCategory="Beauty Services" serviceName="Packages" />;
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
