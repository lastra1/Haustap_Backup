import { Category } from "./types";

export const nailCategories: Category[] = [
  // Basic Services
  { title: "BASIC SERVICES", section: "header", desc: "Standard manicure & pedicure services" },
  {
    title: "Manicure",
    price: "₱250",
    desc: "Inclusions:\nNail trimming and shaping according to client's preference (square, round, oval, etc.)\nCuticle cleaning and pushing\nNail buffing for smoothness\nApplication of base coat, polish, and top coat\nQuick hand massage with lotion or oil"
  },
  {
    title: "Pedicure",
    price: "₱300",
    desc: "Inclusions:\nWarm foot soak for relaxation and softening\nNail cutting and shaping\nCuticle cleaning and pushing\nGentle removal of dead skin or light calluses\nNail buffing for smooth finish\nApplication of base coat, polish, and top coat\nLight foot massage"
  },
  
  // Gel Services
  { title: "GEL SERVICES", section: "header", desc: "Gel manicure and pedicure options" },
  {
    title: "Gel Manicure",
    price: "₱700",
    desc: "Inclusions:\nNail shaping and cuticle cleaning\nBuffing for proper gel adhesion\nApplication of base gel coat, 2–3 layers of gel color, and top gel coat\nEach layer cured under UV/LED lamp for durability\nLong-lasting glossy finish (2–3 weeks wear)\nOptional: nail art or design (may have add-on cost)"
  },
  {
    title: "Gel Pedicure",
    price: "₱800",
    desc: "Inclusions:\nRelaxing foot soak\nNail trimming, shaping, and cuticle cleaning\nGentle buffing to prep nails\nApplication of gel polish with curing under UV/LED lamp\nLong-lasting, chip-resistant color for feet\nLight foot massage with lotion"
  },

  // Extensions
  { title: "EXTENSIONS", section: "header", desc: "Nail extension services (acrylic, gel, polygel)" },
  {
    title: "Nail Extensions (Acrylic or Gel)",
    price: "₱1,000",
    desc: "Inclusions:\nNatural nail prep: cleaning, buffing, and cuticle care\nApplication of nail tips or forms for extension\nAcrylic or gel overlay applied and shaped\nFiling and smoothing to achieve desired length and shape\nPolish or gel color application with top coat\nDurable, lengthened nails (ideal for styling or nail art)"
  },
  {
    title: "Polygel Extensions",
    price: "₱1,200",
    desc: "Inclusions:\nNatural nail preparation (cleaning and buffing)\nApplication of polygel using dual forms or sculpting method\nLightweight yet durable extension shaping\nFiling and finishing to client's desired style (square, coffin, stiletto, etc.)\nPolish or gel color application with glossy top coat\nStronger and more flexible than acrylic"
  },

  // Spa Treatments
  { title: "SPA TREATMENTS", section: "header", desc: "Hand & foot spa treatments and paraffin wax" },
  {
    title: "Hand Spa",
    price: "₱500",
    desc: "Inclusions:\nCleansing wash for hands\nExfoliating scrub to remove dead skin cell\nCuticle softening soak\nMoisturizing hand mask or cream application\nRelaxing hand massage with lotion/oil\nOptional nail buffing or light polish (basic)"
  },
  {
    title: "Foot Spa",
    price: "₱600",
    desc: "Inclusions:\nWarm foot soak with salts or essential oils\nExfoliating scrub for feet and legs\nCallus and dead skin removal (heels & soles)\nMoisturizing foot mask or lotion treatment\nRelaxing foot and lower leg massage\nOptional nail buffing or light polish (basic)"
  },
  {
    title: "Paraffin Wax (Hands or Feet)",
    price: "₱400",
    desc: "Inclusions:\nGentle cleansing of hands/feet\nApplication of hydrating cream or oil\nDipping in warm paraffin wax for deep moisture\nWax wrapping (gloves/socks) to lock in hydration\nAfter set time, wax removed to reveal smoother, softer skin\nLeaves skin deeply moisturized and rejuvenated"
  }
];