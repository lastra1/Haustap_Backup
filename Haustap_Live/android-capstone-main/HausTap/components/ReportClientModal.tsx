import { Ionicons } from "@expo/vector-icons";
import React, { useState } from "react";
import {
    Modal,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";

interface ReportClientModalProps {
  visible: boolean;
  onClose: () => void;
  onSubmit: (reason: string, notes: string) => void;
  clientName?: string;
}

const REPORT_REASONS = [
  "Client not at the location",
  "Wrong or incomplete address",
  "Asked for extra tasks not booked",
  "Did not answer calls or messages",
  "Cancellation after service",
  "Was rude or aggressive",
  "Gave wrong job details or size",
  "Changed schedule without notice",
];

export default function ReportClientModal({
  visible,
  onClose,
  onSubmit,
  clientName = "Client",
}: ReportClientModalProps) {
  const [selectedReason, setSelectedReason] = useState<string | null>(null);
  const [additionalNotes, setAdditionalNotes] = useState("");

  const handleSubmit = () => {
    if (!selectedReason) {
      alert("Please select a report reason");
      return;
    }
    onSubmit(selectedReason, additionalNotes);
    // Reset form
    setSelectedReason(null);
    setAdditionalNotes("");
    onClose();
  };

  const handleClose = () => {
    // Reset form on close
    setSelectedReason(null);
    setAdditionalNotes("");
    onClose();
  };

  return (
    <Modal
      visible={visible}
      transparent={true}
      animationType="slide"
      onRequestClose={handleClose}
    >
      <View style={styles.container}>
        {/* Header */}
        <View style={styles.header}>
          <TouchableOpacity onPress={handleClose}>
            <Ionicons name="chevron-back" size={28} color="#000" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Report Client</Text>
          <View style={{ width: 28 }} />
        </View>

        <ScrollView
          style={styles.content}
          contentContainerStyle={{ paddingHorizontal: 16, paddingBottom: 100 }}
        >
          {/* Instructions */}
          <Text style={styles.instructionText}>
            Why are you requesting to report your client?
          </Text>

          {/* Report Details Section */}
          <Text style={styles.sectionLabel}>Report details</Text>

          {/* Reason Options */}
          <View style={styles.reasonsContainer}>
            {REPORT_REASONS.map((reason, index) => (
              <TouchableOpacity
                key={index}
                style={[
                  styles.reasonOption,
                  selectedReason === reason && styles.reasonOptionSelected,
                ]}
                onPress={() => setSelectedReason(reason)}
              >
                <View
                  style={[
                    styles.radioCircle,
                    selectedReason === reason && styles.radioCircleSelected,
                  ]}
                >
                  {selectedReason === reason && (
                    <View style={styles.radioInner} />
                  )}
                </View>
                <Text
                  style={[
                    styles.reasonText,
                    selectedReason === reason && styles.reasonTextSelected,
                  ]}
                >
                  {reason}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          {/* Additional Notes Section */}
          <Text style={[styles.sectionLabel, { marginTop: 24 }]}>
            Additional Notes
          </Text>
          <TextInput
            style={styles.notesInput}
            placeholder="Add any additional details..."
            placeholderTextColor="#999"
            multiline
            value={additionalNotes}
            onChangeText={setAdditionalNotes}
          />

          {/* Submit Button */}
          <TouchableOpacity
            style={styles.submitButton}
            onPress={handleSubmit}
          >
            <Text style={styles.submitButtonText}>Submit</Text>
          </TouchableOpacity>
        </ScrollView>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#FFF",
    paddingTop: 40,
  },
  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: "#E0E0E0",
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: "700",
    color: "#000",
  },
  content: {
    flex: 1,
    paddingTop: 16,
  },
  instructionText: {
    fontSize: 14,
    color: "#333",
    marginBottom: 20,
    fontWeight: "500",
  },
  sectionLabel: {
    fontSize: 13,
    fontWeight: "600",
    color: "#000",
    marginBottom: 12,
  },
  reasonsContainer: {
    gap: 10,
  },
  reasonOption: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 12,
    paddingVertical: 12,
    borderWidth: 1,
    borderColor: "#E0E0E0",
    borderRadius: 6,
    backgroundColor: "#FFF",
  },
  reasonOptionSelected: {
    borderColor: "#3DC1C6",
    backgroundColor: "#F0F8F9",
  },
  radioCircle: {
    width: 20,
    height: 20,
    borderRadius: 10,
    borderWidth: 2,
    borderColor: "#CCC",
    marginRight: 12,
    alignItems: "center",
    justifyContent: "center",
  },
  radioCircleSelected: {
    borderColor: "#3DC1C6",
  },
  radioInner: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: "#3DC1C6",
  },
  reasonText: {
    fontSize: 13,
    color: "#333",
    flex: 1,
  },
  reasonTextSelected: {
    color: "#000",
    fontWeight: "500",
  },
  notesInput: {
    borderWidth: 1,
    borderColor: "#E0E0E0",
    borderRadius: 6,
    paddingHorizontal: 12,
    paddingVertical: 12,
    minHeight: 120,
    fontSize: 13,
    color: "#333",
    textAlignVertical: "top",
  },
  submitButton: {
    backgroundColor: "#3DC1C6",
    paddingVertical: 14,
    borderRadius: 6,
    alignItems: "center",
    marginTop: 24,
  },
  submitButtonText: {
    color: "#FFF",
    fontWeight: "700",
    fontSize: 15,
  },
});
