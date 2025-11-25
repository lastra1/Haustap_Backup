import { Ionicons } from "@expo/vector-icons";
import DateTimePicker from '@react-native-community/datetimepicker';
import { router } from "expo-router";
import React, { useEffect, useState } from "react";
import {
    Modal,
    Platform,
    SafeAreaView,
    ScrollView,
    StyleSheet,
    Text,
    TouchableOpacity,
    View
} from "react-native";
import CancelledBookingCard from "../../components/CancelledBookingCard";
import { Booking, bookingStore } from "../../src/services/bookingStore";

export default function CancelledScreen() {
  const [expanded, setExpanded] = useState(false);
  // Filter states (modal) — match Completed screen
  const [filterVisible, setFilterVisible] = useState(false);
  const [filterFrom, setFilterFrom] = useState<string | null>(null);
  const [filterTo, setFilterTo] = useState<string | null>(null);
  const [filterApplied, setFilterApplied] = useState(false);
  const [fromDate, setFromDate] = useState<Date | null>(null);
  const [toDate, setToDate] = useState<Date | null>(null);
  const [showFromPicker, setShowFromPicker] = useState(false);
  const [showToPicker, setShowToPicker] = useState(false);

  // Cancelled bookings from store
  const [cancelledBookings, setCancelledBookings] = useState<Booking[]>([]);
  useEffect(() => {
    const unsub = bookingStore.subscribe((updated: Booking[]) => {
      const cancelled = updated.filter((b) => b.status === 'cancelled');
      setCancelledBookings(cancelled);
    });
    return () => unsub();
  }, []);

  return (
    <SafeAreaView style={styles.safeArea}>
      <ScrollView contentContainerStyle={styles.scrollContainer}>
        {/* Header */}
        <View style={styles.headerRow}>
          <Text style={styles.headerTitle}>Bookings</Text>
          <TouchableOpacity style={styles.filterBtn} onPress={() => setFilterVisible(true)}>
            <Ionicons name="filter" size={20} color="#3DC1C6" />
          </TouchableOpacity>
        </View>

        {/* Filter Modal (match Completed) */}
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
          <TouchableOpacity style={[styles.tabButton, styles.tabActive]}>
            <Text style={[styles.tabText, styles.tabTextActive]}>
              Cancelled
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tabButton]}
            onPress={() => router.push("./booking-process-return")}
          >
            <Text style={styles.tabText}>Return</Text>
          </TouchableOpacity>
        </View>

        {/* Cancelled Bookings List (filtered by modal) */}
        {(() => {
          const displayed = !filterApplied || (!filterFrom && !filterTo)
            ? cancelledBookings
            : cancelledBookings.filter((b) => {
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
              <View style={styles.emptyState}>
                <Ionicons name="briefcase-outline" size={64} color="#CCC" />
                <Text style={styles.emptyText}>No cancelled bookings</Text>
              </View>
            );
          }

          return displayed.map((booking) => (
            <CancelledBookingCard
              key={booking.id}
              booking={booking}
            />
          ));
        })()}
      </ScrollView>

    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: "#F9F9F9",
  },
  scrollContainer: {
    paddingHorizontal: 20,
    paddingTop: 60,
    paddingBottom: 120,
  },
  headerTitle: { fontSize: 22, fontWeight: "700", marginBottom: 15 },
  tabs: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 20,
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
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 12,
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

  blueDivider: {
    height: 2,
    backgroundColor: "#00B0B9",
    marginVertical: 12,
    borderRadius: 2,
  },

  headerRow: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", marginBottom: 12 },
  filterBtn: { flexDirection: "row", alignItems: "center" },
  filterPanel: { backgroundColor: "#fff", padding: 12, borderRadius: 8, marginBottom: 12, borderWidth: 1, borderColor: "#EEE" },
  filterTitle: { fontWeight: "700", marginBottom: 8 },
  filterRow: { flexDirection: "row", alignItems: "center", marginBottom: 8 },
  presetBtn: { paddingVertical: 6, paddingHorizontal: 10, borderRadius: 6, borderWidth: 1, borderColor: "#EEE", marginRight: 8 },
  presetActive: { backgroundColor: "#E8F7F8", borderColor: "#00B0B9" },
  dateRow: { flexDirection: "row", justifyContent: "space-between" },
  dateBlock: { width: "48%" },
  datePickerBox: { borderWidth: 1, borderColor: "#DDD", borderRadius: 6, padding: 10, flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  datePickerText: { color: "#333" },
  applyLarge: { backgroundColor: "#00B0B9", paddingVertical: 10, paddingHorizontal: 26, borderRadius: 8 },
  applyLargeText: { color: "#fff", fontWeight: "700" },

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
  emptyState: { justifyContent: 'center', alignItems: 'center', paddingVertical: 60 },
  emptyText: { marginTop: 12, fontSize: 16, color: '#999' },

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
  cancelledTitle: { fontWeight: "700", fontSize: 14, color: "#E53935" },
  approveTextStatic: { fontSize: 13, color: "#00B0B9", fontWeight: "600" },
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
});

