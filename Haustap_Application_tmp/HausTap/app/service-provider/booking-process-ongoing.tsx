import { Ionicons } from "@expo/vector-icons";
import * as ImagePicker from "expo-image-picker";
import { router } from "expo-router";
import React, { useRef, useState } from "react";
import {
    Alert,
    Animated,
    Image,
    Modal,
    PanResponder,
    PanResponderGestureState,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";

export default function OngoingScreen() {
  const [isCompleted, setIsCompleted] = useState(false);
  const [beforePhoto, setBeforePhoto] = useState<string | null>(null);
  const [afterPhoto, setAfterPhoto] = useState<string | null>(null);
  const [agreement, setAgreement] = useState(false);
  const [showAgreementModal, setShowAgreementModal] = useState(false);
  const pan = useRef(new Animated.Value(0)).current;

  // ✅ Request permission for gallery access
  const requestPermission = async () => {
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== "granted") {
      Alert.alert("Permission needed", "Please allow access to your photos to upload.");
      return false;
    }
    return true;
  };

  // ✅ Pick image for before/after photo
  const pickImage = async (type: "before" | "after") => {
    const hasPermission = await requestPermission();
    if (!hasPermission) return;

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [4, 4],
      quality: 1,
    });

    if (!result.canceled) {
      const uri = result.assets[0].uri;
      if (type === "before") setBeforePhoto(uri);
      else setAfterPhoto(uri);
    }
  };

  // ✅ Slide button with navigation
  const panResponder = PanResponder.create({
    onStartShouldSetPanResponder: () => true,
    onPanResponderMove: (_, gesture: PanResponderGestureState) => {
      const maxSlide = 240;
      const newValue = Math.max(0, Math.min(gesture.dx, maxSlide));
      pan.setValue(newValue);
    },
    onPanResponderRelease: (_, gesture: PanResponderGestureState) => {
      // Require agreement before allowing slide-to-complete
      if (!agreement) {
        Animated.spring(pan, {
          toValue: 0,
          useNativeDriver: true,
          friction: 8,
          tension: 40,
        }).start();
        Alert.alert('Agreement required', 'Please accept the Service Agreement before marking as completed.');
        return;
      }
      const maxSlide = 240;
      const threshold = maxSlide * 0.8;

      if (gesture.dx >= threshold) {
        Animated.timing(pan, {
          toValue: maxSlide,
          duration: 200,
          useNativeDriver: true,
        }).start(() => {
          if (!isCompleted) {
            setIsCompleted(true);
            // ✅ Navigate after sliding completed
            router.push("/service-provider/booking-process-completed");
          }
        });
      } else {
        Animated.spring(pan, {
          toValue: 0,
          useNativeDriver: true,
          friction: 8,
          tension: 40,
        }).start();
      }
    },
  });

  return (
    <View style={styles.page}>
      <ScrollView
        style={styles.container}
        contentContainerStyle={{ paddingBottom: 20 }}
        showsVerticalScrollIndicator={false}
      >
        <Text style={styles.headerTitle}>Bookings</Text>

        {/* Tabs */}
        <View style={styles.tabs}>
          <TouchableOpacity
            style={styles.tabButton}
            onPress={() => router.push("/service-provider/before-pending")}
          >
            <Text style={styles.tabText}>Pending</Text>
          </TouchableOpacity>

          <TouchableOpacity style={[styles.tabButton, styles.tabActive]}>
            <Text style={[styles.tabText, styles.tabTextActive]}>Ongoing</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.tabButton}
            onPress={() => router.push("/service-provider/booking-process-completed")}
          >
            <Text style={styles.tabText}>Completed</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.tabButton}
            onPress={() => router.push("/service-provider/booking-process-cancelled")}
          >
            <Text style={styles.tabText}>Cancelled</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.tabButton}
            onPress={() => router.push("/service-provider/booking-process-return")}
          >
            <Text style={styles.tabText}>Return</Text>
          </TouchableOpacity>
        </View>

        {/* Booking Card */}
        <View style={styles.card}>
          {/* Client Info */}
          <View style={styles.cardHeader}>
            <View>
              <Text style={styles.clientName}>Client: Jenn Bornilla</Text>
              <Text style={styles.serviceType}>Home Cleaning</Text>
              <Text style={styles.subType}>Bungalow - Basic Cleaning</Text>
            </View>
          </View>

          {/* Date & Time */}
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
              B1 L50 Mango St. Phase 1 Saint Joseph Village 10{"\n"}
              Barangay Langgam, City of San Pedro, Laguna 4023
            </Text>
          </View>

          {/* Subtotal Info */}
          <View style={styles.section}>
            <Text style={styles.valueBold}>Bungalow 80–150 sqm</Text>
            <Text style={styles.value}>Basic Cleaning – 1 Cleaner</Text>
          </View>

          {/* Inclusions */}
          <View style={styles.section}>
            <Text style={[styles.label, { marginBottom: 2 }]}>Inclusions</Text>
            <Text style={styles.inclusions}>
              <Text style={styles.bold}>Living Room: </Text>walls, mop, dusting furniture, trash removal{"\n"}
              <Text style={styles.bold}>Bedrooms: </Text>bed making, sweeping, dusting, trash removal{"\n"}
              <Text style={styles.bold}>Hallway: </Text>mop & sweep, remove cobwebs{"\n"}
              <Text style={styles.bold}>Windows & Mirrors: </Text>quick wipe
            </Text>
          </View>

          {/* Notes */}
          <View style={styles.section}>
            <Text style={styles.label}>Notes:</Text>
            <TextInput
              style={styles.notesBox}
              placeholder="Type additional notes here..."
              multiline
            />
          </View>

          {/* Voucher */}
          <View style={styles.voucherBox}>
            <Ionicons name="pricetag-outline" size={18} color="#555" />
            <Text style={styles.voucherText}>No voucher added</Text>
          </View>

          {/* Total */}
          <View style={styles.totalBox}>
            <View style={styles.rowBetween}>
              <Text style={styles.label}>Sub Total</Text>
              <Text style={styles.value}>₱1,000.00</Text>
            </View>
            <View style={styles.rowBetween}>
              <Text style={styles.label}>Voucher Discount</Text>
              <Text style={styles.value}>₱0</Text>
            </View>
            <View style={styles.divider} />
            <View style={styles.rowBetween}>
              <Text style={styles.totalLabel}>TOTAL</Text>
              <Text style={styles.totalValue}>₱1,000.00</Text>
            </View>
            <Text style={styles.footnote}>
              Full payment in cash will be collected upon service completion.
            </Text>
          </View>

          {/* Upload Photos */}
          <View style={styles.section}>
            <Text style={styles.uploadLabel}>Upload Photo</Text>
            <View style={styles.rowBetween}>
              <TouchableOpacity
                style={styles.uploadBox}
                onPress={() => pickImage("before")}
              >
                {beforePhoto ? (
                  <Image source={{ uri: beforePhoto }} style={styles.uploadImage} />
                ) : (
                  <>
                    <Ionicons name="camera-outline" size={20} color="#AAA" />
                    <Text style={styles.uploadText}>Before</Text>
                  </>
                )}
              </TouchableOpacity>

              <TouchableOpacity
                style={styles.uploadBox}
                onPress={() => pickImage("after")}
              >
                {afterPhoto ? (
                  <Image source={{ uri: afterPhoto }} style={styles.uploadImage} />
                ) : (
                  <>
                    <Ionicons name="camera-outline" size={20} color="#AAA" />
                    <Text style={styles.uploadText}>After</Text>
                  </>
                )}
              </TouchableOpacity>
            </View>
          </View>

          {/* ✅ Slide Button with Navigation */}
          <View style={styles.sliderContainer}>
            <View style={styles.agreementBlock}>
              <TouchableOpacity style={styles.checkboxRow} onPress={() => {
                if (!agreement) setShowAgreementModal(true);
                else setAgreement(false);
              }}>
                <View style={[styles.checkboxBox, agreement && styles.checkboxChecked]}>
                  {agreement && <Ionicons name="checkmark" size={14} color="#fff" />}
                </View>
                <Text style={styles.agreementText}>Service Agreement</Text>
              </TouchableOpacity>
            </View>
            <View style={styles.sliderTrack}>
              <Text style={styles.slideText}>
                {isCompleted ? "Service Completed!" : "Slide to mark as completed"}
              </Text>
            </View>
            <Animated.View
              {...panResponder.panHandlers}
              style={[
                styles.slider,
                { transform: [{ translateX: pan }] },
              ]}
            >
              <Ionicons name="arrow-forward" size={24} color="#fff" />
            </Animated.View>
          </View>
        </View>
      </ScrollView>
        {/* Agreement Modal for providers */}
        <Modal
          visible={showAgreementModal}
          animationType="fade"
          transparent
          onRequestClose={() => {
            // Android back button -> accept and close
            setShowAgreementModal(false);
            setAgreement(true);
          }}
        >
          <View style={styles.modalOverlay}>
            <View style={styles.modalCard}>
              <View style={styles.modalCardHeader}>
                <Text style={styles.modalCardTitle}>HAUSTAP BOOKING SERVICE AGREEMENT</Text>
                <TouchableOpacity onPress={() => { setShowAgreementModal(false); setAgreement(true); }}>
                  <Ionicons name="close" size={20} color="#666" />
                </TouchableOpacity>
              </View>

              <ScrollView contentContainerStyle={styles.modalCardBody}>
                <Text style={styles.modalText}>
                  (Applicable to both Client and Service Provider){"\n\n"}
                  1. Scope and Acknowledgment{"\n"}
                  - By proceeding with a booking through the Haustap platform, both the Client and Service Provider acknowledge and agree to comply with the terms stated in this Service Agreement.{"\n\n"}
                  - This agreement serves as a mutual understanding of responsibilities, service scope, and conduct throughout the booking process — from confirmation to completion.{"\n\n"}
                  2. Additional Services During Inspection{"\n"}
                  If the Client requests additional service(s) during the inspection, the following applies:{"\n"}
                  a. The original inspection fee (if any) will be waived.{"\n"}
                  b. The Client will only be charged the service fee for the additional service(s).{"\n\n"}
                  3. Outside Transactions{"\n"}
                  - Haustap shall not be held liable for any transactions, agreements, or activities conducted outside the Haustap platform.{"\n\n"}
                  4. Accuracy of Booking Details{"\n"}
                  - All information entered in the booking form must be true, accurate, and complete.{"\n\n"}
                  5. Communication{"\n"}
                  - Both the Client and Service Provider must communicate clearly and respectfully regarding any concerns through Haustap’s messaging or contact channels.{"\n\n"}
                  6. Transportation Fee{"\n"}
                  - A Transportation Fee is charged to cover the Service Provider’s travel cost to and from the service location. This fee is visible upon booking confirmation and is included in the total bill.{"\n\n"}
                  7. Payments{"\n"}
                  - All payments are strictly handled in cash between the Client and the Service Provider after the service is completed.{"\n\n"}
                  8. Cancellation Policy{"\n"}
                  - Clients may cancel a booking only while it is in “Pending” status. Once the booking is “Ongoing,” the Client no longer has the right to cancel.{"\n\n"}
                  9. Ongoing Service{"\n"}
                  - The Service Provider must upload photo evidence (before and after) of the service performed.{"\n\n"}
                  10. Completion and Reporting{"\n"}
                  - When marking a booking as “Completed,” both the Client and Service Provider confirm that the report or feedback submitted is true and fair.{"\n\n"}
                  Haustap reserves the right to determine the severity of each case based on the frequency and nature of non-compliance.
                </Text>
              </ScrollView>

              <View style={styles.modalCardFooter}>
                <TouchableOpacity style={styles.modalCardClose} onPress={() => { setShowAgreementModal(false); setAgreement(true); }}>
                  <Text style={styles.modalCloseText}>Close</Text>
                </TouchableOpacity>
              </View>
            </View>
          </View>
        </Modal>
    </View>
  );
}

