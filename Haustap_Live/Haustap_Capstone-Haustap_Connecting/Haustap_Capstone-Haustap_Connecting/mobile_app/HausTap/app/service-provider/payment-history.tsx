import { Ionicons, MaterialCommunityIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React from 'react';
import {
    ScrollView,
    StyleSheet,
    Text,
    TouchableOpacity,
    View,
} from 'react-native';


export default function App() {
  const router = useRouter();
  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => router.push('/service-provider/my-subscription')}
          accessibilityLabel="Go back to My Subscription"
        >
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Payment History</Text>
      </View>


      <ScrollView contentContainerStyle={styles.content}>
        {/* Payment Entry Item */}
        <View style={styles.paymentItem}>
          <View style={styles.paymentInfo}>
            <Text style={styles.paymentDate}>October 19, 2025</Text>
            <Text style={styles.paymentDescription}>
              Subscription for October 20, 2025 - November 20, 2025
            </Text>
            <View style={styles.paymentMethod}>
              
              {/* CORRECTED: Letter 'G' Icon (Google) */}
              <MaterialCommunityIcons name="google" size={18} color="#4285F4" />
              
              <Text style={styles.paymentMethodText}>63-09***36246</Text>
            </View>
          </View>
          <Text style={styles.paymentAmount}>₱499</Text>
        </View>


        {/* Separator Line */}
        <View style={styles.separator} />


        {/* You can add more payment items here */}
      </ScrollView>
    </View>
  );
}


const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: 'white',
    paddingTop: 40, // For status bar
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 15,
    paddingVertical: 10,
    borderBottomWidth: 2,
    borderBottomColor: '#eee',
  },
  backButton: {
    paddingRight: 15,
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: 'bold',
  },
  content: {
    paddingHorizontal: 20, // Padding on left and right for content
    paddingTop: 15, // Padding from header
  },
  // --- Payment Item Styles ---
  paymentItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 15, 
  },
  paymentInfo: {
    flex: 1, 
    marginRight: 10, 
  },
  paymentDate: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 3,
  },
  paymentDescription: {
    fontSize: 13,
    color: '#555',
    marginBottom: 5,
  },
  paymentMethod: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  // Corrected icon implementation: now uses MaterialCommunityIcons 'google'
  paymentMethodText: {
    fontSize: 13, 
    color: '#000',
    marginLeft: 8, // Space between icon and text
  },
  paymentAmount: {
    fontSize: 16,
    fontWeight: 'bold',
    color: 'black',
  },
  separator: {
    height: 2,
    backgroundColor: '#eee',
    marginBottom: 15, 
  },
});
