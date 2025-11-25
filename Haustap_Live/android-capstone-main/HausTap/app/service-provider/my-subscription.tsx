import { Ionicons, MaterialCommunityIcons } from '@expo/vector-icons'; // For back arrow and right arrow
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
          onPress={() => router.push('/service-provider/my-account')}
          accessibilityLabel="Go back to My Account"
        >
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>My Subscription</Text>
      </View>


      <ScrollView contentContainerStyle={styles.content}>
        {/* Membership Details Section */}
        <Text style={styles.sectionTitle}>Membership Details</Text>
        <Text style={styles.membershipDate}>Member since September 2022</Text>


        {/* Subscription Info Card */}
        <View style={styles.subscriptionCard}>
          <Text style={styles.cardStatus}>Active — ₱499 / month</Text>
          <Text style={styles.cardDetail}>Next payment: November 20, 2025</Text>
          <TouchableOpacity
            style={styles.paymentMethod}
            onPress={() => router.push('/service-provider/payment-method')}
            accessibilityLabel="Change payment method"
          >
            <MaterialCommunityIcons name="google" size={18} color="#4285F4" />
            <Text style={styles.paymentText}>63-09***36246</Text>
          </TouchableOpacity>
        </View>


        {/* View Payment History Button */}
        <TouchableOpacity
          style={styles.paymentHistoryButton}
          onPress={() => router.push('/service-provider/payment-history')}
          accessibilityLabel="View Payment History"
        >
          <Text style={styles.paymentHistoryText}>View Payment History</Text>
          <Ionicons name="chevron-forward" size={20} color="#777" />
        </TouchableOpacity>
      </ScrollView>


      {/* Unsubscribe Button (Fixed at the bottom) */}
      <View style={styles.footer}>
        <TouchableOpacity
          style={styles.unsubscribeButton}
          onPress={() => router.push('/service-provider/unsubscription')}
          accessibilityLabel="Unsubscribe"
        >
          <Text style={styles.unsubscribeButtonText}>Unsubscribe</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}


// Colors used in the UI
const HAUSTAP_TURQUOISE_BORDER = '#39ceb8'; // For the subscription card border


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
    borderBottomWidth: 1,
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
    padding: 20,
    paddingBottom: 20, // Space for the fixed footer button
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 5,
    marginTop: 10,
  },
  membershipDate: {
    fontSize: 13,
    color: '#666',
    marginBottom: 20,
  },
  // --- Subscription Card Styles ---
  subscriptionCard: {
    borderWidth: 1,
    borderColor: HAUSTAP_TURQUOISE_BORDER,
    borderRadius: 8,
    padding: 15,
    marginBottom: 20,
  },
  cardStatus: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  cardDetail: {
    fontSize: 14,
    color: '#000',
    marginBottom: 10,
  },
  paymentMethod: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  paymentText: {
    fontSize: 14,
    color: '#000',
    fontWeight: 'bold',
    marginLeft: 8, // Space between icon and text
  },
  // --- Payment History Button Styles ---
  paymentHistoryButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: 'white',
    borderWidth: 1,
    borderColor: '#000',
    borderRadius: 8,
    padding: 15,
    marginBottom: 20,
  },
  paymentHistoryText: {
    fontSize: 16,
    color: '#333',
  },
  // --- Footer Button Styles ---
  footer: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: 20,
    backgroundColor: 'white',
    borderTopColor: '#eee',
  },
  unsubscribeButton: {
    backgroundColor: 'white', // White background as per image
    borderColor: '#ccc', // Gray border as per image
    borderWidth: 1,
    borderRadius: 8, // Rounded corners
    padding: 15,
    alignItems: 'center',
  },
  unsubscribeButtonText: {
    color: 'black',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
