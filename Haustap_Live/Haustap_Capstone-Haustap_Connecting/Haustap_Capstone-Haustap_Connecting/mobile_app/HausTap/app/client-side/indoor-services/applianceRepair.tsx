import React from "react";
import MultiSelectList from "../components/MultiSelectList";
import { applianceRepairCategories } from "../data/applianceRepair";

export default function ApplianceRepairScreen() {
  return <MultiSelectList items={applianceRepairCategories} mainCategory="Indoor Services" serviceName="Appliance Repair" />;
}
