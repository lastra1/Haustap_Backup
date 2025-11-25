import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../components/MultiSelectList";

const services = [
  {
    title: "Classic Lash Extensions",
    price: "₱500",
    desc: "Inclusions:\n1:1 lash application for a natural look\nLightweight and comfortable for daily wear\nEnhances length and curl without heavy volume"
  },
  {
    title: "Hybrid Lash Extensions",
    price: "₱800",
    desc: "Inclusions:\nCombination of classic and volume lash techniques\nFuller and more textured effect\nBalanced style for both natural and glam looks"
  },
  {
    title: "Volume Lash Extensions",
    price: "₱1,000",
    desc: "Inclusions:\n3D–6D lash fans applied for dramatic volume\nCreates a glamorous, eye-catching effect\nIdeal for clients who prefer bold lashes"
  },
  {
    title: "Mega Volume Lash Extensions",
    price: "₱1,500",
    desc: "Inclusions:\nMultiple ultra-fine lash fans for extra density\nIntense, dramatic lash look\nBest for special occasions or high-glam styles"
  },
  {
    title: "Lash Lift + Tint",
    price: "₱500",
    desc: "Inclusions:\nLifts and curls natural lashes from the root\nTint adds depth and mascara-like effect\nLasts several weeks with low maintenance"
  },
  {
    title: "Lower Lash Extensions",
    price: "₱300",
    desc: "Inclusions:\nExtensions applied to bottom lashes\nEnhances definition and balance to eye look\nComplements upper lash extensions"
  },
  {
    title: "Lash Removal",
    price: "₱500",
    desc: "Inclusions:\nGentle and safe removal of extensions\nProtects natural lashes from damage\nRecommended for switching lash styles"
  },
  {
    title: "Lash Retouch / Refill (2–3 weeks)",
    price: "₱800",
    desc: "Inclusions:\nFills in gaps from natural lash shedding\nMaintains fullness and shape of extensions\nKeeps lashes looking fresh and even"
  }
];

export default function LashesScreen() {
  return <MultiSelectList items={services} mainCategory="Beauty Services" serviceName="Lashes" />;
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