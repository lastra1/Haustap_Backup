import React from "react";
import MultiSelectList from "../components/MultiSelectList";
import { gardeningCategories } from "../data/gardening";

export default function GardenLandscapingScreen() {
  const items = gardeningCategories;
  return (
    <MultiSelectList
      items={items as any}
      mainCategory="Outdoor Services"
      serviceName="Garden & Landscaping"
      pageTitle="Outdoor Services"
      pageSubtitle="Gardening & Landscaping"
      nextLabel="Proceed"
    />
  );
}
