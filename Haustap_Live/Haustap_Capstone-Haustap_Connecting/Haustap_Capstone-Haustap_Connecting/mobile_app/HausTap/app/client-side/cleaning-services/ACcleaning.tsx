import React from "react";
import MultiSelectList from "../components/MultiSelectList";
import { acCleaningCategories } from "../data/cleaning";

export default function ACCleaningScreen() {
  return <MultiSelectList items={acCleaningCategories} mainCategory="Cleaning Services" serviceName="AC Cleaning" />;
}
