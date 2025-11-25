import React from "react";
import MultiSelectList from "../components/MultiSelectList";
import { pestControlOutdoorCategories } from "../data/pestControlOutdoor";

export default function PestControlOutdoorScreen() {
  return <MultiSelectList items={pestControlOutdoorCategories} mainCategory="Outdoor Services" serviceName="Outdoor Pest Control" />;
}
