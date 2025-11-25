// Shared mock providers data used by booking screens
export type ServiceProvider = {
  id: string;
  name: string;
  rating: number;
  distance: number | string; // numeric (km) used in listing; string used in profile for display
  skills: string[];
  location: string;
  experience?: string;
};

export const serviceProvidersByCategory: { [category: string]: ServiceProvider[] } = {
  "Home Cleaning": [
    { id: "1", name: "Ana Santos", rating: 4.8, distance: 2.5, skills: ["Home Cleaning", "Deep Cleaning", "Move-in/Move-out Cleaning"], location: "City of San Pedro, Laguna, Philippines", experience: "5 years" },
    { id: "2", name: "Maria Lopez", rating: 4.5, distance: 2.6, skills: ["Home Cleaning", "Move-in/Move-out Cleaning", "Bathroom Deep Cleaning"], location: "San Pedro City, Laguna", experience: "3 years" },
    { id: "3", name: "Elena Reyes", rating: 4.9, distance: 3.2, skills: ["Home Cleaning", "Kitchen Deep Cleaning", "Organizing"], location: "San Pedro City, Laguna", experience: "6 years" },
    { id: "15", name: "Carla Mendoza", rating: 4.4, distance: 10.0, skills: ["Home Cleaning", "Deep Cleaning"], location: "Cabuyao, Laguna", experience: "3 years" },
  ],
  "Office Cleaning": [
    { id: "4", name: "Lisa Deleon", rating: 4.5, distance: 3.0, skills: ["Office Cleaning", "Commercial Cleaning", "Floor Maintenance"], location: "San Pedro City, Laguna", experience: "4 years" },
    { id: "5", name: "Miguel Santos", rating: 4.7, distance: 4.2, skills: ["Office Cleaning", "Window Cleaning", "Carpet Cleaning"], location: "San Pedro City, Laguna", experience: "5 years" },
  ],
  "Beauty Services": [
    { id: "6", name: "Sofia Garcia", rating: 4.9, distance: 2.1, skills: ["Hair Styling", "Hair Coloring", "Hair Treatment"], location: "San Pedro City, Laguna", experience: "8 years" },
    { id: "7", name: "Isabella Cruz", rating: 4.8, distance: 3.5, skills: ["Makeup", "Nail Care", "Lash Extensions"], location: "San Pedro City, Laguna", experience: "6 years" },
    { id: "16", name: "Renee Valdez", rating: 4.5, distance: 10.0, skills: ["Makeup", "Bridal Makeup"], location: "Santa Rosa, Laguna", experience: "4 years" },
  ],
  "AC Services": [
    { id: "8", name: "Marco Rivera", rating: 4.7, distance: 4.0, skills: ["AC Cleaning", "AC Repair", "AC Installation"], location: "San Pedro City, Laguna", experience: "7 years" },
    { id: "9", name: "Diego Torres", rating: 4.6, distance: 4.8, skills: ["AC Deep Cleaning", "AC Maintenance", "AC Troubleshooting"], location: "Biñan City, Laguna", experience: "5 years" },
    { id: "17", name: "Anton dela Cruz", rating: 4.3, distance: 10.0, skills: ["AC Cleaning", "AC Maintenance"], location: "Cabuyao, Laguna", experience: "4 years" },
  ],
  "Specialized Cleaning": [
    { id: "10", name: "John Rivera", rating: 4.7, distance: 4.5, skills: ["Window Cleaning", "Carpet Cleaning", "Upholstery Cleaning"], location: "San Pedro City, Laguna", experience: "4 years" },
    { id: "11", name: "Sarah Cruz", rating: 4.9, distance: 4.8, skills: ["Deep Cleaning", "Post-construction Cleaning", "Sanitization"], location: "Biñan City, Laguna", experience: "6 years" },
    { id: "18", name: "Victor Santos", rating: 4.2, distance: 10.0, skills: ["Carpet Cleaning", "Upholstery Cleaning"], location: "Santa Rosa, Laguna", experience: "3 years" },
  ],
  "Pest Control": [
    { id: "12", name: "Rafael Mendoza", rating: 4.8, distance: 3.7, skills: ["Termite Control", "Ant Control", "Cockroach Control"], location: "San Pedro City, Laguna", experience: "9 years" },
    { id: "13", name: "Carlos Bautista", rating: 4.7, distance: 4.9, skills: ["Rodent Control", "Mosquito Control", "General Pest Control"], location: "Biñan City, Laguna", experience: "7 years" },
    { id: "14", name: "Luis Gonzales", rating: 4.6, distance: 10.0, skills: ["General Pest Control", "Mosquito Control"], location: "Santa Rosa, Laguna", experience: "5 years" },
  ],
};

// Flat list (useful for profile lookups)
export const serviceProviders: ServiceProvider[] = Object.values(serviceProvidersByCategory).flat();

export default serviceProvidersByCategory;
