import { Ionicons } from "@expo/vector-icons";
import { useLocalSearchParams, useRouter } from "expo-router";
import React, { useState } from "react";
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";

export default function BookingVoucher() {
  const router = useRouter();
  const params = useLocalSearchParams();
  // vouchers available (code, value, short desc)
  const vouchers = [
    { code: "Welcome Voucher", value: 50, title: "₱50 OFF for First-Time Users", note: "New here? Book your first service today and enjoy ₱50 voucher as our welcome gift" },
    { code: "Loyalty Bonus", value: 50, title: "₱50 Voucher after 10 Completed Bookings", note: "Loyal Customer deserve the best, after 10 completed bookings. Enjoy a ₱50 voucher as a thank you for staying with Haustap" },
    { code: "Referral Bonus", value: 10, title: "Earn ₱10 Voucher for every Successful Referral", note: "Share HAUSTAP with friends! Once your friend complete first booking, you earn ₱10 voucher" },
  ];

  const [selected, setSelected] = useState<string | null>(null);

  const apply = () => {
    // push back to booking-overview with voucher params and keep existing booking params
    router.push({
      pathname: "/client-side/booking-process/booking-overview",
      params: {
        ...params,
        voucherCode: selected,
        voucherValue: selected ? vouchers.find((v) => v.code === selected)?.value : 0,
      },
    } as any);
  };

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Vouchers</Text>
      </View>

      <Text style={styles.subtitle}>Choose a voucher to apply</Text>

      <View style={{ paddingHorizontal: 16 }}>
        {vouchers.map((v) => (
          <TouchableOpacity
            key={v.code}
            style={[styles.card, selected === v.code && styles.cardSelected]}
            onPress={() => setSelected(v.code)}
          >
            <View style={styles.cardTop}>
              <Text style={styles.cardTitle}>{v.title}</Text>
              <Text style={styles.cardAmount}>₱{v.value}</Text>
            </View>
            <Text style={styles.cardNote}>{v.note}</Text>
          </TouchableOpacity>
        ))}
      </View>

      <TouchableOpacity style={styles.applyBtn} onPress={apply} disabled={!selected}>
        <Text style={styles.applyText}>Apply</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: "#fff", paddingTop: 40 },
  header: { flexDirection: "row", alignItems: "center", paddingHorizontal: 16, marginBottom: 8 },
  headerTitle: { fontSize: 20, fontWeight: "600", marginLeft: 10 },
  subtitle: { paddingHorizontal: 16, color: "#666", marginBottom: 12 },
  card: { backgroundColor: "#F5F5F5", borderRadius: 8, padding: 12, marginBottom: 12 },
  cardSelected: { borderWidth: 2, borderColor: "#00ADB5" },
  cardTop: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", marginBottom: 8 },
  cardTitle: { fontWeight: "700", fontSize: 14 },
  cardAmount: { fontWeight: "700", color: "#00ADB5" },
  cardNote: { color: "#444", fontSize: 12 },
  applyBtn: { backgroundColor: "#00ADB5", paddingVertical: 14, alignItems: "center", margin: 16, borderRadius: 8 },
  applyText: { color: "#fff", fontWeight: "700" },
});
