import { AntDesign, FontAwesome, Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import {
    Linking,
    SafeAreaView,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    TouchableWithoutFeedback,
    View
} from 'react-native';


const ConnectHaustapScreen = () => {
  const router = useRouter();
  const [agreed, setAgreed] = useState(false);
  const [showPolicy, setShowPolicy] = useState(false);


  const handleFacebookPress = () => {
    Linking.openURL('https://www.facebook.com/share/1DL9VHjz7q/').catch(err =>
      console.error("Couldn't load page", err)
    );
  };


  const handleInstagramPress = () => {
    Linking.openURL('https://www.instagram.com/haustap.ph?igsh=MXIxM2h5cjZyb2tkYQ==').catch(err =>
      console.error("Couldn't load page", err)
    );
  };


  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.statusBarPlaceholder} />


      {/* Nav */}
      <View style={styles.navBar}>
        <TouchableOpacity onPress={() => router.push('/service-provider/my-account')} accessibilityLabel="Go back to My Account">
          <Ionicons name="arrow-back" size={28} color="#000" />
        </TouchableOpacity>
        <Text style={styles.navTitle}>Connect Haustap</Text>
        <View style={{ width: 172 }} />
      </View>


      <ScrollView
        contentContainerStyle={styles.contentContainer}
        keyboardShouldPersistTaps="handled"
      >
        <Text style={styles.heading}>Get in touch</Text>


        {/* Labels above name inputs */}
        <View style={styles.nameFieldsLabelsRow}>
          <Text style={[styles.inputLabel, styles.nameLabel]}>First name</Text>
          <Text style={[styles.inputLabel, styles.miLabel]}>MI</Text>
          <Text style={[styles.inputLabel, styles.nameLabel]}>Last name</Text>
        </View>


        {/* Name inputs row */}
        <View style={styles.nameInputsRow}>
          <TextInput
            style={[styles.input, styles.firstNameInput]}
            placeholder="First name"
            placeholderTextColor="#888"
          />
          <TextInput
            style={[styles.input, styles.miInput]}
            placeholder="MI"
            placeholderTextColor="#888"
            maxLength={1}
          />
          <TextInput
            style={[styles.input, styles.lastNameInput]}
            placeholder="Last name"
            placeholderTextColor="#888"
          />
        </View>


        {/* Email */}
        <Text style={styles.inputLabel}>Email</Text>
        <TextInput
          style={styles.input}
          placeholder="you@email.com"
          placeholderTextColor="#888"
          keyboardType="email-address"
          autoCapitalize="none"
        />


        {/* Phone */}
        <Text style={styles.inputLabel}>Phone Number</Text>
        <TextInput
          style={styles.input}
          placeholder="your number"
          placeholderTextColor="#888"
          keyboardType="phone-pad"
        />


        {/* Message */}
        <Text style={styles.inputLabel}>Message</Text>
        <TextInput
          style={[styles.input, styles.messageInput]}
          multiline
          numberOfLines={6}
          placeholder=""
          placeholderTextColor="#888"
        />


        {/* Checkbox + privacy */}
        <View style={styles.checkboxContainer}>
          <TouchableOpacity
            onPress={() => setAgreed(!agreed)}
            style={[styles.checkboxBase, agreed ? styles.checkboxChecked : styles.checkboxUnchecked]}
            accessibilityLabel="Agree to privacy policy"
          >
            {agreed && <Ionicons name="checkmark" size={16} color="#fff" />}
          </TouchableOpacity>


          <Text style={styles.privacyTextOneLine}>
            You agree to our friendly{' '}
            <Text style={styles.privacyLink} onPress={() => setShowPolicy(true)}>
              privacy policy
            </Text>
          </Text>
        </View>


        {/* Send button */}
        <TouchableOpacity
          style={[styles.sendButton, !agreed && styles.sendButtonDisabled]}
          onPress={() => console.log('Send Message')}
          disabled={!agreed}
        >
          <Text style={styles.sendButtonText}>Send Message</Text>
        </TouchableOpacity>


        {/* Socials */}
        <View style={styles.socialIconsContainer}>
          <TouchableOpacity onPress={handleFacebookPress}>
            <FontAwesome name="facebook-square" size={48} color="#3b5998" />
          </TouchableOpacity>
          <TouchableOpacity onPress={handleInstagramPress} style={{ marginLeft: 20 }}>
            <AntDesign name="instagram" size={48} color="#E4405F" />
          </TouchableOpacity>
        </View>
      </ScrollView>


      {/* Modal-like overlay for Privacy Policy */}
      {showPolicy && (
        <TouchableWithoutFeedback onPress={() => setShowPolicy(false)}>
          <View style={styles.modalOverlay}>
            <TouchableWithoutFeedback onPress={() => {}}>
              <View style={styles.modalContent}>
                <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={{ paddingBottom: 10 }}>
                  <Text style={styles.modalTitle}>HausTap Service Provider Privacy Policy</Text>


                  <Text style={styles.modalText}>
{`Last Updated: October 2025


This Privacy Policy explains how HausTap (“we”, “our”, or “the Platform”) collects, uses, stores, and protects your personal information when you create an account or use our services.
By using HausTap, you confirm that you have read and understood this Privacy Policy.


1. INFORMATION WE COLLECT
We only collect information that is necessary for account creation, booking, and communication, including:
• Full Name
• Email Address & Mobile Number (for OTP verification and notifications)
• Location/Address (for service booking and provider matching)
• Booking history and feedback
We do not collect or store payment card details because all payments are done in cash directly to the service provider.


2. HOW WE USE YOUR INFORMATION
Your data is used solely for platform operations, including:
• Account registration and OTP identity verification
• Sending booking confirmations and service notifications
• Matching you with nearby verified service providers
• Improving platform security and service experience
• Providing support and resolving concerns


3. SHARING OF INFORMATION
HausTap respects your privacy and will not sell or disclose your personal data to unauthorized third parties.
However, we may share limited information with:
• Verified Service Providers only for booking and coordination
• Legal authorities when required by law or for security purposes


4. DATA SECURITY
We implement reasonable security measures, including:
• OTP verification and secure login access
• Restricted access to sensitive data
• Monitoring for suspicious or fraudulent activity
However, no digital system is 100% secure. HausTap shall not be held liable for data breaches caused by hacking, third-party intrusion, or situations beyond our control.


5. YOUR RIGHTS AS A USER
You have the right to:
• Update or correct your information
• Request account deletion (subject to verification and pending transactions)
• Decline promotional messages (but essential system alerts cannot be disabled)
Requests may be sent through HausTap’s official support channels.


6. DATA RETENTION
Your data will be stored only as long as necessary for your account usage, legal compliance, or dispute resolution. Terminated accounts may be securely archived or permanently deleted as needed.


7. POLICY UPDATES
HausTap may update this Privacy Policy at any time to improve safety and compliance.
You will be notified of major changes. Continued use of the platform confirms your acceptance.


8. CONTACT INFORMATION
For any questions, support, or privacy-related concerns, you may reach us through our official communication channels: Email, Facebook, or Instagram.`}
                  </Text>
                </ScrollView>


                <TouchableOpacity style={styles.modalCloseButton} onPress={() => setShowPolicy(false)}>
                  <Text style={styles.modalCloseText}>Close</Text>
                </TouchableOpacity>
              </View>
            </TouchableWithoutFeedback>
          </View>
        </TouchableWithoutFeedback>
      )}
    </SafeAreaView>
  );
};


