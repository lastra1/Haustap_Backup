import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useState } from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

export default function TermsAndAgreement() {
  const { services, email } = useLocalSearchParams();
  const router = useRouter();
  const selectedServices = services ? JSON.parse(decodeURIComponent(services as string)) : [];
  const [accepted, setAccepted] = useState(false);

  const handleAccept = () => {
    if (!accepted) return;
    // Navigate to verification screen — pass services and email as query params
    // Decode the incoming email (if present) and re-encode to avoid double-encoding
    const decodedEmail = email ? decodeURIComponent(email as string) : '';
    // store email as a fallback for downstream screens
    try {
      const { flowStore } = require('../src/services/flowStore');
      if (decodedEmail) flowStore.setEmail(decodedEmail);
    } catch (e) {
      // ignore if require fails in this environment
    }
    const encodedEmail = decodedEmail ? encodeURIComponent(decodedEmail) : '';
    const servicesParam = services ? `services=${services}` : '';
    const emailParam = encodedEmail ? `&email=${encodedEmail}` : '';
    const query = `${servicesParam}${emailParam}`;
    console.log('[terms-and-agreement] navigating to partner-verification with query:', query);
    router.push(`/partner-verification?${query}`);
  };

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.header}>HausTap Service Provider Terms & Conditions</Text>

      <Text style={styles.paragraph}>
        This Agreement is entered into by and between HausTap ("the Platform") and the undersigned Service Provider ("the Provider"). By signing below, the Provider agrees to the following Terms & Conditions:
      </Text>

      <Text style={styles.sectionTitle}>1. Verification & Eligibility</Text>
      <Text style={styles.bullet}>• The Provider affirms that all submitted information and documents are true and correct.</Text>
      <Text style={styles.bullet}>• HausTap reserves the right to approve, reject, or revoke applications at its sole discretion.</Text>

      <Text style={styles.sectionTitle}>2. Subscription & Access</Text>
      <Text style={styles.bullet}>• An active subscription is required to access bookings through HausTap.</Text>
      <Text style={styles.bullet}>• Non-payment or failure to renew may result in suspension of account access.</Text>

      <Text style={styles.sectionTitle}>3. Service Standards</Text>
      <Text style={styles.bullet}>• The Provider shall render services with professionalism, integrity, and safety.</Text>
      <Text style={styles.bullet}>• Tools, equipment, and materials are the Provider's responsibility unless otherwise agreed with the client.</Text>
      <Text style={styles.bullet}>• Client privacy and confidentiality must always be respected.</Text>

      <Text style={styles.sectionTitle}>4. Payments</Text>
      <Text style={styles.bullet}>• Clients shall pay Providers directly in cash upon completion of services.</Text>
      <Text style={styles.bullet}>• HausTap does not process or hold payments. Disputes must be settled between Provider and Client.</Text>

      <Text style={styles.sectionTitle}>5. Ratings & Reviews</Text>
      <Text style={styles.bullet}>• Clients may leave ratings and reviews based on performance.</Text>
      <Text style={styles.bullet}>• Consistently poor feedback or misconduct may result in suspension or termination.</Text>

      <Text style={styles.sectionTitle}>6. Termination</Text>
      <Text style={styles.bullet}>• HausTap may suspend or terminate this Agreement for violation of terms, false information, or repeated negative feedback.</Text>

      <Text style={styles.sectionTitle}>7. Liability</Text>
      <Text style={styles.bullet}>• HausTap serves solely as a booking platform and bears no liability for disputes, damages, or losses arising from services.</Text>
      <Text style={styles.bullet}>• The Provider assumes full responsibility for service quality and outcomes.</Text>

      <Text style={styles.sectionTitle}>8. Amendments</Text>
      <Text style={styles.bullet}>• HausTap may amend these Terms at any time. Continued use of the platform constitutes acceptance of such amendments.</Text>

      <Text style={styles.header}>HausTap Service Provider Privacy Policy</Text>
      <Text style={styles.paragraph}>
        This Privacy Policy explains how HausTap handles personal information of Service Providers.
      </Text>

      <Text style={styles.sectionTitle}>1. Information We Collect</Text>
      <Text style={styles.bullet}>• Name, contact details, government IDs, certifications, and service history.</Text>

      <Text style={styles.sectionTitle}>2. Use of Information</Text>
      <Text style={styles.bullet}>• For verification, account management, subscription records, and booking facilitation.</Text>

      <Text style={styles.sectionTitle}>3. Sharing of Information</Text>
      <Text style={styles.bullet}>• Data will not be sold or disclosed to unauthorized third parties.</Text>
      <Text style={styles.bullet}>• Limited sharing with Clients is permitted only for booking and service fulfillment.</Text>

      <Text style={styles.sectionTitle}>4. Data Security</Text>
      <Text style={styles.bullet}>• HausTap employs reasonable safeguards, including OTP verification and restricted access.</Text>
      <Text style={styles.bullet}>• HausTap is not liable for breaches beyond its control (e.g., hacking or unauthorized access).</Text>

      <Text style={styles.sectionTitle}>5. Provider Rights</Text>
      <Text style={styles.bullet}>• Providers may request updates, corrections, or deletion of their personal data, subject to legal and operational requirements.</Text>

      <Text style={styles.sectionTitle}>6. Policy Updates</Text>
      <Text style={styles.bullet}>• HausTap may revise this Privacy Policy from time to time. Continued use of the platform constitutes acceptance of updated terms.</Text>

      <Text style={styles.sectionTitle}>AGREEMENT ACCEPTANCE</Text>
      <Text style={styles.paragraph}>
        I, the undersigned, confirm that I have read, understood, and agree to the Terms & Conditions and the Privacy Policy set forth by HausTap.
      </Text>
      <View style={styles.checkboxRow}>
        <TouchableOpacity
          onPress={() => setAccepted((s) => !s)}
          style={[styles.checkbox, accepted && styles.checked]}
        />
        <Text style={styles.checkboxLabel}>I have read and agree to the Terms & Conditions</Text>
      </View>

      <TouchableOpacity
        style={[styles.acceptButton, !accepted && styles.acceptButtonDisabled]}
        disabled={!accepted}
        onPress={handleAccept}
      >
        <Text style={styles.acceptButtonText}>Accept</Text>
      </TouchableOpacity>

      <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
        <Text style={styles.backButtonText}>Back</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { padding: 20, backgroundColor: '#fff', flexGrow: 1 },
  header: { fontSize: 20, fontWeight: 'bold', marginBottom: 16, textAlign: 'center', color: '#333' },
  sectionTitle: { fontSize: 16, fontWeight: 'bold', marginTop: 16, marginBottom: 8, color: '#333' },
  paragraph: { fontSize: 14, color: '#333', lineHeight: 20, marginBottom: 12 },
  bullet: { fontSize: 14, color: '#333', lineHeight: 20, paddingLeft: 8, marginBottom: 4 },
  checkboxRow: { flexDirection: 'row', alignItems: 'center', marginTop: 24 },
  checkbox: { width: 22, height: 22, borderWidth: 1, borderColor: '#3DC1C6', marginRight: 10, borderRadius: 4 },
  checked: { backgroundColor: '#3DC1C6', borderColor: '#3DC1C6' },
  checkboxLabel: { flex: 1, fontSize: 14, color: '#333' },
  acceptButton: { backgroundColor: '#3DC1C6', paddingVertical: 12, borderRadius: 8, alignItems: 'center', marginTop: 24 },
  acceptButtonDisabled: { backgroundColor: '#bcdad9' },
  acceptButtonText: { color: '#fff', fontWeight: 'bold', fontSize: 16 },
  backButton: { marginTop: 16, alignItems: 'center' },
  backButtonText: { color: '#3DC1C6', fontSize: 14 },
});
