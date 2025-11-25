import { MaterialIcons } from '@expo/vector-icons';
import { useRouter, useSegments } from 'expo-router';
import React, { useEffect, useState } from 'react';
import {
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';

export default function App() {
  const router = useRouter();
  const segments = useSegments();
  const [addresses, setAddresses] = useState<any[]>([]);
  const { addressStore } = require('../../src/services/addressStore');

  useEffect(() => {
    console.log('[address] mounted, segments:', segments);
    try {
      console.log('[address] router keys:', Object.keys(Object.getPrototypeOf(router)).slice(0, 20));
    } catch (err) {
      console.log('[address] router (raw):', router);
    }
    // load addresses from store
    try {
      const store = require('../../src/services/addressStore').addressStore;
      setAddresses(store.getAddresses());
    } catch (e) {
      console.log('[address] failed to load addressStore', e);
    }
  }, [router, segments]);

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => { console.log('[address] back pressed'); router.back(); }}>
          <MaterialIcons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Addresses</Text>
        <TouchableOpacity style={styles.addButton} onPress={() => { console.log('[address] add pressed'); router.push('/service-provider/add-address'); }} accessibilityLabel="Add Address">
          <Text style={styles.addButtonText}>Add</Text>
        </TouchableOpacity>
      </View>

      <ScrollView contentContainerStyle={styles.scrollViewContent}>
        {addresses.length === 0 ? (
          <View style={{ padding: 20 }}>
            <Text style={{ color: '#666' }}>No addresses yet. Tap Add to create one.</Text>
          </View>
        ) : (
          addresses.map(addr => (
            <View key={addr.id} style={styles.addressCard}>
              <View style={styles.addressCardHeader}>
                <Text style={styles.addressTitle}>{addr.houseNumber} {addr.street}</Text>
                <TouchableOpacity
                  style={styles.setDefaultButton}
                  onPress={() => console.log('Set as default', addr.id)}
                >
                  <Text style={styles.setDefaultButtonText}>Set as Default</Text>
                </TouchableOpacity>
              </View>
              <Text style={styles.addressDetails}>
                {addr.barangayName} {'\n'}{addr.municipal}, {addr.province}
              </Text>
              <View style={styles.addressActions}>
                <TouchableOpacity
                  style={styles.actionButton}
                  onPress={() => router.push(`/service-provider/edit-address?id=${addr.id}`)}
                >
                  <Text style={[styles.actionButtonText, styles.underlineText]}>Edit</Text>
                </TouchableOpacity>
                <TouchableOpacity
                  style={styles.actionButton}
                  onPress={() => {
                    const store = require('../../src/services/addressStore').addressStore;
                    store.deleteAddress(addr.id);
                    setAddresses(store.getAddresses());
                  }}
                >
                  <Text style={[styles.actionButtonText, styles.underlineText]}>Delete</Text>
                </TouchableOpacity>
              </View>
            </View>
          ))
        )}
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
    borderBottomColor: '#ffffff',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    marginLeft: 10,
    flex: 1, // Allows title to take up available space
  },
  addButton: {
    backgroundColor: '#e0e0e0', // Light grey background
    paddingVertical: 5,
    paddingHorizontal: 15,
    borderRadius: 11,
  },
  addButtonText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: 'black',
  },
  scrollViewContent: {
    paddingHorizontal: 15,
    paddingTop: 50,
    paddingBottom: 20,
  },
  addressCard: {
    backgroundColor: '#fff',
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#ddd',
    padding: 15,
    marginBottom: 15, // Space between cards if more are added
    shadowColor: '#000', // Subtle shadow
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 1,
    elevation: 2, // For Android shadow
  },
  addressCardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  addressTitle: {
    fontSize: 14,
    fontWeight: 'bold',
  },
  setDefaultButton: {
    borderWidth: 1,
    borderColor: '#3dc1c6', 
    borderRadius: 5,
    paddingVertical: 4,
    paddingHorizontal: 8,
  },
  setDefaultButtonText: {
    fontSize: 12,
    color: '#000000',
    fontWeight: '500',
  },
  addressDetails: {
    fontSize: 14,
    color: 'gray',
    marginBottom: 15,
    lineHeight: 20,
  },
  addressActions: {
    flexDirection: 'row',
    justifyContent: 'flex-end', // Align actions to the right
  },
  actionButton: {
    marginLeft: 20, // Space between action buttons
  },
  actionButtonText: {
    fontSize: 14,
    color: 'black', // Black text for actions
    fontWeight: 'bold',
  },
  underlineText: {
    textDecorationLine: 'underline', // Add this style for underline
  },
});
