import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import * as ImagePicker from 'expo-image-picker';
import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useCallback, useState } from 'react';
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

const REPORT_REASONS = [
  'Late Arrival',
  'Did Not Show Up',
  'Poor Quality of Work',
  'Unprofessional Behavior',
  'Overpricing / Additional Charges',
  'Damaged Property',
  'Others',
] as const;

type ReportReason = typeof REPORT_REASONS[number];

export default function ReportForm() {
  const router = useRouter();
  const { bookingId } = useLocalSearchParams();
  
  const [selectedReason, setSelectedReason] = useState<ReportReason | null>(null);
  const [showReasonPicker, setShowReasonPicker] = useState(false);
  const [additionalDetails, setAdditionalDetails] = useState('');
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

  const handleSubmit = useCallback(async () => {
    if (!selectedReason) {
      Alert.alert('Error', 'Please select a reason for your report.');
      return;
    }

    if (!additionalDetails.trim()) {
      Alert.alert('Error', 'Please provide additional details about the issue.');
      return;
    }

    try {
      // Get existing reports or initialize empty array
      const existingReports = await AsyncStorage.getItem('HT_reports');
      const reports = existingReports ? JSON.parse(existingReports) : [];

      // Add new report
      reports.push({
        id: Date.now().toString(),
        bookingId,
        reason: selectedReason,
        details: additionalDetails,
        image: image,
        timestamp: new Date().toISOString(),
        status: 'pending'
      });

      // Save updated reports
      await AsyncStorage.setItem('HT_reports', JSON.stringify(reports));

      Alert.alert(
        'Report Submitted',
        'Thank you for your feedback. We will review your report and take appropriate action.',
        [
          {
            text: 'OK',
            onPress: () => router.back()
          }
        ]
      );
    } catch (error) {
      console.error('Failed to save report:', error);
      Alert.alert('Error', 'Failed to submit report. Please try again.');
    }
  }, [bookingId, selectedReason, additionalDetails, router]);

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
          <Text style={styles.headerTitle}>Report Service</Text>
          <View style={{ width: 24 }} /* Spacer for alignment */ />
        </View>

        <View style={styles.form}>
          <Text style={styles.label}>Reason *</Text>
          <TouchableOpacity
            style={styles.pickerButton}
            onPress={() => setShowReasonPicker(true)}
          >
            <Text style={selectedReason ? styles.pickerText : styles.pickerPlaceholder}>
              {selectedReason || 'Select a reason'}
            </Text>
            <Ionicons name="chevron-down" size={20} color="#666" />
          </TouchableOpacity>

          <Text style={[styles.label, { marginTop: 20 }]}>Additional Details *</Text>
          <TextInput
            style={styles.detailsInput}
            multiline
            numberOfLines={6}
            value={additionalDetails}
            onChangeText={setAdditionalDetails}
            placeholder="Please provide more details about the issue..."
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
        </View>

        <TouchableOpacity 
          style={styles.submitButton}
          onPress={handleSubmit}
        >
          <Text style={styles.submitButtonText}>Submit Report</Text>
        </TouchableOpacity>
      </ScrollView>

      {/* Reason Picker Modal */}
      <Modal
        visible={showReasonPicker}
        transparent
        animationType="fade"
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Select a Reason</Text>
            
            {REPORT_REASONS.map((reason) => (
              <TouchableOpacity
                key={reason}
                style={styles.reasonOption}
                onPress={() => {
                  setSelectedReason(reason);
                  setShowReasonPicker(false);
                }}
              >
                <Text style={[
                  styles.reasonOptionText,
                  selectedReason === reason && styles.selectedReasonText
                ]}>
                  {reason}
                </Text>
              </TouchableOpacity>
            ))}

            <TouchableOpacity
              style={styles.closeModalButton}
              onPress={() => setShowReasonPicker(false)}
            >
              <Text style={styles.closeModalText}>Cancel</Text>
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
  detailsInput: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    backgroundColor: '#fff',
    minHeight: 120,
  },
  submitButton: {
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
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalContent: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 20,
    width: '85%',
    maxHeight: '80%',
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: '#333',
    marginBottom: 16,
    textAlign: 'center',
  },
  reasonOption: {
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  reasonOptionText: {
    fontSize: 16,
    color: '#333',
  },
  selectedReasonText: {
    color: '#3DC1C6',
    fontWeight: '600',
  },
  closeModalButton: {
    marginTop: 16,
    paddingVertical: 12,
    alignItems: 'center',
  },
  closeModalText: {
    fontSize: 16,
    color: '#666',
  },
});
