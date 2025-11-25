import { Image } from 'expo-image';
import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useState } from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import logo from '../assets/images/logo.png';
import { flowStore } from '../src/services/flowStore';

const SERVICES = [
  {
    category: 'Cleaning Services',
    options: ['Home cleaning', 'AC cleaning'],
  },
  {
    category: 'Indoor Services',
    options: ['Carpentry', 'Plumbing', 'Electrical', 'Appliance Repair', 'Pest Control'],
  },
  {
    category: 'Outdoor Services',
    options: ['Gardening & Landscaping', 'Pest Control'],
  },
  {
    category: 'Beauty Services',
    options: ['Hair Services', 'Nail Care', 'Make-up', 'Lashes'],
  },
  {
    category: 'Tech & Gadget Services',
    options: ['Mobile Phone', 'Laptop & Desktop PC', 'Tablet & Ipad', 'Game & Console'],
  },
  {
    category: 'Wellness Services',
    options: ['Massage'],
  },
];

export default function PartnerServicesScreen() {
  const router = useRouter();
  const { email } = useLocalSearchParams();
  const [selectedServices, setSelectedServices] = useState<string[]>([]);

  // debug: log incoming email param (may be encoded or decoded depending on router)
  const decodedEmail = email ? decodeURIComponent(email as string) : '';
  console.log('[partner-services] received email param:', email, 'decoded:', decodedEmail);
  if (decodedEmail) {
    flowStore.setEmail(decodedEmail);
  }

  const toggleService = (service: string) => {
    setSelectedServices((prev) =>
      prev.includes(service) ? prev.filter((s) => s !== service) : [...prev, service]
    );
  };

  const handleNext = () => {
    console.log('Selected Services:', selectedServices);
    // Navigate to Terms & Agreement and pass selected services as a search param
    const payload = encodeURIComponent(JSON.stringify(selectedServices));
    // include email if available so terms and verification screens can display/use it
    // decode then re-encode to avoid double-encoding issues
    const emailParam = decodedEmail ? `&email=${encodeURIComponent(decodedEmail)}` : '';
    console.log('[partner-services] navigating to terms with services payload and email:', decodedEmail);
    router.push(`/terms-and-agreement?services=${payload}${emailParam}`);
  };

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Image source={logo} style={styles.logo} contentFit="contain" />
      <Text style={styles.header}>What Services do you offer?</Text>
      {SERVICES.map((section) => (
        <View key={section.category} style={styles.section}>
          <Text style={styles.category}>{section.category}</Text>
          {section.options.map((option) => (
            <TouchableOpacity
              key={option}
              style={styles.optionContainer}
              onPress={() => toggleService(option)}
            >
              <View style={styles.checkbox}>
                {selectedServices.includes(option) && <View style={styles.checked} />}
              </View>
              <Text style={styles.optionLabel}>{option}</Text>
            </TouchableOpacity>
          ))}
        </View>
      ))}

      <TouchableOpacity style={styles.nextButton} onPress={handleNext}>
        <Text style={styles.nextButtonText}>Next</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { padding: 20, alignItems: 'center', backgroundColor: '#f5f5f5', flexGrow: 1 },
  logo: { width: 120, height: 120, marginBottom: 10 },
  header: { fontSize: 18, fontWeight: 'bold', marginBottom: 15, textAlign: 'center' },
  section: { width: '100%', marginBottom: 15, padding: 10, backgroundColor: '#e0e0e0', borderRadius: 10 },
  category: { fontWeight: 'bold', marginBottom: 5 },
  optionContainer: { flexDirection: 'row', alignItems: 'center', marginVertical: 3 },
  checkbox: { width: 20, height: 20, borderWidth: 1, borderColor: '#3DC1C6', marginRight: 8, justifyContent: 'center', alignItems: 'center' },
  checked: { width: 12, height: 12, backgroundColor: '#3DC1C6' },
  optionLabel: { fontSize: 14 },
  nextButton: { backgroundColor: '#3DC1C6', borderRadius: 10, paddingVertical: 12, paddingHorizontal: 50, marginTop: 20 },
  nextButtonText: { color: '#fff', fontWeight: 'bold', fontSize: 16 },
});
