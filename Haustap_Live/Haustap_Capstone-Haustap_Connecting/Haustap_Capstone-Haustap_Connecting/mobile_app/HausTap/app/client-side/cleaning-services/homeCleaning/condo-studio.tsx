import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

export default function CondoStudioScreen() {
  const services = [
    {
      title: "Basic Cleaning – 1 Cleaner",
      price: "₱800",
      desc: "Inclusions:\nLiving room & bedroom: walis, mop, dusting, trash removal\nWindows & mirrors: quick wipe\nHallway (if any): sweep & mop",
    },
    {
      title: "Standard Cleaning – 1–2 cleaners",
      price: "₱1,200",
      desc: "Inclusions:\nAll Basic tasks\nKitchen: wipe countertops, sink, stove top\nBathroom: scrub toilet, sink, shower area\nFurniture: cleaning under light furniture",
    },
    {
      title: "Deep Cleaning – 2 cleaners",
      price: "₱2,000",
      desc: "Inclusions:\nAll Standard tasks\nScrub floor tiles & grout\nClean behind appliances (ref, stove)\nDisinfect high-touch surfaces\nCarpet/rug vacuum",
    },
  ];

  return <MultiSelectList items={services} mainCategory="Cleaning Services" serviceName="Condo — Studio" />;
}

const styles = StyleSheet.create({});
