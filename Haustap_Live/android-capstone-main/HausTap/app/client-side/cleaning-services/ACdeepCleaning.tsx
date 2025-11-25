import React from "react";
import MultiSelectList from "../components/MultiSelectList";
import { acDeepCleaningCategories } from "../data/cleaning";

export default function ACDeepCleaningScreen() {
  return <MultiSelectList items={acDeepCleaningCategories} mainCategory="Cleaning Services" serviceName="AC Deep Cleaning" />;
}
