import AsyncStorage from '@react-native-async-storage/async-storage';
import { useFocusEffect, useRouter } from 'expo-router';
import React, { useCallback, useState } from 'react';
import { Alert, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { completeBooking } from '../../src/services/bookingService';

// Example ongoing booking shown when no stored bookings exist
const mockOngoing = [{
  id: 'ongoing-001',
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
  voucherCode: '',
  voucherValue: 0,
  status: 'ongoing'
}];

export default function ProviderOngoing() {
  const router = useRouter();
  const [bookings, setBookings] = useState(mockOngoing);
  const [expandedId, setExpandedId] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  useFocusEffect(
    useCallback(() => {
      let mounted = true;
      const load = async () => {
        try {
          const raw = await AsyncStorage.getItem('HT_bookings');
          if (raw) {
            const parsed = JSON.parse(raw);
            if (Array.isArray(parsed) && mounted) {
              // Only show ongoing bookings for the current provider
              // TODO: Replace 'provider-1' with actual provider ID from auth
              const ongoing = parsed.filter((b: any) => 
                b.status === 'ongoing' && b.providerId === 'provider-1'
              );
              setBookings(ongoing.length ? ongoing : mockOngoing);
              return;
            }
          }
          if (mounted) setBookings(mockOngoing);
        } catch (err) {
          console.warn('Failed to load bookings', err);
          if (mounted) setBookings(mockOngoing);
        }
      };
      load();
      return () => {
        mounted = false;
      };
    }, [])
  );

  const handleComplete = async (bookingId: string) => {
    setLoading(true);
    try {
      const success = await completeBooking(bookingId);
      if (success) {
        // Refresh bookings list
        setBookings(prev => prev.filter(b => b.id !== bookingId));
        Alert.alert(
          'Success',
          'Service has been marked as completed.',
          [{ text: 'OK' }]
        );
      } else {
        Alert.alert('Error', 'Could not mark service as completed.');
      }
    } catch (err) {
      console.error('Failed to complete booking:', err);
      Alert.alert('Error', 'Could not mark service as completed.');
    } finally {
      setLoading(false);
    }
  };

  const formatCurrency = (v: number) => '₱' + v.toFixed(2);

  return (
    <View style={styles.pageContainer}>
      <ScrollView style={styles.container} contentContainerStyle={{ padding: 16, paddingBottom: 140 }}>
        <View style={styles.headerRow}>
          <Text style={styles.headerTitle}>Ongoing Services</Text>
        </View>

        {bookings.map((b) => {
          const expanded = expandedId === b.id;
          return (
            <View key={b.id} style={styles.card}>
              <TouchableOpacity activeOpacity={0.85} onPress={() => setExpandedId(expanded ? null : b.id)}>
                <View style={styles.cardHeaderRow}>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.mainCategory}>{b.mainCategory}</Text>
                    <Text style={styles.subcategory}>{b.subCategory}</Text>
                    <Text style={styles.bookingId}>Booking ID: {b.id}</Text>
                  </View>
                  <View style={{ alignItems: 'flex-end' }}>
                    <Text style={styles.priceText}>{formatCurrency(b.total)}</Text>
                    <Text style={styles.chev}>{expanded ? '▲' : '▼'}</Text>
                  </View>
                </View>
              </TouchableOpacity>

              {!expanded && (
                <View style={[styles.actionRow, { marginTop: 12 }]}>
                  <TouchableOpacity 
                    style={[styles.actionButton, styles.completeButton]}
                    onPress={() => {
                      Alert.alert(
                        'Complete Service',
                        'Are you sure you want to mark this service as completed?',
                        [
                          { text: 'Cancel', style: 'cancel' },
                          { text: 'Complete', onPress: () => handleComplete(b.id) }
                        ]
                      );
                    }}
                    disabled={loading}
                  >
                    <Text style={styles.completeButtonText}>
                      {loading ? 'Loading...' : 'Mark Complete'}
                    </Text>
                  </TouchableOpacity>
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
                    <Text style={[styles.metaLabel, { marginBottom: 6 }]}>Service Details</Text>
                    <Text style={styles.selectedTitle}>{b.serviceTitle || b.subCategory}</Text>
                    <Text style={styles.inclusions}>{b.desc}</Text>
                  </View>

                  <View style={{ marginTop: 12 }}>
                    <Text style={styles.metaLabel}>Notes:</Text>
                    <View style={styles.notesInput}>
                      <Text style={styles.notesText}>{b.notes || 'No notes added.'}</Text>
                    </View>
                  </View>

                  <View style={styles.totalsRow}>
                    <View>
                      <Text style={styles.smallLabel}>Sub Total</Text>
                      <Text style={styles.smallLabel}>Voucher Discount</Text>
                    </View>
                    <View style={{ alignItems: 'flex-end' }}>
                      <Text style={styles.smallLabel}>{formatCurrency(b.total)}</Text>
                      <Text style={styles.smallLabel}>{b.voucherValue ? formatCurrency(b.voucherValue) : '₱0.00'}</Text>
                    </View>
                  </View>

                  <View style={styles.totalRow}>
                    <Text style={styles.totalLabel}>TOTAL</Text>
                    <Text style={styles.totalValue}>
                      {formatCurrency(Math.max(0, b.total - (b.voucherValue || 0)))}
                    </Text>
                  </View>

                  <View style={[styles.actionRow, { marginTop: 16 }]}>
                    <TouchableOpacity 
                      style={[styles.actionButton, styles.completeButton]}
                      onPress={() => {
                        Alert.alert(
                          'Complete Service',
                          'Are you sure you want to mark this service as completed?',
                          [
                            { text: 'Cancel', style: 'cancel' },
                            { text: 'Complete', onPress: () => handleComplete(b.id) }
                          ]
                        );
                      }}
                      disabled={loading}
                    >
                      <Text style={styles.completeButtonText}>
                        {loading ? 'Loading...' : 'Mark Complete'}
                      </Text>
                    </TouchableOpacity>
                  </View>
                </View>
              )}
            </View>
          );
        })}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  pageContainer: { flex: 1, backgroundColor: '#fff' },
  container: { flex: 1 },
  headerRow: { paddingVertical: 8 },
  headerTitle: { fontSize: 20, fontWeight: '700' },
  card: { backgroundColor: '#fff', borderRadius: 8, padding: 12, marginBottom: 12, borderWidth: 1, borderColor: '#E0E0E0' },
  cardHeaderRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  mainCategory: { fontSize: 18, fontWeight: '700' },
  subcategory: { fontSize: 14, color: '#666', marginTop: 6 },
  bookingId: { fontSize: 12, color: '#666', marginTop: 4 },
  priceText: { fontSize: 16, fontWeight: '700', color: '#00ADB5' },
  chev: { fontSize: 12, color: '#999', marginTop: 6 },

  // Actions
  actionRow: { flexDirection: 'row', justifyContent: 'flex-end', gap: 8 },
  actionButton: { paddingVertical: 8, paddingHorizontal: 16, borderRadius: 8 },
  completeButton: { backgroundColor: '#3DC1C6' },
  completeButtonText: { color: '#fff', fontWeight: '600', fontSize: 14 },

  // Expanded content
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
  smallLabel: { fontSize: 13, color: '#666' },
  totalsRow: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 12 },
  totalRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 10 },
  totalLabel: { fontSize: 16, fontWeight: '800', color: '#333' },
  totalValue: { fontSize: 16, fontWeight: '700', color: '#00ADB5' },
});