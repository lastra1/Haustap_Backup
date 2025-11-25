import { Ionicons } from "@expo/vector-icons";
import React, { useState } from "react";
import {
    Modal,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";
import { Booking } from "../src/services/bookingStore";

interface ReturnBookingCardProps {
  booking: Booking;
}

export default function ReturnBookingCard({ booking }: ReturnBookingCardProps) {
  const [expanded, setExpanded] = useState(false);
  const [returnExpanded, setReturnExpanded] = useState(false);
  const [penaltiesModalVisible, setPenaltiesModalVisible] = useState(false);
  const [termsChecked, setTermsChecked] = useState(false);

  return (
    <View style={styles.card}>
      {/* Card Header - Reuse CancelledBookingCard layout */}
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

          {/* Service Provider Section */}
          <View style={styles.serviceProviderSection}>
            <Text style={styles.serviceProviderLabel}>Service provider</Text>
            
            <View style={styles.providerInfoRow}>
              <View style={styles.providerContent}>
                <View style={styles.providerNameRow}>
                  <Ionicons name="person-circle" size={32} color="#00B0B9" />
                  <Text style={styles.providerName}>Ana Santos</Text>
                  <Ionicons name="chatbubble-outline" size={18} color="#00B0B9" style={{ marginLeft: -4 }} />
                </View>
                <Text style={styles.providerRating}>★ Rate: 4.5 / 5.0</Text>
              </View>
            </View>
          </View>

          <View style={styles.separator} />

          {/* RETURN Section - Single Box with Dropdown */}
          <View style={styles.returnSection}>
            <View style={styles.returnContent}>
              <Text style={styles.returnLabel}>RETURN</Text>
              <Text style={styles.returnDivider}>|</Text>
              <Text style={styles.returnStatus}>Pending Review</Text>
            </View>
            <TouchableOpacity 
              style={styles.dropdownToggle}
              onPress={() => setReturnExpanded(!returnExpanded)}
            >
              <Ionicons 
                name={returnExpanded ? "chevron-up" : "chevron-down"} 
                size={20} 
                color="#00B0B9" 
              />
            </TouchableOpacity>
          </View>

          {/* Return Details - Expandable Section */}
          {returnExpanded && (
            <View style={styles.returnDetailsSection}>
              <View style={styles.separator} />

              {/* Return Reason */}
              <View style={styles.returnDetailItem}>
                <Text style={styles.returnDetailLabel}>Reason for Return:</Text>
                <Text style={styles.returnDetailValue}>Unsatisfactory Service Quality</Text>
              </View>

              {/* Additional Notes */}
              <View style={styles.returnDetailItem}>
                <Text style={styles.returnDetailLabel}>Additional Notes:</Text>
                <Text style={styles.returnDetailValue}>The quality of the service did not meet the expected standards or description.</Text>
              </View>

              {/* Request Date */}
              <View style={styles.returnDetailItem}>
                <Text style={styles.returnDetailLabel}>Request Return Date:</Text>
                <Text style={styles.returnDetailValue}>May 04, 2026</Text>
              </View>

              <View style={styles.separator} />

              {/* Return Fee Breakdown */}
              <View style={styles.returnFeeSection}>
                <View style={styles.feeRow}>
                  <Text style={styles.feeLabel}>Return Fee</Text>
                  <Text style={styles.feeValue}>₱</Text>
                </View>
                <View style={styles.feeRow}>
                  <Text style={styles.feeLabel}>Sub Total</Text>
                  <Text style={styles.feeValue}>₱</Text>
                </View>
                <View style={styles.feeRow}>
                  <Text style={styles.feeLabel}>Transpo Fee</Text>
                  <Text style={styles.feeValue}>+₱100</Text>
                </View>
                <View style={styles.feeRow}>
                  <Text style={styles.feeLabel}>Voucher Discount</Text>
                  <Text style={styles.feeValue}>₱0</Text>
                </View>
              </View>

              <View style={styles.separator} />

              {/* Total */}
              <View style={styles.returnTotalRow}>
                <Text style={styles.returnTotalLabel}>TOTAL</Text>
                <Text style={styles.returnTotalValue}>₱</Text>
              </View>

              <Text style={styles.returnPaymentNote}>
                Full payment will be collected directly by the service provider upon completion of the service.
              </Text>

              <View style={styles.separator} />

              {/* Confirm Button */}
              <TouchableOpacity style={styles.confirmButton}>
                <Text style={styles.confirmButtonText}>Confirm</Text>
              </TouchableOpacity>

              {/* Terms Checkbox */}
              <TouchableOpacity 
                style={styles.termsContainer}
                onPress={() => setTermsChecked(!termsChecked)}
              >
                <View style={[styles.checkbox, termsChecked && styles.checkboxChecked]}>
                  {termsChecked && <Text style={styles.checkmarkText}>✓</Text>}
                </View>
                <Text style={styles.termsText}>
                  The service provider must complete the approved return within 7 days from the admin's approval date. Failure to do so will cancel the request and may result in <TouchableOpacity onPress={() => setPenaltiesModalVisible(true)}>
                    <Text style={styles.penaltyText}>penalties.</Text>
                  </TouchableOpacity>
                </Text>
              </TouchableOpacity>
            </View>
          )}
        </>
      )}

      {/* Penalties Modal */}
      <Modal
        visible={penaltiesModalVisible}
        transparent={true}
        animationType="fade"
        onRequestClose={() => setPenaltiesModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Return Policy and Penalties</Text>
              <TouchableOpacity onPress={() => setPenaltiesModalVisible(false)}>
                <Ionicons name="close" size={24} color="#000" />
              </TouchableOpacity>
            </View>

            <ScrollView style={styles.modalBody}>
              <Text style={styles.modalText}>
                Once a return request has been approved by the admin, the service provider must complete the re-service within seven (7) days from the approval date.
              </Text>

              <Text style={styles.modalText}>
                Failure to comply within the allowed period will result in the following penalties, depending on the severity or frequency of non-compliance:
              </Text>

              <Text style={styles.penaltyHeading}>Possible Penalties:</Text>

              <View style={styles.penaltyItem}>
                <Text style={styles.penaltyNumber}>1. First Violation – Warning Notice</Text>
                <Text style={styles.penaltyDescription}>
                  A formal written warning will be sent to the provider as a reminder to comply with the return policy.
                </Text>
              </View>

              <View style={styles.penaltyItem}>
                <Text style={styles.penaltyNumber}>2. Second Violation – Temporary Account Suspension</Text>
                <Text style={styles.penaltyDescription}>
                  Temporary account suspension lasting 3 to 7 days, preventing them from accepting new bookings during this period.
                </Text>
              </View>

              <View style={styles.penaltyItem}>
                <Text style={styles.penaltyNumber}>3. Severe or Repeated Violations – Account Termination</Text>
                <Text style={styles.penaltyDescription}>
                  Continued failure to complete approved return requests within the given timeframe may lead to permanent account suspension or removal from the platform.
                </Text>
              </View>
            </ScrollView>

            <TouchableOpacity
              style={styles.modalCloseButton}
              onPress={() => setPenaltiesModalVisible(false)}
            >
              <Text style={styles.modalCloseButtonText}>Close</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
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
  serviceProviderSection: {
    marginVertical: 8,
  },
  serviceProviderLabel: {
    fontSize: 11,
    color: "#999",
    fontWeight: "500",
    marginBottom: 8,
  },
  providerInfoRow: {
    flexDirection: "row",
    alignItems: "flex-start",
  },
  providerContent: {
    flex: 1,
  },
  providerNameRow: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 6,
    gap: 8,
  },
  providerName: {
    fontSize: 13,
    fontWeight: "600",
    color: "#000",
  },
  providerRating: {
    fontSize: 12,
    color: "#666",
    marginLeft: 40,
  },
  returnSection: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    backgroundColor: "#fff",
    borderWidth: 2,
    borderColor: "#3DC1C6",
    borderRadius: 6,
    paddingHorizontal: 12,
    paddingVertical: 10,
  },
  returnContent: {
    flexDirection: "row",
    alignItems: "center",
    gap: 10,
    flex: 1,
  },
  returnLabel: {
    fontSize: 12,
    fontWeight: "700",
    color: "#E65100",
  },
  returnDivider: {
    fontSize: 14,
    color: "#CCC",
    marginHorizontal: 4,
  },
  returnStatus: {
    fontSize: 13,
    color: "#00B0B9",
    fontWeight: "600",
  },
  dropdownToggle: {
    padding: 4,
  },
  returnDetailsSection: {
    backgroundColor: "#fff",
    borderLeftWidth: 2,
    borderRightWidth: 2,
    borderBottomWidth: 2,
    borderColor: "#3DC1C6",
    borderBottomLeftRadius: 6,
    borderBottomRightRadius: 6,
    paddingHorizontal: 12,
    paddingVertical: 12,
    marginBottom: 16,
  },
  returnDetailItem: {
    marginVertical: 8,
  },
  returnDetailLabel: {
    fontSize: 12,
    fontWeight: "600",
    color: "#333",
    marginBottom: 4,
  },
  returnDetailValue: {
    fontSize: 12,
    color: "#666",
    lineHeight: 18,
  },
  returnFeeSection: {
    marginVertical: 8,
  },
  feeRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginVertical: 4,
  },
  feeLabel: {
    fontSize: 12,
    color: "#333",
  },
  feeValue: {
    fontSize: 12,
    color: "#000",
    fontWeight: "500",
  },
  returnTotalRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginVertical: 8,
  },
  returnTotalLabel: {
    fontSize: 14,
    fontWeight: "700",
    color: "#000",
  },
  returnTotalValue: {
    fontSize: 14,
    fontWeight: "700",
    color: "#000",
  },
  returnPaymentNote: {
    fontSize: 10,
    color: "#888",
    marginVertical: 8,
    fontStyle: "italic",
  },
  confirmButton: {
    backgroundColor: "#3DC1C6",
    paddingVertical: 12,
    paddingHorizontal: 24,
    borderRadius: 6,
    alignItems: "center",
    marginVertical: 12,
  },
  confirmButtonText: {
    color: "#fff",
    fontWeight: "700",
    fontSize: 14,
  },
  termsContainer: {
    flexDirection: "row",
    alignItems: "flex-start",
    gap: 8,
    marginTop: 12,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: "#E0E0E0",
  },
  checkbox: {
    width: 20,
    height: 20,
    backgroundColor: "#000",
    borderRadius: 2,
    alignItems: "center",
    justifyContent: "center",
    marginTop: 2,
  },
  checkboxChecked: {
    backgroundColor: "#3DC1C6",
  },
  checkmarkText: {
    color: "#fff",
    fontSize: 14,
    fontWeight: "700",
  },
  termsText: {
    fontSize: 11,
    color: "#333",
    lineHeight: 16,
    flex: 1,
  },
  penaltyText: {
    color: "#E53935",
    fontWeight: "600",
  },
  dropdownButtonText: {
    color: "#00B0B9",
    fontWeight: "600",
    fontSize: 13,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(0, 0, 0, 0.5)",
    justifyContent: "center",
    alignItems: "center",
  },
  modalContent: {
    backgroundColor: "#fff",
    borderRadius: 12,
    width: "90%",
    maxHeight: "85%",
    overflow: "hidden",
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 16,
    paddingVertical: 14,
    borderBottomWidth: 1,
    borderBottomColor: "#E0E0E0",
  },
  modalTitle: {
    fontSize: 16,
    fontWeight: "700",
    color: "#000",
    flex: 1,
  },
  modalBody: {
    paddingHorizontal: 16,
    paddingVertical: 12,
  },
  modalText: {
    fontSize: 13,
    color: "#333",
    lineHeight: 18,
    marginBottom: 12,
  },
  penaltyHeading: {
    fontSize: 13,
    fontWeight: "700",
    color: "#000",
    marginTop: 8,
    marginBottom: 10,
  },
  penaltyItem: {
    marginBottom: 14,
  },
  penaltyNumber: {
    fontSize: 12,
    fontWeight: "700",
    color: "#000",
    marginBottom: 6,
  },
  penaltyDescription: {
    fontSize: 12,
    color: "#555",
    lineHeight: 16,
  },
  modalCloseButton: {
    backgroundColor: "#3DC1C6",
    paddingVertical: 12,
    paddingHorizontal: 24,
    borderRadius: 6,
    alignItems: "center",
    margin: 16,
  },
  modalCloseButtonText: {
    color: "#fff",
    fontWeight: "700",
    fontSize: 14,
  },
});
