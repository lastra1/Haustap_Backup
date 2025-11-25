import { Category } from "./types";

export const electricalCategories: Category[] = [
  { title: "Inspection", price: "₱300", desc: "Applies if service did not proceed\nInclusions:\nOn-site visit by electrician, Assessment of wiring / electrical issue, Basic recommendations / cost estimate, Repair not included" },
  { title: "Outlet installation / repair", price: "₱400 per outlet", desc: "Inclusions:\nMounting of new outlet or repair of damaged one, Electrical connection and safety check, Test using appliance or tester" },
  { title: "Light Switch repair", price: "₱400 per repair", desc: "Inclusions:\nRemoval of damaged socket (if any), Installation of new socket, Connection to existing wiring, Functionality and safety test" },
  { title: "Light installation", price: "₱500 per install", desc: "Inclusions:\nSwitch installation, Installation of bulb socket / fixture, Power-on functionality test" },
  { title: "Light switch replacement", price: "₱350 per replacement", desc: "Inclusions:\nCheck faulty switch, Replace or repair as needed, Test switch and connected light" },
  { title: "Circuit breaker installation / replacement", price: "₱500 per install/ replacement", desc: "Inclusions:\nRemoval of old breaker (if applicable), Installation of new breaker, Proper wiring connection, Functionality and safety test" },
  { title: "Ceiling fan installation", price: "₱500 per install", desc: "Inclusions:\nMount fan on ceiling, Proper electrical connection, Balance and functionality test" },
];
