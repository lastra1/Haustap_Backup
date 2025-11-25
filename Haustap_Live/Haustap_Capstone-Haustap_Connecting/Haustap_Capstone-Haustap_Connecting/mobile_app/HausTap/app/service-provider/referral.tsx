import { Ionicons } from "@expo/vector-icons";
import React, { useState } from "react";
import {
    Modal,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";

export default function ReferralScreen({ navigation }: { navigation?: any }) {
  const [referralCode, setReferralCode] = useState("");
  const [modalVisible, setModalVisible] = useState(false);
  const userCode = "6AYI6F";

  const copyCode = () => {
    alert("Referral code copied to clipboard!");
  };

  const submitCode = () => {
    if (!referralCode.trim()) {
      alert("Please enter a referral code.");
      return;
    }
    setModalVisible(true); // show success modal
  };

  return (
    <View style={styles.container}>
      {/* Header with back arrow + title */}
      <View style={styles.headerRow}>
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => navigation?.goBack?.()}
        >
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Referral</Text>
      </View>

      {/* Your Code */}
      <Text style={styles.label}>    Your code:</Text>

      <View style={styles.tealCard}>
        {/* White box inside teal card */}
        <View style={styles.whiteBox}>
          <Text style={styles.codeText}>{userCode}</Text>
        </View>

        <TouchableOpacity style={styles.copyButton} onPress={copyCode}>
          <Text style={styles.copyButtonText}>Copy</Text>
        </TouchableOpacity>
      </View>

      {/* Invitation section */}
      <Text style={styles.inviteLabel}>
          Received an invitation from a friend?
      </Text>

      <View style={styles.inputCard}>
        <Text style={styles.inputLabel}>
          Add the referral code you have received from your friend
        </Text>

        <TextInput
          style={styles.input}
          value={referralCode}
          onChangeText={setReferralCode}
        />

        <TouchableOpacity style={styles.submitButton} onPress={submitCode}>
          <Text style={styles.submitText}>Submit</Text>
        </TouchableOpacity>
      </View>

      {/* ✅ Success Modal */}
      <Modal
        transparent
        animationType="fade"
        visible={modalVisible}
        onRequestClose={() => setModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalBox}>
            <TouchableOpacity
              style={styles.closeButton}
              onPress={() => setModalVisible(false)}
            >
              <Text style={styles.closeText}>x</Text>
            </TouchableOpacity>

            <Text style={styles.modalTitle}>Success!!!</Text>
            <Text style={styles.modalMessage}>
              You'll get your referral reward once the booking of your invitee is
              completed.
            </Text>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
    paddingHorizontal: 18,
    paddingTop: 45,
  },
  headerRow: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 35,
  },
  backButton: {
    marginRight: 10,
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: "bold",
  },
  label: {
    fontSize: 16,
    fontWeight: "bold",
    marginBottom: 12,
  },
  tealCard: {
    backgroundColor: "#3dc1c6",
    borderRadius: 10,
    paddingVertical: 25,
    alignItems: "center",
    shadowColor: "#000",
    shadowOpacity: 0.2,
    shadowRadius: 5,
    shadowOffset: { width: 0, height: 3 },
    elevation: 4,
    marginBottom: 25,
    marginLeft: 20,
    marginRight: 20,
  },
  whiteBox: {
    backgroundColor: "#fff",
    borderRadius: 8,
    paddingVertical: 15,
    paddingHorizontal: 89,
    marginBottom: 15,
    shadowColor: "#000",
    shadowOpacity: 0.1,
    shadowRadius: 3,
    shadowOffset: { width: 0, height: 1 },
  },
  codeText: {
    fontSize: 28,
    fontWeight: "bold",
    color: "#000",
  },
  copyButton: {
    backgroundColor: "#fff",
    borderRadius: 6,
    borderWidth: 1,
    paddingVertical: 8,
    paddingHorizontal: 30,
  },
  copyButtonText: {
    fontSize: 15,
    fontWeight: "bold",
  },
  inviteLabel: {
    fontSize: 15,
    fontWeight: "bold",
    marginBottom: 12,
  },
  inputCard: {
    backgroundColor: "#fff",
    borderRadius: 10,
    padding: 18,
    elevation: 4,
    shadowColor: "#000",
    shadowOpacity: 0.2,
    shadowOffset: { width: 0, height: 2 },
    shadowRadius: 4,
    marginLeft: 20,
    marginRight: 20,
  },
  inputLabel: {
    color: "#555",
    fontSize: 13,
    marginBottom: 10,
  },
  input: {
    borderColor: "#000",
    borderWidth: 1,
    borderRadius: 6,
    height: 40,
    marginBottom: 18,
    paddingHorizontal: 10,
  },
  submitButton: {
    backgroundColor: "#3dc1c6",
    borderRadius: 6,
    paddingVertical: 12,
    alignItems: "center",
    marginLeft: 20,
    marginRight: 20,
  },
  submitText: {
    color: "#000",
    fontSize: 15,
    fontWeight: "bold",
  },

  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(0,0,0,0.4)",
    justifyContent: "center",
    alignItems: "center",
  },
  modalBox: {
    backgroundColor: "#fff",
    borderRadius: 10,
    width: "80%",
    padding: 25,
    alignItems: "center",
    position: "relative",
    marginTop: -108,
  },
  closeButton: {
    position: "absolute",
    top: 8,
    right: 10,
  },
  closeText: {
    color: "red",
    fontWeight: "bold",
    fontSize: 16,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: "bold",
    marginBottom: 10,
  },
  modalMessage: {
    textAlign: "center",
    fontSize: 14,
    color: "#444",
    marginTop: 35,
    marginBottom: 35,
  },
});
