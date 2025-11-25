import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React from 'react';
import { Platform, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

export default function PrivacyPolicy() {
  const router = useRouter();
  return (
    <ScrollView style={styles.container} contentContainerStyle={{ paddingBottom: 96 }}>
      <View style={styles.headerRow}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Privacy Policy</Text>
      </View>

      <Text style={styles.updated}>Last Updated: October 2025</Text>

      <Text style={styles.body}>
        This Privacy Policy explains how HausTap (“we”, “our”, or “the Platform”) collects, uses, stores, and protects your personal information when you create an account or use our services. By using HausTap, you confirm that you have read and understood this Privacy Policy.
      </Text>

      <Text style={styles.sectionTitle}>1. INFORMATION WE COLLECT</Text>
      <Text style={styles.body}>
        We only collect information that is necessary for account creation, booking, and communication, including:{"\n"}
        • Full Name{"\n"}
        • Email Address & Mobile Number (for OTP verification and notifications){"\n"}
        • Location/Address (for service booking and provider matching){"\n"}
        • Booking history and feedback
      </Text>
      <Text style={styles.body}>
        We do not collect or store payment card details because all payments are done in cash directly to the service provider.
      </Text>

      <Text style={styles.sectionTitle}>2. HOW WE USE YOUR INFORMATION</Text>
      <Text style={styles.body}>
        Your data is used solely for platform operations, including:{"\n"}
        • Account registration and OTP identity verification{"\n"}
        • Sending booking confirmations and service notifications{"\n"}
        • Matching you with nearby verified service providers{"\n"}
        • Improving platform security and service experience{"\n"}
        • Providing support and resolving concerns
      </Text>

      <Text style={styles.sectionTitle}>3. SHARING OF INFORMATION</Text>
      <Text style={styles.body}>
        HausTap respects your privacy and will not sell or disclose your personal data to unauthorized third parties. However, we may share limited information with:{"\n"}
        • Verified Service Providers only for booking and coordination{"\n"}
        • Legal authorities when required by law or for security purposes
      </Text>

      <Text style={styles.sectionTitle}>4. DATA SECURITY</Text>
      <Text style={styles.body}>
        We implement reasonable security measures, including:{"\n"}
        • OTP verification and secure login access{"\n"}
        • Restricted access to sensitive data{"\n"}
        • Monitoring for suspicious or fraudulent activity{"\n"}
        However, no digital system is 100% secure. HausTap shall not be held liable for data breaches caused by hacking, third-party intrusion, or situations beyond our control.
      </Text>

      <Text style={styles.sectionTitle}>5. YOUR RIGHTS AS A USER</Text>
      <Text style={styles.body}>
        You have the right to:{"\n"}
        • Update or correct your information{"\n"}
        • Request account deletion (subject to verification and pending transactions){"\n"}
        • Decline promotional messages (but essential system alerts cannot be disabled){"\n"}
        Requests may be sent through HausTap’s official support channels.
      </Text>

      <Text style={styles.sectionTitle}>6. DATA RETENTION</Text>
      <Text style={styles.body}>
        Your data will be stored only as long as necessary for your account usage, legal compliance, or dispute resolution. Terminated accounts may be securely archived or permanently deleted as needed.
      </Text>

      <Text style={styles.sectionTitle}>7. POLICY UPDATES</Text>
      <Text style={styles.body}>
        HausTap may update this Privacy Policy at any time to improve safety and compliance. You will be notified of major changes. Continued use of the platform confirms your acceptance.
      </Text>

      <Text style={styles.sectionTitle}>8. CONTACT INFORMATION</Text>
      <Text style={styles.body}>
        For any questions, support, or privacy-related concerns, you may reach us through our official communication channels: Email, Facebook, or Instagram.
      </Text>
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
    marginBottom: 10,
  },
  backBtn: {
    padding: 8,
    marginRight: 8,
  },
  headerTitle: {
    fontSize: 16,
    fontWeight: '600',
  },
  updated: {
    fontSize: 13,
    color: '#888',
    marginBottom: 10,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '700',
    marginTop: 18,
    marginBottom: 6,
  },
  body: {
    fontSize: 13,
    color: '#222',
    marginBottom: 6,
    lineHeight: 20,
  },
});
