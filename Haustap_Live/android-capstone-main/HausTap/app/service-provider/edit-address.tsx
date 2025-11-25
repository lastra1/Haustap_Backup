import { MaterialIcons } from '@expo/vector-icons';
import { Picker } from '@react-native-picker/picker';
import { useLocalSearchParams, useRouter, useSegments } from 'expo-router';
import React, { useEffect, useState } from 'react';
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
  const params = useLocalSearchParams();
  const segments = useSegments();

  useEffect(() => {
    console.log('[edit-address] mounted, segments:', segments);
    console.log('[edit-address] params:', params);
    // if an id param is provided, prefill with example data
    if (params.id === 'home') {
      setHouseNumber('B3 L1');
      setStreet('Apple st.');
      setBarangayName('Laram');
      setMunicipal('San Pedro');
      setProvince('Laguna');
    }
  }, [params, segments]);
  const [houseNumber, setHouseNumber] = useState('');
  const [street, setStreet] = useState('');
  const [barangayName, setBarangayName] = useState('');
  const [municipal, setMunicipal] = useState('');
  const [province, setProvince] = useState('');

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => { console.log('[edit-address] back pressed'); router.back(); }}>
          <MaterialIcons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Edit Address</Text>
        <TouchableOpacity style={styles.deleteButton} onPress={() => { console.log('[edit-address] delete pressed for id:', params.id); /* implement delete */ router.back(); }}>
          <Text style={styles.deleteButtonText}>Delete</Text>
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.scrollViewContent}>
        <Text style={styles.sectionTitle}>Address Details</Text>

        {/* House Number and Street */}
        <View style={styles.row}>
          <View style={styles.halfWidthInputContainer}>
            <Text style={styles.inputLabel}>House Number</Text>
            <TextInput
              style={styles.input}
              value={houseNumber}
              onChangeText={setHouseNumber}
              placeholder=""
            />
          </View>
          <View style={styles.halfWidthInputContainer}>
            <Text style={styles.inputLabel}>Street</Text>
            <TextInput
              style={styles.input}
              value={street}
              onChangeText={setStreet}
              placeholder=""
            />
          </View>
        </View>

        {/* Barangay Name */}
        <Text style={styles.inputLabel}>Barangay Name</Text>
        <View style={styles.pickerContainer}>
          <Picker
            selectedValue={barangayName}
            onValueChange={(itemValue, itemIndex) => setBarangayName(itemValue)}
            style={styles.picker}
          >
            <Picker.Item label="" value="" />
            <Picker.Item label="Laram" value="Laram" />
            <Picker.Item label="San Roque" value="San Roque" />
          </Picker>
          <MaterialIcons name="keyboard-arrow-down" size={24} color="black" style={styles.pickerIcon} />
        </View>

        {/* Municipal */}
        <Text style={styles.inputLabel}>Municipal</Text>
        <View style={styles.pickerContainer}>
          <Picker
            selectedValue={municipal}
            onValueChange={(itemValue, itemIndex) => setMunicipal(itemValue)}
            style={styles.picker}
          >
            <Picker.Item label="" value="" />
            <Picker.Item label="San Pedro" value="San Pedro" />
            <Picker.Item label="Biñan" value="Biñan" />
          </Picker>
          <MaterialIcons name="keyboard-arrow-down" size={24} color="black" style={styles.pickerIcon} />
        </View>

        {/* Province */}
        <Text style={styles.inputLabel}>Province</Text>
        <View style={styles.pickerContainer}>
          <Picker
            selectedValue={province}
            onValueChange={(itemValue, itemIndex) => setProvince(itemValue)}
            style={styles.picker}
          >
            <Picker.Item label="" value="" />
            <Picker.Item label="Laguna" value="Laguna" />
            <Picker.Item label="Cavite" value="Cavite" />
          </Picker>
          <MaterialIcons name="keyboard-arrow-down" size={24} color="black" style={styles.pickerIcon} />
        </View>

        {/* Submit Button */}
        <TouchableOpacity style={styles.submitButton}>
          <Text style={styles.submitButtonText}>Submit</Text>
        </TouchableOpacity>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingTop: 50, // Adjust for status bar
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 15,
    paddingBottom: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    marginLeft: 10,
    flex: 1, // Allows title to take up available space
  },
  deleteButton: {
    // textDecorationLine: 'underline', // If delete needs to be underlined
  },
  deleteButtonText: {
    fontSize: 14,
    color: 'black',
  },
  scrollViewContent: {
    paddingHorizontal: 15,
    paddingTop: 20,
    paddingBottom: 20,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 15, // Space after the row
  },
  halfWidthInputContainer: {
    width: '48%', // Roughly half width with some spacing
  },
  inputLabel: {
    fontSize: 14,
    color: 'gray',
    marginBottom: 5,
  },
  input: {
    borderWidth: 0,
    borderColor: '#ccc',
    backgroundColor: '#f6f6f6', 
    borderRadius: 5,
    padding: 10,
    fontSize: 16,
    height: 44, // Consistent height with pickers
  },
  pickerContainer: {
    borderWidth: 0,
    backgroundColor: '#f6f6f6', 
    borderRadius: 5,
    marginBottom: 15,
    justifyContent: 'center',
    height: 44,
  },
  picker: {
    height: 44,
    width: '100%',
  },
  pickerIcon: {
    position: 'absolute',
    right: 10,
  },
  pickerBackground: {
    backgroundColor: '#f6f6f6',
  },
  submitButton: {
    backgroundColor: '#3dc1c6', 
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 30, // Space above button
  },
  submitButtonText: {
    color: 'black',
    fontSize: 18,
    fontWeight: 'bold',
  },
});
