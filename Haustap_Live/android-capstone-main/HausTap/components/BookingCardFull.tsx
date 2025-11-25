import { Feather, MaterialIcons } from "@expo/vector-icons";
import * as Clipboard from 'expo-clipboard';
import React, { useState } from "react";
import { Alert, LayoutAnimation, Platform, StyleSheet, Text, TextInput, TouchableOpacity, UIManager, View } from "react-native";
import { Booking, bookingStore } from "../src/services/bookingStore";

// Enable LayoutAnimation on Android
if (Platform.OS === "android" && UIManager.setLayoutAnimationEnabledExperimental) {
  UIManager.setLayoutAnimationEnabledExperimental(true);
}

interface BookingCardFullProps {
  booking: Booking;
  showMoreMenu?: boolean;
  onToggleMoreMenu?: () => void;
  showFooterButtons?: boolean;
  onMarkArrived?: () => void;
  onContactClient?: () => void;
  onCancelBooking?: () => void;
  showPhotoUpload?: boolean;
  showProceedButton?: boolean;
  onProceed?: (beforePhoto: string, afterPhoto: string) => void;
  showPhotosReadOnly?: boolean;
  showReportButton?: boolean;
  onReport?: () => void;
}

export default function BookingCardFull({
  booking,
  showMoreMenu = false,
  onToggleMoreMenu,
  showFooterButtons = true,
  onMarkArrived,
  onContactClient,
  onCancelBooking,
  showPhotoUpload = false,
  showProceedButton = false,
  onProceed,
  showPhotosReadOnly = false,
  showReportButton = false,
  onReport,
}: BookingCardFullProps) {
  const [expanded, setExpanded] = useState(false);
  const [beforePhoto, setBeforePhoto] = useState<string | null>(booking.beforePhoto || null);
  const [afterPhoto, setAfterPhoto] = useState<string | null>(booking.afterPhoto || null);
  const [agreedToTerms, setAgreedToTerms] = useState(false);

  const toggleExpand = () => {
    LayoutAnimation.configureNext(LayoutAnimation.Presets.easeInEaseOut);
    setExpanded(!expanded);
  };

  const handleMarkComplete = () => {
    // Call onProceed with photos
    if (onProceed) {
      onProceed(beforePhoto || "", afterPhoto || "");
    }
  };

  return (
    <View style={styles.card}>
      {/* Card Header - Client, Service, Booking ID */}
      <View style={styles.cardHeader}>
        <View style={{ flex: 1 }}>
          <View style={{ flexDirection: "row", alignItems: "center", marginBottom: 4 }}>
            <Text style={styles.clientName}>
              <Text style={{ fontWeight: "700" }}>Client:</Text> {booking.clientName}
            </Text>
            <TouchableOpacity style={{ marginLeft: 8 }}>
              <Feather name="phone" size={16} color="#007AFF" />
            </TouchableOpacity>
          </View>
          <Text style={styles.serviceTitle}>Home Cleaning</Text>
          <Text style={styles.serviceSubtitle}>{booking.service}</Text>
        </View>
        <View style={{ alignItems: "flex-end", minWidth: 90 }}>
          {booking.bookingId && (
            <View style={styles.bookingIdContainer}>
              <Text style={styles.bookingIdText}>{booking.bookingId}</Text>
              <TouchableOpacity
                onPress={async () => {
                  if (booking.bookingId) {
                    await Clipboard.setStringAsync(booking.bookingId);
                    Alert.alert("Copied", `Booking ID ${booking.bookingId} copied to clipboard`);
                  }
                }}
                style={styles.copyIconButton}
                hitSlop={{ top: 4, bottom: 4, left: 4, right: 4 }}
              >
                <MaterialIcons name="content-copy" size={14} color="#00B0B9" />
              </TouchableOpacity>
            </View>
          )}
          <TouchableOpacity
            onPress={toggleExpand}
            style={styles.expandToggle}
            hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
            activeOpacity={0.7}
          >
            <MaterialIcons
              name={expanded ? "keyboard-arrow-up" : "keyboard-arrow-down"}
              size={22}
              color="#333"
            />
          </TouchableOpacity>
        </View>
      </View>

      {/* Collapsed view: Show date/time, total price, and notes preview */}
      {!expanded && (
        <View style={styles.collapsedPreview}>
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
          <View style={styles.collapsedTotalRow}>
            <Text style={styles.collapsedTotalLabel}>TOTAL</Text>
            <Text style={styles.collapsedTotalAmount}>₱{(booking.price + 100 - 50).toFixed(2)}</Text>
          </View>
          <View style={styles.separator} />
          <View style={{ marginVertical: 8 }}>
            <Text style={styles.label}>Notes</Text>
            <TextInput
              style={[styles.notesInput, { height: 50 }]}
              value={booking.notes || ""}
              placeholder="No notes"
              multiline
              editable={false}
              pointerEvents="none"
            />
          </View>
            {/* Mark as Complete Button in Collapsed View (for Ongoing) */}
            {showPhotoUpload && showProceedButton && (
              <>
                <View style={styles.separator} />
                <TouchableOpacity
                  style={[
                    styles.completeButton,
                    !(beforePhoto && afterPhoto) && styles.completeButtonDisabled,
                  ]}
                  onPress={handleMarkComplete}
                  disabled={!(beforePhoto && afterPhoto)}
                >
                  <Text style={styles.completeButtonText}>Mark as complete</Text>
                </TouchableOpacity>
              </>
            )}
          </View>
        )}

      {/* Expanded view: Full details */}
      {expanded && (
        <View style={styles.expandedContent}>
          {/* Date and Time row */}
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

          {/* Address section */}
          <View style={{ marginBottom: 8 }}>
            <Text style={styles.label}>Address</Text>
            <Text style={styles.value}>{booking.address}</Text>
          </View>

          <View style={styles.separator} />

          {/* Selected service */}
          <View style={{ marginBottom: 6 }}>
            <Text style={styles.sectionLabel}>Selected</Text>
            <Text style={[styles.serviceDetailValue, { fontWeight: "700" }]}>Bungalow 80–150 sqm</Text>
            <Text style={[styles.serviceDetailValue, { fontWeight: "700", marginTop: 4 }]}>Basic Cleaning – 1 Cleaner</Text>
            <Text style={[styles.inclusionsLabel, { marginTop: 8 }]}>Inclusions:</Text>
            <Text style={styles.inclusionsText}>
              Living Room: walis, mop, dusting furniture, trash removal{"\n"}
              Bedrooms: bed making, sweeping, dusting, trash removal{"\n"}
              Hallways: mop & sweep, remove cobwebs{"\n"}
              Windows & Mirrors: quick wipe
            </Text>
          </View>

          <View style={styles.separator} />

          {/* Notes section */}
          <View style={styles.notesContainer}>
            <Text style={styles.sectionLabel}>Notes</Text>
            <TextInput
              style={styles.notesInput}
              placeholder="Enter notes..."
              multiline
              value={booking.notes || ""}
              editable={false}
              pointerEvents="none"
            />
          </View>

          <View style={styles.separator} />

          {/* Voucher box */}
          <View style={styles.voucherBoxRow}>
            <View style={styles.voucherBox}>
              <MaterialIcons name="local-offer" size={18} color="#00B0B9" />
              <Text style={styles.voucherText}> Welcome Voucher</Text>
            </View>
          </View>

          <View style={styles.separator} />

          {/* Payment Breakdown */}
          <View style={styles.paymentBreakdownSection}>
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

          {/* Disclaimer Text */}
          <Text style={styles.disclaimerText}>
            Full payment will be collected directly by the service provider upon completion of the service.
          </Text>

          {/* Photo Upload Section (for Ongoing & Completed) */}
          {(showPhotoUpload || showPhotosReadOnly) && (
            <View style={styles.photoUploadSection}>
              <Text style={styles.photoUploadTitle}>Upload Photos</Text>
              
              <View style={styles.photoUploadRow}>
                <View style={styles.photoColumn}>
                  <Text style={styles.photoLabel}>Before:</Text>
                  <TouchableOpacity
                    style={styles.photoBox}
                    onPress={() => {
                      if (showPhotosReadOnly) return; // Don't open dialog in read-only mode
                      Alert.alert(
                        "Attach Before Photo",
                        "Choose an option",
                        [
                          {
                            text: "Camera",
                            onPress: () => {
                              // Simulate camera capture
                              setBeforePhoto("📸 Photo captured");
                            },
                          },
                          {
                            text: "Gallery",
                            onPress: () => {
                              // Simulate gallery selection
                              setBeforePhoto("📷 Photo selected");
                            },
                          },
                          { text: "Cancel", onPress: () => {}, style: "cancel" },
                        ],
                        { cancelable: false }
                      );
                    }}
                    disabled={showPhotosReadOnly}
                  >
                    {beforePhoto ? (
                      <View style={styles.photoPreview}>
                        <MaterialIcons name="check-circle" size={32} color="#00B0B9" />
                        <Text style={styles.photoPreviewText}>{beforePhoto}</Text>
                      </View>
                    ) : (
                      <View style={styles.photoPlaceholderBox}>
                        <MaterialIcons name="cloud-upload" size={28} color="#00B0B9" />
                        <Text style={styles.photoPlaceholderText}>Tap to attach</Text>
                      </View>
                    )}
                  </TouchableOpacity>
                </View>

                <View style={styles.photoColumn}>
                  <Text style={styles.photoLabel}>After:</Text>
                  <TouchableOpacity
                    style={styles.photoBox}
                    onPress={() => {
                      if (showPhotosReadOnly) return; // Don't open dialog in read-only mode
                      Alert.alert(
                        "Attach After Photo",
                        "Choose an option",
                        [
                          {
                            text: "Camera",
                            onPress: () => {
                              // Simulate camera capture
                              setAfterPhoto("📸 Photo captured");
                            },
                          },
                          {
                            text: "Gallery",
                            onPress: () => {
                              // Simulate gallery selection
                              setAfterPhoto("📷 Photo selected");
                            },
                          },
                          { text: "Cancel", onPress: () => {}, style: "cancel" },
                        ],
                        { cancelable: false }
                      );
                    }}
                    disabled={showPhotosReadOnly}
                  >
                    {afterPhoto ? (
                      <View style={styles.photoPreview}>
                        <MaterialIcons name="check-circle" size={32} color="#00B0B9" />
                        <Text style={styles.photoPreviewText}>{afterPhoto}</Text>
                      </View>
                    ) : (
                      <View style={styles.photoPlaceholderBox}>
                        <MaterialIcons name="cloud-upload" size={28} color="#00B0B9" />
                        <Text style={styles.photoPlaceholderText}>Tap to attach</Text>
                      </View>
                    )}
                  </TouchableOpacity>
                </View>
              </View>

              {/* Service Agreement Checkbox - Only show in upload mode */}
              {!showPhotosReadOnly && (
                <View style={styles.checkboxRow}>
                  <TouchableOpacity
                    style={[styles.checkbox, agreedToTerms && styles.checkboxChecked]}
                    onPress={() => setAgreedToTerms(!agreedToTerms)}
                  >
                    {agreedToTerms && <Text style={styles.checkmark}>✓</Text>}
                  </TouchableOpacity>
                  <Text style={styles.checkboxLabel}>Service Agreement</Text>
                </View>
              )}

              {/* Mark as Complete Button - Only show in upload mode */}
              {!showPhotosReadOnly && (
                <TouchableOpacity
                  style={[
                    styles.completeButton,
                    !(agreedToTerms && beforePhoto && afterPhoto) && styles.completeButtonDisabled,
                  ]}
                  onPress={handleMarkComplete}
                  disabled={!(agreedToTerms && beforePhoto && afterPhoto)}
                >
                  <Text style={styles.completeButtonText}>Mark as complete</Text>
                </TouchableOpacity>
              )}

              {/* Report Button - Only show in read-only mode, at bottom right */}
              {showPhotosReadOnly && showReportButton && (
                <View style={styles.reportButtonContainer}>
                  <TouchableOpacity
                    style={styles.reportButtonSmall}
                    onPress={onReport}
                  >
                    <Text style={styles.reportButtonText}>Report</Text>
                  </TouchableOpacity>
                </View>
              )}
            </View>
          )}
        </View>
      )}

      {/* Footer Buttons */}
      {showFooterButtons && (
        <View style={styles.footerButtons}>
          <TouchableOpacity
            style={styles.arrivedBtn}
            onPress={onMarkArrived}
          >
            <Text style={styles.arrivedBtnText}>Mark Arrived</Text>
          </TouchableOpacity>
          <View style={{ width: 12 }} />
          <View style={{ flex: 1, position: "relative" }}>
            <TouchableOpacity
              style={styles.moreBtn}
              onPress={onToggleMoreMenu}
            >
              <Text style={styles.moreBtnText}>More</Text>
            </TouchableOpacity>

            {/* More Popup Menu */}
            {showMoreMenu && (
              <View style={styles.menuModal}>
                <TouchableOpacity
                  style={styles.menuItem}
                  onPress={() => {
                    onContactClient?.();
                    onToggleMoreMenu?.();
                  }}
                >
                  <Text style={styles.menuItemText}>Contact Client</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={styles.menuItem}
                  onPress={() => {
                    bookingStore.cancelBooking(booking.id);
                    onCancelBooking?.();
                    onToggleMoreMenu?.();
                  }}
                >
                  <Text style={styles.menuItemText}>Cancel Booking</Text>
                </TouchableOpacity>
              </View>
            )}
          </View>
        </View>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: "#F5F5F5",
    borderRadius: 12,
    padding: 15,
    marginBottom: 16,
    // thicker shadow for stronger elevation
    shadowColor: "#000",
    shadowOpacity: 0.22,
    shadowOffset: { width: 0, height: 8 },
    shadowRadius: 14,
    elevation: 10,
  },
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    marginBottom: 12,
  },
  clientName: {
    fontSize: 14,
    fontWeight: "600",
    color: "#222",
  },
  serviceTitle: {
    fontSize: 16,
    fontWeight: "600",
    color: "#222",
    marginTop: 2,
  },
  serviceSubtitle: {
    fontSize: 13,
    color: "#555",
    marginTop: 2,
  },
  bookingIdText: {
    color: "#007AFF",
    fontWeight: "600",
    fontSize: 13,
    marginBottom: 6,
  },
  bookingIdContainer: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    marginBottom: 6,
  },
  copyIconButton: {
    padding: 4,
  },
  expandToggle: {
    padding: 6,
  },
  collapsedPreview: {
    paddingVertical: 10,
  },
  expandedContent: {
    paddingTop: 10,
  },
  row: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    marginBottom: 2,
  },
  col: {
    flex: 1,
    flexDirection: "column",
    marginHorizontal: 4,
  },
  divider: {
    width: 1,
    backgroundColor: "#ddd",
    marginHorizontal: 10,
  },
  label: {
    fontSize: 13,
    color: "#888",
    marginBottom: 2,
  },
  value: {
    fontSize: 15,
    color: "#222",
    fontWeight: "500",
    marginBottom: 2,
  },
  separator: {
    height: 1,
    backgroundColor: "#ddd",
    marginVertical: 10,
  },
  sectionLabel: {
    fontWeight: "700",
    fontSize: 13,
    color: "#222",
    marginBottom: 6,
  },
  serviceDetailValue: {
    color: "#555",
    fontSize: 13,
    fontWeight: "500",
  },
  inclusionsLabel: {
    color: "#666",
    fontSize: 12,
    fontWeight: "600",
    marginBottom: 8,
  },
  inclusionsText: {
    color: "#555",
    fontSize: 12,
    lineHeight: 18,
  },
  notesContainer: {
    marginVertical: 8,
  },
  notesInput: {
    borderWidth: 1,
    borderColor: "#eee",
    borderRadius: 8,
    padding: 8,
    fontSize: 14,
    backgroundColor: "#fafafa",
    marginTop: 4,
    marginBottom: 8,
  },
  voucherBoxRow: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 10,
  },
  voucherBox: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#fff",
    borderRadius: 8,
    padding: 10,
    borderWidth: 1,
    borderColor: "#E0F7FA",
  },
  voucherText: {
    marginLeft: 8,
    color: "#333",
    fontWeight: "600",
  },
  paymentBreakdownSection: {
    marginVertical: 10,
  },
  pricingRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 8,
    paddingBottom: 8,
    borderBottomWidth: 0.5,
    borderBottomColor: "#eee",
  },
  pricingLabel: {
    color: "#666",
    fontSize: 13,
    fontWeight: "500",
  },
  pricingValue: {
    color: "#333",
    fontSize: 13,
    fontWeight: "500",
  },
  totalRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    borderTopWidth: 1,
    borderTopColor: "#ddd",
    paddingTop: 10,
  },
  totalLabel: {
    fontWeight: "700",
    fontSize: 14,
    color: "#222",
  },
  totalAmount: {
    fontWeight: "700",
    fontSize: 14,
    color: "#222",
  },
  disclaimerText: {
    color: "#999",
    fontSize: 11,
    fontStyle: "italic",
    marginTop: 10,
    marginBottom: 15,
    lineHeight: 16,
  },
  footerButtons: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-end",
    marginTop: 20,
    paddingBottom: 10,
  },
  arrivedBtn: {
    flex: 1,
    backgroundColor: "#00B0B9",
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: "center",
    justifyContent: "center",
  },
  arrivedBtnText: {
    color: "#fff",
    fontWeight: "700",
    fontSize: 15,
  },
  moreBtn: {
    flex: 1,
    backgroundColor: "#fff",
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#ccc",
    paddingVertical: 14,
    alignItems: "center",
    justifyContent: "center",
  },
  moreBtnText: {
    color: "#333",
    fontWeight: "700",
    fontSize: 15,
  },
  menuModal: {
    position: "absolute",
    bottom: 60,
    right: 0,
    backgroundColor: "#fff",
    borderRadius: 8,
    minWidth: 160,
    shadowColor: "#000",
    shadowOpacity: 0.2,
    shadowOffset: { width: 0, height: 4 },
    shadowRadius: 8,
    elevation: 5,
    zIndex: 100,
  },
  menuItem: {
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderBottomWidth: 0.5,
    borderBottomColor: "#eee",
  },
  menuItemText: {
    fontSize: 14,
    color: "#222",
    fontWeight: "500",
  },
  photoUploadSection: {
    marginVertical: 16,
    paddingVertical: 16,
    borderTopWidth: 1,
    borderBottomWidth: 1,
    borderColor: "#eee",
  },
  photoUploadTitle: {
    fontSize: 14,
    fontWeight: "600",
    color: "#222",
    marginBottom: 12,
  },
  photoUploadRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 16,
  },
  photoColumn: {
    flex: 1,
    marginHorizontal: 6,
  },
  photoLabel: {
    fontSize: 13,
    color: "#666",
    fontWeight: "500",
    marginBottom: 8,
  },
  photoBox: {
    height: 80,
    borderWidth: 1,
    borderColor: "#ddd",
    borderRadius: 8,
    backgroundColor: "#fafafa",
    alignItems: "center",
    justifyContent: "center",
  },
  photoIcon: {
    fontSize: 32,
  },
  photoPlaceholder: {
    fontSize: 12,
    color: "#999",
  },
  checkboxRow: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 12,
    marginTop: 8,
  },
  checkbox: {
    width: 20,
    height: 20,
    borderRadius: 3,
    borderWidth: 2,
    borderColor: "#00B0B9",
    marginRight: 10,
    alignItems: "center",
    justifyContent: "center",
  },
  checkboxChecked: {
    backgroundColor: "#00B0B9",
  },
  checkmark: {
    color: "#fff",
    fontWeight: "700",
    fontSize: 12,
  },
  checkboxLabel: {
    fontSize: 13,
    color: "#222",
    fontWeight: "500",
  },
  completeButton: {
    backgroundColor: "#00B0B9",
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: "center",
    justifyContent: "center",
    marginTop: 8,
  },
  completeButtonDisabled: {
    backgroundColor: "#ccc",
    opacity: 0.6,
  },
  completeButtonText: {
    color: "#fff",
    fontWeight: "700",
    fontSize: 15,
  },
  collapsedTotalRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 8,
  },
  collapsedTotalLabel: {
    fontSize: 15,
    fontWeight: "700",
    color: "#222",
  },
  collapsedTotalAmount: {
    fontSize: 15,
    fontWeight: "700",
    color: "#00B0B9",
  },
  photoPreview: {
    alignItems: "center",
    justifyContent: "center",
  },
  photoPreviewText: {
    marginTop: 4,
    fontSize: 12,
    color: "#00B0B9",
    fontWeight: "600",
  },
  photoPlaceholderBox: {
    alignItems: "center",
    justifyContent: "center",
    gap: 8,
  },
  photoPlaceholderText: {
    fontSize: 12,
    color: "#00B0B9",
    fontWeight: "600",
  },
  reportButtonContainer: {
    flexDirection: "row",
    justifyContent: "flex-end",
    marginTop: 12,
  },
  reportButtonSmall: {
    backgroundColor: "#FF6B6B",
    borderRadius: 6,
    paddingVertical: 8,
    paddingHorizontal: 16,
    alignItems: "center",
    justifyContent: "center",
  },
  reportButtonText: {
    color: "#fff",
    fontWeight: "600",
    fontSize: 12,
  },
});
