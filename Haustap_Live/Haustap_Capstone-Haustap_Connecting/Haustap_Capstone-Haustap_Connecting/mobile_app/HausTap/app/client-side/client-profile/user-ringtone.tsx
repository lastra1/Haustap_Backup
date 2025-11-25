import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

interface RingtoneOption {
  id: string;
  name: string;
}

export default function SelectRingtone() {
  const router = useRouter();
  const [selectedRingtone, setSelectedRingtone] = useState('honk');

  const ringtones: RingtoneOption[] = [
    { id: 'honk', name: 'Honk (Default)' },
    { id: 'bottle', name: 'Bottle' },
    { id: 'bubble', name: 'Bubble' },
    { id: 'bullfrog', name: 'Bullfrog' },
    { id: 'burst', name: 'Burst' },
    { id: 'chirp', name: 'Chirp' },
    { id: 'clank', name: 'Clank' },
    { id: 'crystal', name: 'Crystal' },
    { id: 'fadein', name: 'FadeIn' },
  ];

  const handleRingtoneSelect = (id: string) => {
    setSelectedRingtone(id);
  };

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Select Ringtone</Text>
      </View>

      <ScrollView style={styles.content}>
        {ringtones.map((ringtone) => (
          <TouchableOpacity
            key={ringtone.id}
            style={styles.ringtoneItem}
            onPress={() => handleRingtoneSelect(ringtone.id)}
          >
            <Text style={styles.ringtoneName}>{ringtone.name}</Text>
            <View style={styles.radioButton}>
              {selectedRingtone === ringtone.id && <View style={styles.radioButtonInner} />}
            </View>
          </TouchableOpacity>
        ))}
      </ScrollView>

      <View style={styles.footer}>
        <TouchableOpacity 
          style={styles.confirmButton}
          onPress={() => router.back()}
        >
          <Text style={styles.confirmButtonText}>Confirm</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: 60,
    paddingHorizontal: 16,
    paddingBottom: 20,
    backgroundColor: '#FFFFFF',
  },
  backButton: {
    padding: 4,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: '600',
    marginLeft: 12,
    fontFamily: 'DMSans-Bold',
  },
  content: {
    flex: 1,
  },
  ringtoneItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 16,
    paddingHorizontal: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#F0F0F0',
  },
  ringtoneName: {
    fontSize: 16,
    color: '#000000',
    fontFamily: 'DMSans-Regular',
  },
  radioButton: {
    width: 20,
    height: 20,
    borderRadius: 10,
    borderWidth: 2,
    borderColor: '#00ADB5',
    justifyContent: 'center',
    alignItems: 'center',
  },
  radioButtonInner: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: '#00ADB5',
  },
  footer: {
    padding: 16,
    borderTopWidth: 1,
    borderTopColor: '#F0F0F0',
  },
  confirmButton: {
    backgroundColor: '#00ADB5',
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  confirmButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontFamily: 'DMSans-Medium',
  },
});
