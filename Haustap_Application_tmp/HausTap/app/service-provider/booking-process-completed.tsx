import { Ionicons } from "@expo/vector-icons";
import DateTimePicker from '@react-native-community/datetimepicker';
import { router } from "expo-router";
import React, { useEffect, useState } from "react";
import {
    Modal,
    Platform,
    ScrollView,
    StyleSheet,
    Text,
    TouchableOpacity,
    View
} from "react-native";
import BookingCardFull from "../../components/BookingCardFull";
import ReportClientModal from "../../components/ReportClientModal";
import { Booking, bookingStore } from "../../src/services/bookingStore";


export default function CompletedDetailsScreen() {
  const [selectedTab, setSelectedTab] = useState("Completed");
  const [completedBookings, setCompletedBookings] = useState<Booking[]>([]);
  const [showMoreMenu, setShowMoreMenu] = useState<string | null>(null);

  // Report modal states
  const [reportModalVisible, setReportModalVisible] = useState(false);
  const [selectedBookingForReport, setSelectedBookingForReport] = useState<Booking | null>(null);

  // Filter states (modal)
  const [filterVisible, setFilterVisible] = useState(false);
  const [filterFrom, setFilterFrom] = useState<string | null>(null);
  const [filterTo, setFilterTo] = useState<string | null>(null);
  const [filterApplied, setFilterApplied] = useState(false);
  const [fromDate, setFromDate] = useState<Date | null>(null);
  const [toDate, setToDate] = useState<Date | null>(null);
  const [showFromPicker, setShowFromPicker] = useState(false);
  const [showToPicker, setShowToPicker] = useState(false);

  useEffect(() => {
    const unsubscribe = bookingStore.subscribe((updatedBookings: Booking[]) => {
      const completed = updatedBookings.filter(b => b.status === 'completed');
      setCompletedBookings(completed);
    });
    return () => unsubscribe();
  }, []);

  // compute displayed list based on filter
  const displayedBookings = (() => {
    if (!filterApplied || (!filterFrom && !filterTo)) return completedBookings;
    try {
      const from = filterFrom ? new Date(filterFrom) : null;
      const to = filterTo ? new Date(filterTo) : null;
      return completedBookings.filter((b) => {
        const bd = new Date(b.dateTime);
        if (isNaN(bd.getTime())) return false;
        if (from && bd < from) return false;
        if (to && bd > to) return false;
        return true;
      });
    } catch (e) {
      return completedBookings;
    }
  })();

  const handleTabPress = (tab: string) => {
    setSelectedTab(tab);
    try {
      switch (tab) {
        case "Pending":
          router.push("/service-provider/before-pending");
          break;
        case "Ongoing":
          router.push("/service-provider/before-ongoing");
          break;
        case "Completed":
          router.push("/service-provider/booking-process-completed");
          break;
        case "Cancelled":
          router.push("/service-provider/booking-process-cancelled");
          break;
        case "Return":
          router.push("/service-provider/booking-process-return");
          break;
        default:
          break;
      }
    } catch (e) {
      console.error("Tab navigation failed", e);
    }
  };

  return (
    <ScrollView
      style={styles.container}
      contentContainerStyle={{
        flexGrow: 1,
        paddingHorizontal: 16,
        paddingTop: 60,
        paddingBottom: 100,
      }}
    >
      {/* Header with Filter Button */}
      <View style={styles.headerRow}>
        <Text style={styles.headerTitle}>Bookings</Text>
        <TouchableOpacity style={styles.filterBtn} onPress={() => setFilterVisible(true)}>
          <Ionicons name="filter" size={20} color="#3DC1C6" />
        </TouchableOpacity>
      </View>

      {/* Filter Modal */}
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


      {/* Tabs */}
      <View style={styles.tabs}>
  <TouchableOpacity style={[styles.tabButton]} onPress={() => router.push('/service-provider/before-pending')}>
          <Text style={styles.tabText}>Pending</Text>
        </TouchableOpacity>

  <TouchableOpacity style={[styles.tabButton]} onPress={() => router.push('/service-provider/before-ongoing')}>
          <Text style={styles.tabText}>Ongoing</Text>
        </TouchableOpacity>

        <TouchableOpacity style={[styles.tabButton, styles.tabActive]} onPress={() => router.push('/service-provider/booking-process-completed')}>
          <Text style={[styles.tabText, styles.tabTextActive]}>Completed</Text>
        </TouchableOpacity>

        <TouchableOpacity style={styles.tabButton} onPress={() => router.push('/service-provider/booking-process-cancelled')}>
          <Text style={styles.tabText}>Cancelled</Text>
        </TouchableOpacity>

        <TouchableOpacity style={styles.tabButton} onPress={() => router.push('/service-provider/booking-process-return')}>
          <Text style={styles.tabText}>Return</Text>
        </TouchableOpacity>
      </View>


      {/* Completed Bookings List */}
      {displayedBookings.length > 0 ? (
        displayedBookings.map((booking) => (
          <BookingCardFull
            key={booking.id}
            booking={booking}
            showMoreMenu={showMoreMenu === booking.id}
            onToggleMoreMenu={() =>
              setShowMoreMenu(
                showMoreMenu === booking.id ? null : booking.id
              )
            }
            showFooterButtons={false}
            showPhotoUpload={false}
            showProceedButton={false}
            showPhotosReadOnly={true}
            showReportButton={true}
            onReport={() => {
              setSelectedBookingForReport(booking);
              setReportModalVisible(true);
            }}
          />
        ))
      ) : (
        <View style={styles.emptyState}>
          <Ionicons name="briefcase-outline" size={64} color="#CCC" />
          <Text style={styles.emptyText}>No completed bookings</Text>
        </View>
      )}

      {/* Report Client Modal */}
      <ReportClientModal
        visible={reportModalVisible}
        onClose={() => setReportModalVisible(false)}
        onSubmit={(reason: string, notes: string) => {
          console.log(`Report submitted for booking ${selectedBookingForReport?.id}`);
          console.log(`Reason: ${reason}`);
          console.log(`Notes: ${notes}`);
          // TODO: Send report to API
        }}
        clientName={selectedBookingForReport?.clientName}
      />

      {/* Bottom Navigation removed */}
    </ScrollView>
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
  headerTitle: {
    fontSize: 22,
    fontWeight: "700",
  },
  filterButton: {
    flexDirection: "row",
    alignItems: "center",
  },
  filterText: {
    marginLeft: 4,
    fontSize: 13,
    color: "#000",
  },
  filterBtn: {
    flexDirection: "row",
    alignItems: "center",
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
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 12,
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
  valueBold: {
    fontSize: 14,
    color: "#000",
    fontWeight: "600",
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
  rowBetween: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  totalLabel: {
    fontWeight: "700",
    fontSize: 15,
  },
  totalValue: {
    fontWeight: "700",
    fontSize: 15,
  },
  footerNote: {
    fontSize: 11,
    color: "#888",
    textAlign: "center",
    marginTop: 10,
  },
  reportBtn: {
    backgroundColor: "#00B0B9",
    borderRadius: 8,
    paddingVertical: 12,
    alignItems: "center",
    marginTop: 15,
  },
  reportText: {
    color: "#fff",
    fontWeight: "600",
    fontSize: 14,
  },
  bottomNav: {
    flexDirection: "row",
    justifyContent: "space-around",
    backgroundColor: "#fff",
    paddingVertical: 12,
    borderTopWidth: 1,
    borderColor: "#ddd",
    marginTop: 60,
  },
  navItem: {
    alignItems: "center",
  },
  navText: {
    fontSize: 12,
    color: "#000",
    marginTop: 4,
  },
  /* Filter panel styles */
  filterPanel: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#E6E6E6',
    padding: 12,
    borderRadius: 8,
    marginVertical: 12,
  },
  filterLabel: { fontWeight: '700', marginBottom: 8 },
  dateRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 8 },
  dateLabel: { width: 70, color: '#555' },
  dateBtn: { borderWidth: 1, borderColor: '#DDD', paddingVertical: 8, paddingHorizontal: 10, borderRadius: 6 },
  dateBtnText: { color: '#000' },
  filterActions: { flexDirection: 'row', justifyContent: 'flex-end', marginTop: 8 },
  applyBtn: { backgroundColor: '#00B0B9', paddingVertical: 8, paddingHorizontal: 14, borderRadius: 6, marginRight: 8 },
  applyText: { color: '#fff', fontWeight: '600' },
  closeFilterBtn: { paddingVertical: 8, paddingHorizontal: 14, borderRadius: 6, borderWidth: 1, borderColor: '#CCC' },
  closeText: { color: '#333' },
  // Modal/filter aliases used by updated UI
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
  clearBtn: { marginTop: 10, paddingVertical: 8, paddingHorizontal: 20 },
  clearText: { color: '#666' },
  emptyState: { justifyContent: 'center', alignItems: 'center', paddingVertical: 60 },
  emptyText: { marginTop: 12, fontSize: 16, color: '#999' },
});
