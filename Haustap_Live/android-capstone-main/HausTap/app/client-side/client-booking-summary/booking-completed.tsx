import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import DateTimePicker from '@react-native-community/datetimepicker';
import { useFocusEffect } from '@react-navigation/native';
import * as Clipboard from 'expo-clipboard';
import { useRouter } from 'expo-router';
import React, { useCallback, useState } from 'react';
import { Alert, Modal, Platform, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

// Example completed booking shown when no stored completed bookings exist
const mockCompleted = [
  {
    id: 'completed-001',
    mainCategory: 'Home Cleaning',
    subCategory: 'Bungalow - Basic Cleaning',
    serviceTitle: 'Bungalow - Basic Cleaning',
    providerId: 'provider-1',
    providerName: 'Ana Santos',
    providerRating: 4.5,
    date: new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    address: 'B1 L50 Mango St., Saint Joseph Village 10, San Pedro, Laguna',
    total: 1000,
    desc: 'Basic Cleaning — living room, kitchen, bedroom, mopping, dusting.',
    notes: '',
    voucherCode: 'Referral Voucher',
    voucherValue: 10,
    status: 'completed',
    completedAt: new Date().toISOString(), // Add timestamp when booking is completed
    isRated: false,
    conversationId: 'completed-001', // Using booking ID as conversation ID
  },
];

export default function BookingCompleted() {
  const router = useRouter();
  const [bookings, setBookings] = useState<typeof mockCompleted>(mockCompleted);
  const [expandedId, setExpandedId] = useState<string | null>(null);
  const [showMoreOptions, setShowMoreOptions] = useState<string | null>(null);
  const [filterVisible, setFilterVisible] = useState(false);
  const [filterFrom, setFilterFrom] = useState<string | null>(null);
  const [filterTo, setFilterTo] = useState<string | null>(null);
  const [filterApplied, setFilterApplied] = useState(false);
  // date picker states
  const [fromDate, setFromDate] = useState<Date | null>(null);
  const [toDate, setToDate] = useState<Date | null>(null);
  const [showFromPicker, setShowFromPicker] = useState(false);
  const [showToPicker, setShowToPicker] = useState(false);

  useFocusEffect(
    useCallback(() => {
      let mounted = true;
      const load = async () => {
        try {
          const raw = await AsyncStorage.getItem('HT_bookings');
          if (raw) {
            const parsed = JSON.parse(raw);
            if (Array.isArray(parsed) && mounted) {
              const completed = parsed.filter((b: any) => b.status === 'completed');
              setBookings(completed.length ? completed : mockCompleted);
              return;
            }
          }
          if (mounted) setBookings(mockCompleted);
        } catch (err) {
          console.warn('Failed to load bookings', err);
          if (mounted) setBookings(mockCompleted);
        }
      };
      load();
      return () => {
        mounted = false;
      };
    }, [])
  );

  const formatCurrency = (v: number) => '₱' + v.toFixed(2);

  const handleRate = (bookingId: string) => {
    const booking = bookings.find(b => b.id === bookingId);
    router.push({ 
      pathname: '/client-side/client-booking-summary/rate-service-form',
      params: { bookingId, bookingData: JSON.stringify(booking) }
    } as any);
  };

  const handleReturn = (bookingId: string) => {
    const booking = bookings.find(b => b.id === bookingId);
    router.push({ 
      pathname: '/client-side/client-booking-summary/return-request-form',
      params: { bookingId, bookingData: JSON.stringify(booking) }
    } as any);
    setShowMoreOptions(null);
  };

  const handleReport = (bookingId: string) => {
    const booking = bookings.find(b => b.id === bookingId);
    router.push({ 
      pathname: '/client-side/client-booking-summary/report-form',
      params: { bookingId, bookingData: JSON.stringify(booking) }
    } as any);
    setShowMoreOptions(null);
  };

  // compute displayed list based on filter
  const displayedBookings = (() => {
    if (!filterApplied || (!filterFrom && !filterTo)) return bookings;
    try {
      const from = filterFrom ? new Date(filterFrom) : null;
      const to = filterTo ? new Date(filterTo) : null;
      return bookings.filter((b) => {
        const bd = new Date(b.date);
        if (isNaN(bd.getTime())) return false;
        if (from && bd < from) return false;
        if (to && bd > to) return false;
        return true;
      });
    } catch (e) {
      return bookings;
    }
  })();

  return (
    <View style={styles.pageContainer}>
      <ScrollView style={styles.container} contentContainerStyle={{ padding: 16, paddingBottom: 140 }}>
        <View style={styles.headerRow}>
          <Text style={styles.headerTitle}>Bookings</Text>
          <TouchableOpacity style={styles.filterBtn} onPress={() => setFilterVisible(true)}>
            <Ionicons name="filter" size={20} color="#3DC1C6" />
          </TouchableOpacity>
        </View>
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
                    // on Android the picker closes automatically; on iOS keep open while user interacts
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
              <Text style={styles.modalLabel}>Return:</Text>
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

        <View style={styles.tabsRow}>
          <ScrollView horizontal showsHorizontalScrollIndicator={false}>
            <TouchableOpacity style={styles.tabButton} onPress={() => router.push('/client-side/client-booking-summary/booking-pending')}>
              <Text style={styles.tabText}>Pending</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.tabButton} onPress={() => router.push('/client-side/client-booking-summary/booking-ongoing')}>
              <Text style={styles.tabText}>Ongoing</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[styles.tabButton, styles.tabActive]}>
              <Text style={styles.tabTextActive}>Completed</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.tabButton} onPress={() => router.push('/client-side/client-booking-summary/booking-cancelled')}>
              <Text style={styles.tabText}>Cancelled</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.tabButton} onPress={() => router.push('/client-side/client-booking-summary/booking-return')}>
              <Text style={styles.tabText}>Return</Text>
            </TouchableOpacity>
          </ScrollView>
        </View>

  {displayedBookings.map((b) => {
          const expanded = expandedId === b.id;
          return (
            <View key={b.id} style={styles.card}>
              <TouchableOpacity activeOpacity={0.85} onPress={() => setExpandedId(expanded ? null : b.id)}>
                <View style={styles.cardHeaderRow}>
                  <View style={{ flex: 1 }}>
                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                      <Text style={styles.mainCategory}>{b.mainCategory}</Text>
                      <TouchableOpacity style={{ marginLeft: 8 }} onPress={() => {
                        Alert.alert('Call', 'Open dialer to contact the service provider.');
                      }}>
                        <Ionicons name="call-outline" size={18} color="#007AFF" />
                      </TouchableOpacity>
                    </View>
                    <Text style={styles.subcategory}>{b.subCategory}</Text>
                  </View>
                  <View style={{ alignItems: 'flex-end', minWidth: 90 }}>
                    {b.id && (
                      <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                        <Text style={styles.bookingIdHeader}>{b.id}</Text>
                        <TouchableOpacity style={{ marginLeft: 8 }} onPress={async () => {
                          try { await Clipboard.setStringAsync(b.id); Alert.alert('Copied', `Booking ID ${b.id} copied to clipboard`); } catch (e) { console.warn(e); }
                        }}>
                          <Ionicons name="copy-outline" size={16} color="#00B0B9" />
                        </TouchableOpacity>
                      </View>
                    )}
                    <Text style={styles.chev}>{expanded ? '▲' : '▼'}</Text>
                  </View>
                </View>
              </TouchableOpacity>

              {!expanded && (
                <View>
                  <View style={styles.divider} />
                  <View style={styles.rowSmall}>
                    <View style={styles.rowCol}>
                      <Text style={styles.metaLabel}>Date</Text>
                      <Text style={styles.metaValue}>{b.date}</Text>
                    </View>
                    <View style={styles.vertSeparator} />
                    <View style={styles.rowCol}>
                      <Text style={styles.metaLabel}>Time</Text>
                      <Text style={styles.metaValue}>{b.time}</Text>
                    </View>
                  </View>

                  <View style={[styles.addressBox, { marginTop: 12 }]}> 
                    <Text style={styles.metaLabel}>Address</Text>
                    <Text style={styles.addressText} numberOfLines={1}>{b.address}</Text>
                  </View>

                  <View style={{ marginTop: 12 }}>
                    <Text style={styles.metaLabel}>Notes:</Text>
                    <Text style={[styles.notesInput, { minHeight: 40 }]} numberOfLines={2}>{b.notes || '—'}</Text>
                  </View>

                  <View style={styles.totalRow}>
                    <Text style={styles.totalLabel}>TOTAL</Text>
                    <Text style={styles.totalValue}>{formatCurrency(Math.max(0, Number(b.total || 0) + 100 - (b.voucherValue ? Number(b.voucherValue) : 0)))}</Text>
                  </View>

                  <View style={[styles.actionRow, { marginTop: 12 }]}>
                    {!b.isRated && (
                      <TouchableOpacity 
                        style={[styles.actionButton, styles.rateButton]} 
                        onPress={() => handleRate(b.id)}
                      >
                        <Text style={styles.rateButtonText}>Rate</Text>
                      </TouchableOpacity>
                    )}
                    <TouchableOpacity 
                      style={[styles.actionButton, styles.moreButton]} 
                      onPress={() => setShowMoreOptions(showMoreOptions === b.id ? null : b.id)}
                    >
                      <Text style={styles.moreButtonText}>More</Text>
                    </TouchableOpacity>

                    {showMoreOptions === b.id && (
                      <View style={styles.moreOptionsPopup}>
                        <TouchableOpacity
                          style={styles.moreOption}
                          onPress={() => handleReturn(b.id)}
                        >
                          <Text style={styles.moreOptionText}>Return</Text>
                        </TouchableOpacity>
                        <TouchableOpacity
                          style={styles.moreOption}
                          onPress={() => handleReport(b.id)}
                        >
                          <Text style={styles.moreOptionText}>Report</Text>
                        </TouchableOpacity>
                      </View>
                    )}
                  </View>

                  <Text style={styles.reminderText}>Reminder: You may request a return for free within 24 hours after the service. After 24 hours, a ₱300 return fee will be charged.</Text>
                </View>
              )}

              {expanded && (
                <View>
                  <View style={styles.divider} />

                  <View style={styles.rowSmall}>
                    <View style={styles.rowCol}>
                      <Text style={styles.metaLabel}>Date</Text>
                      <Text style={styles.metaValue}>{b.date}</Text>
                    </View>
                    <View style={styles.vertSeparator} />
                    <View style={styles.rowCol}>
                      <Text style={styles.metaLabel}>Time</Text>
                      <Text style={styles.metaValue}>{b.time}</Text>
                    </View>
                  </View>

                  <View style={[styles.addressBox, { marginTop: 12 }]}> 
                    <Text style={styles.metaLabel}>Address</Text>
                    <Text style={styles.addressText}>{b.address}</Text>
                  </View>

                  <View style={[styles.selectedBox, { marginTop: 12 }]}>
                    <Text style={[styles.metaLabel, { marginBottom: 6 }]}>You selected</Text>
                    <Text style={styles.selectedTitle}>{b.serviceTitle || b.subCategory}</Text>
                    <Text style={styles.inclusions}>{b.desc}</Text>
                  </View>

                  <View style={{ marginTop: 12 }}>
                    <Text style={styles.metaLabel}>Notes:</Text>
                    <View style={styles.notesInput}>
                      <Text style={styles.notesText}>{b.notes || 'No notes added.'}</Text>
                    </View>
                  </View>

                  {/* Referral Voucher row */}
                  <View style={styles.voucherRowOverview}>
                    <View style={styles.voucherLeft}>
                      <Ionicons name="pricetag-outline" size={18} color="#333" style={{ marginRight: 8 }} />
                      <Text style={styles.voucherText}>{b.voucherCode ? b.voucherCode : 'Add Referral Voucher'}</Text>
                      {b.voucherValue ? (
                        <View style={styles.voucherBadge}>
                          <Text style={styles.voucherBadgeText}>{formatCurrency(Number(b.voucherValue))}</Text>
                        </View>
                      ) : null}
                    </View>
                  </View>

                  <View style={styles.totalsRow}>
                    <View>
                      <Text style={styles.smallLabel}>Sub Total</Text>
                      <Text style={styles.smallLabel}>Voucher Discount</Text>
                    </View>
                    <View style={{ alignItems: 'flex-end' }}>
                      <Text style={styles.smallLabel}>{formatCurrency(Number(b.total || 0))}</Text>
                      <Text style={styles.smallLabel}>{b.voucherValue ? formatCurrency(Number(b.voucherValue)) : '₱0.00'}</Text>
                    </View>
                  </View>

                  <View style={[styles.totalsRow, { marginTop: 6 }]}> 
                    <Text style={styles.smallLabel}>Transpo fee</Text>
                    <Text style={styles.smallLabel}>+₱100.00</Text>
                  </View>

                  <View style={styles.totalRow}>
                    <Text style={styles.totalLabel}>TOTAL</Text>
                    <Text style={styles.totalValue}>{formatCurrency(Math.max(0, Number(b.total || 0) + 100 - (b.voucherValue ? Number(b.voucherValue) : 0)))}</Text>
                  </View>
                  
                  <Text style={styles.payNote}>Full payment will be collected directly by the service provider upon completion of the service.</Text>

                  <View style={[styles.divider, { marginVertical: 16 }]} />

                  <View style={styles.providerRow}>
                    <View style={{ flexDirection: 'row', alignItems: 'flex-start' }}>
                      <Ionicons name="person-circle" size={36} color="#333" />
                      <View style={{ marginLeft: 10 }}>
                        <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                          <Text style={styles.providerName}>{b.providerName}</Text>
                          <TouchableOpacity
                              style={styles.messageBtn}
                              onPress={() => router.push({ 
                                pathname: '/client-side/chat', 
                                params: { 
                                  conversationId: b.id,  // Use booking ID as conversation ID
                                  providerId: b.providerId,
                                  providerName: b.providerName
                                } 
                              } as any)}
                            >
                              <Ionicons name="chatbubble-ellipses-outline" size={18} color="#3DC1C6" />
                            </TouchableOpacity>
                        </View>
                        <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 4 }}>
                          <Ionicons name="star" size={14} color="#FFD700" />
                          <Text style={styles.ratingText}>{b.providerRating ?? '—'}</Text>
                        </View>
                      </View>
                    </View>
                  </View>

                  <Text style={styles.reminderText}>
                    Reminder: You may request a return for free within 24 hours after the service. After 24 hours, a ₱300 return fee will be charged.
                  </Text>

                  <View style={[styles.actionRow, { marginTop: 16 }]}>
                    {!b.isRated && (
                      <TouchableOpacity 
                        style={[styles.actionButton, styles.rateButton]} 
                        onPress={() => handleRate(b.id)}
                      >
                        <Text style={styles.rateButtonText}>Rate</Text>
                      </TouchableOpacity>
                    )}
                    <TouchableOpacity 
                      style={[styles.actionButton, styles.moreButton]} 
                      onPress={() => setShowMoreOptions(showMoreOptions === b.id ? null : b.id)}
                    >
                      <Text style={styles.moreButtonText}>More</Text>
                    </TouchableOpacity>

                    {showMoreOptions === b.id && (
                      <View style={styles.moreOptionsPopup}>
                        <TouchableOpacity
                          style={styles.moreOption}
                          onPress={() => handleReturn(b.id)}
                        >
                          <Text style={styles.moreOptionText}>Return</Text>
                        </TouchableOpacity>
                        <TouchableOpacity
                          style={styles.moreOption}
                          onPress={() => handleReport(b.id)}
                        >
                          <Text style={styles.moreOptionText}>Report</Text>
                        </TouchableOpacity>
                      </View>
                    )}
                  </View>
                </View>
              )}
            </View>
          );
        })}
      </ScrollView>

      {/* Footer provided by layout */}
    </View>
  );
}