// --- STYLES (unchanged) ---
const styles = StyleSheet.create({
  page: { flex: 1, backgroundColor: "#F9F9F9" },
  container: { flex: 1, paddingHorizontal: 16, paddingTop: 55 },
  headerTitle: { fontSize: 21, fontWeight: "700", marginBottom: 10 },
  tabs: { flexDirection: "row", justifyContent: "space-between", marginBottom: 12 },
  tabButton: { paddingVertical: 4, paddingHorizontal: 8, borderBottomWidth: 2, borderColor: "transparent" },
  tabActive: { borderColor: "#00B0B9" },
  tabText: { fontSize: 13, color: "#666" },
  tabTextActive: { color: "#00B0B9", fontWeight: "600" },
  card: {
    backgroundColor: "#F5F5F5",
    borderRadius: 10,
    padding: 14,
    marginBottom: 20,
    shadowColor: "#000",
    shadowOpacity: 0.05,
    shadowOffset: { width: 0, height: 1 },
    shadowRadius: 1.5,
    elevation: 1,
  },
  cardHeader: { flexDirection: "row", justifyContent: "space-between", marginBottom: 8 },
  clientName: { fontWeight: "600", fontSize: 14 },
  serviceType: { fontSize: 13.5, color: "#000" },
  subType: { fontSize: 12.5, color: "#555" },
  row: { flexDirection: "row", justifyContent: "space-between", marginVertical: 4 },
  half: { width: "48%" },
  label: { fontSize: 12.5, color: "#666" },
  value: { fontSize: 13.5, color: "#000" },
  valueBold: { fontSize: 13.5, color: "#000", fontWeight: "600" },
  section: { marginVertical: 6 },
  bold: { fontWeight: "600", color: "#000" },
  inclusions: { fontSize: 13, color: "#444", lineHeight: 18 },
  notesBox: { borderWidth: 1, borderColor: "#DDD", borderRadius: 6, height: 50, marginTop: 4, padding: 8 },
  voucherBox: {
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#EEE",
    borderRadius: 6,
    padding: 8,
    marginTop: 8,
  },
  voucherText: { fontSize: 13, color: "#555", marginLeft: 6 },
  totalBox: { marginTop: 10 },
  rowBetween: { flexDirection: "row", justifyContent: "space-between" },
  divider: { borderBottomWidth: 1, borderColor: "#E0E0E0", marginVertical: 6 },
  totalLabel: { fontWeight: "700", fontSize: 14 },
  totalValue: { fontWeight: "700", fontSize: 14 },
  footnote: { fontSize: 11, color: "#888", marginTop: 4 },
  uploadLabel: { fontWeight: "600", marginBottom: 6, fontSize: 12 },
  uploadBox: {
    alignItems: "center",
    justifyContent: "center",
    width: "46%",
    height: 120,
    borderWidth: 1,
    borderColor: "#DDD",
    borderRadius: 8,
    backgroundColor: "#FAFAFA",
    overflow: "hidden",
  },
  uploadImage: {
    width: "100%",
    height: "100%",
    resizeMode: "cover",
  },
  uploadText: { fontSize: 12.5, color: "#555", marginTop: 4 },
  sliderContainer: {
    width: "100%",
    height: 55,
    backgroundColor: "#f0f0f0",
    borderRadius: 30,
    overflow: "hidden",
    marginTop: 14,
    marginBottom: 4,
  },
  slider: {
    width: 55,
    height: 55,
    borderRadius: 30,
    backgroundColor: "#00B0B9",
    position: "absolute",
    left: 0,
    justifyContent: "center",
    alignItems: "center",
  },
  sliderTrack: { width: "100%", height: "100%", alignItems: "center", justifyContent: "center" },
  slideText: { color: "#666", fontWeight: "600", fontSize: 15 },
  bottomNav: { display: "none" },
  navItem: { display: "none" },
  navText: { display: "none" },
  /* Agreement & modal styles */
  agreementBlock: {
    width: '100%',
    marginBottom: 8,
  },
  checkboxRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  checkboxBox: {
    width: 20,
    height: 20,
    borderWidth: 1,
    borderColor: '#CCC',
    borderRadius: 4,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 8,
  },
  checkboxChecked: {
    backgroundColor: '#00ADB5',
    borderColor: '#00ADB5',
  },
  agreementText: {
    fontSize: 14,
    color: '#333',
  },
  modalContainer: {
    flex: 1,
    backgroundColor: '#fff',
  },
  modalHeader: {
    padding: 16,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: '700',
  },
  modalContent: {
    padding: 16,
  },
  modalText: {
    fontSize: 14,
    color: '#333',
    lineHeight: 20,
  },
  modalFooter: {
    padding: 16,
    borderTopWidth: 1,
    borderTopColor: '#eee',
  },
  modalCloseBtn: {
    alignSelf: 'flex-end',
    paddingVertical: 10,
    paddingHorizontal: 16,
  },
  modalCloseText: {
    color: '#00ADB5',
    fontWeight: '700',
  },
  modalFooterRow: {
    padding: 16,
    flexDirection: 'row',
    justifyContent: 'space-between',
    borderTopWidth: 1,
    borderTopColor: '#eee',
  },
  modalAgreeBtn: {
    backgroundColor: '#00ADB5',
    paddingVertical: 10,
    paddingHorizontal: 18,
    borderRadius: 8,
  },
  modalAgreeText: {
    color: '#fff',
    fontWeight: '700',
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.4)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 16,
  },
  modalCard: {
    width: '100%',
    maxWidth: 520,
    backgroundColor: '#fff',
    borderRadius: 12,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOpacity: 0.08,
    shadowRadius: 10,
    elevation: 6,
  },
  modalCardHeader: {
    padding: 12,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  modalCardTitle: { fontSize: 14, fontWeight: '700' },
  modalCardBody: { padding: 14, maxHeight: 440 },
  modalCardFooter: { padding: 12, borderTopWidth: 1, borderTopColor: '#eee', alignItems: 'flex-end' },
  modalCardClose: { paddingVertical: 8, paddingHorizontal: 12 },
});
