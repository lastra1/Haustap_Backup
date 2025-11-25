import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import * as ImagePicker from 'expo-image-picker';
import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useCallback, useEffect, useState } from 'react';
import {
    Alert,
    Image,
    Modal,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View
} from 'react-native';

const RETURN_REASONS = [
  'Service Not Rendered',
  'Wrong Service Provided',
  'Unsatisfactory Service Quality',
  'Overpayment or Incorrect Charges',
  'Duplicate Booking or Payment',
  'Cancellation Before Service Date',
  'Service Provider Unavailable',
  'System Error or Technical Issue',
] as const;

type ReturnReason = typeof RETURN_REASONS[number];

type Booking = {
  id: string;
  mainCategory: string;
  subCategory: string;
  date: string;
  total: number;
  status: string;
  completedAt?: string; // ISO string of when service was completed
};

export default function ReturnRequestForm() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const bookingId = params.bookingId as string;
  
  const [booking, setBooking] = useState<Booking | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [selectedReason, setSelectedReason] = useState<ReturnReason | null>(null);
  const [showReasonPicker, setShowReasonPicker] = useState(false);
  const [description, setDescription] = useState('');
  const [isWithinFreeWindow, setIsWithinFreeWindow] = useState(true);
  const [image, setImage] = useState<string | null>(null);

  const pickImage = async () => {
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [4, 3],
      quality: 1,
    });

    if (!result.canceled) {
      setImage(result.assets[0].uri);
    }
  };

  // Load booking and check if within 24 hours
  useEffect(() => {
    const loadBooking = async () => {
      if (!bookingId) {
        setError('No booking ID provided');
        setLoading(false);
        return;
      }

      try {
        let found = null;

        // First, try to use bookingData from params if available
        if (params.bookingData) {
          try {
            found = JSON.parse(params.bookingData as string);
            console.log('Using booking from params:', found);
          } catch (e) {
            console.warn('Failed to parse bookingData from params:', e);
          }
        }

        // If not found in params, query AsyncStorage
        if (!found) {
          const raw = await AsyncStorage.getItem('HT_bookings');
          if (!raw) {
            setError('No bookings found');
            setLoading(false);
            return;
          }

          const bookings = JSON.parse(raw);
          found = bookings.find((b: any) => b.id === bookingId);
        }
        
        if (!found) {
          setError('Booking not found');
          setLoading(false);
          return;
        }

        console.log('Found booking:', found); // Debug log
        setBooking(found);
        
        // Check if within 24 hours of completion
        if (found.completedAt) {
          console.log('Completion time:', found.completedAt); // Debug log
          const completedTime = new Date(found.completedAt).getTime();
          const now = new Date().getTime();
          const hoursDiff = (now - completedTime) / (1000 * 60 * 60);
          console.log('Hours difference:', hoursDiff); // Debug log
          setIsWithinFreeWindow(hoursDiff <= 24);
        } else {
          console.log('No completedAt timestamp found'); // Debug log
          // Default to free if no completion time (shouldn't happen in production)
          setIsWithinFreeWindow(true);
        }
        
        setLoading(false);
      } catch (error) {
        console.error('Failed to load booking:', error);
        setError('Failed to load booking details');
        setLoading(false);
      }
    };

    loadBooking();
  }, [bookingId, params.bookingData]);

  const handleSubmit = useCallback(async () => {
    if (!selectedReason) {
      Alert.alert('Error', 'Please select a reason for your return request.');
      return;
    }

    if (!description.trim()) {
      Alert.alert('Error', 'Please provide a description of the issue.');
      return;
    }

    try {
      // Get existing return requests or initialize empty array
      const existingReturns = await AsyncStorage.getItem('HT_returns');
      const returns = existingReturns ? JSON.parse(existingReturns) : [];

      // Add new return request
      returns.push({
        id: Date.now().toString(),
        bookingId,
        reason: selectedReason,
        description: description.trim(),
        image: image,
        timestamp: new Date().toISOString(),
        status: 'pending',
        charge: isWithinFreeWindow ? 0 : 300,
        isPaid: false
      });

      // Save updated returns
      await AsyncStorage.setItem('HT_returns', JSON.stringify(returns));

      Alert.alert(
        'Return Request Submitted',
        isWithinFreeWindow 
          ? 'Your return request has been submitted. We will review it and get back to you soon.'
          : 'Your return request has been submitted. A ₱300 fee will be charged upon service completion.',
        [
          {
            text: 'OK',
            onPress: () => router.back()
          }
        ]
      );
    } catch (error) {
      console.error('Failed to save return request:', error);
      Alert.alert('Error', 'Failed to submit return request. Please try again.');
    }
  }, [bookingId, selectedReason, description, isWithinFreeWindow, router]);

  if (loading) {
    return (
      <View style={styles.container}>
        <View style={styles.loadingContainer}>
          <Text style={styles.loadingText}>Loading booking details...</Text>
        </View>
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.container}>
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>{error}</Text>
          <TouchableOpacity
            style={styles.errorButton}
            onPress={() => router.back()}
          >
            <Text style={styles.errorButtonText}>Go Back</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  }

  if (!booking) {
    return (
      <View style={styles.container}>
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>Booking not found</Text>
          <TouchableOpacity
            style={styles.errorButton}
            onPress={() => router.back()}
          >
            <Text style={styles.errorButtonText}>Go Back</Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.header}>
          <TouchableOpacity 
            style={styles.backButton} 
            onPress={() => router.back()}
          >
            <Ionicons name="arrow-back" size={24} color="#333" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Return Request</Text>
          <View style={{ width: 24 }} /* Spacer for alignment */ />
        </View>

        <View style={styles.serviceInfo}>
          <Text style={styles.sectionTitle}>Service you want to return</Text>
          <Text style={styles.serviceName}>{booking.mainCategory}</Text>
          <Text style={styles.serviceSubcategory}>{booking.subCategory}</Text>
        </View>

        <View style={styles.form}>
          <Text style={styles.label}>Why do you want to return?</Text>
          <TouchableOpacity
            style={styles.pickerButton}
            onPress={() => setShowReasonPicker(true)}
          >
            <Text style={selectedReason ? styles.pickerText : styles.pickerPlaceholder}>
              {selectedReason || 'Select a reason'}
            </Text>
            <Ionicons name="chevron-down" size={20} color="#666" />
          </TouchableOpacity>

          <Modal
            visible={showReasonPicker}
            transparent
            animationType="fade"
            onRequestClose={() => setShowReasonPicker(false)}
          >
            <View style={styles.modalOverlay}>
              <View style={styles.pickerModal}>
                <View style={styles.pickerHeader}>
                  <Text style={styles.pickerHeaderTitle}>Select Return Reason</Text>
                  <TouchableOpacity onPress={() => setShowReasonPicker(false)}>
                    <Ionicons name="close" size={24} color="#333" />
                  </TouchableOpacity>
                </View>
                <ScrollView style={styles.pickerList}>
                  {RETURN_REASONS.map((reason) => (
                    <TouchableOpacity
                      key={reason}
                      style={[
                        styles.pickerOption,
                        selectedReason === reason && styles.pickerOptionSelected
                      ]}
                      onPress={() => {
                        setSelectedReason(reason);
                        setShowReasonPicker(false);
                      }}
                    >
                      <Text
                        style={[
                          styles.pickerOptionText,
                          selectedReason === reason && styles.pickerOptionTextSelected
                        ]}
                      >
                        {reason}
                      </Text>
                      {selectedReason === reason && (
                        <Ionicons name="checkmark" size={20} color="#3DC1C6" />
                      )}
                    </TouchableOpacity>
                  ))}
                </ScrollView>
              </View>
            </View>
          </Modal>

          <Text style={[styles.label, { marginTop: 20 }]}>Description</Text>
          <TextInput
            style={styles.descriptionInput}
            multiline
            numberOfLines={4}
            value={description}
            onChangeText={setDescription}
            placeholder="Write your description here"
            placeholderTextColor="#999"
            textAlignVertical="top"
          />

          <Text style={[styles.label, { marginTop: 20 }]}>Attach Image</Text>
          <TouchableOpacity style={styles.imagePickerButton} onPress={pickImage}>
            <Ionicons name="attach" size={24} color="#666" />
            <Text style={styles.imagePickerText}>Add Photo</Text>
          </TouchableOpacity>
          
          {image && (
            <View style={styles.imagePreview}>
              <Image source={{ uri: image }} style={styles.previewImage} />
              <TouchableOpacity 
                style={styles.removeImage} 
                onPress={() => setImage(null)}
              >
                <Ionicons name="close-circle" size={24} color="#ff4444" />
              </TouchableOpacity>
            </View>
          )}

          {!isWithinFreeWindow && (
            <View style={styles.chargeSection}>
              <View style={styles.chargeRow}>
                <Text style={styles.chargeLabel}>Sub Total</Text>
                <Text style={styles.chargeValue}>₱300.00</Text>
              </View>
              <View style={[styles.chargeRow, styles.totalRow]}>
                <Text style={styles.totalLabel}>TOTAL</Text>
                <Text style={styles.totalValue}>₱300.00</Text>
              </View>
              <Text style={styles.chargeNote}>
                * Return request fee will be collected by the service provider upon completion of the service
              </Text>
            </View>
          )}
        </View>

        <View style={styles.buttonRow}>
          <TouchableOpacity 
            style={styles.cancelButton}
            onPress={() => router.back()}
          >
            <Text style={styles.cancelButtonText}>Cancel</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={styles.submitButton}
            onPress={handleSubmit}
          >
            <Text style={styles.submitButtonText}>Submit</Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  imagePickerButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f0f0f0',
    padding: 12,
    borderRadius: 8,
    marginTop: 8,
  },
  imagePickerText: {
    marginLeft: 8,
    color: '#666',
    fontSize: 16,
  },
  imagePreview: {
    marginTop: 16,
    position: 'relative',
  },
  previewImage: {
    width: '100%',
    height: 200,
    borderRadius: 8,
  },
  removeImage: {
    position: 'absolute',
    top: 8,
    right: 8,
    backgroundColor: 'white',
    borderRadius: 12,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    fontSize: 16,
    color: '#666',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  errorText: {
    fontSize: 16,
    color: '#666',
    textAlign: 'center',
    marginBottom: 20,
  },
  errorButton: {
    backgroundColor: '#3DC1C6',
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  errorButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  scrollContent: {
    padding: 16,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 24,
  },
  backButton: {
    padding: 8,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: '700',
    color: '#333',
  },
  serviceInfo: {
    marginBottom: 24,
  },
  sectionTitle: {
    fontSize: 14,
    color: '#666',
    marginBottom: 8,
  },
  serviceName: {
    fontSize: 16,
    fontWeight: '700',
    color: '#333',
  },
  serviceSubcategory: {
    fontSize: 14,
    color: '#666',
    marginTop: 4,
  },
  form: {
    marginBottom: 24,
  },
  label: {
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
    marginBottom: 8,
  },
  pickerButton: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: '#fff',
  },
  pickerText: {
    fontSize: 16,
    color: '#333',
  },
  pickerPlaceholder: {
    fontSize: 16,
    color: '#999',
  },
  descriptionInput: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    backgroundColor: '#fff',
    height: 100,
  },
  chargeSection: {
    marginTop: 24,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#eee',
  },
  chargeRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  chargeLabel: {
    fontSize: 14,
    color: '#666',
  },
  chargeValue: {
    fontSize: 14,
    color: '#333',
  },
  totalRow: {
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#eee',
  },
  totalLabel: {
    fontSize: 16,
    fontWeight: '700',
    color: '#333',
  },
  totalValue: {
    fontSize: 16,
    fontWeight: '700',
    color: '#333',
  },
  chargeNote: {
    fontSize: 12,
    color: '#666',
    marginTop: 8,
    fontStyle: 'italic',
  },
  buttonRow: {
    flexDirection: 'row',
    gap: 12,
    marginTop: 8,
  },
  cancelButton: {
    flex: 1,
    backgroundColor: '#f0f0f0',
    borderRadius: 8,
    paddingVertical: 16,
    alignItems: 'center',
  },
  cancelButtonText: {
    color: '#666',
    fontSize: 16,
    fontWeight: '600',
  },
  submitButton: {
    flex: 1,
    backgroundColor: '#3DC1C6',
    borderRadius: 8,
    paddingVertical: 16,
    alignItems: 'center',
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'flex-end',
  },
  pickerModal: {
    backgroundColor: '#fff',
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    maxHeight: '70%',
    paddingBottom: 16,
  },
  pickerHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  pickerHeaderTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
  },
  pickerList: {
    paddingHorizontal: 0,
  },
  pickerOption: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  pickerOptionSelected: {
    backgroundColor: '#f0f0f0',
  },
  pickerOptionText: {
    fontSize: 16,
    color: '#333',
    flex: 1,
  },
  pickerOptionTextSelected: {
    fontWeight: '600',
    color: '#3DC1C6',
  },
});

