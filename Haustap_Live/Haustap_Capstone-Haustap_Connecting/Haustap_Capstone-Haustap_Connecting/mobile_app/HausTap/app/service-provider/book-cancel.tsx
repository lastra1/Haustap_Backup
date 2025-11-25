import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
import React from "react";
import {
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from "react-native";


export default function CancelledScreen() {
  return (
    <ScrollView style={styles.container}>
      {/* Header */}
      <View style={styles.headerRow}>
        <Text style={styles.headerTitle}>Bookings</Text>
        <TouchableOpacity>
          <Ionicons name="filter-outline" size={20} color="#000" />
        </TouchableOpacity>
      </View>


      {/* Tabs */}
      <View style={styles.tabs}>
        {["Pending", "Ongoing", "Completed", "Cancelled", "Return"].map(
          (tab, i) => (
            <TouchableOpacity
              key={i}
              style={[
                styles.tabButton,
                tab === "Cancelled" && styles.tabActive,
              ]}
            >
              <Text
                style={[
                  styles.tabText,
                  tab === "Cancelled" && styles.tabTextActive,
                ]}
              >
                {tab}
              </Text>
            </TouchableOpacity>
          )
        )}
      </View>


      {/* Booking Card */}
      <View style={styles.card}>
        {/* Client Info */}
        <Text style={styles.clientName}>Client: Jenn Bornilla</Text>
        <Text style={styles.serviceType}>Home Cleaning</Text>
        <Text style={styles.subType}>Bungalow - Basic Cleaning</Text>


        {/* Date and Time */}
        <View style={styles.row}>
          <View style={styles.half}>
            <Text style={styles.label}>Date</Text>
            <Text style={styles.value}>May 21, 2025</Text>
          </View>
          <View style={styles.half}>
            <Text style={styles.label}>Time</Text>
            <Text style={styles.value}>8:00 AM</Text>
          </View>
        </View>


        {/* Address */}
        <View style={styles.section}>
          <Text style={styles.label}>Address</Text>
          <Text style={styles.value}>
            B1 L50 Mango st. Phase 1 Saint Joseph Village 10{"\n"}
            Barangay Langgam, City of San Pedro, Laguna 4023
          </Text>
        </View>


        {/* Selected Service */}
        <View style={styles.section}>
          <Text style={styles.label}>Selected:</Text>
          <Text style={styles.value}>
            <Text style={{ fontWeight: "600" }}>
              Bungalow 80–150 sqm{"\n"}Basic Cleaning – 1 Cleaner
            </Text>
            {"\n\n"}Inclusions:{"\n"}Living Room: mopping, dusting furniture,
            trash removal{"\n"}Bedroom: bed making, mopping, dusting, trash
            removal{"\n"}Hallways: mop & sweep, remove cobwebs, Windows &
            Mirrors: quick wipe
          </Text>
        </View>


        {/* Notes */}
        <View style={styles.section}>
          <Text style={styles.label}>Notes:</Text>
          <TextInput style={styles.notesBox} placeholder=" " />
        </View>


        {/* Voucher */}
        <View style={styles.voucherBox}>
          <Ionicons name="pricetag-outline" size={18} color="#000" />
          <Text style={styles.voucherText}>No voucher added</Text>
        </View>


        {/* Total */}
        <View style={styles.priceSection}>
          <View style={styles.priceRow}>
            <Text style={styles.subLabel}>Sub Total</Text>
            <Text style={styles.subValue}>₱1,000.00</Text>
          </View>
          <View style={styles.priceRow}>
            <Text style={styles.subLabel}>Voucher Discount</Text>
            <Text style={styles.subValue}>₱0</Text>
          </View>
          <View style={styles.divider} />
          <View style={styles.totalRow}>
            <Text style={styles.totalLabel}>TOTAL</Text>
            <Text style={styles.totalValue}>P1,000.00</Text>
          </View>
          <Text style={styles.noteText}>
            All payment shall be collected directly by the service provider upon
            completion of service.
          </Text>
        </View>


        {/* 🔵 Blue Divider Line */}
        <View style={styles.blueDivider} />


        {/* Cancelled Info */}
        <View style={styles.cancelledBox}>
          <View style={styles.cancelledHeader}>
            <Text style={styles.cancelledTitle}>CANCELLED</Text>
            <Text style={styles.approveTextStatic}>Approve</Text>
          </View>


          <View style={styles.reasonSection}>
            <Text style={styles.reasonLabel}>Reason for Cancellation</Text>
            <TextInput
              style={styles.reasonInput}
              value="No need service"
              editable={false}
            />


            <Text style={styles.reasonLabel}>Requested by</Text>
            <TextInput
              style={styles.reasonInput}
              value="Jenn Bornilla"
              editable={false}
            />


            <Text style={styles.reasonLabel}>Submission Date</Text>
            <TextInput
              style={styles.reasonInput}
              value="May 04, 2025"
              editable={false}
            />


            <Text style={styles.reasonLabel}>Submission Time</Text>
            <TextInput
              style={styles.reasonInput}
              value="12:00 AM"
              editable={false}
            />
          </View>
        </View>


        {/* Buttons */}
        {/* Earnings card (placed below booking card, above action buttons) */}
        <View style={styles.earningsCard}>
          <View style={styles.earningsHeader}>
            <Text style={styles.earningsTitle}>Earnings</Text>
            <TouchableOpacity>
              <Text style={styles.earningsDetails}>Details</Text>
            </TouchableOpacity>
          </View>

          <View style={styles.earningsBody}>
            <View style={styles.earnCol}>
              <Text style={styles.earnLabel}>Total Earnings</Text>
              <Text style={styles.earnAmount}>₱0</Text>

              <Text style={[styles.earnLabel, { marginTop: 8 }]}>Total Bookings</Text>
              <Text style={styles.earnAmountSmall}>0</Text>

              <Text style={[styles.earnLabel, { marginTop: 8 }]}>Cancelled Bookings</Text>
              <Text style={styles.earnAmount}>₱0</Text>
            </View>

            <View style={styles.earnCol}>
              <Text style={styles.earnLabel}>Earnings in November</Text>
              <Text style={styles.earnAmount}>₱0</Text>

              <Text style={[styles.earnLabel, { marginTop: 8 }]}>Completed Bookings</Text>
              <Text style={styles.earnAmountSmall}>0</Text>
            </View>
          </View>
        </View>

        <View style={styles.buttonRow}>
          <TouchableOpacity style={styles.rebookBtn}>
            <Text style={styles.rebookText}>Rebook</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={styles.detailsBtn}
            onPress={() => router.push('/service-provider/cancellation-details')}
          >
            <Text style={styles.detailsText}>View Cancellation Details</Text>
          </TouchableOpacity>
        </View>
      </View>
    </ScrollView>
  );
}


const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#F9F9F9",
    paddingHorizontal: 20,
    paddingTop: 50,
  },
  headerRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  headerTitle: { fontSize: 22, fontWeight: "700" },
  tabs: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 20,
    marginBottom: 15,
  },
  tabButton: {
    paddingVertical: 6,
    paddingHorizontal: 10,
    borderBottomWidth: 2,
    borderColor: "transparent",
  },
  tabActive: { borderColor: "#00B0B9" },
  tabText: { fontSize: 13, color: "#666" },
  tabTextActive: { color: "#00B0B9", fontWeight: "600" },
  card: {
    backgroundColor: "#F5F5F5",
    borderRadius: 12,
    padding: 16,
    shadowColor: "#000",
    shadowOpacity: 0.1,
    shadowOffset: { width: 0, height: 1 },
    shadowRadius: 2,
    marginBottom: 25,
  },
  clientName: { fontWeight: "700", fontSize: 14 },
  serviceType: { fontSize: 14, color: "#000" },
  subType: { fontSize: 13, color: "#555" },
  row: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginVertical: 6,
  },
  half: { width: "48%" },
  label: { fontSize: 13, color: "#666", marginBottom: 2 },
  value: { fontSize: 14, color: "#000" },
  section: { marginVertical: 8 },
  notesBox: {
    borderWidth: 1,
    borderColor: "#DDD",
    borderRadius: 6,
    height: 50,
    marginTop: 4,
    padding: 8,
  },
  voucherBox: {
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#DDD",
    borderRadius: 8,
    padding: 10,
    marginVertical: 10,
  },
  voucherText: { fontSize: 13, color: "#333", marginLeft: 6 },
  priceSection: { marginTop: 10 },
  priceRow: { flexDirection: "row", justifyContent: "space-between" },
  subLabel: { fontSize: 13, color: "#666" },
  subValue: { fontSize: 13, color: "#000" },
  divider: {
    borderBottomWidth: 1,
    borderColor: "#E0E0E0",
    marginVertical: 10,
  },
  totalRow: { flexDirection: "row", justifyContent: "space-between" },
  totalLabel: { fontWeight: "700", fontSize: 15 },
  totalValue: { fontWeight: "700", fontSize: 15 },
  noteText: { fontSize: 11, color: "#777", marginTop: 6 },


  // 🔵 Blue divider before cancelled section
  blueDivider: {
    height: 2,
    backgroundColor: "#00B0B9",
    marginVertical: 12,
    borderRadius: 2,
  },


  cancelledBox: {
    backgroundColor: "#FAFAFA",
    borderRadius: 10,
    padding: 12,
    borderWidth: 1,
    borderColor: "#DDD",
  },
  cancelledHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  cancelledTitle: {
    fontWeight: "700",
    fontSize: 14,
    color: "#E53935",
  },
  approveTextStatic: {
    fontSize: 13,
    color: "#00B0B9",
    fontWeight: "600",
  },
  reasonSection: { marginTop: 10 },
  reasonLabel: { fontSize: 13, color: "#666", marginTop: 6 },
  reasonInput: {
    borderWidth: 1,
    borderColor: "#DDD",
    borderRadius: 6,
    padding: 8,
    color: "#000",
    fontSize: 13,
    backgroundColor: "#F9F9F9",
  },
  buttonRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 16,
  },
  rebookBtn: {
    backgroundColor: "#00B0B9",
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 25,
  },
  rebookText: { color: "#fff", fontWeight: "600" },
  detailsBtn: {
    borderWidth: 1,
    borderColor: "#999",
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 20,
  },
  detailsText: { color: "#333", fontWeight: "600", fontSize: 13 },
  bottomNav: { display: "none" },
  navItem: { display: "none" },
  navText: { display: "none" },
  /* Earnings card */
  earningsCard: {
    backgroundColor: "#fff",
    borderWidth: 1.5,
    borderColor: "#00B0B9",
    borderRadius: 12,
    padding: 12,
    marginVertical: 16,
    shadowColor: "#000",
    shadowOpacity: 0.06,
    shadowOffset: { width: 0, height: 2 },
    shadowRadius: 4,
    elevation: 2,
  },
  earningsHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 8,
  },
  earningsTitle: { fontSize: 16, fontWeight: "700", color: "#222" },
  earningsDetails: { color: "#00B0B9", fontWeight: "600" },
  earningsBody: { flexDirection: "row", justifyContent: "space-between" },
  earnCol: { width: "48%" },
  earnLabel: { color: "#666", fontSize: 12 },
  earnAmount: { fontSize: 14, fontWeight: "700", color: "#222", marginTop: 4 },
  earnAmountSmall: { fontSize: 13, color: "#222", marginTop: 4 },
});
