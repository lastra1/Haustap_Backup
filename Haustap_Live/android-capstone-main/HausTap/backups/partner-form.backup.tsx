// Backup of app/(partner)/partner-form.tsx
// Created before consolidation

import { useLocalSearchParams } from 'expo-router';
import React from 'react';
import { StyleSheet, Text, View } from 'react-native';

export default function PartnerForm() {
  const { email, services } = useLocalSearchParams();
  const selectedServices = services ? JSON.parse(decodeURIComponent(services as string)) : [];
  
  return (
    <View style={styles.container}>
      <Text style={styles.header}>Complete Your Profile</Text>
      {/* Add your form fields here */}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    padding: 20,
  },
  header: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 20,
    color: '#333',
  },
});
