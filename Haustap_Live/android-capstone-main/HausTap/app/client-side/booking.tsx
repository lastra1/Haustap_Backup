
import { Ionicons } from "@expo/vector-icons";
import { useLocalSearchParams, useRouter } from "expo-router";
import React, { useEffect, useState } from "react";
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";
import { applianceRepairCategories } from "./data/applianceRepair";
import { acCleaningCategories, acDeepCleaningCategories, homeCleaningCategories } from "./data/cleaning";
import { electricalCategories } from "./data/electrical";
import { handymanCategories } from "./data/handyman";
import { pestControlCategories } from "./data/pestControl";
import { plumbingCategories } from "./data/plumbing";
import { Category } from "./data/types";

export default function BookingScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const [selectedService, setSelectedService] = useState("Home Cleaning");
  // For Indoor Services we have subservices (Handyman, Plumbing, ...).
  const [selectedSubservice, setSelectedSubservice] = useState("Handyman");

  // keep selected category per service so choosing a category in one tab
  // doesn't affect the others. For Indoor Services we store a nested map.
  const [selectedCategoryMap, setSelectedCategoryMap] = useState<any>({
    "Home Cleaning": null,
    "AC Cleaning": null,
    "AC Deep Cleaning (Chemical Cleaning)": null,
    "Indoor Services": {
      Handyman: null,
      Plumbing: null,
      Electrical: null,
      "Appliance Repair": null,
      "Pest Control": null,
    },
  });
  // Multi-select state for categories when checkboxes are enabled
  const [selectedMulti, setSelectedMulti] = useState<string[]>([]);

  // If the booking screen is opened with query params (from client home),
  // preselect the service / subservice accordingly, but only on initial mount
  useEffect(() => {
    if (params && params.service) {
      const svc = (params.service as string) || "";
      if (svc) setSelectedService(svc as string);
    }
    if (params && params.sub) {
      const sub = (params.sub as string) || "";
      if (sub) setSelectedSubservice(sub as string);
    }
  }, []); // Empty dependency array so it only runs once on mount


  // Determine nav items and categories based on selectedService.
  const isIndoor = selectedService === "Indoor Services";
  // Always allow multi-select (checkbox) for all services
  const allowMultiSelect = true;
  const navItems = isIndoor
    ? ["Handyman", "Plumbing", "Electrical", "Appliance Repair", "Pest Control"]
    : [
        "Home Cleaning",
        "AC Cleaning",
        "AC Deep Cleaning (Chemical Cleaning)",
      ];

  // Select category list
  let categories: Category[] = [];
  if (isIndoor) {
    if (selectedSubservice === "Handyman") {
      categories = handymanCategories;
    } else if (selectedSubservice === "Plumbing") {
      categories = plumbingCategories;
    } else if (selectedSubservice === "Electrical") {
      categories = electricalCategories;
    } else if (selectedSubservice === "Pest Control") {
      categories = pestControlCategories;
    } else if (selectedSubservice === "Appliance Repair") {
      categories = applianceRepairCategories;
    } else {
      categories = [
        { title: `${selectedSubservice} - Service`, price: "Contact for price", desc: "Details will be added soon." },
      ];
    }
  } else {
    categories =
      selectedService === "Home Cleaning"
        ? homeCleaningCategories
        : selectedService === "AC Cleaning"
        ? acCleaningCategories
        : acDeepCleaningCategories;
  }

  return (
    <ScrollView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
  <Text style={styles.headerText}>{isIndoor ? 'Indoor Services' : 'Cleaning Services'}</Text>
      </View>

      {/* Scrollable Nav */}
      <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.navContainer}>
        {navItems.map((service, index) => (
          <View key={index} style={styles.navItem}>
            <TouchableOpacity
              onPress={() => {
                if (isIndoor) {
                  setSelectedSubservice(service);
                } else {
                  setSelectedService(service);
                }
              }}
            >
              <Text
                style={[
                  styles.navText,
                  (isIndoor ? selectedSubservice === service : selectedService === service) && styles.activeNavText,
                ]}
              >
                {service}
              </Text>
            </TouchableOpacity>
            {index !== navItems.length - 1 && <Text style={styles.navDivider}>|</Text>}
          </View>
        ))}
      </ScrollView>

      {/* Categories */}
      <View style={styles.categoriesContainer}>
        {categories.map((item, index) => (
          item.section === "header" ? (
            <View key={index} style={{ marginTop: 18, marginBottom: 6 }}>
              <Text style={{ fontSize: 18, fontWeight: "bold", color: "#222" }}>{item.title}</Text>
              {item.desc && (
                <Text style={{ fontSize: 14, color: "#444", marginBottom: 4 }}>{item.desc}</Text>
              )}
            </View>
          ) : (
            <TouchableOpacity
              key={index}
              style={[
                styles.categoryBox,
                ((allowMultiSelect && selectedMulti.includes(item.title)) || (!allowMultiSelect && (isIndoor
                  ? selectedCategoryMap[selectedService][selectedSubservice] === item.title
                  : selectedCategoryMap[selectedService] === item.title))) && styles.selectedBox,
              ]}
              onPress={() => {
                if (allowMultiSelect) {
                  // toggle selection
                  setSelectedMulti(prev => {
                    if (prev.includes(item.title)) return prev.filter(p => p !== item.title);
                    return [...prev, item.title];
                  });
                } else {
                  setSelectedCategoryMap((prev: any) => {
                    const copy = { ...prev };
                    if (isIndoor) {
                      copy[selectedService] = { ...copy[selectedService], [selectedSubservice]: item.title };
                    } else {
                      copy[selectedService] = item.title;
                    }
                    return copy;
                  });
                }
              }}
            >
              <View style={styles.categoryTextContainer}>
                <Text style={styles.categoryTitle}>{item.title}</Text>
                {'size' in item && item.size && (
                  <Text style={styles.categorySize}>{item.size}</Text>
                )}
                {'price' in item && item.price && (
                  <Text style={styles.categorySize}>{item.price}</Text>
                )}
                <Text style={styles.categoryDesc}>{item.desc}</Text>
              </View>
              <View style={styles.checkboxBox}>
                {selectedMulti.includes(item.title) && <View style={styles.checkboxInner} />}
              </View>
            </TouchableOpacity>
          )
        ))}
      </View>

      {/* Preview of selected services */}
      {selectedMulti.length > 0 && (
        <View style={{ backgroundColor: "#F5F5F5", padding: 16, borderRadius: 10, marginBottom: 16 }}>
          <Text style={{ fontSize: 18, fontWeight: "700", color: "#00ADB5", marginBottom: 8 }}>Selected Services</Text>
          <View style={{ height: 1, backgroundColor: "#E0E0E0", marginBottom: 12 }} />
          {selectedMulti.map((t, i) => (
            <View key={i} style={{ flexDirection: "row", justifyContent: "space-between", marginBottom: 8 }}>
              <Text style={{ color: "#222", fontSize: 16 }}>{t}</Text>
              <Text style={{ color: "#00ADB5", fontSize: 16 }}>{priceMap[t] ?? "Price varies"}</Text>
            </View>
          ))}
        </View>
      )}

      {/* Next Button */}
      <TouchableOpacity
        style={styles.nextButton}
        onPress={() => {
          const paramsObj: Record<string, string> = {
            selectedItems: JSON.stringify(selectedMulti),
          };
          if (isIndoor) {
            paramsObj.mainCategory = "Indoor Services";
            paramsObj.subCategory = selectedSubservice;
          } else {
            paramsObj.mainCategory = "Cleaning Services";
            paramsObj.subCategory = selectedService;
          }
          router.push({ pathname: "/client-side/booking-summary", params: paramsObj } as any);
        }}
      >
        <Text style={styles.nextButtonText}>Next</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