const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff' },
  statusBarPlaceholder: { height: 10, backgroundColor: '#fff' },
  navBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 15,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  navTitle: { fontSize: 18, fontWeight: '600', color: '#000' },
  contentContainer: { padding: 20, paddingBottom: 40 },


  heading: { fontSize: 24, fontWeight: '700', marginBottom: 20, color: '#000' },


  // Labels row for name inputs
  nameFieldsLabelsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 5,
  },
  nameLabel: { flex: 0.45, marginRight: 10, color: '#555', fontSize: 16, fontWeight: 'bold' },
  miLabel: { width: 60, marginHorizontal: 5, textAlign: 'center', color: '#555', fontSize: 16, fontWeight: 'bold' },


  // Inputs row
  nameInputsRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 15 },
  input: {
    flex: 1,
    height: 50,
    borderColor: '#ddd',
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: 15,
    fontSize: 16,
    color: '#000',
    backgroundColor: '#fff',
    marginBottom: 15,
  },
  firstNameInput: { marginRight: 10, flex: 0.45, marginBottom: 0 },
  miInput: { width: 60, textAlign: 'center', marginHorizontal: 5, flex: 0.1, marginBottom: 0 },
  lastNameInput: { marginLeft: 10, flex: 0.45, marginBottom: 0 },


  inputLabel: { fontSize: 16, color: '#555', marginBottom: 5, fontWeight: '500' },


  messageInput: { height: 120, textAlignVertical: 'top', paddingVertical: 15, marginBottom: 25 },


  // Checkbox
  checkboxContainer: { flexDirection: 'row', alignItems: 'center', marginBottom: 25, paddingRight: 15 },
  checkboxBase: {
    width: 22,
    height: 22,
    borderRadius: 4,
    marginRight: 10,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1.5,
  },
  checkboxUnchecked: { backgroundColor: 'transparent', borderColor: '#888' },
  checkboxChecked: { backgroundColor: '#4ACDCA', borderColor: '#4ACDCA' },
  privacyTextOneLine: { fontSize: 14, color: '#555', flexShrink: 1, lineHeight: 20 },
  privacyLink: { color: '#007AFF', textDecorationLine: 'underline', fontSize: 14 },


  // Send button
  sendButton: { backgroundColor: '#4ACDCA', paddingVertical: 15,  borderRadius: 8, alignItems: 'center', marginBottom: 40 },
  sendButtonDisabled: { backgroundColor: '#A9E5E4' },
  sendButtonText: { color: '#000', fontSize: 18, fontWeight: 'bold' },


  // Socials
  socialIconsContainer: { flexDirection: 'row', justifyContent: 'center', marginTop: 10, marginBottom: 50 },


  // Modal styles
  modalOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'rgba(0,0,0,0.45)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
    zIndex: 9999,
  },
  modalContent: {
    width: '100%',
    maxHeight: '80%',
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 18,
    shadowColor: '#000',
    shadowOpacity: 0.15,
    shadowRadius: 8,
    elevation: 10,
  },
  modalTitle: { fontSize: 18, fontWeight: 'bold', marginBottom: 10, color: '#000' },
  modalText: { fontSize: 14, lineHeight: 20, color: '#444', marginBottom: 6 },
  modalCloseButton: {
    marginTop: 10,
    paddingVertical: 12,
    borderRadius: 8,
    backgroundColor: '#3dc1c6',
    alignItems: 'center',
  },
  modalCloseText: { color: '#000', fontWeight: 'bold', fontSize: 16 },
});


export default ConnectHaustapScreen;
