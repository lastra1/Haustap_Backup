import React from "react";
import MultiSelectList from "../components/MultiSelectList";
import { pestControlCategories } from "../data/pestControl";

export default function PestControlScreen() {
  return <MultiSelectList items={pestControlCategories} mainCategory="Indoor Services" serviceName="Pest Control" />;
}
