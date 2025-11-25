import React, { useState } from "react";
import { Modal, View, Text, TextInput, TouchableOpacity, StyleSheet } from "react-native";
import { Picker } from "@react-native-picker/picker";

interface CancelBookingFormProps {
  visible: boolean;
  onClose: () => void;
  onSubmit: (reason: string, description: string) => void;
  clientName: string;
  bookingId: string;
}

const CancelBookingForm: React.FC<CancelBookingFormProps> = ({
  visible,
  onClose,
  onSubmit,
  clientName,
  bookingId,
}) => {
  const [reason, setReason] = useState("");
  const [description, setDescription] = useState("");

  const handleSubmit = () => {
    onSubmit(reason, description);
    onClose();
  };

  return (
    <Modal animationType="slide" transparent visible={visible}>
      <View style={styles.overlay}>
        <View style={styles.container}>
          <Text style={styles.title}>Cancelled by Service Provider</Text>
          <Text style={styles.name}>{clientName}</Text>
          <Text style={styles.bookingId}>Booking ID: {bookingId}</Text>

          <Text style={styles.subtitle}>Do you want to report?</Text>

          <View style={styles.field}>
            <Text style={styles.label}>Reason:</Text>
            <View style={styles.dropdown}>
              <Picker
                selectedValue={reason}
                onValueChange={(itemValue) => setReason(itemValue)}
              >
                <Picker.Item label="Select Reason" value="" />
                <Picker.Item label="Last-Minute Cancellation" value="Last-Minute Cancellation" />
                <Picker.Item label="No Prior Notice" value="No Prior Notice" />
                <Picker.Item label="Unprofessional Communication" value="Unprofessional Communication" />
                <Picker.Item label="Failure to Arrive / No-Show" value="Failure to Arrive / No-Show" />
                <Picker.Item label="Cancelled to Force Outside-App Transaction" value="Cancelled to Force Outside-App Transaction" />
                <Picker.Item label="Fake or Incorrect Availability" value="Fake or Incorrect Availability" />
                <Picker.Item label="Misleading Information / Dishonesty" value="Misleading Information / Dishonesty" />
                <Picker.Item label="Safety or Security Concern" value="Safety or Security Concern" />
                <Picker.Item label="Other Valid Concerns" value="Other Valid Concerns" />
              </Picker>
            </View>
          </View>

          <View style={styles.field}>
            <Text style={styles.label}>Description:</Text>
            <TextInput
              style={styles.textArea}
              placeholder="Leave your comments here"
              multiline
              value={description}
              onChangeText={setDescription}
            />
          </View>

          <View style={styles.buttonRow}>
            <TouchableOpacity style={styles.cancelBtn} onPress={onClose}>
              <Text style={styles.cancelText}>Cancel</Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.submitBtn} onPress={handleSubmit}>
              <Text style={styles.submitText}>Submit</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: "rgba(0,0,0,0.5)",
    justifyContent: "center",
    alignItems: "center",
  },
  container: {
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 20,
    width: "90%",
    elevation: 5,
  },
  title: {
    fontSize: 18,
    fontWeight: "600",
    textAlign: "center",
    marginBottom: 4,
  },
  name: {
    fontSize: 16,
    fontWeight: "500",
    textAlign: "center",
  },
  bookingId: {
    fontSize: 13,
    color: "#555",
    textAlign: "center",
    marginBottom: 20,
  },
  subtitle: {
    fontSize: 16,
    fontWeight: "500",
    marginBottom: 10,
    textAlign: "center",
  },
  field: {
    marginBottom: 15,
  },
  label: {
    fontSize: 14,
    fontWeight: "500",
    marginBottom: 4,
  },
  dropdown: {
    borderWidth: 1,
    borderColor: "#ccc",
    borderRadius: 6,
  },
  textArea: {
    borderWidth: 1,
    borderColor: "#ccc",
    borderRadius: 6,
    padding: 10,
    height: 100,
    textAlignVertical: "top",
  },
  buttonRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 20,
  },
  cancelBtn: {
    flex: 1,
    borderWidth: 1,
    borderColor: "#000",
    borderRadius: 8,
    paddingVertical: 10,
    marginRight: 10,
    alignItems: "center",
  },
  submitBtn: {
    flex: 1,
    backgroundColor: "#00A8A8",
    borderRadius: 8,
    paddingVertical: 10,
    alignItems: "center",
  },
  cancelText: {
    color: "#000",
    fontWeight: "500",
  },
  submitText: {
    color: "#fff",
    fontWeight: "600",
  },
});

export default CancelBookingForm;
