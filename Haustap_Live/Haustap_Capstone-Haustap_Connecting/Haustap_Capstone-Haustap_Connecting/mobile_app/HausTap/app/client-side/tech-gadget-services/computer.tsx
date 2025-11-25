import React from "react";
import MultiSelectList from "../components/MultiSelectList";

const services = [
  {
    title: "Fan / Cooling Repair (Laptop)",
    price: "₱700 per unit",
    desc: "Inclusions:\nDiagnosis of cooling system (fan, vents, heat sink)\nCleaning of dust and minor obstructions. Fan repair or replacement (if needed, part provided separately)\nThermal paste application (if required)\nOverheating test after repair"
  },
  {
    title: "Keyboard Replacement (Laptop)",
    price: "₱700 per unit",
    desc: "Inclusions:\nReplacement of external desktop keyboard\nInstallation of new keyboard (client-provided or available stock)\nFunctional test of all keys"
  },
  {
    title: "OS Reformat + Software Installation (Laptop)",
    price: "₱1,000 per unit",
    desc: "Inclusions:\nFull OS reinstallation (Windows/macOS/Linux, as provided by client)\nInstallation of basic drivers (audio, display, network, etc.)\nInstallation of up to 3 client-provided software/applications\nBasic system optimization and testing, Data backup not included"
  },
  {
    title: "Fan / Cooling Repair (Desktop PC)",
    price: "₱700 per unit",
    desc: "Inclusions:\nDiagnosis of cooling system (fan, vents, heat sink)\nCleaning of dust and minor obstructions. Fan repair or replacement (if needed, part provided separately)\nThermal paste application (if required)\nOverheating test after repair"
  },
  {
    title: "Keyboard Replacement (Desktop PC)",
    price: "₱300 per unit",
    desc: "Inclusions:\nReplacement of external desktop keyboard\nInstallation of new keyboard (client-provided or available stock)\nFunctional test of all keys"
  },
  {
    title: "OS Reformat + Software Installation (Desktop PC)",
    price: "₱800 per unit",
    desc: "Inclusions:\nFull OS reinstallation (Windows/macOS/Linux, as provided by client)\nInstallation of basic drivers (audio, display, network, etc.)\nInstallation of up to 3 client-provided software/applications\nBasic system optimization and testing, Data backup not included"
  }
];

export default function ComputerScreen() {
  return <MultiSelectList items={services} mainCategory="Tech & Gadget Services" serviceName="Computer Repair" />;
}