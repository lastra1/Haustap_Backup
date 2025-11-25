import React from "react";
import MultiSelectList from "../components/MultiSelectList";

const services = [
  {
    title: "Controller repair",
    price: "₱300 per unit",
    desc: "Inclusions:\nDiagnosis of controller issues (buttons, joystick, triggers, or connectivity)\nCleaning and calibration of internal parts\nReplacement of minor components/parts (if provided by client)\nFunctionality test and gameplay test after repair"
  },
  {
    title: "HDMI port repair",
    price: "₱700 per unit",
    desc: "Inclusions:\nDiagnosis of HDMI port issue (loose, bent, or damaged pins)\nRemoval of faulty HDMI port\nInstallation of new HDMI port (customer-provided or available stock)\nTesting of video/audio output to monitor/TV\nDevice assembly and functionality check"
  },
  {
    title: "Disc Drive Repair / Replacement",
    price: "₱800 per unit",
    desc: "Inclusions:\nDiagnosis of disc reading/ejecting issues\nCleaning of optical lens\nAdjustment or repair of disc tray mechanism\nReplacement of faulty disc drive (if provided by client)\nPlayback test with game disc"
  },
  {
    title: "Power Supply Repair / Replacement",
    price: "₱900 per unit",
    desc: "Inclusions:\nDiagnosis of power-related issues (no power, sudden shutdowns)\nChecking and repairing internal power supply\nReplacement of power supply unit (if provided by client)\nFunctionality and safety test after repair"
  },
  {
    title: "Software Reinstallation / Update",
    price: "₱300 per unit",
    desc: "Inclusions:\nSystem software update/reinstallation (PlayStation, Xbox, Nintendo, etc.)\nInstallation of latest firmware version\nOptimization of console performance\nTesting of system functions and online connectivity"
  }
];

export default function GamingScreen() {
  return <MultiSelectList items={services} mainCategory="Tech & Gadget Services" serviceName="Gaming Console Repair" />;
}