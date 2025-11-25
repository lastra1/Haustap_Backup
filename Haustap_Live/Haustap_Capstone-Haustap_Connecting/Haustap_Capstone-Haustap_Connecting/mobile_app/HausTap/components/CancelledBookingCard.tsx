import { Ionicons } from "@expo/vector-icons";
import React, { useState } from "react";
import {
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";
import { Booking } from "../src/services/bookingStore";

interface CancelledBookingCardProps {
  booking: Booking;
}

export default function CancelledBookingCard({ booking }: CancelledBookingCardProps) {
  const [expanded, setExpanded] = useState(false);

  return (
    <View style={styles.card}>
      {/* Card Header */}
      <View style={styles.cardHeader}>
        <View style={{ flex: 1 }}>
          <Text style={styles.clientName}>
            <Text style={{ fontWeight: "700" }}>Client:</Text> {booking.clientName}
          </Text>
          <Text style={styles.serviceTitle}>Home Cleaning</Text>
          <Text style={styles.serviceSubtitle}>{booking.service}</Text>
        </View>
        <View style={{ alignItems: "flex-end", minWidth: 90 }}>
          {booking.bookingId && (
            <Text style={styles.bookingIdText}>{booking.bookingId}</Text>
          )}
          <TouchableOpacity
            onPress={() => setExpanded(!expanded)}
            style={styles.expandToggle}
            hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
          >
            <Ionicons
              name={expanded ? "chevron-up" : "chevron-down"}
              size={20}
              color="#000"
            />
          </TouchableOpacity>
        </View>
      </View>

      {/* Basic Info - Always Visible */}
      <View style={styles.separator} />
      <View style={styles.row}>
        <View style={styles.col}>
          <Text style={styles.label}>Date</Text>
          <Text style={styles.value}>{booking.dateTime}</Text>
        </View>
        <View style={styles.divider} />
        <View style={styles.col}>
          <Text style={styles.label}>Time</Text>
          <Text style={styles.value}>{booking.dateTime}</Text>
        </View>
      </View>

      <View style={styles.separator} />

      <View style={styles.section}>
        <Text style={styles.label}>Address</Text>
        <Text style={styles.value}>{booking.address}</Text>
      </View>

      {/* Expanded Content */}
      {expanded && (
        <>
          <View style={styles.separator} />

          {/* Selected Service */}
          <View style={styles.section}>
            <Text style={styles.sectionLabel}>Selected</Text>
            <Text style={[styles.value, { fontWeight: "700" }]}>
              Bungalow 80–150 sqm
            </Text>
            <Text style={[styles.value, { fontWeight: "700", marginTop: 6 }]}>
              Basic Cleaning – 1 Cleaner
            </Text>
            <Text style={styles.inclusionsLabel}>Inclusions:</Text>
            <Text style={styles.inclusionsText}>
              Living Room: mopping, dusting furniture, trash removal{"\n"}
              Bedroom: bed making, mopping, dusting, trash removal{"\n"}
              Hallways: mop & sweep, remove cobwebs{"\n"}
              Windows & Mirrors: quick wipe
            </Text>
          </View>

          <View style={styles.separator} />

          {/* Notes */}
          <View style={styles.section}>
            <Text style={styles.label}>Notes</Text>
            <TextInput
              style={styles.notesBox}
              placeholder="No notes"
              multiline
              editable={false}
              pointerEvents="none"
              value={booking.notes || ""}
            />
          </View>

          <View style={styles.separator} />

          {/* Voucher */}
          <View style={styles.voucherBox}>
            <Ionicons name="pricetag-outline" size={18} color="#00B0B9" />
            <Text style={styles.voucherText}> Welcome Voucher</Text>
          </View>

          <View style={styles.separator} />

          {/* Payment Breakdown */}
          <View style={styles.paymentSection}>
            <View style={styles.pricingRow}>
              <Text style={styles.pricingLabel}>Service Fee</Text>
              <Text style={styles.pricingValue}>₱{booking.price.toFixed(2)}</Text>
            </View>
            <View style={styles.pricingRow}>
              <Text style={styles.pricingLabel}>Sub Total</Text>
              <Text style={styles.pricingValue}>₱{booking.price.toFixed(2)}</Text>
            </View>
            <View style={styles.pricingRow}>
              <Text style={styles.pricingLabel}>Transpo Fee</Text>
              <Text style={styles.pricingValue}>₱100.00</Text>
            </View>
            <View style={styles.pricingRow}>
              <Text style={styles.pricingLabel}>Voucher Discount</Text>
              <Text style={styles.pricingValue}>-₱50.00</Text>
            </View>
          </View>

          <View style={styles.separator} />

          {/* TOTAL */}
          <View style={styles.totalRow}>
            <Text style={styles.totalLabel}>TOTAL</Text>
            <Text style={styles.totalAmount}>₱{(booking.price + 100 - 50).toFixed(2)}</Text>
          </View>

          <Text style={styles.disclaimerText}>
            All payment shall be collected directly by the service provider upon completion of service.
          </Text>

          <View style={styles.separator} />

          {/* CANCELLED Section */}
          <View style={styles.cancelledBox}>
            <View style={styles.cancelledHeader}>
              <Text style={styles.cancelledTitle}>CANCELLED</Text>
              <Text style={styles.cancelledStatus}>Approve</Text>
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
                value={booking.clientName}
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
        </>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: "#F5F5F5",
    borderRadius: 12,
    padding: 16,
    marginBottom: 20,
    shadowColor: "#000",
    shadowOpacity: 0.08,
    shadowOffset: { width: 0, height: 2 },
    shadowRadius: 3,
  },
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    marginBottom: 12,
  },
  clientName: {
    fontSize: 14,
    color: "#000",
    marginBottom: 4,
  },
  serviceTitle: {
    fontSize: 14,
    fontWeight: "600",
    color: "#000",
  },
  serviceSubtitle: {
    fontSize: 13,
    color: "#555",
  },
  bookingIdText: {
    fontSize: 12,
    fontWeight: "600",
    color: "#00B0B9",
    marginBottom: 4,
  },
  expandToggle: {
    padding: 4,
  },
  separator: {
    height: 1,
    backgroundColor: "#E0E0E0",
    marginVertical: 10,
  },
  row: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginVertical: 6,
  },
  col: {
    flex: 1,
  },
  divider: {
    width: 1,
    backgroundColor: "#E0E0E0",
    marginHorizontal: 8,
  },
  label: {
    fontSize: 13,
    color: "#666",
    marginBottom: 4,
  },
  value: {
    fontSize: 14,
    color: "#000",
  },
  section: {
    marginVertical: 8,
  },
  sectionLabel: {
    fontSize: 13,
    color: "#666",
    fontWeight: "600",
    marginBottom: 6,
  },
  inclusionsLabel: {
    fontSize: 13,
    color: "#666",
    marginTop: 8,
    marginBottom: 4,
  },
  inclusionsText: {
    fontSize: 13,
    color: "#555",
    lineHeight: 18,
  },
  notesBox: {
    borderWidth: 1,
    borderColor: "#DDD",
    borderRadius: 6,
    padding: 8,
    height: 50,
    marginTop: 4,
    backgroundColor: "#fff",
    color: "#666",
    fontSize: 13,
  },
  voucherBox: {
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#EEE",
    borderRadius: 8,
    padding: 10,
    backgroundColor: "#FAFAFA",
  },
  voucherText: {
    fontSize: 13,
    color: "#333",
  },
  paymentSection: {
    marginVertical: 8,
  },
  pricingRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 6,
  },
  pricingLabel: {
    fontSize: 13,
    color: "#666",
  },
  pricingValue: {
    fontSize: 13,
    color: "#000",
    fontWeight: "500",
  },
  totalRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginVertical: 8,
  },
  totalLabel: {
    fontSize: 15,
    fontWeight: "700",
    color: "#000",
  },
  totalAmount: {
    fontSize: 15,
    fontWeight: "700",
    color: "#000",
  },
  disclaimerText: {
    fontSize: 11,
    color: "#888",
    marginTop: 8,
    lineHeight: 14,
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
    marginBottom: 12,
  },
  cancelledTitle: {
    fontSize: 14,
    fontWeight: "700",
    color: "#E53935",
  },
  cancelledStatus: {
    fontSize: 13,
    color: "#00B0B9",
    fontWeight: "600",
  },
  reasonSection: {
    marginTop: 8,
  },
  reasonLabel: {
    fontSize: 13,
    color: "#666",
    marginTop: 6,
    marginBottom: 4,
    fontWeight: "500",
  },
  reasonInput: {
    borderWidth: 1,
    borderColor: "#DDD",
    borderRadius: 6,
    padding: 8,
    backgroundColor: "#fff",
    color: "#000",
    fontSize: 13,
  },
});
