
import { Feather, FontAwesome, Ionicons, MaterialIcons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import React from "react";
import { Animated, Keyboard, KeyboardAvoidingView, LayoutAnimation, PanResponder, Platform, ScrollView, StyleSheet, Text, TextInput, TouchableOpacity, TouchableWithoutFeedback, UIManager, View } from "react-native";
import { bookingStore } from "../../src/services/bookingStore";

export default function ServiceProviderDashboard() {
  const router = useRouter();
  const [notes, setNotes] = React.useState("");
  const [isDropdownOpen, setIsDropdownOpen] = React.useState(false);
  const [bookingState, setBookingState] = React.useState("available"); // "available" or "pending"
  const [bookingId, setBookingId] = React.useState("");
  const [bookingHTId, setBookingHTId] = React.useState("");
  const [showMoreMenu, setShowMoreMenu] = React.useState(false);
  const [arrived, setArrived] = React.useState(false);
  const [currentBookingId, setCurrentBookingId] = React.useState("1"); // Default to first booking
  const pan = React.useRef(new Animated.Value(0)).current;
  const knobWidth = 60;
  const trackWidthRef = React.useRef(0);
  const pos = React.useRef(0);
  const startX = React.useRef(0);
  const [accepted, setAccepted] = React.useState(false);

  React.useEffect(() => {
    if (Platform.OS === "android" && UIManager.setLayoutAnimationEnabledExperimental) {
      UIManager.setLayoutAnimationEnabledExperimental(true);
    }
  }, []);

  const toggleDropdown = () => {
    LayoutAnimation.configureNext(LayoutAnimation.Presets.easeInEaseOut);
    setIsDropdownOpen((v) => !v);
  };

  const onAccept = () => {
    setAccepted(true);
    setBookingState("pending");
    
    // Update booking in store and get the booking ID
    const booking = bookingStore.acceptBooking(currentBookingId);
    if (booking && booking.bookingId) {
      setBookingHTId(booking.bookingId);
      setBookingId(currentBookingId);
      console.log("Booking accepted, ID:", booking.bookingId);
      
      // Navigate to Bookings page with Pending tab selected
      // Using a slight delay to ensure state updates first
      setTimeout(() => {
        router.push({
          pathname: "/service-provider/before-pending",
          params: { tab: "pending" }
        });
      }, 300);
    }
  };

  const panResponder = React.useRef(
    PanResponder.create({
      onStartShouldSetPanResponder: () => !accepted,
      onMoveShouldSetPanResponder: () => !accepted,
      onPanResponderGrant: () => {
        startX.current = pos.current;
      },
      onPanResponderMove: (_e, gestureState) => {
        const max = Math.max(0, (trackWidthRef.current || 0) - knobWidth);
        let newPos = Math.max(0, Math.min(startX.current + gestureState.dx, max));
        pos.current = newPos;
        pan.setValue(newPos);
      },
      onPanResponderRelease: () => {
        const max = Math.max(0, (trackWidthRef.current || 0) - knobWidth);
        const threshold = max * 0.7;
        if (pos.current >= threshold) {
          Animated.timing(pan, { toValue: max, duration: 150, useNativeDriver: true }).start(() => {
            setAccepted(true);
            onAccept();
          });
        } else {
          Animated.timing(pan, { toValue: 0, duration: 150, useNativeDriver: true }).start(() => {
            pos.current = 0;
            startX.current = 0;
          });
        }
      },
    })
  ).current;

  const handleNotificationPress = () => {
    // router.push("/service-provider/notification");
  };

  const handleBookingsPress = () => {
    // router.push("/service-provider/before-pending");
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === "ios" ? "padding" : undefined}
    >
      <TouchableWithoutFeedback onPress={Keyboard.dismiss}>
        <ScrollView
          contentContainerStyle={styles.scrollContainer}
          keyboardShouldPersistTaps="handled"
          showsVerticalScrollIndicator={false}
        >
          {/* Header */}
          <View style={styles.header}>
            <View style={styles.statusBadge}>
              <Text style={styles.statusText}>On Duty</Text>
            </View>
            <TouchableOpacity onPress={handleNotificationPress}>
              <Ionicons name="notifications-outline" size={24} color="#333" />
            </TouchableOpacity>
          </View>

          <Text style={styles.welcomeText}>Welcome, Ana Santos</Text>

          <View style={styles.ratingContainer}>
            <FontAwesome name="star" size={14} color="#f1c40f" />
            <Text style={styles.ratingText}>Ratings: 4.8</Text>
          </View>

          <Text style={styles.subText}>Bookings nearby you</Text>


          {/* Booking Card */}
          <View style={styles.card}>
            {/* Header for both states */}
            <View style={styles.summaryContainer}>
              <View style={styles.headerRow}>
                <View style={{ flex: 1 }}>
                  <View style={{ flexDirection: "row", alignItems: "center" }}>
                    <Text style={styles.clientName}>Client: Jenn Bornilla</Text>
                    {bookingState === "pending" && (
                      <TouchableOpacity style={{ marginLeft: 6 }}>
                        <Feather name="phone" size={16} color="#007AFF" />
                      </TouchableOpacity>
                    )}
                  </View>
                  <Text style={styles.cardTitle}>Home Cleaning</Text>
                  <Text style={styles.summaryServiceText}>Bungalow - Basic Cleaning</Text>
                </View>
                <View style={{ alignItems: "flex-end", minWidth: 90 }}>
                  {bookingState === "pending" && bookingHTId && (
                    <Text style={styles.bookingIdText}>{bookingHTId}</Text>
                  )}
                  <TouchableOpacity
                    onPress={toggleDropdown}
                    style={styles.expandToggle}
                    hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
                    activeOpacity={0.7}
                  >
                    <MaterialIcons
                      name={isDropdownOpen ? "keyboard-arrow-up" : "keyboard-arrow-down"}
                      size={22}
                      color="#333"
                    />
                  </TouchableOpacity>
                </View>
              </View>
            </View>

              {/* Collapsed view: Show date/time and notes preview */}
              {!isDropdownOpen && (
                <View style={styles.collapsedPreview}>
                  <View style={styles.separator} />
                  <View style={styles.row}>
                    <View style={styles.col}>
                      <Text style={styles.label}>Date</Text>
                      <Text style={styles.value}>May 21, 2025</Text>
                    </View>
                    <View style={styles.divider} />
                    <View style={styles.col}>
                      <Text style={styles.label}>Time</Text>
                      <Text style={styles.value}>8:00 AM</Text>
                    </View>
                  </View>
                  <View style={styles.separator} />
                  <View style={{ marginVertical: 8 }}>
                    <Text style={styles.label}>Notes</Text>
                    <TextInput
                      style={[styles.notesInput, { height: 50 }]}
                      value={notes}
                      placeholder="No notes"
                      multiline
                      editable={false}
                      pointerEvents="none"
                    />
                  </View>
                </View>
              )}

              {isDropdownOpen && (
                <View style={styles.expandedContent}>
                {/* Date and Time row */}
                <View style={styles.row}>
                  <View style={styles.col}>
                    <Text style={styles.label}>Date</Text>
                    <Text style={styles.value}>Nov 15, 2025</Text>
                  </View>
                  <View style={styles.divider} />
                  <View style={styles.col}>
                    <Text style={styles.label}>Time</Text>
                    <Text style={styles.value}>10:00 AM</Text>
                  </View>
                </View>

                <View style={styles.separator} />

                {/* Address section */}
                <View style={{ marginBottom: 8 }}>
                  <Text style={styles.label}>Address</Text>
                  <Text style={styles.value}>1234 Palm Grove, Brgy. San Miguel, City, Province</Text>
                </View>

                <View style={styles.separator} />

                {/* Selected service */}
                <View style={{ marginBottom: 6 }}>
                  <Text style={styles.sectionLabel}>Selected</Text>
                  <Text style={[styles.serviceDetailValue, { fontWeight: "700" }]}>Bungalow  80–150 sqm</Text>
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
                    value={notes}
                    onChangeText={setNotes}
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
                    <Text style={styles.pricingValue}>₱1,000.00</Text>
                  </View>
                  <View style={styles.pricingRow}>
                    <Text style={styles.pricingLabel}>Sub Total</Text>
                    <Text style={styles.pricingValue}>₱1,000.00</Text>
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
                  <Text style={styles.totalAmount}>₱1,050.00</Text>
                </View>

                <Text style={styles.disclaimerText}>
                  Full payment will be collected directly by the service provider upon completion of the service.
                </Text>

                {/* Accept Booking or Pending Footer */}
                {bookingState === "available" ? (
                  <View style={styles.acceptBookingSection}>
                    <Text style={styles.sectionLabel}>Accept Booking?</Text>
                    <Text style={[styles.smallCaption, { marginTop: 6 }]}>Be the one to accept it</Text>
                    <View
                      style={[styles.sliderTrack, accepted && { opacity: 0.7 }]}
                      onLayout={(e) => {
                        trackWidthRef.current = e.nativeEvent.layout.width;
                      }}
                    >
                      <Text style={styles.sliderCenterText}>{accepted ? "Accepted" : "Slide to Accept Booking"}</Text>
                      <Animated.View
                        style={[
                          styles.sliderKnob,
                          { transform: [{ translateX: pan }] },
                          accepted && styles.sliderKnobAccepted,
                        ]}
                        {...panResponder.panHandlers}
                      >
                        <MaterialIcons name={accepted ? "check" : "arrow-forward"} size={20} color={accepted ? "#fff" : "#000"} />
                      </Animated.View>
                    </View>
                  </View>
                ) : (
                  <View style={styles.pendingFooterRow}>
                    <TouchableOpacity
                      style={styles.arrivedBtn}
                      onPress={() => setArrived(true)}
                    >
                      <Text style={styles.arrivedBtnText}>Mark Arrived</Text>
                    </TouchableOpacity>
                    <View style={{ width: 12 }} />
                    <TouchableOpacity
                      style={styles.moreBtn}
                      onPress={() => setShowMoreMenu(!showMoreMenu)}
                    >
                      <Text style={styles.moreBtnText}>More</Text>
                    </TouchableOpacity>
                    {/* Dropdown/Modal for More */}
                    {showMoreMenu && (
                      <View style={styles.menuModalOverlay}>
                        <View style={styles.menuModal}>
                          <TouchableOpacity
                            style={styles.menuItem}
                            onPress={() => {
                              console.log("Contact Client");
                              setShowMoreMenu(false);
                            }}
                          >
                            <Text style={styles.menuItemText}>Contact Client</Text>
                          </TouchableOpacity>
                          <TouchableOpacity
                            style={styles.menuItem}
                            onPress={() => {
                              console.log("Cancel Booking");
                              bookingStore.cancelBooking(currentBookingId);
                              setShowMoreMenu(false);
                            }}
                          >
                            <Text style={styles.menuItemText}>Cancel Booking</Text>
                          </TouchableOpacity>
                        </View>
                      </View>
                    )}
                  </View>
                )}
              </View>
            )}
          </View>

          {/* Earnings section (above footer) */}
          <View style={styles.earningsSection}>
            <View style={styles.earningsHeader}>
              <Text style={styles.earningsTitle}>Earnings</Text>
              <TouchableOpacity>
                <Text style={styles.earningsDetails}>Details</Text>
              </TouchableOpacity>
            </View>
            <View style={styles.earningsCard}>
              <View style={styles.earningsRow}>
                <View style={styles.earningsCol}>
                  <Text style={styles.earningsLabel}>Total Earnings</Text>
                  <Text style={styles.earningsValue}>₱0</Text>
                  <Text style={[styles.earningsLabel, { marginTop: 16 }]}>Total Bookings</Text>
                  <Text style={styles.earningsValue}>₱0</Text>
                  <Text style={[styles.earningsLabel, { marginTop: 16 }]}>Cancelled Bookings</Text>
                  <Text style={styles.earningsValue}>₱0</Text>
                </View>
                <View style={styles.earningsCol}>
                  <Text style={styles.earningsLabel}>Earnings for November</Text>
                  <Text style={styles.earningsValue}>₱0</Text>
                  <Text style={[styles.earningsLabel, { marginTop: 16 }]}>Completed Bookings</Text>
                  <Text style={styles.earningsValue}>₱0</Text>
                </View>
              </View>
            </View>
          </View>
        </ScrollView>
      </TouchableWithoutFeedback>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 12,
  },
  container: { flex: 1, backgroundColor: "#fff" },
  scrollContainer: { padding: 20, paddingBottom: 140 },
  statusBadge: {
    paddingVertical: 4,
    paddingHorizontal: 10,
    borderRadius: 15,
    backgroundColor: "#E0F7FA",
    alignSelf: "flex-start",
    marginBottom: 8,
  },
  statusText: { color: "#2E7D32", fontSize: 12, fontWeight: "600" },
  welcomeText: { fontSize: 18, fontWeight: "600", color: "#222" },
  ratingContainer: { flexDirection: "row", alignItems: "center", marginTop: 4 },
  ratingText: { marginLeft: 5, color: "#444", fontSize: 13 },
  subText: { marginTop: 15, marginBottom: 10, color: "#777", fontSize: 12, textAlign: "center", fontStyle: "italic" },
  card: {
    backgroundColor: "#F5F5F5",
    borderRadius: 12,
    padding: 15,
    shadowColor: "#000",
    shadowOpacity: 0.15,
    shadowOffset: { width: 0, height: 6 },
    shadowRadius: 12,
    elevation: 6,
  },
  cardHeaderPressable: {
    paddingVertical: 8,
  },
  cardHeader: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  headerRight: { flexDirection: "row", alignItems: "center" },
  priceText: { fontWeight: "700", fontSize: 14, color: "#222", marginRight: 6 },
  cardTitle: { fontSize: 16, fontWeight: "600", color: "#222" },
  summaryContainer: { paddingVertical: 10 },
  clientName: { fontWeight: "700", fontSize: 14, color: "#222", marginBottom: 4 },
  cardHeaderRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  expandToggle: { padding: 6 },
  summaryServiceText: { color: "#555", fontSize: 13, fontWeight: "700" },
  serviceText: { color: "#555", marginBottom: 10 },
  separator: { height: 1, backgroundColor: "#ddd", marginVertical: 10 },
  divider: { width: 1, backgroundColor: "#ddd", marginHorizontal: 10 },
  // Removed stray import statement
  totalRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    borderTopWidth: 1,
    borderTopColor: "#ddd",
    paddingTop: 10,
  },
  totalLabel: { fontWeight: "700", fontSize: 14, color: "#222" },
  totalAmount: { fontWeight: "700", fontSize: 14, color: "#222" },
  openQueueBtn: {
    display: "none",
  },
  openQueueText: { display: "none" },
  navbar: {
    display: "none",
  },
  navItem: { display: "none" },
  navText: { display: "none" },
  earningsSection: { marginTop: 48, marginBottom: 16 },
  earningsHeader: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", marginBottom: 8 },
  earningsTitle: { fontSize: 16, fontWeight: "700", color: "#222" },
  earningsDetails: { color: "#00B0B9", fontWeight: "600" },
  earningsCard: { backgroundColor: "#FAFAFA", borderRadius: 14, borderWidth: 1, borderColor: "#E0F7FA", padding: 18, shadowColor: "#000", shadowOpacity: 0.05, shadowOffset: { width: 0, height: 1 }, shadowRadius: 3, elevation: 2 },
  earningsRow: { flexDirection: "row", justifyContent: "space-between" },
  earningsCol: { flex: 1, marginRight: 12 },
  earningsLabel: { color: "#666", fontSize: 13, fontWeight: "500" },
  earningsValue: { fontSize: 15, color: "#222", fontWeight: "700", marginTop: 2 },
  // Dropdown expanded content styles
  expandedContent: {
    paddingTop: 10,
  },
  collapsedPreview: {
    paddingTop: 10,
  },
  expandedServiceTitle: {
    fontSize: 14,
    fontWeight: "600",
    color: "#222",
    marginBottom: 10,
  },
  headerRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    marginBottom: 2,
  },
  bookingIdText: {
    color: "#007AFF",
    fontWeight: "600",
    fontSize: 13,
    marginBottom: 2,
    textAlign: "right",
  },
  pendingFooterRow: {
    flexDirection: "row",
    alignItems: "center",
    marginTop: 18,
    marginBottom: 2,
    justifyContent: "space-between",
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
  menuModalOverlay: {
    position: "absolute",
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: "rgba(0,0,0,0.2)",
    justifyContent: "center",
    alignItems: "center",
    zIndex: 99,
  },
  menuModal: {
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 18,
    minWidth: 220,
    shadowColor: "#000",
    shadowOpacity: 0.15,
    shadowOffset: { width: 0, height: 4 },
    shadowRadius: 12,
    elevation: 6,
  },
  menuItem: {
    paddingVertical: 12,
  },
  menuItemText: {
    fontSize: 15,
    color: "#222",
    fontWeight: "500",
  },
  menuCancel: {
    paddingVertical: 10,
    marginTop: 8,
    borderTopWidth: 1,
    borderColor: "#eee",
  },
  menuCancelText: {
    fontSize: 15,
    color: "#007AFF",
    fontWeight: "600",
    textAlign: "center",
  },
  serviceDetailsRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 10,
  },
  serviceDetailCol: {
    flex: 1,
  },
  serviceDetailLabel: {
    color: "#666",
    fontSize: 12,
    fontWeight: "500",
    marginBottom: 4,
  },
  serviceDetailValue: {
    color: "#222",
    fontSize: 13,
    fontWeight: "500",
  },
  inclusionsSection: {
    marginVertical: 10,
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
  pricingSection: {
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
  pricingRowTotal: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginTop: 10,
    paddingTop: 10,
    borderTopWidth: 1,
    borderTopColor: "#ddd",
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
  pricingLabelTotal: {
    color: "#222",
    fontSize: 14,
    fontWeight: "700",
  },
  pricingValueTotal: {
    color: "#222",
    fontSize: 14,
    fontWeight: "700",
  },
  disclaimerText: {
    color: "#999",
    fontSize: 11,
    fontStyle: "italic",
    marginTop: 10,
    marginBottom: 15,
    lineHeight: 16,
  },
  buttonsContainer: {
    marginTop: 15,
    flexDirection: "row",
    gap: 10,
  },
  acceptButton: {
    flex: 1,
    backgroundColor: "#00B0B9",
    borderRadius: 8,
    paddingVertical: 14,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 8,
  },
  acceptButtonText: {
    color: "#fff",
    fontSize: 14,
    fontWeight: "600",
  },
  voucherContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#fff",
    borderRadius: 8,
    padding: 10,
    marginTop: 8,
    borderWidth: 1,
    borderColor: "#eee",
  },
  voucherText: { marginLeft: 8, color: "#333", fontWeight: "600" },
  sectionLabel: { fontWeight: "700", fontSize: 13, color: "#222", marginBottom: 6 },
  voucherContainerRow: { flexDirection: "row", alignItems: "center", justifyContent: "space-between", gap: 10 },
  voucherInput: { flex: 1, borderWidth: 1, borderColor: "#eee", borderRadius: 8, padding: 8, backgroundColor: "#fff" },
  voucherBoxRow: { flexDirection: "row", alignItems: "center" },
  voucherBox: { flexDirection: "row", alignItems: "center", backgroundColor: "#fff", borderRadius: 8, padding: 10, borderWidth: 1, borderColor: "#E0F7FA" },
  paymentBreakdownSection: { marginVertical: 10 },
  smallCaption: { color: "#888", fontSize: 12, marginTop: 6 },
  acceptBookingSection: { marginTop: 12, marginBottom: 6 },
  sliderWrapper: { marginTop: 12 },
  sliderTrack: {
    height: 56,
    backgroundColor: "#00B0B9",
    borderRadius: 10,
    justifyContent: "center",
    overflow: "hidden",
    marginTop: 8,
  },
  sliderTrackAccepted: { backgroundColor: "#2E7D32" },
  sliderKnob: {
    position: "absolute",
    left: 0,
    width: 60,
    height: 56,
    borderRadius: 10,
    backgroundColor: "#fff",
    borderWidth: 1,
    borderColor: "#E6E6E6",
    justifyContent: "center",
    alignItems: "center",
  },
  sliderKnobAccepted: { backgroundColor: "#2E7D32" },
  sliderKnobInner: { width: 36, height: 36, borderRadius: 18, backgroundColor: "transparent", justifyContent: "center", alignItems: "center" },
  sliderLabelContainer: { position: "absolute", left: 0, right: 0, alignItems: "center" },
  sliderLabel: { color: "#00675b", fontWeight: "600", fontSize: 14 },
  sliderCenterText: { position: "absolute", alignSelf: "center", color: "#fff", fontWeight: "600", fontSize: 14 },
  row: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 8,
  },
  col: {
    flex: 1,
    flexDirection: "column",
    marginHorizontal: 4,
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
  notesContainer: {
    marginVertical: 8,
  },
});
