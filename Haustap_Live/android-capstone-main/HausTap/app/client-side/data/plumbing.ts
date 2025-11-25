import { Category } from "./types";

export const plumbingCategories: Category[] = [
  { title: "Inspection", price: "₱300", desc: "Applies if service did not proceed\nInclusions:\nOn-site visit by a plumber, Assessment of repair/service needs, Basic recommendation, No actual repair or installation included" },
  { title: "Faucet leak repair", price: "₱350", desc: "Inclusions:\nDiagnosis of faucet leak (washer, cartridge, or seal issue), Minor tightening / washer replacement, Leak test after repair, Replacement faucet not included" },
  { title: "Pipe leak repair", price: "₱600", desc: "Inclusions:\nLocate and assess pipe leak, Apply sealing, patch, or minor joint repair, Pressure test after repair, Replacement pipe parts not included" },
  { title: "Clogged sink / Drain cleaning", price: "₱500", desc: "Inclusions:\nCheck and remove debris / blockage, Use of plumber's snake / pump if needed, Water flow test after clearing" },
  { title: "Toilet bowl clog removal", price: "₱650", desc: "Assessment of blockage, Manual unclogging or use of auger/pump, Flush test to ensure flow" },
  { title: "Toilet flash repair/ replacement", price: "₱700", desc: "Inclusions:\nCheck flush mechanism (valve, handle, tank parts), Minor adjustment or replacement of faulty parts, Functionality test after repair, Replacement parts not included" },
  { title: "Shower head installation / replacement", price: "₱400", desc: "Inclusions:\nRemoval of old shower head (if any), Installation of new shower head, Water pressure test to ensure function" },
  { title: "Water heater installation - Basic", price: "₱1,500", desc: "Inclusions:\nMounting of heater unit, Basic plumbing connection to water line, Leak and functionality check, Electrical wiring not included (separate fee)" },
  { title: "Pipe installation - New connection", price: "₱200", desc: "Inclusions:\nInstallation of short pipe line (sink, toilet, faucet connection), Sealing of joints to prevent leaks, Water pressure test" },
  { title: "Siphon / Trap replacement", price: "₱500", desc: "Inclusions:\nRemoval of old siphon or trap (sink or toilet), Installation of new part, Leak test after replacement" },
];
