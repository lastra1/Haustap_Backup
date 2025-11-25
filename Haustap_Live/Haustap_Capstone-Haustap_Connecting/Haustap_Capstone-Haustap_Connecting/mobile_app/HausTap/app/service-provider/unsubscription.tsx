import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import {
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from 'react-native';


export default function App() {
  const router = useRouter();
  const [selectedReason, setSelectedReason] = useState<string | null>(null);
  const reasons = [
    'Too expensive',
    'Not using the platform often',
    'Technical Issues',
    'Unsatisfied with services',
    'Other',
  ];


  // Custom Radio Button Component
  type RadioOptionBoxProps = {
    label: string;
    isSelected: boolean;
    onPress: () => void;
  };

  const RadioOptionBox: React.FC<RadioOptionBoxProps> = ({ label, isSelected, onPress }) => {
    // Style adjustments based on the image:
    // 1. Box border changes to the turquoise color when selected.
    // 2. Radio circle itself remains a hollow circle when not selected.
    // 3. Radio circle has a black dot when selected.
    const isOther = label === 'Other';


    return (
      <TouchableOpacity 
        style={[
          styles.radioBox,
          isSelected && styles.radioBoxSelected,
          isOther && styles.radioBoxOther, // Special style for "Other" box
        ]} 
        onPress={onPress}
      >
        <View style={styles.radioContent}>
            <Text style={styles.radioLabel}>{label}</Text>
            <View style={styles.radioCircle}>
                {isSelected && (
                    <View style={styles.radioInnerCircle} />
                )}
            </View>
        </View>


        {/* Text Input for 'Other' reason, rendered inside the 'Other' box */}
        {isOther && isSelected && (
            <View>
                <TextInput
                    style={styles.otherTextInput}
                    placeholder="" 
                    multiline
                />
                <View style={styles.horizontalLine} />
            </View>
        )}
      </TouchableOpacity>
    );
  };


  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => router.push('/service-provider/my-subscription')}
          accessibilityLabel="Go back to My Subscription"
        >
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Unsubscribe</Text>
      </View>


      <ScrollView contentContainerStyle={styles.content}>
        {/* Main Prompt Text */}
        <Text style={styles.mainQuestion}>
          Are you sure you want to unsubscribe?
        </Text>
        <Text style={styles.subText}>
          Your current plan (₱499/month) will end after the current billing
          period. Please tell us why you're unsubscribing:
        </Text>


        {/* Reason List */}
        <View style={styles.reasonsList}>
          {reasons.map((reason, index) => (
            <RadioOptionBox
              key={index}
              label={reason}
              isSelected={selectedReason === reason}
              onPress={() => setSelectedReason(reason)}
            />
          ))}
        </View>
      </ScrollView>


      {/* Footer/Action Buttons */}
      <View style={styles.footer}>
        <TouchableOpacity style={styles.unsubscribeButton}>
          <Text style={styles.unsubscribeButtonText}>Unsubscribe</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.confirmButton}>
          <Text style={styles.confirmButtonText}>Confirm Unsubscribe</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}


const HAUSTAP_TURQUOISE = '#3dc1c6';


const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: 'white',
    paddingTop: 40, // For status bar
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 15,
    paddingVertical: 10,
  },
  backButton: {
    paddingRight: 15,
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: 'bold',
  },
  content: {
    padding: 20,
    paddingBottom: 20, // To give space above the footer
  },
  mainQuestion: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  subText: {
    fontSize: 13,
    color: '#666',
    lineHeight: 18,
    marginBottom: 20,
  },
  reasonsList: {
    // Container for the list items
  },
  // --- Radio Box Styles (NEW) ---
  radioBox: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    marginBottom: 10, // Space between boxes
    paddingHorizontal: 15,
    paddingVertical: 5,
  },
  radioBoxSelected: {
    borderColor: HAUSTAP_TURQUOISE, // Highlight border when selected
  },
  radioBoxOther: {
    // No specific change for 'Other' box yet, but allows future styling
  },
  radioContent: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 10,
  },
  radioLabel: {
    fontSize: 16,
  },
  // --- Radio Button Circle Styles ---
  radioCircle: {
    height: 20,
    width: 20,
    borderRadius: 10,
    borderWidth: 2,
    borderColor: '#999',
    alignItems: 'center',
    justifyContent: 'center',
  },
  radioInnerCircle: {
    height: 10, // Slightly smaller dot than the previous version
    width: 10,
    borderRadius: 5,
    backgroundColor: 'black', 
  },
  // --- Other Text Input Styles ---
  otherTextInput: {
    fontSize: 16,
    paddingTop: 0,
    paddingBottom: 5,
    marginTop: -5, // Pulls the input up closer to the "Other" label
    minHeight: 30,
    textAlignVertical: 'top',
  },
  horizontalLine: {
    height: 1,
    backgroundColor: '#999', 
    marginBottom: 10,
  },
  // --- Footer/Button Styles ---
  footer: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: 20,
    backgroundColor: 'white',
    borderTopColor: '#eee',
  },
  unsubscribeButton: {
    backgroundColor: 'white',
    borderColor: '#ccc',
    borderWidth: 1,
    borderRadius: 5,
    padding: 15,
    marginBottom: 10,
    alignItems: 'center',
  },
  unsubscribeButtonText: {
    color: 'black',
    fontSize: 16,
    fontWeight: 'bold',
  },
  confirmButton: {
    backgroundColor: HAUSTAP_TURQUOISE,
    borderRadius: 5,
    padding: 15,
    alignItems: 'center',
  },
  confirmButtonText: {
    color: 'black',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
