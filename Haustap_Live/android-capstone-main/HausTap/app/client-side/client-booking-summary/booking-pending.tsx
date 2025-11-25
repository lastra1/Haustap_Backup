import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useFocusEffect } from '@react-navigation/native';
import * as Clipboard from 'expo-clipboard';
import { useRouter } from 'expo-router';
import React, { useCallback, useEffect, useState } from 'react';
import { Alert, Modal, ScrollView, StyleSheet, Text, TextInput, TouchableOpacity, View } from 'react-native';

// Example bookings shown when no real bookings exist yet
const mockBookings = [
  {
    id: 'example-001',
    mainCategory: 'Home Cleaning',
    subCategory: 'Bungalow - Basic Cleaning',
    serviceTitle: 'Bungalow - Basic Cleaning Service',
    providerId: 'provider-1',
    providerName: 'Juan Dela Cruz',
    date: new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    address: 'BT L50 Mango st. Phase 1 Saint Joseph Village 10, barangay Langgam, City of San Pedro, Laguna 4023',
    total: 1000,
    desc: 'Basic cleaning package — includes living room, kitchen, bedroom cleaning, mopping, dusting, trash removal.',
    notes: 'Please focus on the kitchen and living room. Bring disinfectant.',
    voucherCode: 'SPRING10',
    voucherValue: 100,
    status: 'pending'
  }
];

