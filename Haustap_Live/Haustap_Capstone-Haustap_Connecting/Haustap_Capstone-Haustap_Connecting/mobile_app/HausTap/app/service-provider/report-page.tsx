import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
import React, { useState } from "react";
import {
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";


export default function ReportClientScreen() {
  const [selectedReason, setSelectedReason] = useState("");
  const [notes, setNotes] = useState("");


  const reasons = [
    "Client not at the location",
    "Wrong or incomplete address",
    "Asked for extra tasks not booked",
    "Did not answer calls or messages",
    "Cancelation after service",
    "Was rude or aggressive",
    "Gave wrong job details or size",
    "Changed schedule without notice",
  ];


  return (
    <ScrollView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
  <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={22} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Report Client</Text>
        <View style={{ width: 22 }} /> {/* spacer for alignment */}
      </View>


      {/* Question */}
      <Text style={styles.question}>
        Why are you requesting to report your client?
      </Text>


      {/* Report Details */}
      <Text style={styles.sectionTitle}>Report details</Text>


      {reasons.map((reason, index) => (
        <TouchableOpacity
          key={index}
          style={[
            styles.reasonButton,
            selectedReason === reason && styles.reasonSelected,
          ]}
          onPress={() => setSelectedReason(reason)}
        >
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


      {/* Notes */}
      <Text style={[styles.sectionTitle, { marginTop: 20 }]}>
        Additional Notes
      </Text>
      <TextInput
        style={styles.notesInput}
        multiline
        placeholder="Type your notes here..."
        value={notes}
        onChangeText={setNotes}
      />


      {/* Submit */}
      <TouchableOpacity
        style={[
          styles.submitButton,
          !selectedReason && { opacity: 0.5 },
        ]}
        disabled={!selectedReason}
        onPress={() => alert("Report submitted")}
      >
        <Text style={styles.submitText}>Submit</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}


const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#FAFAFA",
    paddingHorizontal: 20,
    paddingTop: 60,
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: "600",
    color: "#000",
  },
  question: {
    fontSize: 15,
    color: "#000",
    marginTop: 20,
    marginBottom: 10,
  },
  sectionTitle: {
    fontSize: 14,
    color: "#555",
    marginBottom: 8,
  },
  reasonButton: {
    borderWidth: 1,
    borderColor: "#E0E0E0",
    borderRadius: 6,
    paddingVertical: 10,
    paddingHorizontal: 12,
    backgroundColor: "#fff",
    marginBottom: 10,
  },
  reasonSelected: {
    borderColor: "#00B0B9",
    backgroundColor: "#E0F7F9",
  },
  reasonText: {
    color: "#333",
    fontSize: 14,
  },
  reasonTextSelected: {
    color: "#00B0B9",
    fontWeight: "600",
  },
  notesInput: {
    backgroundColor: "#fff",
    borderWidth: 1,
    borderColor: "#E0E0E0",
    borderRadius: 8,
    height: 100,
    padding: 10,
    textAlignVertical: "top",
    fontSize: 14,
  },
  submitButton: {
    backgroundColor: "#00B0B9",
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: "center",
    marginTop: 25,
    marginBottom: 40,
  },
  submitText: {
    color: "#fff",
    fontSize: 15,
    fontWeight: "600",
  },
});
