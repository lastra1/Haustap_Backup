import { useLocalSearchParams, useRouter } from "expo-router";
import React, { useMemo } from "react";
import { StyleSheet, Text, TouchableOpacity, View } from "react-native";
//import { useAuth } from "../context/AuthContext";
import { getUserRole } from "../../src/config/apiConfig";
import { applianceRepairCategories } from "./data/applianceRepair";
import { acCleaningCategories, acDeepCleaningCategories, homeCleaningCategories } from "./data/cleaning";
import { electricalCategories } from "./data/electrical";
import { gardeningCategories } from "./data/gardening";
import { hairCategories } from "./data/hair";
import { handymanCategories } from "./data/handyman";
import { makeupCategories } from "./data/makeup";
import { nailCategories } from "./data/nails";
import { pestControlCategories } from "./data/pestControl";
import { pestControlOutdoorCategories } from "./data/pestControlOutdoor";
import { plumbingCategories } from "./data/plumbing";
import { wellnessCategories } from "./data/wellness";

export default function BookingSummary() {
  const router = useRouter();
  const params = useLocalSearchParams() as Record<string, any>;
  //const auth = useAuth();

  const categoryTitle = params.categoryTitle as string | undefined;
  const categoryPrice = params.categoryPrice as string | undefined;
  const categoryDesc = params.categoryDesc as string | undefined;
  const date = params.date as string | undefined;
  const time = params.time as string | undefined;
  const mainCategory = params.mainCategory as string | undefined;
  const subCategory = params.subCategory as string | undefined;
  const service = params.service as string | undefined;
  const selectedItemsRaw = params.selectedItems as string | undefined;
  const voucher = params.voucher as string | undefined;

  const serviceToMain: Record<string, string> = {
    Plumbing: "Indoor Services",
    Electrical: "Indoor Services",
    "Appliance Repair": "Indoor Services",
    "Pest Control": "Indoor Services",
    Makeup: "Beauty Services",
    Lashes: "Beauty Services",
    Hair: "Beauty Services",
    Nails: "Beauty Services",
    "Home Cleaning": "Cleaning Services",
    "Garden Landscaping": "Outdoor Services",
    Massage: "Wellness Services",
    Mobile: "Tech & Gadget Services",
  };

  const effectiveMainCategory = mainCategory ?? (service ? serviceToMain[service] ?? "Main Category" : "Main Category");
  const effectiveSubCategory = subCategory ?? service ?? "";

  const priceMap = useMemo(() => {
    const lists = [
      ...nailCategories,
      ...hairCategories,
      ...makeupCategories,
      ...handymanCategories,
      ...plumbingCategories,
      ...pestControlCategories,
      ...pestControlOutdoorCategories,
      ...gardeningCategories,
      ...wellnessCategories,
      ...applianceRepairCategories,
      ...electricalCategories,
      ...homeCleaningCategories,
      ...acCleaningCategories,
      ...acDeepCleaningCategories,
    ];
    const map: Record<string, string> = {};
    lists.forEach((it: any) => {
      if (it && it.title && it.price) map[it.title] = it.price;
    });
    return map;
  }, []);

  const parsedSelected = useMemo(() => {
    if (!selectedItemsRaw) return [] as string[];
    try {
      const p = JSON.parse(String(selectedItemsRaw));
      if (Array.isArray(p)) return p.map(String);
      if (typeof p === "string") return [p];
      return [];
    } catch (e) {
      return [String(selectedItemsRaw)];
    }
  }, [selectedItemsRaw]);

  const parsePriceNumber = (priceStr?: string) => {
    if (!priceStr) return NaN;
    const cleaned = String(priceStr).replace(/[^0-9.]/g, "");
    const v = parseFloat(cleaned);
    return Number.isFinite(v) ? v : NaN;
  };

  const subtotal = useMemo(() => {
    if (parsedSelected.length) {
      let sum = 0;
      let any = false;
      parsedSelected.forEach((t) => {
        const p = priceMap[t];
        const n = parsePriceNumber(p);
        if (!Number.isNaN(n)) {
          sum += n;
          any = true;
        }
      });
      if (any) return sum;
    }
    const cp = parsePriceNumber(categoryPrice);
    if (!Number.isNaN(cp)) return cp;
    return 0;
  }, [parsedSelected, priceMap, categoryPrice]);

  const transpoFee = 100;
  const voucherValue = parsePriceNumber(voucher) || 0;
  const total = Math.max(0, subtotal + transpoFee - voucherValue);

  const handleConfirm = () => {
    // If guest (not authenticated), send them to signup before they can continue booking
    (async () => {
      const role = await getUserRole();
      if (!role) {
        // pass the full intended booking URL as `redirect` so signup can return here
        const redirect = encodeURIComponent(
          `/client-side/booking-process/booking-location?categoryTitle=${encodeURIComponent(categoryTitle ?? "")}` +
          `&categoryPrice=${encodeURIComponent(categoryPrice ?? "")}` +
          `&categoryDesc=${encodeURIComponent(categoryDesc ?? "")}` +
          `&mainCategory=${encodeURIComponent(mainCategory ?? "")}` +
          `&subCategory=${encodeURIComponent(subCategory ?? "")}` +
          (service ? `&service=${encodeURIComponent(service)}` : "") +
          (selectedItemsRaw ? `&selectedItems=${encodeURIComponent(String(selectedItemsRaw))}` : "")
        );
        router.push(`/signup?redirect=${redirect}` as any);
        return;
      }

      // otherwise continue booking flow
      const base =
        `/client-side/booking-process/booking-location?categoryTitle=${encodeURIComponent(categoryTitle ?? "")}` +
        `&categoryPrice=${encodeURIComponent(categoryPrice ?? "")}` +
        `&categoryDesc=${encodeURIComponent(categoryDesc ?? "")}` +
        `&mainCategory=${encodeURIComponent(mainCategory ?? "")}` +
        `&subCategory=${encodeURIComponent(subCategory ?? "")}` +
        (service ? `&service=${encodeURIComponent(service)}` : "") +
        (selectedItemsRaw ? `&selectedItems=${encodeURIComponent(String(selectedItemsRaw))}` : "");

      router.push(base as any);
    })();
  };

  return (
    <View style={styles.container}>
      <Text style={styles.header}>Booking Summary</Text>

      <View style={styles.summaryBox}>
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Service Details</Text>
          <View style={styles.divider} />

          <Text style={styles.label}>Service Category:</Text>
          <View style={styles.categoryHierarchyColumn}>
            <Text style={styles.mainCategory}>{effectiveMainCategory}</Text>
            <Text style={styles.subcategoryLine}>{effectiveSubCategory ? `${effectiveSubCategory} - ${categoryTitle ?? ""}` : categoryTitle ?? ""}</Text>
          </View>

          {categoryPrice ? (
            <>
              <Text style={styles.label}>Price:</Text>
              <Text style={styles.valuePrice}>{categoryPrice}</Text>
            </>
          ) : null}

          <Text style={styles.label}>Inclusions:</Text>
          <Text style={styles.desc}>{categoryDesc}</Text>
        </View>

        {parsedSelected.length > 0 ? (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Selected Services</Text>
            <View style={styles.divider} />
            {parsedSelected.map((t, i) => (
              <View key={i} style={{ flexDirection: "row", justifyContent: "space-between", marginBottom: 8 }}>
                <Text style={{ color: "#222", fontSize: 16 }}>{t}</Text>
                <Text style={{ color: "#00ADB5", fontSize: 16 }}>{priceMap[t] ?? "Price varies"}</Text>
              </View>
            ))}
          </View>
        ) : null}

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Payment Summary</Text>
          <View style={styles.divider} />
          <View style={styles.row}>
            <Text style={styles.label}>Sub Total</Text>
            <Text style={styles.valuePrice}>{`₱${subtotal.toFixed(2)}`}</Text>
          </View>
          <View style={styles.row}>
            <Text style={styles.label}>Transpo fee</Text>
            <Text style={styles.valuePrice}>{`₱${transpoFee.toFixed(2)}`}</Text>
          </View>
          {voucherValue > 0 ? (
            <View style={styles.row}>
              <Text style={styles.label}>Voucher</Text>
              <Text style={[styles.valuePrice, { color: "#D9534F" }]}>{`- ₱${voucherValue.toFixed(2)}`}</Text>
            </View>
          ) : null}
          <View style={[styles.row, { marginTop: 8 }]}> 
            <Text style={[styles.label, { fontSize: 18 }]}>TOTAL</Text>
            <Text style={[styles.valuePrice, { fontSize: 20 }]}>{`₱${total.toFixed(2)}`}</Text>
          </View>
        </View>

        {(date || time) ? (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Schedule Details</Text>
            <View style={styles.divider} />
            {date ? (
              <>
                <Text style={styles.label}>Service Date:</Text>
                <Text style={styles.value}>{date}</Text>
              </>
            ) : null}
            {time ? (
              <>
                <Text style={styles.label}>Service Time:</Text>
                <Text style={styles.value}>{time}</Text>
              </>
            ) : null}
          </View>
        ) : null}
      </View>

      <TouchableOpacity style={styles.nextButton} onPress={handleConfirm}>
        <Text style={styles.nextButtonText}>Confirm Booking</Text>
      </TouchableOpacity>

      <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
        <Text style={styles.backButtonText}>Go Back</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    backgroundColor: "#fff", 
    padding: 20 
  },
  categoryHierarchy: {
    marginTop: 8,
  },
  categoryPath: {
    fontSize: 14,
    color: "#666",
    marginRight: 4,
    lineHeight: 20,
  },
  selectedService: {
    fontSize: 16,
    color: "#00ADB5",
    fontWeight: "600",
    lineHeight: 22,
  },
  categoryHierarchyColumn: {
    marginTop: 8,
    flexDirection: "column",
  },
  mainCategory: {
    fontSize: 20,
    fontWeight: "700",
    color: "#222",
    marginBottom: 6,
  },
  subcategoryLine: {
    fontSize: 16,
    color: "#666",
  },
  header: { 
    fontSize: 22, 
    fontWeight: "bold", 
    marginBottom: 20, 
    color: "black" 
  },
  summaryBox: {
    backgroundColor: "#F5F5F5",
    padding: 20,
    borderRadius: 12,
    marginBottom: 40,
  },
  section: {
    marginBottom: 24,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: "700",
    color: "#00ADB5",
    marginBottom: 8,
  },
  divider: {
    height: 1,
    backgroundColor: "#E0E0E0",
    marginBottom: 16,
  },
  label: { 
    fontSize: 14, 
    fontWeight: "600", 
    color: "#666", 
    marginTop: 12 
  },
  value: { 
    fontSize: 16, 
    color: "black", 
    marginTop: 4 
  },
  valuePrice: {
    fontSize: 18,
    color: "#00ADB5",
    fontWeight: "700",
    marginTop: 4,
  },
  desc: { 
    fontSize: 14, 
    color: "#444", 
    marginTop: 8, 
    lineHeight: 20 
  },
  nextButton: {
    backgroundColor: "#00ADB5",
    paddingVertical: 14,
    borderRadius: 10,
    alignItems: "center",
    marginBottom: 10,
  },
  nextButtonText: { 
    color: "white", 
    fontSize: 18, 
    fontWeight: "bold" 
  },
  backButton: {
    borderColor: "#00ADB5",
    borderWidth: 1.5,
    paddingVertical: 12,
    borderRadius: 10,
    alignItems: "center",
  },
  backButtonText: { 
    color: "#00ADB5", 
    fontSize: 16, 
    fontWeight: "600" 
  },
  row: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
});
