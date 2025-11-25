import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React from 'react';
import { Platform, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

export default function TermsConditions() {
  const router = useRouter();
  return (
    <ScrollView style={styles.container} contentContainerStyle={{ paddingBottom: 96 }}>
      <View style={styles.headerRow}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Terms & Conditions</Text>
      </View>
      <Text style={styles.updated}>Last Updated: October 2025</Text>
      <Text style={styles.body}>
        These Terms and Conditions ("Terms") govern your use of the HausTap app and services. By using our platform, you agree to comply with these Terms. If you do not agree, please do not use the app or services.
      </Text>
      <Text style={styles.sectionTitle}>1. ELIGIBILITY & ACCOUNT REGISTRATION</Text>
      <Text style={styles.body}>
        - You must be at least 18 years old to use the platform. {'\n'}
        - You are responsible for all activities under your account. {'\n'}
        - Keep your credentials safe. {'\n'}
        - Provide accurate, up-to-date info or risk service removal.
      </Text>
      <Text style={styles.sectionTitle}>2. SERVICE REQUEST & BOOKING</Text>
      <Text style={styles.body}>
        - Service requests are subject to availability. {'\n'}
        - Bookings may be accepted or declined by service providers. {'\n'}
        - You agree to pay deposits as scheduled. {'\n'}
        - Cancellations after deposit may incur charges as stated in the app. {'\n'}
        - HausTap is not liable for provider delays or cancellations.
      </Text>
      <Text style={styles.sectionTitle}>3. PAYMENT TERMS</Text>
      <Text style={styles.body}>
        - Payments are collected via the app or upon payment request. {'\n'}
        - All fees are stated in the app. {'\n'}
        - Any dispute or refund must be made via support. {'\n'}
        - HausTap does not control or hold any service payments on behalf of providers.
      </Text>
      <Text style={styles.sectionTitle}>4. CLIENT RESPONSIBILITIES</Text>
      <Text style={styles.body}>
        - Treat service providers with respect. {'\n'}
        - No harassment, abuse, or inappropriate behavior. {'\n'}
        - Follow all instructions and schedules from providers. {'\n'}
        - HausTap may suspend or terminate accounts for violations.
      </Text>
      <Text style={styles.sectionTitle}>5. RATINGS & REVIEWS</Text>
      <Text style={styles.body}>
        - Leave honest, respectful reviews for service providers. {'\n'}
        - HausTap may remove reviews that violate policies.
      </Text>
      <Text style={styles.sectionTitle}>6. LIMITATION OF LIABILITY</Text>
      <Text style={styles.body}>
        - HausTap is a service marketplace, NOT the employer of providers. {'\n'}
        - We are not responsible for loss, damage, or injury. {'\n'}
        - Use the app at your own risk. {'\n'}
        - Our liability is limited to the service fee paid to HausTap.
      </Text>
      <Text style={styles.sectionTitle}>7. SUSPENSION OR TERMINATION</Text>
      <Text style={styles.body}>
        - HausTap may suspend or terminate accounts for breach of these Terms or fraudulent behavior. {'\n'}
        - Notice may be given via the app or email.
      </Text>
      <Text style={styles.sectionTitle}>8. COMMUNICATION CONSENT</Text>
      <Text style={styles.body}>
        - By using the app, you consent to receive notifications and updates from HausTap.
      </Text>
      <Text style={styles.sectionTitle}>9. AMENDMENTS</Text>
      <Text style={styles.body}>
        - HausTap reserves the right to update or modify these Terms at any time. Updates will be posted in-app or sent to your registered email.
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
