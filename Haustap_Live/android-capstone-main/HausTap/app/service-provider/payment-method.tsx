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
        <Text style={styles.headerTitle}>Payment Methods</Text>
        <TouchableOpacity style={styles.editButton}>
          <MaterialCommunityIcons name="pencil" size={20} color="black" />
        </TouchableOpacity>
      </View>


      <ScrollView contentContainerStyle={styles.content}>
        {/* Existing Payment Method */}
        <TouchableOpacity
          style={styles.paymentMethodItem}
          onPress={() => router.push('/service-provider/my-subscription-voucher')}
          accessibilityLabel="Open subscription vouchers"
        >
          <MaterialCommunityIcons name="google" size={18} color="#4285F4" />
          <Text style={styles.paymentMethodText}>63-09***36246</Text>
        </TouchableOpacity>


        {/* Separator */}
        <View style={styles.separator} />


        {/* Add Payment Method Button */}
        <TouchableOpacity style={styles.addPaymentButton}>
          <Text style={styles.addPaymentButtonText}>Add payment method</Text>
          <Ionicons name="chevron-forward" size={20} color="#777" />
        </TouchableOpacity>
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
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  backButton: {
    paddingRight: 15,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    flex: 1, // Allows title to take up space and push edit button to the right
  },
  editButton: {
    paddingLeft: 15,
  },
  content: {
    paddingTop: 10, // Small padding from header
  },
  // --- Existing Payment Method Item ---
  paymentMethodItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 15,
  },
  paymentMethodText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#000',
    marginLeft: 10, // Space between icon and text
  },
  separator: {
    height: 2,
    backgroundColor: '#eee',
    marginLeft: 20, // Indent the separator
    marginRight: 20,
  },
  // --- Add Payment Method Button ---
  addPaymentButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    paddingVertical: 15,
  },
  addPaymentButtonText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#000',
  },
});
