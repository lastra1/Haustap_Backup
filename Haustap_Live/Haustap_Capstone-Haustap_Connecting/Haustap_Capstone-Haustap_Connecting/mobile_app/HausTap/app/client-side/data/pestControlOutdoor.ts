import { Category } from "./types";

export const pestControlOutdoorCategories: Category[] = [
  // Mosquito Control Section
  { title: "MOSQUITO CONTROL", section: "header", desc: "Mosquito Control Services" },
  { title: "Mosquito Misting - Single Treatment", price: "₱2,950", desc: "Inclusions:\nInspection of breeding sites, Misting application to target areas, Coverage up to 200 sqm" },
  { title: "Mosquito Misting - Monthly Contract", price: "₱2,450/month", desc: "Inclusions:\nMonthly misting application, Regular inspection, Coverage up to 200 sqm" },
  
  // Garden Pest Control Section
  { title: "GARDEN PESTS", section: "header", desc: "Garden Pest Control Services" },
  { title: "Garden Pest Treatment - Small Area", price: "₱3,450", desc: "Inclusions:\nInspection of affected areas, Treatment application, Coverage up to 100 sqm" },
  { title: "Garden Pest Treatment - Medium Area", price: "₱4,950", desc: "Inclusions:\nInspection of affected areas, Treatment application, Coverage up to 200 sqm" },
  { title: "Garden Pest Treatment - Large Area", price: "₱6,450", desc: "Inclusions:\nInspection of affected areas, Treatment application, Coverage up to 300 sqm" },

  // Rodent Control Section
  { title: "RODENT CONTROL", section: "header", desc: "Outdoor Rodent Control Services" },
  { title: "Rodent Control - Basic Package", price: "₱3,950", desc: "Inclusions:\nInspection of entry points, Placement of bait stations, Initial treatment" },
  { title: "Rodent Control - Advanced Package", price: "₱5,950", desc: "Inclusions:\nComprehensive inspection, Multiple bait stations, Preventive measures, Follow-up visit" }
];
