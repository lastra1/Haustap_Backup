import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useCallback, useEffect, useState } from 'react';
import {
    Alert,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from 'react-native';

const CANCELLATION_REASONS = [
  'Change of Schedule',
  'Service No Longer Needed',
  'Incorrect Booking Details',
  'Price Concerns',
  'Service Provider Unavailable',
  'Health/Safety Concerns',
  'Emergency/Personal Reasons',
  'Others',
] as const;

type CancellationReason = typeof CANCELLATION_REASONS[number];

type Booking = {
  id: string;
  status: string;
  mainCategory: string;
  subCategory: string;
  date: string;
  total: number;
};

export default function CancelBookingForm() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const bookingId = params.bookingId as string;

  const [booking, setBooking] = useState<Booking | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [selectedReason, setSelectedReason] = useState<CancellationReason | null>(null);
  const [showReasonPicker, setShowReasonPicker] = useState(false);
  const [description, setDescription] = useState('');

  // Load booking details
  useEffect(() => {
    const loadBooking = async () => {
      if (!bookingId) {
        setError('No booking ID provided');
        setLoading(false);
        return;
      }

      try {
        const raw = await AsyncStorage.getItem('HT_bookings');
        if (!raw) {
          setError('No bookings found');
          setLoading(false);
          return;
        }

        const bookings = JSON.parse(raw);
        const found = bookings.find((b: any) => b.id === bookingId);
        
        if (!found) {
          setError('Booking not found');
          setLoading(false);
          return;
        }

        setBooking(found);
        setLoading(false);
      } catch (error) {
        console.error('Failed to load booking:', error);
        setError('Failed to load booking details');
        setLoading(false);
      }
    };
    loadBooking();
  }, [bookingId]);

  const handleSubmit = useCallback(async () => {
    if (!selectedReason) {
      Alert.alert('Error', 'Please select a reason for cancellation.');
      return;
    }

    setLoading(true);
    try {
      const raw = await AsyncStorage.getItem('HT_bookings');
      if (!raw) throw new Error('No bookings found');

      const bookings = JSON.parse(raw);
      const updatedBookings = bookings.map((b: any) => {
        if (b.id === bookingId) {
          return {
            ...b,
            status: 'cancelled',
            cancellationReason: selectedReason,
            cancellationDescription: description,
            cancelledAt: new Date().toISOString()
          };
        }
        return b;
      });

      await AsyncStorage.setItem('HT_bookings', JSON.stringify(updatedBookings));
      
      Alert.alert(
        'Booking Cancelled',
        'Your booking has been successfully cancelled.',
        [
          {
            text: 'OK',
            onPress: () => router.push('/client-side/client-booking-summary/booking-cancelled')
          }
        ]
      );
    } catch (error) {
      console.error('Failed to cancel booking:', error);
      Alert.alert('Error', 'Failed to cancel booking. Please try again.');
    } finally {
      setLoading(false);
    }
  }, [bookingId, selectedReason, description, router]);

  if (loading) {
    return (
      <View style={styles.container}>
        <Text>Loading...</Text>
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.container}>
        <Text style={styles.errorText}>{error}</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity
          onPress={() => router.back()}
          style={styles.backButton}
        >
          <Ionicons name="arrow-back" size={24} color="#333" />
          <Text style={styles.backText}>Cancel booking</Text>
        </TouchableOpacity>
      </View>

      <ScrollView style={styles.content}>
        <Text style={styles.title}>Why are you requesting to cancel your booking?</Text>

        <View style={styles.reasonSelector}>
          <Text style={styles.label}>Reason:</Text>
          <TouchableOpacity
            style={styles.dropdownButton}
            onPress={() => setShowReasonPicker(!showReasonPicker)}
          >
            <Text style={[styles.dropdownButtonText, !selectedReason && styles.placeholder]}>
              {selectedReason || 'Select a reason'}
            </Text>
            <Ionicons
              name={showReasonPicker ? 'chevron-up' : 'chevron-down'}
              size={20}
              color="#666"
            />
          </TouchableOpacity>

          {showReasonPicker && (
            <View style={styles.dropdownList}>
              {CANCELLATION_REASONS.map((reason) => (
                <TouchableOpacity
                  key={reason}
                  style={styles.dropdownItem}
                  onPress={() => {
                    setSelectedReason(reason);
                    setShowReasonPicker(false);
                  }}
                >
                  <Text style={styles.dropdownItemText}>{reason}</Text>
                </TouchableOpacity>
              ))}
            </View>
          )}
        </View>

        <View style={styles.descriptionContainer}>
          <Text style={styles.label}>Description:</Text>
          <TextInput
            style={styles.descriptionInput}
            multiline
            numberOfLines={4}
            placeholder="Please provide more details about your cancellation reason..."
            value={description}
            onChangeText={setDescription}
          />
        </View>

        <TouchableOpacity
          style={[styles.submitButton, loading && styles.submitButtonDisabled]}
          onPress={handleSubmit}
          disabled={loading}
        >
          <Text style={styles.submitButtonText}>
            {loading ? 'Submitting...' : 'Submit'}
          </Text>
        </TouchableOpacity>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  header: {
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#E0E0E0',
  },
  backButton: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  backText: {
    marginLeft: 8,
    fontSize: 16,
    fontWeight: '600',
  },
  content: {
    flex: 1,
    padding: 16,
  },
  title: {
    fontSize: 18,
    fontWeight: '600',
    marginBottom: 24,
    color: '#333',
  },
  errorText: {
    color: '#ff0000',
    textAlign: 'center',
    marginTop: 20,
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    marginBottom: 8,
    color: '#333',
  },
  reasonSelector: {
    marginBottom: 24,
  },
  dropdownButton: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 12,
    borderWidth: 1,
    borderColor: '#E0E0E0',
    borderRadius: 8,
    backgroundColor: '#fff',
  },
  dropdownButtonText: {
    fontSize: 14,
    color: '#333',
  },
  placeholder: {
    color: '#999',
  },
  dropdownList: {
    marginTop: 8,
    borderWidth: 1,
    borderColor: '#E0E0E0',
    borderRadius: 8,
    backgroundColor: '#fff',
  },
  dropdownItem: {
    padding: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#E0E0E0',
  },
  dropdownItemText: {
    fontSize: 14,
    color: '#333',
  },
  descriptionContainer: {
    marginBottom: 24,
  },
  descriptionInput: {
    borderWidth: 1,
    borderColor: '#E0E0E0',
    borderRadius: 8,
    padding: 12,
    fontSize: 14,
    minHeight: 100,
    textAlignVertical: 'top',
  },
  submitButton: {
    backgroundColor: '#3DC1C6',
    padding: 16,
    borderRadius: 8,
    alignItems: 'center',
  },
  submitButtonDisabled: {
    opacity: 0.6,
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
});