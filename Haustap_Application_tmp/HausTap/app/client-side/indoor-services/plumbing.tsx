import React from "react";
import MultiSelectList from "../components/MultiSelectList";
import { plumbingCategories } from "../data/plumbing";

export default function PlumbingScreen() {
  return <MultiSelectList items={plumbingCategories} mainCategory="Indoor Services" serviceName="Plumbing" />;
}
