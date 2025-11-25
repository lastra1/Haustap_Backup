import React from "react";
import MultiSelectList from "../components/MultiSelectList";
import { electricalCategories } from "../data/electrical";

export default function ElectricalScreen() {
  return <MultiSelectList items={electricalCategories} mainCategory="Indoor Services" serviceName="Electrical" />;
}
