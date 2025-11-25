import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useEffect, useState } from 'react';
import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';

export default function RatingSummary() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const bookingId = (params as any).bookingId as string | undefined;
  const ratingParam = Number((params as any).rating) || undefined;

  const [providerName, setProviderName] = useState('');
  const [rating, setRating] = useState<number | undefined>(ratingParam);

  useEffect(() => {
    let mounted = true;
    const load = async () => {
      if (!bookingId) return;
      try {
        const raw = await AsyncStorage.getItem('HT_bookings');
        if (raw) {
          const parsed = JSON.parse(raw);
          if (Array.isArray(parsed)) {
            const found = parsed.find((b: any) => b.id === bookingId);
            if (found && mounted) {
              setProviderName(found.providerName ?? 'Service Provider');
            }
          }
        }
      } catch (err) {
        console.warn('Failed to load booking in rating summary', err);
      }
    };
    load();
    return () => { mounted = false; };
  }, [bookingId]);

  return (
    <View style={styles.page}>
      <View style={styles.headerRow}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Thank you</Text>
        <View style={{ width: 24 }} />
      </View>

      <View style={styles.content}>
        <Text style={styles.thanksText}>Your review was submitted</Text>
        {providerName ? <Text style={styles.providerName}>{providerName}</Text> : null}
        {rating ? (
          <View style={styles.starsRow}>
            {Array.from({ length: 5 }).map((_, i) => (
              <Ionicons key={i} name={i < rating ? 'star' : 'star-outline'} size={28} color="#FFD700" style={{ marginHorizontal: 4 }} />
            ))}
          </View>
        ) : null}

        <TouchableOpacity style={styles.doneBtn} onPress={() => router.push('/client-side/client-booking-summary/booking-completed')}>
          <Text style={styles.doneText}>Done</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  page: { flex: 1, backgroundColor: '#fff' },
  headerRow: { flexDirection: 'row', alignItems: 'center', padding: 16, justifyContent: 'space-between' },
  headerTitle: { fontSize: 18, fontWeight: '700' },
  content: { padding: 24, alignItems: 'center' },
  thanksText: { fontSize: 16, fontWeight: '700', marginBottom: 8 },
  providerName: { fontSize: 15, color: '#333', marginBottom: 12 },
  starsRow: { flexDirection: 'row', marginBottom: 24 },
  doneBtn: { backgroundColor: '#3DC1C6', paddingVertical: 12, paddingHorizontal: 28, borderRadius: 12 },
  doneText: { color: '#fff', fontWeight: '700' },
});