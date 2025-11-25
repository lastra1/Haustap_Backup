import { MaterialIcons } from '@expo/vector-icons';
import { useRouter, useSegments } from 'expo-router';
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
  const segments = useSegments();

  useEffect(() => {
    console.log('[new-password] mounted, segments:', segments);
    try {
      console.log('[new-password] router keys:', Object.keys(Object.getPrototypeOf(router)).slice(0, 20));
    } catch (err) {
      console.log('[new-password] router (raw):', router);
    }
  }, [router, segments]);

  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmNewPassword, setConfirmNewPassword] = useState('');

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <MaterialIcons name="arrow-back" size={24} color="black" />
        <Text style={styles.headerTitle}>Change Password</Text>
      </View>

      <ScrollView contentContainerStyle={styles.scrollViewContent}>
        {/* Current Password */}
        <Text style={styles.inputLabel}>Current Password</Text>
        <TextInput
          style={styles.input}
          secureTextEntry={true}
          value={currentPassword}
          onChangeText={setCurrentPassword}
          placeholder="Enter current password"
        />

        {/* New Password */}
        <Text style={styles.inputLabel}>New Password</Text>
        <TextInput
          style={styles.input}
          secureTextEntry={true}
          value={newPassword}
          onChangeText={setNewPassword}
          placeholder="Enter new password"
        />

        {/* Confirm New Password */}
        <Text style={styles.inputLabel}>Confirm New Password</Text>
        <TextInput
          style={styles.input}
          secureTextEntry={true}
          value={confirmNewPassword}
          onChangeText={setConfirmNewPassword}
          placeholder="Re-enter new password"
        />

        {/* Save Button */}
        <TouchableOpacity style={styles.saveButton}>
          <Text style={styles.saveButtonText}>Save</Text>
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
  },
  scrollViewContent: {
    paddingHorizontal: 15,
    paddingTop: 50,
    paddingBottom: 20,
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
    marginBottom: 30, // Space between inputs
  },
  saveButton: {
    backgroundColor: '#3dc1c6',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 20, // Space above button
  },
  saveButtonText: {
    color: 'black',
    fontSize: 18,
    fontWeight: 'bold',
  },
});
