import React from "react";
import MultiSelectList from "../components/MultiSelectList";
import { gardeningCategories } from "../data/gardening";

export default function GardenLandscapingScreen() {
  return <MultiSelectList items={gardeningCategories} mainCategory="Outdoor Services" serviceName="Garden & Landscaping" />;
}
