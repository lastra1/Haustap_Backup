import { Ionicons } from "@expo/vector-icons";
import { Picker } from "@react-native-picker/picker";
import { useLocalSearchParams, useRouter } from "expo-router";
import React, { useEffect, useState } from "react";
import {
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";

export default function BookingEditAddress() {
  const router = useRouter();

  const [houseNumber, setHouseNumber] = useState("");
  const [street, setStreet] = useState("");
  const [barangay, setBarangay] = useState("");
  const [municipal, setMunicipal] = useState("");
  const [province, setProvince] = useState("");
  const { 
    address,
    categoryTitle,
    categoryPrice,
    categoryDesc,
    mainCategory,
    subCategory,
    service,
    selectedItems,
  } = useLocalSearchParams();

  useEffect(() => {
    if (address) {
      // try to prefill basic fields from incoming address string
      const str = String(address);
      const lines = str.split(/\r?\n/).map((l) => l.trim()).filter(Boolean);
      if (lines.length > 0) {
        // put first line into street (may include house number)
        const first = lines[0];
        // naive split: try to find first space after a short token for house number
        const parts = first.split(" ");
        if (parts.length > 1) {
          // if it starts with 'Blk' or a number, treat the first two tokens as houseNumber
          if (/^Blk|^Lot|^\d+/i.test(parts[0])) {
            setHouseNumber(parts.slice(0, 2).join(" "));
            setStreet(parts.slice(2).join(" "));
          } else {
            setStreet(first);
          }
        } else {
          setStreet(first);
        }
      }
      if (lines.length > 1) {
        const second = lines[1];
        // try to extract barangay if contains 'Brgy' or 'Brgy.'
        const brgyMatch = second.match(/Brgy\.?\s*([^,]+)/i);
        if (brgyMatch) setBarangay(brgyMatch[1].trim());
        // attempt to extract municipal/province from remaining text
        const remaining = second.replace(/Brgy\.?\s*[^,]+,?\s*/i, "").trim();
        if (remaining) {
          // split by space - take last token as province if there is one
          const tokens = remaining.split(" ");
          if (tokens.length >= 2) {
            setMunicipal(tokens.slice(0, tokens.length - 1).join(" "));
            setProvince(tokens[tokens.length - 1]);
          } else {
            setMunicipal(remaining);
          }
        }
      }
    }
  }, [address]);

  function onSubmit() {
    // compose a display address and return to booking-location with it
    const firstLine = `${houseNumber ? houseNumber + ' ' : ''}${street}`.trim();
    const secondLine = `${barangay ? 'Brgy. ' + barangay + ' ' : ''}${municipal ? municipal + ' ' : ''}${province ? province : ''}`.trim();
    const composed = [firstLine, secondLine].filter(Boolean).join('\n');
    router.replace({ 
      pathname: '/client-side/booking-process/booking-location', 
      params: { 
        address: composed,
        categoryTitle,
        categoryPrice,
        categoryDesc,
        mainCategory,
        subCategory,
        service,
        selectedItems,
      } 
    } as any);
  }

  function onDelete() {
    // clear fields and navigate back with empty address
    setHouseNumber("");
    setStreet("");
    setBarangay("");
    setMunicipal("");
    setProvince("");
    router.replace({ 
      pathname: '/client-side/booking-process/booking-location', 
      params: { 
        address: '',
        categoryTitle,
        categoryPrice,
        categoryDesc,
        mainCategory,
        subCategory,
        service,
        selectedItems,
      } 
    } as any);
  }

  return (
    <ScrollView style={styles.container} contentContainerStyle={{ paddingBottom: 40 }}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Edit Address</Text>
        <TouchableOpacity onPress={onDelete} style={styles.deleteButton}>
          <Text style={styles.deleteText}>Delete</Text>
        </TouchableOpacity>
      </View>

      <Text style={styles.sectionTitle}>Address Details</Text>

      <View style={styles.row}>
        <View style={styles.half}>
          <Text style={styles.label}>House Number</Text>
          <TextInput
            style={styles.input}
            placeholder="House Number"
            value={houseNumber}
            onChangeText={setHouseNumber}
          />
        </View>
        <View style={styles.half}>
          <Text style={styles.label}>Street</Text>
          <TextInput
            style={styles.input}
            placeholder="Street"
            value={street}
            onChangeText={setStreet}
          />
        </View>
      </View>

      <Text style={styles.label}>Barangay Name</Text>
      <View style={styles.pickerWrapper}>
        <Picker
          selectedValue={barangay}
          onValueChange={(v) => setBarangay(v)}
          style={styles.picker}
        >
          <Picker.Item label="Select barangay" value="" />
          <Picker.Item label="Langgam" value="Langgam" />
          <Picker.Item label="Laram" value="Laram" />
          <Picker.Item label="Other" value="Other" />
        </Picker>
      </View>

      <Text style={styles.label}>Municipal</Text>
      <View style={styles.pickerWrapper}>
        <Picker
          selectedValue={municipal}
          onValueChange={(v) => setMunicipal(v)}
          style={styles.picker}
        >
          <Picker.Item label="Select municipal" value="" />
          <Picker.Item label="San Pedro" value="San Pedro" />
          <Picker.Item label="Biñan" value="Binan" />
        </Picker>
      </View>

      <Text style={styles.label}>Province</Text>
      <View style={styles.pickerWrapper}>
        <Picker
          selectedValue={province}
          onValueChange={(v) => setProvince(v)}
          style={styles.picker}
        >
          <Picker.Item label="Select province" value="" />
          <Picker.Item label="Laguna" value="Laguna" />
          <Picker.Item label="Cavite" value="Cavite" />
        </Picker>
      </View>

      <TouchableOpacity style={styles.submitBtn} onPress={onSubmit}>
        <Text style={styles.submitText}>Submit</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
    paddingHorizontal: 16,
    paddingTop: 40,
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 20,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: "600",
    marginLeft: 10,
    flex: 1,
  },
  deleteButton: {
    paddingHorizontal: 8,
    paddingVertical: 4,
  },
  deleteText: {
    color: "#999",
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: "700",
    marginBottom: 12,
  },
  row: {
    flexDirection: "row",
    gap: 12,
    marginBottom: 12,
  },
  half: {
    flex: 1,
  },
  label: {
    fontSize: 12,
    color: "#444",
    marginBottom: 6,
  },
  input: {
    backgroundColor: "#F5F5F5",
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 12,
    fontSize: 14,
    borderWidth: 0,
  },
  pickerWrapper: {
    backgroundColor: "#F5F5F5",
    borderRadius: 8,
    marginBottom: 12,
    overflow: "hidden",
  },
  picker: {
    height: 48,
  },
  submitBtn: {
    backgroundColor: "#00ADB5",
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: "center",
    marginTop: 16,
  },
  submitText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "700",
  },
});