export default function BookingPending() {
  const router = useRouter();

  // bookings state — start with mock until AsyncStorage loads real ones
  const [bookings, setBookings] = useState<typeof mockBookings>(mockBookings);
  const [expandedId, setExpandedId] = useState<string | null>(null);
  const [moreMenuId, setMoreMenuId] = useState<string | null>(null);
  const [cancelModalBookingId, setCancelModalBookingId] = useState<string | null>(null);
  const [cancelReason, setCancelReason] = useState<string | null>(null);
  const [cancelOtherText, setCancelOtherText] = useState<string>('');
  const [cancelDescription, setCancelDescription] = useState<string>('');

  const cancelReasons = [
    'Change of Schedule',
    'Service No Longer Needed',
    'Incorrect Booking Details',
    'Price Concerns',
    'Payment Issues',
    'Health/Safety Concerns',
    'Service Provider Unavailable',
    'Emergency/Personal Reasons',
    'Other/s:'
  ];

  // helper to parse price-like values
  const parsePrice = (p: any) => {
    if (!p && p !== 0) return 0;
    try {
      const num = String(p).replace(/[^0-9.]/g, "");
      return Number(num) || 0;
    } catch {
      return 0;
    }
  };

  const formatCurrency = (v: number) => `₱${v.toFixed(2)}`;

  // load bookings from AsyncStorage whenever screen is focused
  useFocusEffect(
    useCallback(() => {
      let mounted = true;
      const load = async () => {
        try {
          const raw = await AsyncStorage.getItem('HT_bookings');
          if (raw) {
            const parsed = JSON.parse(raw);
            if (Array.isArray(parsed) && mounted) {
              // show saved bookings first, then fall back to mock ones
              setBookings([...parsed, ...mockBookings]);
              return;
            }
          }
          if (mounted) setBookings(mockBookings);
        } catch (err) {
          console.warn('Failed to load bookings from storage', err);
          if (mounted) setBookings(mockBookings);
        }
      };
      load();
      return () => {
        mounted = false;
      };
    }, [])
  );

  // Dev-only: log bookings so we can verify AsyncStorage/loading state in Metro
  useEffect(() => {
    if ((global as any).__DEV__) {
      try {
        console.log('[DEV] BookingPending bookings length:', Array.isArray(bookings) ? bookings.length : 'not-array');
        console.log('[DEV] BookingPending bookings sample:', bookings && bookings.slice ? bookings.slice(0,3) : bookings);
      } catch (e) {
        console.log('[DEV] BookingPending logging failed', e);
      }
    }
  }, [bookings]);

  return (
    <View style={styles.pageContainer}>
      <ScrollView style={styles.container} contentContainerStyle={{ padding: 16, paddingBottom: 120 }}>
      <View style={styles.headerRow}>
        <Text style={styles.headerTitle}>Bookings</Text>
      </View>

      <View style={styles.tabsRow}>
        <TouchableOpacity style={[styles.tabButton, styles.tabActive]}>
          <Text style={styles.tabTextActive}>Pending</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.tabButton} onPress={() => router.push('/client-side/client-booking-summary/booking-ongoing')}>
          <Text style={styles.tabText}>Ongoing</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.tabButton} onPress={() => router.push('/client-side/client-booking-summary/booking-completed')}>
          <Text style={styles.tabText}>Completed</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.tabButton} onPress={() => router.push('/client-side/client-booking-summary/booking-cancelled')}>
          <Text style={styles.tabText}>Cancelled</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.tabButton} onPress={() => router.push('/client-side/client-booking-summary/booking-return')}>
          <Text style={styles.tabText}>Return</Text>
        </TouchableOpacity>
      </View>

    {bookings.map((b) => {
      const expanded = expandedId === b.id;
      const isInspection = String(b.mainCategory || '').toLowerCase().includes('inspection') || String(b.subCategory || '').toLowerCase().includes('inspection');
        return (
          <View key={b.id} style={styles.card}>
            <TouchableOpacity activeOpacity={0.85} onPress={() => setExpandedId(expanded ? null : b.id)}>
              <View style={styles.cardHeaderRow}>
                <View style={{ flex: 1 }}>
                  <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                    <Text style={styles.mainCategory}>{b.mainCategory}</Text>
                    <TouchableOpacity style={{ marginLeft: 8 }} onPress={() => {
                      // Try to open dialer to the provider if phone available on data
                      // mock data doesn't include a phone number; show an alert for now
                      Alert.alert('Call', 'Open dialer to contact the service provider.');
                    }}>
                      <Ionicons name="call-outline" size={18} color="#007AFF" />
                    </TouchableOpacity>
                  </View>
                  <Text style={styles.subcategory}>{b.subCategory}</Text>
                </View>
                <View style={{ alignItems: 'flex-end', minWidth: 100 }}>
                  <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                    <Text style={styles.bookingIdHeader}>{b.id}</Text>
                    <TouchableOpacity
                      style={{ marginLeft: 8 }}
                      onPress={async () => {
                        try {
                          await Clipboard.setStringAsync(b.id);
                          Alert.alert('Copied', `Booking ID ${b.id} copied to clipboard`);
                        } catch (err) {
                          console.warn('Failed to copy booking id', err);
                        }
                      }}
                    >
                      <Ionicons name="copy-outline" size={16} color="#00B0B9" />
                    </TouchableOpacity>
                  </View>
                  <Text style={styles.chev}>{expanded ? '▲' : '▼'}</Text>
                </View>
              </View>
            </TouchableOpacity>

            {/* Collapsed preview: show date/time, address (short), notes preview, total and More button */}
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

                {isInspection ? (
                  <View>
                    <View style={styles.breakdownRow}>
                      <Text style={styles.smallLabel}>Service Fee</Text>
                      <Text style={styles.smallLabel}>{formatCurrency(parsePrice(b.total))}</Text>
                    </View>
                    <View style={styles.breakdownRow}>
                      <Text style={styles.smallLabel}>Sub Total</Text>
                      <Text style={styles.smallLabel}>{formatCurrency(parsePrice(b.total))}</Text>
                    </View>
                    <View style={styles.breakdownRow}>
                      <Text style={styles.smallLabel}>Transpo fee</Text>
                      <Text style={styles.smallLabel}>+₱100.00</Text>
                    </View>
                    <View style={styles.breakdownRow}>
                      <Text style={styles.smallLabel}>Voucher Discount</Text>
                      <Text style={styles.smallLabel}>{b.voucherValue ? `- ${formatCurrency(Number(b.voucherValue))}` : '₱0.00'}</Text>
                    </View>
                    <View style={[styles.totalRow, { marginTop: 8 }]}> 
                      <Text style={styles.totalLabel}>TOTAL</Text>
                      <Text style={styles.totalValue}>{formatCurrency(Math.max(0, parsePrice(b.total) + 100 - (b.voucherValue ? Number(b.voucherValue) : 0)))}</Text>
                    </View>
                  </View>
                ) : (
                  <View style={styles.totalRow}>
                    <Text style={styles.totalLabel}>TOTAL</Text>
                    <Text style={styles.totalValue}>{formatCurrency(Math.max(0, parsePrice(b.total) + 100 - (b.voucherValue ? Number(b.voucherValue) : 0)))}</Text>
                  </View>
                )}

                <View style={styles.footerRow}>
                  <View style={{ position: 'relative' }}>
                    <TouchableOpacity
                      style={styles.moreBtn}
                      onPress={() => setMoreMenuId(moreMenuId === b.id ? null : b.id)}
                    >
                      <Text style={styles.moreText}>More</Text>
                    </TouchableOpacity>

                    {moreMenuId === b.id && (
                      <View style={styles.moreMenu}>
                        <TouchableOpacity
                          style={styles.moreMenuItem}
                          onPress={() => {
                            setMoreMenuId(null);
                            Alert.alert('Contact SP', 'Opening dialer to contact the service provider.');
                          }}
                        >
                          <Text style={styles.moreMenuText}>Contact SP</Text>
                        </TouchableOpacity>

                        <TouchableOpacity
                          style={styles.moreMenuItem}
                          onPress={() => {
                            setMoreMenuId(null);
                            setCancelModalBookingId(b.id);
                            setCancelReason(null);
                            setCancelOtherText('');
                            setCancelDescription('');
                          }}
                        >
                          <Text style={[styles.moreMenuText, { color: '#D9534F' }]}>Cancel</Text>
                        </TouchableOpacity>
                      </View>
                    )}
                  </View>
                </View>
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
                  <Text style={styles.selectedTitle}>{b.subCategory}</Text>
                  <Text style={styles.inclusions}>{b.desc || 'Basic cleaning package — includes living room, kitchen, bedroom cleaning, mopping, dusting, trash removal.'}</Text>
                </View>

                <View style={{ marginTop: 12 }}>
                  <Text style={styles.metaLabel}>Notes:</Text>
                  <Text style={[styles.notesInput, { minHeight: 40 }]}>{b.notes || '—'}</Text>
                </View>

                {/* Voucher row (matches booking-overview style) */}
                <View style={styles.voucherRowOverview}>
                  <View style={styles.voucherLeft}>
                    <Ionicons name="pricetag-outline" size={18} color="#333" style={{ marginRight: 8 }} />
                    <Text style={styles.voucherText}>{b.voucherCode ? b.voucherCode : 'Add a Voucher'}</Text>
                    {b.voucherValue ? (
                      <View style={styles.voucherBadge}>
                        <Text style={styles.voucherBadgeText}>{formatCurrency(Number(b.voucherValue))}</Text>
                      </View>
                    ) : null}
                  </View>
                </View>

                {/* Totals */}
                <View style={styles.totalsRow}>
                  <View>
                    <Text style={styles.smallLabel}>Sub Total</Text>
                    <Text style={styles.smallLabel}>Voucher Discount</Text>
                  </View>
                  <View style={{ alignItems: 'flex-end' }}>
                    <Text style={styles.smallLabel}>{formatCurrency(parsePrice(b.total))}</Text>
                    <Text style={styles.smallLabel}>{b.voucherValue ? formatCurrency(Number(b.voucherValue)) : '₱0.00'}</Text>
                  </View>
                </View>

                <View style={[styles.totalsRow, { marginTop: 6 }]}> 
                  <Text style={styles.smallLabel}>Transpo fee</Text>
                  <Text style={styles.smallLabel}>+₱100.00</Text>
                </View>

                <View style={styles.totalRow}>
                  <Text style={styles.totalLabel}>TOTAL</Text>
                  <Text style={styles.totalValue}>{formatCurrency(Math.max(0, parsePrice(b.total) + 100 - (b.voucherValue ? Number(b.voucherValue) : 0)))}</Text>
                </View>

                <Text style={styles.payNote}>Full payment will be collected directly by the service provider upon completion of the service.</Text>

                <View style={styles.footerRow}>
                  <View style={{ position: 'relative' }}>
                    <TouchableOpacity
                      style={styles.moreBtn}
                      onPress={() => setMoreMenuId(moreMenuId === b.id ? null : b.id)}
                    >
                      <Text style={styles.moreText}>More</Text>
                    </TouchableOpacity>

                    {moreMenuId === b.id && (
                      <View style={styles.moreMenu}>
                        <TouchableOpacity
                          style={styles.moreMenuItem}
                          onPress={() => {
                            setMoreMenuId(null);
                            Alert.alert('Contact SP', 'Opening dialer to contact the service provider.');
                          }}
                        >
                          <Text style={styles.moreMenuText}>Contact SP</Text>
                        </TouchableOpacity>

                        <TouchableOpacity
                          style={styles.moreMenuItem}
                          onPress={() => {
                            setMoreMenuId(null);
                            setCancelModalBookingId(b.id);
                            setCancelReason(null);
                            setCancelOtherText('');
                            setCancelDescription('');
                          }}
                        >
                          <Text style={[styles.moreMenuText, { color: '#D9534F' }]}>Cancel</Text>
                        </TouchableOpacity>
                      </View>
                    )}
                  </View>
                </View>
              </View>
            )}
          </View>
        );
      })}
      </ScrollView>

      {/* Cancel Modal (inline) */}
      <Modal
        visible={!!cancelModalBookingId}
        animationType="slide"
        transparent
        onRequestClose={() => setCancelModalBookingId(null)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <TouchableOpacity onPress={() => setCancelModalBookingId(null)}>
                <Text style={styles.backArrow}>←</Text>
              </TouchableOpacity>
              <Text style={styles.modalTitle}>Cancel booking</Text>
              <View style={{ width: 32 }} />
            </View>

            <Text style={styles.modalSubtitle}>Why are you requesting to cancel your booking?</Text>

            <Text style={[styles.modalLabel, { marginTop: 8 }]}>Reason:</Text>
            <ScrollView style={{ maxHeight: 260 }}>
              {cancelReasons.map((r) => {
                const selected = cancelReason === r || (r === 'Other/s:' && cancelReason === 'Other/s:');
                return (
                  <TouchableOpacity
                    key={r}
                    style={[styles.reasonItem, selected && styles.reasonItemSelected]}
                    onPress={() => setCancelReason(r)}
                  >
                    <Text style={[styles.reasonText, selected && { fontWeight: '700' }]}>{r}</Text>
                  </TouchableOpacity>
                );
              })}

              {cancelReason === 'Other/s:' && (
                <TextInput
                  style={styles.otherInput}
                  placeholder="Other reason"
                  value={cancelOtherText}
                  onChangeText={setCancelOtherText}
                />
              )}
            </ScrollView>

            <Text style={[styles.modalLabel, { marginTop: 12 }]}>Description:</Text>
            <TextInput
              style={styles.descriptionInput}
              multiline
              value={cancelDescription}
              onChangeText={setCancelDescription}
              placeholder=""
            />

            <TouchableOpacity
              style={styles.submitButton}
              onPress={() => {
                // Compose reason text
                const reasonText = cancelReason === 'Other/s:' ? cancelOtherText : (cancelReason || '');
                // Navigate to cancel form with params to preserve existing flow
                router.push({
                  pathname: '/client-side/client-booking-summary/cancel-booking-form',
                  params: { bookingId: cancelModalBookingId, reason: reasonText, description: cancelDescription }
                } as any);
                setCancelModalBookingId(null);
              }}
            >
              <Text style={styles.submitButtonText}>Submit</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>

      {/* Footer provided by layout */}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff' },
  headerRow: { paddingVertical: 8 },
  headerTitle: { fontSize: 20, fontWeight: '700' },
  tabsRow: { flexDirection: 'row', marginVertical: 12, gap: 8 },
  tabButton: { paddingVertical: 6, paddingHorizontal: 10, borderRadius: 8, backgroundColor: '#f0f0f0' },
  tabActive: { backgroundColor: '#fff' },
  tabText: { color: '#666' },
  tabTextActive: { color: '#3DC1C6', fontWeight: '700' },
  card: { backgroundColor: '#fff', borderRadius: 8, padding: 12, marginBottom: 12, borderWidth: 1, borderColor: '#E0E0E0',
    shadowColor: '#000', shadowOffset: { width: 0, height: 8 }, shadowOpacity: 0.22, shadowRadius: 12, elevation: 10 },
  cardHeaderRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  mainCategory: { fontSize: 18, fontWeight: '700' },
  subcategory: { fontSize: 14, color: '#666', marginTop: 6 },
  bookingId: { fontSize: 12, color: '#666', marginTop: 4 },
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
  notesInput: { fontSize: 14, color: '#333', padding: 10, backgroundColor: '#fff', borderWidth: 1, borderColor: '#EAEAEA', borderRadius: 6 },
  voucherRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 12 },
  voucherLeft: { flexDirection: 'row', alignItems: 'center' },
  voucherText: { fontSize: 14, color: '#333' },
  voucherRowOverview: { backgroundColor: '#fff', borderRadius: 8, borderWidth: 1, borderColor: '#E0E0E0', paddingVertical: 8, paddingHorizontal: 12, marginTop: 12, flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  voucherBadge: { backgroundColor: '#00ADB5', paddingHorizontal: 8, paddingVertical: 4, borderRadius: 12, marginLeft: 8 },
  voucherBadgeText: { color: '#fff', fontWeight: '700', fontSize: 12 },
  smallLabel: { fontSize: 13, color: '#666' },
  totalsRow: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 12 },
  totalRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 10, paddingTop: 10, borderTopWidth: 1, borderTopColor: '#F0F0F0' },
  totalLabel: { fontSize: 16, fontWeight: '800', color: '#333' },
  totalValue: { fontSize: 16, fontWeight: '800', color: '#000' },
  payNote: { fontSize: 12, color: '#666', marginTop: 8 },
  footerRow: { flexDirection: 'row', justifyContent: 'flex-end', gap: 8, marginTop: 12 },
  cancelBtn: { backgroundColor: '#eee', paddingHorizontal: 12, paddingVertical: 8, borderRadius: 6 },
  cancelText: { color: '#666', fontWeight: '600' },
  viewBtn: { backgroundColor: '#3DC1C6', paddingHorizontal: 12, paddingVertical: 8, borderRadius: 6 },
  viewText: { color: '#fff', fontWeight: '700' },
  bookingIdHeader: { fontSize: 13, color: '#333', fontWeight: '700' },
  moreBtn: { backgroundColor: '#eee', paddingHorizontal: 12, paddingVertical: 8, borderRadius: 6 },
  moreText: { color: '#333', fontWeight: '700' },
  moreMenu: { position: 'absolute', right: 12, top: '100%', marginTop: 4, backgroundColor: '#fff', borderWidth: 1, borderColor: '#E0E0E0', borderRadius: 8, paddingVertical: 6, width: 140, zIndex: 99, shadowColor: '#000', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.15, shadowRadius: 4, elevation: 5 },
  moreMenuItem: { paddingVertical: 10, paddingHorizontal: 12 },
  moreMenuText: { fontSize: 14, color: '#333' },
  // footer handled by layout
  pageContainer: { flex: 1, backgroundColor: '#fff' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'flex-end' },
  modalContent: { backgroundColor: '#fff', padding: 16, borderTopLeftRadius: 12, borderTopRightRadius: 12, maxHeight: '90%' },
  modalHeader: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: 8 },
  backArrow: { fontSize: 20, color: '#333' },
  modalTitle: { fontSize: 18, fontWeight: '700' },
  modalSubtitle: { fontSize: 12, color: '#666', marginBottom: 8 },
  modalLabel: { fontSize: 14, color: '#333' },
  reasonItem: { paddingVertical: 12, paddingHorizontal: 8, borderWidth: 1, borderColor: '#EFEFEF', borderRadius: 6, marginTop: 8 },
  reasonItemSelected: { borderColor: '#3DC1C6', backgroundColor: '#F0FFFE' },
  reasonText: { fontSize: 14, color: '#333' },
  otherInput: { borderWidth: 1, borderColor: '#EAEAEA', borderRadius: 6, padding: 10, marginTop: 8 },
  descriptionInput: { borderWidth: 1, borderColor: '#EAEAEA', borderRadius: 6, padding: 10, height: 100, marginTop: 8 },
  submitButton: { backgroundColor: '#3DC1C6', paddingVertical: 12, borderRadius: 8, marginTop: 12, alignItems: 'center' },
  submitButtonText: { color: '#fff', fontWeight: '700' },
  breakdownRow: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 6 },
});
