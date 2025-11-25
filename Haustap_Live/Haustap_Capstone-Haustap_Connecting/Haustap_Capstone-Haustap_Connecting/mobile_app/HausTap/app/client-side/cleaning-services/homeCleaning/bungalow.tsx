import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

export default function BungalowScreen() {
  const services = [
    {
      title: "Basic Cleaning – 1 Cleaner",
      price: "₱1,000",
      desc: "Inclusions:\nLiving Room: walis, mop, dusting furniture, trash removal\nBedrooms: bed making, sweeping, dusting, trash removal\nHallways: mop & sweep, remove cobwebs\nWindows & Mirrors: quick wipe",
    },
    {
      title: "Standard Cleaning – 2 Cleaner",
      price: "₱2,000",
      desc: "Inclusions:\nAll Basic Cleaning tasks plus Kitchen: wipe countertops, sink cleaning, stove top degrease, trash removal\nBathroom: scrub toilet, sink, shower, floor disinfecting\nFurniture: cleaning under/behind furniture\nWindows & Mirrors: full wipe & polish",
    },
    {
      title: "Deep Cleaning – 3 Cleaner",
      price: "₱3,000",
      desc: "Inclusions:\nAll Standard Cleaning tasks plus Flooring: scrubbing tiles/grout, polishing if applicable\nAppliances: behind refrigerator, oven, washing machine\nCarpets/Rugs: vacuum or shampoo\nDisinfection: doorknobs, switches, high-touch surfaces",
    },
  ];

  return <MultiSelectList items={services} mainCategory="Cleaning Services" serviceName="Bungalow" />;
}

const styles = StyleSheet.create({});
