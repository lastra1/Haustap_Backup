import { useRouter } from 'expo-router';
import React from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
// Make sure to install react-native-vector-icons:
// npm install react-native-vector-icons
import { Ionicons as Icon } from '@expo/vector-icons';


const ServiceProviderPrivacyPolicyScreen = () => {
  const router = useRouter();
  // Tinanggal ang handlePrivacyPolicyLinkPress dahil hindi na ito clickable.
  
  return (
    <View style={styles.container}>
      {/* Status Bar Placeholder */}
      <View style={styles.statusBarPlaceholder} />


      {/* Navigation Bar */}
      <View style={styles.navBar}>
        <TouchableOpacity onPress={() => router.push('/service-provider/my-account')} accessibilityLabel="Go back to My Account">
          <Icon name="arrow-back" size={28} color="#000" />
        </TouchableOpacity>
        <Text style={styles.navTitle}>Privacy Policy</Text>
        <View style={{ width: 127 }} /> {/* Spacer */}
      </View>


      <ScrollView contentContainerStyle={styles.contentContainer}>
        
        {/* Main Heading */}
        <Text style={styles.mainHeading}>HausTap Service Provider Privacy Policy</Text>


        {/* Last Updated */}
        <Text style={styles.lastUpdatedText}>Last Updated: October 2025</Text>


        {/* Introductory Paragraph (FIXED: Plain Text na ang "Privacy Policy") */}
        <Text style={styles.paragraph}>
          This Privacy Policy explains how HausTap (“we”, “our”, or “the Platform”) collects, uses, stores, and protects your personal information when you create an account or use our services.
          By using HausTap, you confirm that you have read and understood this 
          <Text style={styles.plainBoldText}> Privacy Policy</Text>.
        </Text>


        {/* 1. INFORMATION WE COLLECT */}
        <Text style={styles.sectionHeading}>1. INFORMATION WE COLLECT</Text>
        <Text style={styles.paragraph}>
          We only collect information that is necessary for account creation, booking, and communication, including:
          {"\n"}• Full Name
          {"\n"}• Email Address & Mobile Number (for OTP verification and notifications)
          {"\n"}• Location/Address (for service booking and provider matching)
          {"\n"}• Booking history and feedback
        </Text>
        <Text style={styles.paragraph}>
          We do not collect or store payment card details because all payments are done in cash directly to the service provider.
        </Text>


        {/* 2. HOW WE USE YOUR INFORMATION */}
        <Text style={styles.sectionHeading}>2. HOW WE USE YOUR INFORMATION</Text>
        <Text style={styles.paragraph}>
          Your data is used solely for platform operations, including:
          {"\n"}• Account registration and OTP identity verification
          {"\n"}• Sending booking confirmations and service notifications
          {"\n"}• Matching you with nearby verified service providers
          {"\n"}• Improving platform security and service experience
          {"\n"}• Providing support and resolving concerns
        </Text>


        {/* 3. SHARING OF INFORMATION */}
        <Text style={styles.sectionHeading}>3. SHARING OF INFORMATION</Text>
        <Text style={styles.paragraph}>
          HausTap respects your privacy and will not sell or disclose your personal data to unauthorized third parties.
          {"\n"}However, we may share limited information with:
          {"\n"}• Verified Service Providers only for booking and coordination
          {"\n"}• Legal authorities when required by law or for security purposes
        </Text>


        {/* 4. DATA SECURITY */}
        <Text style={styles.sectionHeading}>4. DATA SECURITY</Text>
        <Text style={styles.paragraph}>
          We implement reasonable security measures, including:
          {"\n"}• OTP verification and secure login access
          {"\n"}• Restricted access to sensitive data
          {"\n"}• Monitoring for suspicious or fraudulent activity
        </Text>
        <Text style={styles.paragraph}>
          However, no digital system is 100% secure. HausTap shall not be held liable for data breaches caused by hacking, third-party intrusion, or situations beyond our control.
        </Text>


        {/* 5. YOUR RIGHTS AS A USER */}
        <Text style={styles.sectionHeading}>5. YOUR RIGHTS AS A USER</Text>
        <Text style={styles.paragraph}>
          You have the right to:
          {"\n"}• Update or correct your information
          {"\n"}• Request account deletion (subject to verification and pending transactions)
          {"\n"}• Decline promotional messages (but essential system alerts cannot be disabled)
          {"\n"}Requests may be sent through HausTap’s official support channels.
        </Text>


        {/* 6. DATA RETENTION */}
        <Text style={styles.sectionHeading}>6. DATA RETENTION</Text>
        <Text style={styles.paragraph}>
          Your data will be stored only as long as necessary for your account usage, legal compliance, or dispute resolution. Terminated accounts may be securely archived or permanently deleted as needed.
        </Text>


        {/* 7. POLICY UPDATES */}
        <Text style={styles.sectionHeading}>7. POLICY UPDATES</Text>
        <Text style={styles.paragraph}>
          HausTap may update this Privacy Policy at any time to improve safety and compliance.
          {"\n"}You will be notified of major changes. Continued use of the platform confirms your acceptance.
        </Text>


        {/* 8. CONTACT INFORMATION */}
        <Text style={styles.sectionHeading}>8. CONTACT INFORMATION</Text>
        <Text style={styles.paragraph}>
          For any questions, support, or privacy-related concerns, you may reach us through our official communication channels: Email, Facebook, or Instagram.
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
    height: 40, 
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
    paddingBottom: 50, 
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
    lineHeight: 20, 
    marginBottom: 15,
  },
  // NEW STYLE: Para lang sa plain, bold text
  plainBoldText: {
    fontWeight: 'bold',
    color: '#333', // Regular text color
  },
  // Inalis ang introLink style dito
});


export default ServiceProviderPrivacyPolicyScreen;
