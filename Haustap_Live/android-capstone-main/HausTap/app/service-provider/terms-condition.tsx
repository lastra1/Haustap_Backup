import React from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
// Make sure to install react-native-vector-icons:
// npm install react-native-vector-icons
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';


const TermsAndConditionsScreen = () => {
  const router = useRouter();
  return (
    <View style={styles.container}>
      {/* Status Bar Placeholder - usually handled by Safe Area Context */}
      <View style={styles.statusBarPlaceholder} />


      {/* Navigation Bar */}
      <View style={styles.navBar}>
        <TouchableOpacity onPress={() => router.push('/service-provider/my-account')} accessibilityLabel="Go back to My Account">
          <Ionicons name="arrow-back" size={28} color="#000" />
        </TouchableOpacity>
        <Text style={styles.navTitle}>Terms & Conditions</Text>
        <View style={{ width: 172 }} /> {/* Spacer to balance the back icon */}
      </View>


      <ScrollView contentContainerStyle={styles.contentContainer}>
        {/* Main Heading */}
        <Text style={styles.mainHeading}>HausTap Service Provider Terms & Conditions</Text>


        {/* Last Updated */}
        <Text style={styles.lastUpdatedText}>Last Updated: October 2025</Text>


        {/* Introductory Paragraph */}
        <Text style={styles.paragraph}>
          This Agreement is entered into by and between HausTap the platform and the undersigned
          Service Provider ("the Provider"). By signing below, the Provider agrees to the following
          Terms & Conditions:
        </Text>


        {/* 1. Verification & Eligibility */}
        <Text style={styles.sectionHeading}>1. Verification & Eligibility</Text>
        <Text style={styles.paragraph}>
          The Provider affirms that all submitted information and documents are true and correct.
          HausTap reserves the right to approve, reject, or revoke applications at its sole discretion.
        </Text>


        {/* 2. Subscription & Access */}
        <Text style={styles.sectionHeading}>2. Subscription & Access</Text>
        <Text style={styles.paragraph}>
          An active subscription is required to access bookings through HausTap.
          Non-payment or failure to renew may result in suspension of account access.
        </Text>


        {/* 3. Service Standards */}
        <Text style={styles.sectionHeading}>3. Service Standards</Text>
        <Text style={styles.paragraph}>
          The Provider shall render services with professionalism, integrity, and safety.
          Tools, equipment, and materials are the Provider's responsibility unless otherwise agreed with the client.
          Client privacy and confidentiality must always be respected.
        </Text>


        {/* 4. Payments */}
        <Text style={styles.sectionHeading}>4. Payments</Text>
        <Text style={styles.paragraph}>
          Clients shall pay Providers directly in cash upon completion of services.
          HausTap does not process or hold payments.
          Disputes must be settled between Provider and Client.
        </Text>


        {/* 5. Ratings & Reviews */}
        <Text style={styles.sectionHeading}>5. Ratings & Reviews</Text>
        <Text style={styles.paragraph}>
          Clients may leave ratings and reviews based on performance.
          Consistently poor feedback or misconduct may result in suspension or termination.
        </Text>


        {/* 6. Termination */}
        <Text style={styles.sectionHeading}>6. Termination</Text>
        <Text style={styles.paragraph}>
          HausTap may suspend or terminate this Agreement
          for violation of terms, false information, or repeated negative feedback.
        </Text>


        {/* 7. Liability */}
        <Text style={styles.sectionHeading}>7. Liability</Text>
        <Text style={styles.paragraph}>
          HausTap serves solely as a booking platform and
          bears no liability for disputes, damages, or losses
          arising from services.
          The Provider assumes full responsibility for service
          quality and outcomes.
        </Text>


        {/* 8. Amendments */}
        <Text style={styles.sectionHeading}>8. Amendments</Text>
        <Text style={styles.paragraph}>
          HausTap may amend these Terms at any time.
          Continued use of the platform constitutes
          acceptance of such amendments.
        </Text>
      </ScrollView>
    </View>
  );
};


const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  statusBarPlaceholder: {
    height: 40, // Adjust as needed for actual status bar height
    backgroundColor: '#fff',
  },
  navBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 15,
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
    backgroundColor: '#fff',
  },
  navTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#000',
  },
  contentContainer: {
    padding: 20,
    paddingBottom: 50, // Add some padding at the bottom for scrolling
  },
  mainHeading: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#000',
    marginBottom: 10,
  },
  lastUpdatedText: {
    fontSize: 14,
    color: '#888',
    marginBottom: 20,
  },
  sectionHeading: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#000',
    marginTop: 20,
    marginBottom: 5,
  },
  paragraph: {
    fontSize: 14,
    color: '#333',
    lineHeight: 20, // Adjust for better readability
    marginBottom: 15,
  },
});


export default TermsAndConditionsScreen;
