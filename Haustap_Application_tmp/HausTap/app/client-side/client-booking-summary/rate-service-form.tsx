import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useEffect, useState } from 'react';
import { Alert, StyleSheet, Text, TextInput, TouchableOpacity, View } from 'react-native';

export default function RateServiceForm() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const bookingId = (params as any).bookingId as string | undefined;

  const [providerName, setProviderName] = useState('Ana Santos');
  const [rating, setRating] = useState<number>(0);
  const [comment, setComment] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    let mounted = true;
    const loadBooking = async () => {
      if (!bookingId) return;
      try {
        const raw = await AsyncStorage.getItem('HT_bookings');
        if (raw) {
          const parsed = JSON.parse(raw);
          if (Array.isArray(parsed)) {
            const found = parsed.find((b: any) => b.id === bookingId);
            if (found && mounted) {
              setProviderName(found.providerName ?? 'Ana Santos');
            }
          }
        }
      } catch (err) {
        console.warn('Failed to load booking for rating', err);
      }
    };
    loadBooking();
    return () => {
      mounted = false;
    };
  }, [bookingId]);

  const submitReview = async () => {
    if (rating <= 0) {
      Alert.alert('Please rate', 'Please select a star rating before submitting.');
      return;
    }
    setLoading(true);
    try {
      const raw = await AsyncStorage.getItem('HT_reviews');
      const existing = raw ? JSON.parse(raw) : [];
      const review = {
        bookingId: bookingId ?? `manual-${Date.now()}`,
        providerName,
        rating,
        comment,
        createdAt: new Date().toISOString(),
      };
      existing.unshift(review);
      await AsyncStorage.setItem('HT_reviews', JSON.stringify(existing));

      // mark booking as rated if exists
      if (bookingId) {
        const rawB = await AsyncStorage.getItem('HT_bookings');
        const existingB = rawB ? JSON.parse(rawB) : [];
        const updated = (existingB || []).map((b: any) => (b.id === bookingId ? { ...b, isRated: true } : b));
        await AsyncStorage.setItem('HT_bookings', JSON.stringify(updated));
      }

  // redirect to rating summary page showing a thank you message
  router.push({ pathname: '/client-side/client-booking-summary/rating-summary', params: { bookingId: bookingId ?? review.bookingId, rating } } as any);
    } catch (err) {
      console.warn('Failed to save review', err);
      Alert.alert('Error', 'Failed to submit review.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.page}>
      <View style={styles.headerRow}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Rate Service</Text>
        <View style={{ width: 24 }} />
      </View>

      <View style={styles.content}>
        <Text style={styles.subtitle}>Rate your Service Provider</Text>
        <Text style={styles.providerName}>{providerName}</Text>

        <Text style={styles.question}>How was your service?</Text>
        <View style={styles.starsRow}>
          {[1, 2, 3, 4, 5].map((s) => (
            <TouchableOpacity key={s} onPress={() => setRating(s)} activeOpacity={0.8}>
              <Ionicons
                name={s <= rating ? 'star' : 'star-outline'}
                size={32}
                color={s <= rating ? '#FFD700' : '#333'}
                style={{ marginHorizontal: 6 }}
              />
            </TouchableOpacity>
          ))}
        </View>

        <Text style={styles.label}>What did you think about the service?</Text>
        <TextInput
          value={comment}
          onChangeText={setComment}
          multiline
          style={styles.textArea}
        />

        <TouchableOpacity style={styles.submitBtn} onPress={submitReview} disabled={loading}>
          <Text style={styles.submitText}>{loading ? 'Submitting...' : 'Submit Review'}</Text>
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
  subtitle: { fontSize: 14, color: '#333', marginBottom: 8 },
  providerName: { fontSize: 16, fontWeight: '800', marginBottom: 18 },
  question: { fontSize: 14, marginTop: 6, marginBottom: 8 },
  starsRow: { flexDirection: 'row', justifyContent: 'center', alignItems: 'center', marginBottom: 18 },
  label: { alignSelf: 'flex-start', marginLeft: 6, marginBottom: 6, color: '#666' },
  textArea: { width: '100%', minHeight: 120, borderWidth: 1, borderColor: '#E0E0E0', borderRadius: 8, padding: 12, backgroundColor: '#fff' },
  submitBtn: { marginTop: 24, backgroundColor: '#3DC1C6', paddingVertical: 12, paddingHorizontal: 26, borderRadius: 12, alignSelf: 'stretch', alignItems: 'center', shadowColor: '#000', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.12, shadowRadius: 4, elevation: 3 },
  submitText: { color: '#fff', fontWeight: '700' },
});
