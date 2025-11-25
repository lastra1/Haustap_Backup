import { Category } from "./types";

export const wellnessCategories: Category[] = [
  // Massage
  { title: "Full Body Massage (Swedish)", price: "₱800 / 60 mins", desc: "Relaxing massage to relieve tension and improve circulation." },
  { title: "Full Body Massage (Deep Tissue)", price: "₱1,000 / 60 mins", desc: "Firm pressure to target deep muscle knots and chronic tension." },
  { title: "Aromatherapy Massage", price: "₱1,200 / 60 mins", desc: "Relaxing massage with essential oils for stress relief" },
  { title: "Reflexology (Foot Massage)", price: "₱600 / 45 mins", desc: "Focused massage on pressure points in the feet." },
  { title: "Scalp & Head Massage", price: "₱500 / 30 mins", desc: "Relieves headaches and promotes blood circulation." },
  { title: "Back & Shoulder Massage", price: "₱500 / 30 mins", desc: "Quick relief for upper body tension." },

  // Packages
  { title: "Total Relaxation Package", price: "₱1,200", desc: "Swedish Full Body Massage (60 mins) + Reflexology (45 mins)" },
  { title: "Stress Relief Duo", price: "₱900", desc: "Back & Shoulder Massage (30 mins) + Scalp & Head Massage (30 mins)" },

  // Spa
  { title: "Facial Treatment", price: "From ₱1,500", desc: "Customized facial treatment based on skin type" },
  { title: "Body Scrub", price: "₱1,200", desc: "Full body exfoliation and moisturizing treatment" },
  { title: "Hot Stone Therapy", price: "₱2,000", desc: "Relaxing hot stone massage with aromatherapy" },
  { title: "Body Wrap", price: "₱1,800", desc: "Detoxifying body wrap treatment" },
  { title: "Aromatherapy Package", price: "₱2,500", desc: "Complete aromatherapy session with massage" },

  // Therapy
  { title: "Initial Assessment", price: "₱1,000", desc: "Complete physical assessment and treatment plan development" },
  { title: "General Physical Therapy", price: "₱800/session", desc: "Standard physical therapy treatment for general conditions" },
  { title: "Sports Injury Therapy", price: "₱1,200/session", desc: "Specialized treatment for sports-related injuries" },
  { title: "Post-Surgery Rehabilitation", price: "₱1,500/session", desc: "Rehabilitation therapy for post-surgery recovery" },
  { title: "Exercise Program", price: "₱2,000", desc: "Customized exercise program development with follow-up sessions" },
];