const styles = StyleSheet.create({
  pageContainer: { flex: 1, backgroundColor: '#fff' },
  container: { flex: 1 },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingVertical: 8, paddingHorizontal: 16 },
  headerTitle: { fontSize: 20, fontWeight: '700' },
  tabsRow: { flexDirection: 'row', marginVertical: 12, gap: 8, paddingHorizontal: 16 },
  tabButton: { paddingVertical: 6, paddingHorizontal: 10, borderRadius: 8, backgroundColor: '#f0f0f0' },
  tabActive: { backgroundColor: '#fff' },
  tabText: { color: '#666' },
  tabTextActive: { color: '#3DC1C6', fontWeight: '700' },
  card: { 
    backgroundColor: '#fff', 
    borderRadius: 8, 
    padding: 12, 
    marginBottom: 12, 
    borderWidth: 1, 
    borderColor: '#E0E0E0', 
    marginHorizontal: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.22,
    shadowRadius: 12,
    elevation: 10,
  },
  cardHeaderRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  mainCategory: { fontSize: 18, fontWeight: '700' },
  subcategory: { fontSize: 14, color: '#666', marginTop: 6 },
  priceText: { fontSize: 16, fontWeight: '700', color: '#00ADB5' },
  chev: { fontSize: 12, color: '#999', marginTop: 6 },
  divider: { height: 1, backgroundColor: '#E0E0E0', marginVertical: 12 },
  rowSmall: { flexDirection: 'row', justifyContent: 'space-between' },
  rowCol: { flex: 1 },
  metaLabel: { fontSize: 12, color: '#666' },
  metaValue: { fontSize: 14, fontWeight: '600', marginTop: 4 },
  vertSeparator: { width: 1, backgroundColor: '#E0E0E0', marginHorizontal: 12, alignSelf: 'stretch' },
  addressBox: { backgroundColor: '#f9f9f9', padding: 10, borderRadius: 6, borderWidth: 1, borderColor: '#EAEAEA' },
  addressText: { fontSize: 13, color: '#333', marginTop: 6 },
  selectedBox: { backgroundColor: '#fafafa', padding: 10, borderRadius: 6, borderWidth: 1, borderColor: '#EFEFEF' },
  selectedTitle: { fontSize: 16, fontWeight: '700', marginTop: 6 },
  inclusions: { fontSize: 13, color: '#666', marginTop: 8 },
  notesInput: { backgroundColor: '#f9f9f9', padding: 10, borderRadius: 6, borderWidth: 1, borderColor: '#EAEAEA' },
  notesText: { fontSize: 13, color: '#666' },
  voucherRowOverview: { backgroundColor: '#fff', borderRadius: 8, borderWidth: 1, borderColor: '#E0E0E0', paddingVertical: 8, paddingHorizontal: 12, marginTop: 12, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  voucherLeft: { flexDirection: 'row', alignItems: 'center' },
  voucherText: { fontSize: 14, color: '#333' },
  voucherBadge: { backgroundColor: '#00ADB5', paddingHorizontal: 8, paddingVertical: 4, borderRadius: 12, marginLeft: 8 },
  voucherBadgeText: { color: '#fff', fontWeight: '700', fontSize: 12 },
  bookingId: { fontSize: 12, color: '#666', marginTop: 4 },
  smallLabel: { fontSize: 13, color: '#666' },
  totalsRow: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 12 },
  totalRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 10 },
  actionRow: { flexDirection: 'row', justifyContent: 'flex-end', gap: 8 },
  totalLabel: { fontSize: 16, fontWeight: '800', color: '#333' },
  totalValue: { fontSize: 16, fontWeight: '800', color: '#000' },
  payNote: { fontSize: 11, color: '#666', marginTop: 8, textAlign: 'center', paddingHorizontal: 20 },
  providerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 12 },
  providerName: { fontSize: 14, fontWeight: '700' },
  ratingText: { fontSize: 13, color: '#666', marginLeft: 6 },
  messageBtn: { marginLeft: 8, padding: 6, borderRadius: 20, backgroundColor: '#E8F8F8' },
  filterBtn: { padding: 8 },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.35)', justifyContent: 'center', alignItems: 'center' },
  modalContent: { width: 320, backgroundColor: '#fff', padding: 20, borderRadius: 8, alignItems: 'center' },
  modalTitle: { fontSize: 16, fontWeight: '700' },
  modalLabel: { fontSize: 13, color: '#333', marginBottom: 6 },
  dateInput: { width: '100%', borderWidth: 1, borderColor: '#E0E0E0', borderRadius: 6, padding: 8, backgroundColor: '#fff' },
  applyBtn: { marginTop: 16, backgroundColor: '#3DC1C6', paddingVertical: 10, paddingHorizontal: 28, borderRadius: 8 },
  applyText: { color: '#fff', fontWeight: '700' },
  clearBtn: { marginTop: 10, paddingVertical: 8, paddingHorizontal: 20 },
  clearText: { color: '#666' },
  
  // Action buttons
  actionButtons: { 
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    position: 'relative'
  },
  actionButton: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
    borderWidth: 1,
  },
  rateButton: {
    backgroundColor: '#3DC1C6',
    borderColor: '#3DC1C6',
  },
  moreButton: {
    backgroundColor: '#fff',
    borderColor: '#3DC1C6',
  },
  rateButtonText: {
    color: '#fff',
    fontWeight: '600',
    fontSize: 14,
  },
  moreButtonText: {
    color: '#3DC1C6',
    fontWeight: '600',
    fontSize: 14,
  },
  moreOptionsPopup: {
    position: 'absolute',
    top: '100%',
    right: 0,
    backgroundColor: '#fff',
    borderRadius: 6,
    padding: 4,
    borderWidth: 1,
    borderColor: '#E0E0E0',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
    zIndex: 1000,
    minWidth: 100,
    marginTop: 4, // Add some space between button and popup
  },
  reminderText: {
    fontSize: 10,
    color: '#666',
    textAlign: 'center',
    marginTop: 16,
    paddingHorizontal: 20,
    fontStyle: 'italic',
  },
  moreOption: {
    alignItems: 'center',
    padding: 8,
    borderRadius: 4,
  },
  moreOptionText: {
    fontSize: 14,
    color: '#666',
  },
  bookingIdHeader: { fontSize: 13, color: '#333', fontWeight: '700' },

  // footer handled by layout
});