// Build a price map for inline preview
const priceMap: Record<string, string> = (() => {
  const lists = [
    ...handymanCategories,
    ...plumbingCategories,
    ...electricalCategories,
    ...pestControlCategories,
    ...applianceRepairCategories,
    ...homeCleaningCategories,
    ...acCleaningCategories,
    ...acDeepCleaningCategories,
  ];
  const map: Record<string, string> = {};
  lists.forEach((it: any) => {
    if (it && it.title && it.price) map[it.title] = it.price;
  });
  return map;
})();

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: "#fff", padding: 16 },
  header: { flexDirection: "row", alignItems: "center", marginBottom: 10 },
  headerText: { fontSize: 22, fontWeight: "bold", marginLeft: 8, color: "black" },

  navContainer: { flexDirection: "row", marginBottom: 20 },
  navItem: { flexDirection: "row", alignItems: "center" },
  navText: { fontSize: 16, color: "black", paddingHorizontal: 8, paddingVertical: 4 },
  activeNavText: {
    borderBottomWidth: 3,
    borderBottomColor: "cyan",
    fontWeight: "600",
  },
  navDivider: { color: "black", fontSize: 18, alignSelf: "center" },

  categoriesContainer: { marginBottom: 30 },
  categoryBox: {
    flexDirection: "row",
    backgroundColor: "#DEE1E0",
    borderRadius: 10,
    padding: 15,
    marginBottom: 15,
    alignItems: "center",
    justifyContent: "space-between",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 3,
    elevation: 3,
  },
  selectedBox: { borderWidth: 2, borderColor: "cyan" },
  categoryTextContainer: { flex: 1, marginRight: 10 },
  categoryTitle: { fontSize: 16, fontWeight: "bold", color: "black" },
  categorySize: { fontSize: 14, color: "black", marginBottom: 4 },
  categoryDesc: { fontSize: 14, color: "black", lineHeight: 18 },

  radioCircle: {
    height: 22,
    width: 22,
    borderRadius: 11,
    borderWidth: 2,
    borderColor: "black",
    alignItems: "center",
    justifyContent: "center",
  },
  radioInner: { height: 12, width: 12, borderRadius: 6, backgroundColor: "black" },

  checkboxBox: {
    height: 22,
    width: 22,
    borderRadius: 6,
    borderWidth: 2,
    borderColor: "#666",
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: '#fff'
  },
  checkboxInner: {
    height: 12,
    width: 12,
    backgroundColor: '#00ADB5',
    borderRadius: 3,
  },

  nextButton: {
    backgroundColor: "#3DC1C6",
    paddingVertical: 14,
    borderRadius: 10,
    alignItems: "center",
    marginBottom: 40,
  },
  nextButtonText: { color: "white", fontSize: 18, fontWeight: "bold" },
});
