import { Ionicons } from "@expo/vector-icons";
// Native date picker (install with: npm install @react-native-community/datetimepicker)
import ReturnBookingCard from "@/components/ReturnBookingCard";
import { Booking, bookingStore } from "@/src/services/bookingStore";
import DateTimePicker from '@react-native-community/datetimepicker';
import { useRouter } from "expo-router";
import React, { useState } from "react";
import {
  Modal,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View
} from "react-native";

export default function ReturnScreen({ navigation }: { navigation?: any }) {
  const router = useRouter();

  // Penalties / info modal (preserve existing modal behavior)
  const [modalVisible, setModalVisible] = useState(false);

  // Filter states (modal) — match Completed screen
  const [filterVisible, setFilterVisible] = useState(false);
  const [filterFrom, setFilterFrom] = useState<string | null>(null);
  const [filterTo, setFilterTo] = useState<string | null>(null);
  const [filterApplied, setFilterApplied] = useState(false);
  const [fromDate, setFromDate] = useState<Date | null>(null);
  const [toDate, setToDate] = useState<Date | null>(null);
  const [showFromPicker, setShowFromPicker] = useState(false);
  const [showToPicker, setShowToPicker] = useState(false);

  // Return bookings from store
  const [returnBookings, setReturnBookings] = useState<Booking[]>([]);
  React.useEffect(() => {
    const unsub = bookingStore.subscribe((updated: Booking[]) => {
      // For now, show a sample return booking (in production, bookingStore would track returns)
      setReturnBookings(updated.filter((b) => b.status === 'completed' || b.status === 'cancelled'));
    });
    return () => unsub();
  }, []);

  return (
    <View style={styles.container}>
      {/* Scrollable Main Content */}
      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Header */}
        <View style={styles.headerRow}>
          <Text style={styles.headerTitle}>Bookings</Text>
          <TouchableOpacity style={styles.filterBtn} onPress={() => setFilterVisible(true)}>
            <Ionicons name="filter" size={20} color="#3DC1C6" />
          </TouchableOpacity>
        </View>

        {/* Filter Modal (matches Completed screen) */}
        <Modal visible={filterVisible} transparent animationType="fade">
          <View style={styles.modalOverlay}>
            <View style={styles.modalContent}>
              <Text style={styles.modalTitle}>Filter by Date</Text>

              <View style={{ marginTop: 12, width: '100%' }}>
                <Text style={styles.modalLabel}>From:</Text>
                <TouchableOpacity style={styles.dateInput} onPress={() => setShowFromPicker(true)}>
                  <Text style={{ color: filterFrom ? '#000' : '#888' }}>{filterFrom ?? 'YYYY-MM-DD'}</Text>
                </TouchableOpacity>
                {showFromPicker && (
                  <DateTimePicker
                    value={fromDate ?? new Date()}
                    mode="date"
                    display={Platform.OS === 'ios' ? 'spinner' : 'default'}
                    onChange={(_e: any, d: Date | undefined) => {
                      if (d) {
                        setFromDate(d);
                        setFilterFrom(d.toISOString().slice(0, 10));
                      }
                      if (Platform.OS !== 'ios') setShowFromPicker(false);
                    }}
                  />
                )}
              </View>

              <View style={{ marginTop: 12, width: '100%' }}>
                <Text style={styles.modalLabel}>To:</Text>
                <TouchableOpacity style={styles.dateInput} onPress={() => setShowToPicker(true)}>
                  <Text style={{ color: filterTo ? '#000' : '#888' }}>{filterTo ?? 'YYYY-MM-DD'}</Text>
                </TouchableOpacity>
                {showToPicker && (
                  <DateTimePicker
                    value={toDate ?? new Date()}
                    mode="date"
                    display={Platform.OS === 'ios' ? 'spinner' : 'default'}
                    onChange={(_e: any, d: Date | undefined) => {
                      if (d) {
                        setToDate(d);
                        setFilterTo(d.toISOString().slice(0, 10));
                      }
                      if (Platform.OS !== 'ios') setShowToPicker(false);
                    }}
                  />
                )}
              </View>

              <TouchableOpacity style={styles.applyBtn} onPress={() => { setFilterApplied(true); setFilterVisible(false); }}>
                <Text style={styles.applyText}>Apply</Text>
              </TouchableOpacity>

              <TouchableOpacity style={styles.clearBtn} onPress={() => { setFilterApplied(false); setFilterFrom(null); setFilterTo(null); setFilterVisible(false); }}>
                <Text style={styles.clearText}>Clear</Text>
              </TouchableOpacity>
            </View>
          </View>
        </Modal>

        {/* Applied filter summary */}
        {filterApplied && (
          <View style={{ marginBottom: 10 }}>
            <Text style={{ color: "#444", fontSize: 13 }}>
              Showing results from {filterFrom ?? '-'} to {filterTo ?? '-'}
            </Text>
          </View>
        )}

        {/* Tabs */}
        <View style={styles.tabs}>
          <TouchableOpacity
            style={[styles.tabButton]}
            onPress={() => router.push("/service-provider/before-pending")}
          >
            <Text style={styles.tabText}>Pending</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tabButton]}
            onPress={() => router.push("/service-provider/before-ongoing")}
          >
            <Text style={styles.tabText}>Ongoing</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tabButton]}
            onPress={() => router.push("./booking-process-completed")}
          >
            <Text style={styles.tabText}>Completed</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tabButton]}
            onPress={() => router.push("./booking-process-cancelled")}
          >
            <Text style={styles.tabText}>Cancelled</Text>
          </TouchableOpacity>
          <TouchableOpacity style={[styles.tabButton, styles.tabActive]}>
            <Text style={[styles.tabText, styles.tabTextActive]}>Return</Text>
          </TouchableOpacity>
        </View>

        {/* Return Booking Cards - Dynamic Rendering */}
        {(() => {
          const displayed = !filterApplied || (!filterFrom && !filterTo)
            ? returnBookings
            : returnBookings.filter((b) => {
                const bd = new Date(b.dateTime);
                if (isNaN(bd.getTime())) return false;
                const from = filterFrom ? new Date(filterFrom) : null;
                const to = filterTo ? new Date(filterTo) : null;
                if (from && bd < from) return false;
                if (to && bd > to) return false;
                return true;
              });

          if (!displayed || displayed.length === 0) {
            return (
              <View style={styles.emptyContainer}>
                <Ionicons name="briefcase-outline" size={64} color="#CCC" />
                <Text style={styles.emptyText}>No return requests</Text>
              </View>
            );
          }

          return displayed.map((booking) => (
            <ReturnBookingCard
              key={booking.id}
              booking={booking}
            />
          ));
        })()}
      </ScrollView>

      {/* Penalties / Return policy modal (preserve original modal) */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={modalVisible}
        onRequestClose={() => setModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalBox}>
            <Text style={styles.modalTitle}>Return Policy and Penalties</Text>
            <ScrollView style={{ maxHeight: 400 }}>
              <Text style={styles.modalText}>
                Once a return request has been approved by the admin, the
                service provider must complete the re-service within seven (7)
                days from the approval date.{"\n\n"}
                Failure to comply within the allowed period will result in:
                {"\n\n"}• Warning notice for first violation{"\n"}• Temporary
                suspension (3–7 days) for second violation{"\n"}• Permanent
                account termination for repeated non-compliance.
              </Text>
            </ScrollView>
            <TouchableOpacity
              style={styles.closeBtn}
              onPress={() => setModalVisible(false)}
            >
              <Text style={styles.closeText}>Close</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#F9F9F9",
    paddingHorizontal: 20,
    paddingTop: 60,
  },
  headerRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  filterBtn: {
    flexDirection: "row",
    alignItems: "center",
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: "700",
  },
  tabs: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginVertical: 20,
  },
  tabButton: {
    paddingVertical: 6,
    paddingHorizontal: 10,
    borderBottomWidth: 2,
    borderColor: "transparent",
  },
  tabActive: {
    borderColor: "#00B0B9",
  },
  tabText: {
    fontSize: 13,
    color: "#666",
  },
  tabTextActive: {
    color: "#00B0B9",
    fontWeight: "600",
  },
  card: {
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 16,
    shadowColor: "#000",
    shadowOpacity: 0.1,
    shadowOffset: { width: 0, height: 1 },
    shadowRadius: 2,
    marginBottom: 25,
  },
  clientName: {
    fontWeight: "700",
    fontSize: 14,
  },
  serviceType: {
    fontSize: 14,
    color: "#000",
  },
  subType: {
    fontSize: 13,
    color: "#555",
  },
  section: {
    marginVertical: 8,
  },
  row: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  half: {
    width: "48%",
  },
  label: {
    fontSize: 13,
    color: "#666",
  },
  value: {
    fontSize: 14,
    color: "#000",
  },
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
    borderColor: "#EEE",
    borderRadius: 8,
    padding: 10,
    backgroundColor: "#FAFAFA",
    marginVertical: 8,
  },
  voucherText: {
    marginLeft: 8,
    color: "#444",
    fontSize: 13,
  },
  divider: {
    borderBottomWidth: 1,
    borderColor: "#E0E0E0",
    marginVertical: 10,
  },
  priceSection: {
    marginTop: 10,
  },
  priceRow: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  subLabel: {
    fontSize: 13,
    color: "#666",
  },
  subValue: {
    fontSize: 13,
    color: "#000",
  },
  totalRow: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  totalLabel: {
    fontWeight: "700",
    fontSize: 15,
  },
  totalValue: {
    fontWeight: "700",
    fontSize: 15,
  },
  noteText: {
    fontSize: 11,
    color: "#888",
    textAlign: "center",
    marginTop: 10,
  },
  blueDivider: {
    height: 2,
    backgroundColor: "#00B0B9",
    marginVertical: 12,
    borderRadius: 2,
  },

  /* RETURN-specific styles (preserve existing ones) */
  returnBox: {
    backgroundColor: "#FAFAFA",
    borderRadius: 10,
    padding: 12,
    borderWidth: 1,
    borderColor: "#DDD",
  },
  returnHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  returnTitle: { fontWeight: "700", fontSize: 14, color: "#E53935" },
  pendingText: { fontSize: 13, color: "#00B0B9", fontWeight: "600" },
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
  acceptBtn: {
    backgroundColor: "#00B0B9",
    borderRadius: 8,
    paddingVertical: 12,
    alignItems: "center",
    marginTop: 10,
  },
  acceptText: { color: "#fff", fontWeight: "600" },
  returnNotice: { marginTop: 12, color: "#666", fontSize: 13 },
  penaltyText: { color: "#E53935", textDecorationLine: "underline" },

  /* Modal/filter aliases used by updated UI */
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.35)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalContent: {
    width: 320,
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 8,
    alignItems: 'center',
  },
  modalTitle: { fontSize: 16, fontWeight: '700' },
  modalLabel: { fontSize: 13, color: '#333', marginBottom: 6 },
  dateInput: { width: '100%', borderWidth: 1, borderColor: '#E0E0E0', borderRadius: 6, padding: 8, backgroundColor: '#fff' },
  applyBtn: { backgroundColor: '#00B0B9', paddingVertical: 8, paddingHorizontal: 14, borderRadius: 6, marginTop: 12 },
  applyText: { color: '#fff', fontWeight: '600' },
  clearBtn: { marginTop: 10, paddingVertical: 8, paddingHorizontal: 20 },
  clearText: { color: '#666' },
  /* Penalties modal styles (preserve for the Return policy modal) */
  scrollContent: { flexGrow: 1, paddingHorizontal: 16, paddingTop: 60, paddingBottom: 100 },
  modalBox: { width: 320, backgroundColor: '#fff', padding: 20, borderRadius: 8, alignItems: 'center' },
  modalText: { fontSize: 14, color: '#333', lineHeight: 20 },
  closeBtn: { backgroundColor: '#00B0B9', paddingVertical: 10, paddingHorizontal: 20, borderRadius: 8, marginTop: 16 },
  closeText: { color: '#fff', fontWeight: '600' },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyText: {
    fontSize: 16,
    color: '#999',
    marginTop: 16,
  },
});