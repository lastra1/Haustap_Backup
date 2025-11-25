import { MaterialIcons } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';
import { Picker } from '@react-native-picker/picker'; // For the dropdown
import { router } from 'expo-router';
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
  const [fullName, setFullName] = useState('Ana Santos');
  const [mobileNumber, setMobileNumber] = useState('09499129312');
  const [email, setEmail] = useState('AnaSantos@gmail.com');
  const [gender, setGender] = useState('Female');
  const [dateOfBirth, setDateOfBirth] = useState(new Date());
  const [showDatePicker, setShowDatePicker] = useState(false);

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <MaterialIcons name="arrow-back" size={24} color="black" />
        <Text style={styles.headerTitle}>Profile</Text>
      </View>

      <ScrollView contentContainerStyle={styles.scrollViewContent}>
        {/* Avatar and Image Selection */}
        <View style={styles.imageSelectionSection}>
          <View style={styles.avatarContainer}>
            <MaterialIcons name="person" size={50} color="black" />
            <TouchableOpacity style={styles.cameraIcon}>
              <MaterialIcons name="camera-alt" size={20} color="white" />
            </TouchableOpacity>
          </View>

          <View style={styles.imageUploadTextContainer}>
            <TouchableOpacity style={styles.selectImageButton}>
              <Text style={styles.selectImageButtonText}>Select image</Text>
            </TouchableOpacity>
            <Text style={styles.imageInfoText}>File size: maximum 1MB</Text>
            <Text style={styles.imageInfoText}>File extension: JPEG, PNG</Text>
          </View>
        </View>

        {/* Form Fields */}
        <View style={styles.formSection}>
          {/* Full Name */}
          <Text style={styles.inputLabel}>Full Name</Text>
          <TextInput
            style={styles.input}
            value={fullName}
            onChangeText={setFullName}
          />

          {/* Mobile Number */}
          <Text style={styles.inputLabel}>Mobile Number</Text>
          <TextInput
            style={styles.input}
            value={mobileNumber}
            onChangeText={setMobileNumber}
            keyboardType="phone-pad"
          />

          {/* Email */}
          <Text style={styles.inputLabel}>Email</Text>
          <TextInput
            style={styles.input}
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
          />

          {/* Gender */}
          <Text style={styles.inputLabel}>Gender</Text>
          <View style={styles.pickerContainer}>
            <Picker
              selectedValue={gender}
              onValueChange={(itemValue, itemIndex) => setGender(itemValue)}
              style={styles.picker}
            >
              <Picker.Item label="Female" value="Female" />
              <Picker.Item label="Male" value="Male" />
              <Picker.Item label="Other" value="Other" />
            </Picker>
            <MaterialIcons name="keyboard-arrow-down" size={24} color="black" style={styles.pickerIcon} />
          </View>

          {/* Date of Birth */}
          <Text style={styles.inputLabel}>Date of Birth</Text>
          <TouchableOpacity
            style={styles.input}
            onPress={() => setShowDatePicker(true)}
          >
            <Text>
              {dateOfBirth instanceof Date
                ? `${dateOfBirth.getFullYear()}-${String(dateOfBirth.getMonth() + 1).padStart(2, '0')}-${String(dateOfBirth.getDate()).padStart(2, '0')}`
                : dateOfBirth}
            </Text>
          </TouchableOpacity>
          {showDatePicker && (
            <DateTimePicker
              value={dateOfBirth instanceof Date ? dateOfBirth : new Date()}
              mode="date"
              display="default"
              maximumDate={new Date()}
              minimumDate={new Date(new Date().setFullYear(new Date().getFullYear() - 100))}
              onChange={(event: any, selectedDate?: Date | undefined) => {
                setShowDatePicker(false);
                if (event.type === 'set' && selectedDate) {
                  setDateOfBirth(selectedDate);
                }
                // If cancelled, keep previous date
              }}
            />
          )}
        </View>

        {/* Action Buttons */}
        <View style={styles.buttonSection}>
          <TouchableOpacity style={styles.saveButton}>
            <Text style={styles.buttonText}>Save</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.changePasswordButton} onPress={() => router.push('./change-password')}>
            <Text style={styles.buttonText}>Change Password</Text>
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
  },
  scrollViewContent: {
    paddingHorizontal: 15,
    paddingBottom: 20, // Add some padding at the bottom for the buttons
  },
  imageSelectionSection: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 20,
    marginBottom: 20,
  },
  avatarContainer: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: '#ffffff', // white background
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
    borderWidth: 1,
    borderColor: '#000000',
  },
  cameraIcon: {
    position: 'absolute',
    bottom: 0,
    right: 0,
    backgroundColor: 'black', // Black background for camera icon
    borderRadius: 15,
    padding: 5,
  },
  imageUploadTextContainer: {
    flex: 1,
  },
  selectImageButton: {
    borderWidth: 1,
    borderColor: '#000000',
    borderRadius: 5,
    paddingVertical: 8,
    paddingHorizontal: 15,
    alignSelf: 'flex-start',
    marginBottom: 5,
  },
  selectImageButtonText: {
    fontSize: 14,
    color: 'black',
  },
  imageInfoText: {
    fontSize: 12,
    color: 'gray',
  },
  formSection: {
    marginBottom: 20,
  },
  inputLabel: {
    fontSize: 14,
    color: 'black',
    marginBottom: 5,
  },
  input: {
    borderWidth: 1,
    borderColor: '#000000',
    borderRadius: 5,
    padding: 10,
    fontSize: 16,
    marginBottom: 15,
  },
  pickerContainer: {
    borderWidth: 1,
    borderColor: '#000000',
    borderRadius: 5,
    marginBottom: 15,
    justifyContent: 'center', // Center the picker content vertically
    height: 44, // Match input height
  },
  picker: {
    height: 44, // Ensure picker takes up full height of container
    width: '100%',
  },
  pickerIcon: {
    position: 'absolute',
    right: 10,
  },
  buttonSection: {
    marginTop: 20,
  },
  saveButton: {
    backgroundColor: '#3dc1c6', 
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginBottom: 10,
  },
  changePasswordButton: {
    backgroundColor: '#d9d9d9',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
  },
  buttonText: {
    color: 'black',
    fontSize: 18,
    fontWeight: 'bold',
  },
});
