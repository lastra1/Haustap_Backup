import React from "react";
import MultiSelectList from "../components/MultiSelectList";

const services = [
  {
    title: "Charging port repair – android",
    price: "₱400 per unit",
    desc: "Inclusions:\nDiagnosis of charging port issue\nCleaning or replacement of charging port\nSoldering work (if required)\nTesting for charging stability and USB connection."
  },
  {
    title: "Charging Port repair – IOS",
    price: "₱600 per unit",
    desc: "Inclusions:\nDetailed check of charging dock/port\nCleaning or replacement of faulty port\nRe-soldering and alignment adjustments\nCharging and data transfer test"
  },
  {
    title: "Screen replacement – android",
    price: "₱500 per unit",
    desc: "Inclusions:\nRemoval of broken/damaged screen\nInstallation of replacement screen (customer-provided part or available stock)\nFunctionality test for touch and display\nBasic cleaning of device exterior"
  },
  {
    title: "Screen replacement – IOS",
    price: "₱700 per unit",
    desc: "Inclusions:\nSafe removal of damaged screen\nInstallation of new/replacement screen\nTouch responsiveness and display quality test\nDevice assembly and sealing check"
  },
  {
    title: "Battery replacement - android",
    price: "₱300 per unit",
    desc: "Inclusions:\nRemoval of old/non-working battery\nInstallation of new battery (customer-provided or available stock)\nPower-on and charging function test\nDisposal of defective battery"
  },
  {
    title: "Battery replacement – IOS",
    price: "₱500 per unit",
    desc: "Inclusions:\nSafe removal of swollen/damaged battery\nInstallation of new battery\nPower, charging, and battery health check\nDevice reassembly with secure fitting"
  }
];

export default function MobilePhoneScreen() {
  return <MultiSelectList items={services} mainCategory="Tech & Gadget Services" serviceName="Mobile Phone Repair" />;
}