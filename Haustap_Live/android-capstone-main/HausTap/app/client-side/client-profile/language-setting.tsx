import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import { Platform, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

export default function LanguageSetting() {
  const router = useRouter();
  const [language, setLanguage] = useState<'english' | 'tagalog'>('english');

  const handleConfirm = () => {
    // TODO: persist user language preference (AsyncStorage / API)
    router.back();
  };

  return (
    <ScrollView style={styles.container} contentContainerStyle={{ paddingBottom: 96 }}>
      <View style={styles.headerRow}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Language Setting</Text>
      </View>

      <View style={styles.optionRow}>
        <Text style={styles.optionLabel}>English</Text>
        <TouchableOpacity style={styles.radio} onPress={() => setLanguage('english')}>
          {language === 'english' && <View style={styles.radioInner} />}
        </TouchableOpacity>
      </View>

      <View style={styles.optionRow}>
        <Text style={styles.optionLabel}>Tagalog</Text>
        <TouchableOpacity style={styles.radio} onPress={() => setLanguage('tagalog')}>
          {language === 'tagalog' && <View style={styles.radioInner} />}
        </TouchableOpacity>
      </View>

      <View style={{ flex: 1 }} />

      <TouchableOpacity style={styles.confirmBtn} onPress={handleConfirm}>
        <Text style={styles.confirmText}>Confirm</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingHorizontal: 18,
    paddingTop: Platform.OS === 'ios' ? 44 : 20,
  },
  headerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  backBtn: { padding: 8, marginRight: 8 },
  headerTitle: { fontSize: 16, fontWeight: '600' },
  optionRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  optionLabel: { fontSize: 15 },
  radio: {
    width: 22,
    height: 22,
    borderRadius: 11,
    borderWidth: 2,
    borderColor: '#888',
    alignItems: 'center',
    justifyContent: 'center',
  },
  radioInner: {
    width: 12,
    height: 12,
    borderRadius: 6,
    backgroundColor: '#00BDB2',
  },
  confirmBtn: {
    backgroundColor: '#00BDB2',
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: 'center',
    marginHorizontal: 48,
    marginBottom: 24,
  },
  confirmText: { color: '#fff', fontWeight: '600' },
});
