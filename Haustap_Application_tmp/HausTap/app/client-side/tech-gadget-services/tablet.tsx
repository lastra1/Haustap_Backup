import React from "react";
import MultiSelectList from "../components/MultiSelectList";

const services = [
  {
    title: "Screen replacement - android tablet",
    price: "₱600 per unit",
    desc: "Inclusions:\nRemoval of cracked or damaged screen\nInstallation of replacement screen (customer-provided or available stock)\nTouch and display quality test\nDevice assembly and sealing check"
  },
  {
    title: "Screen replacement – Ipad",
    price: "₱800 per unit",
    desc: "Inclusions:\nRemoval of cracked or damaged screen\nInstallation of replacement screen (customer-provided or available stock)\nTouch and display quality test\nDevice assembly and sealing check"
  },
  {
    title: "Battery replacement - android tablet",
    price: "₱400 per unit",
    desc: "Inclusions:\nRemoval of defective or swollen battery\nInstallation of new battery (customer-provided or available stock)\nCharging and power functionality test\nSafe disposal of old battery"
  },
  {
    title: "Battery replacement - Ipad",
    price: "₱600 per unit",
    desc: "Inclusions:\nRemoval of defective or swollen battery\nInstallation of new battery (customer-provided or available stock)\nCharging and power functionality test\nSafe disposal of old battery"
  }
];

export default function TabletScreen() {
  return <MultiSelectList items={services} mainCategory="Tech & Gadget Services" serviceName="Tablet Repair" />;
}