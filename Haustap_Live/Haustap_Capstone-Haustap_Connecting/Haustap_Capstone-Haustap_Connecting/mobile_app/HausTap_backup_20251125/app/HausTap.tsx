import { Ionicons } from "@expo/vector-icons";
import { Image } from "expo-image";
import React, { useState } from "react";
import {
  KeyboardAvoidingView,
  Modal,
  Platform,
  ScrollView,
  StatusBar,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View
} from "react-native";
import { SafeAreaView } from 'react-native-safe-area-context';

import { useRouter } from 'expo-router';
import { accountsStore } from '../src/services/accountsStore';

export default function HausTap() {
  const router = useRouter();
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [month, setMonth] = useState("");
  const [day, setDay] = useState("");
  const [year, setYear] = useState("");
  const [showTerms, setShowTerms] = useState(false);
  const [showPrivacy, setShowPrivacy] = useState(false);
  const [showEmailVerification, setShowEmailVerification] = useState(false);
  
  // Form fields
  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");
  const [middleInitial, setMiddleInitial] = useState("");
  const [email, setEmail] = useState("");
  const [mobileNumber, setMobileNumber] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [otp, setOtp] = useState("");
  
  // OTP related states
  const [otpError, setOtpError] = useState("");
  const [remainingTime, setRemainingTime] = useState(300); // 5 minutes in seconds
  const [isOtpExpired, setIsOtpExpired] = useState(false);
  const [generatedOtp, setGeneratedOtp] = useState(""); // In a real app, this would come from the backend

  const validateForm = () => {
    if (!firstName || !lastName || !email || !mobileNumber || !password || !confirmPassword) {
      alert("Please fill in all required fields");
      return false;
    }

    if (password !== confirmPassword) {
      alert("Passwords do not match");
      return false;
    }

    if (!email.includes("@") || !email.includes(".")) {
      alert("Please enter a valid email address");
      return false;
    }

    if (!month || !day || !year) {
      alert("Please enter your complete birthdate");
      return false;
    }

    return true;
  };

  // Format remaining time to mm:ss
  const formatTime = (seconds: number) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
  };

  // Start OTP timer
  const startOtpTimer = () => {
    setRemainingTime(300); // Reset to 5 minutes
    setIsOtpExpired(false);
    
    const timer = setInterval(() => {
      setRemainingTime((prevTime) => {
        if (prevTime <= 1) {
          clearInterval(timer);
          setIsOtpExpired(true);
          return 0;
        }
        return prevTime - 1;
      });
    }, 1000);

    return () => clearInterval(timer);
  };

  // Generate a new OTP and start timer
  const API_URL = Platform.OS === 'android' 
  ? 'http://192.168.1.8:3000' // Your local IP address
  : 'http://localhost:3000'; // iOS simulator or web

// Add debug logging
console.log('Platform:', Platform.OS);
console.log('API URL:', API_URL);

const generateNewOtp = async () => {
    const newOtp = Math.floor(100000 + Math.random() * 900000).toString();
    setGeneratedOtp(newOtp);
    setOtpError("");
    setOtp("");
    
    try {
      console.log('Sending OTP to:', email);
      console.log('Making request to:', `${API_URL}/api/send-otp`);
      
      // Add timeout and error handling
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout
      
      const response = await fetch(`${API_URL}/api/send-otp`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          email,
          otp: newOtp,
        }),
        signal: controller.signal
      });
      
      clearTimeout(timeoutId);

      const data = await response.json();
      
      if (data.success) {
        startOtpTimer();
      } else {
        setOtpError("Failed to send OTP email. Please try again.");
      }
    } catch (error) {
      console.error("Error sending OTP:", error);
      setOtpError("Failed to send OTP email. Please try again.");
    }
  };

  // Verify OTP
  const verifyOtp = () => {
    if (!otp) {
      setOtpError("Please enter the OTP");
      return;
    }

    if (isOtpExpired) {
      setOtpError("OTP has expired. Please request a new one.");
      return;
    }

    if (otp !== generatedOtp) {
      setOtpError("Invalid OTP. Please try again.");
      return;
    }

    // OTP is valid
    setOtpError("");
    // Create account in local accounts store (do NOT auto-login)
    (async () => {
      try {
        await accountsStore.addAccount({ email, password, isHausTapPartner: false });
        alert('Email verified and account created. Please sign in using your credentials.');
      } catch (e) {
        console.error('Failed to save account', e);
        alert('Account created but failed to persist locally.');
      } finally {
        setShowEmailVerification(false);
  try { router.push('/auth/log-in'); } catch (_) {}
      }
    })();
  };

  const handleSignUp = async () => {
    if (validateForm()) {
      setShowEmailVerification(true);
      await generateNewOtp(); // Generate OTP and start timer when modal opens
    }
  };

  return (
    <SafeAreaView style={{ flex: 1 }}>
      <KeyboardAvoidingView
        style={{ flex: 1 }}
        behavior={Platform.OS === "ios" ? "padding" : "height"}
      >
        <ScrollView contentContainerStyle={styles.container}>
          {/* App Logo */}
          <Image
            source={require("../assets/images/logo.png")}
            style={styles.logo}
            contentFit="contain"
          />

          {/* Sign Up Box */}
          <View style={styles.box}>
            <Text style={styles.boxTitle}>Sign Up</Text>

            {/* Full Name Section */}
            <View style={styles.rowContainer}>
              <View style={styles.flex2}>
                <Text style={styles.label}>First Name</Text>
                <TextInput 
                  style={styles.input} 
                  placeholder="First Name"
                  value={firstName}
                  onChangeText={setFirstName}
                />
              </View>
              <View style={styles.flex2}>
                <Text style={styles.label}>Last Name</Text>
                <TextInput 
                  style={styles.input} 
                  placeholder="Last Name"
                  value={lastName}
                  onChangeText={setLastName}
                />
              </View>
              <View style={styles.flex1}>
                <Text style={styles.label}>M.I.</Text>
                <TextInput
                  style={styles.input}
                  maxLength={1}
                  autoCapitalize="characters"
                  placeholder="M.I."
                  value={middleInitial}
                  onChangeText={setMiddleInitial}
                />
              </View>
            </View>

            {/* Birthdate */}
            <View style={styles.rowContainer}>
              <View style={styles.flex1}>
                <Text style={styles.label}>Birthdate</Text>
                <TextInput
                  style={styles.input}
                  placeholder="MM"
                  keyboardType="numeric"
                  maxLength={2}
                  onChangeText={(text) => {
                    text = text.replace(/\D/g, "");
                    const month = parseInt(text);
                    if (month > 12) text = "12";
                    setMonth(text);
                  }}
                  value={month}
                />
              </View>
              <View style={styles.flex1}>
                <TextInput
                  style={[styles.input, styles.noLabel]}
                  placeholder="DD"
                  keyboardType="numeric"
                  maxLength={2}
                  onChangeText={(text) => {
                    text = text.replace(/\D/g, "");
                    const day = parseInt(text);
                    if (day > 31) text = "31";
                    setDay(text);
                  }}
                  value={day}
                />
              </View>
              <View style={styles.flex2}>
                <TextInput
                  style={[styles.input, styles.noLabel]}
                  placeholder="YYYY"
                  keyboardType="numeric"
                  maxLength={4}
                  onChangeText={(text) => {
                    text = text.replace(/\D/g, "");
                    const yearNum = parseInt(text);
                    const currentYear = new Date().getFullYear();
                    if (yearNum > currentYear) text = currentYear.toString();
                    setYear(text);
                  }}
                  value={year}
                />
              </View>
            </View>

            {/* Email */}
            <Text style={styles.label}>Email</Text>
            <TextInput 
              style={styles.input} 
              keyboardType="email-address"
              value={email}
              onChangeText={setEmail}
              autoCapitalize="none"
            />

            {/* Mobile Number */}
            <Text style={styles.label}>Mobile Number</Text>
            <TextInput 
              style={styles.input} 
              keyboardType="phone-pad"
              value={mobileNumber}
              onChangeText={setMobileNumber}
            />

            {/* Password */}
            <Text style={styles.label}>Password</Text>
            <View style={styles.inputContainer}>
              <TextInput
                style={styles.passwordInput}
                secureTextEntry={!showPassword}
                value={password}
                onChangeText={setPassword}
              />
              <TouchableOpacity onPress={() => setShowPassword(!showPassword)}>
                <Ionicons
                  name={showPassword ? "eye-off-outline" : "eye-outline"}
                  size={20}
                  color="#888"
                />
              </TouchableOpacity>
            </View>

            {/* Confirm Password */}
            <Text style={styles.label}>Confirm Password</Text>
            <View style={styles.inputContainer}>
              <TextInput
                style={styles.passwordInput}
                secureTextEntry={!showConfirmPassword}
                value={confirmPassword}
                onChangeText={setConfirmPassword}
              />
              <TouchableOpacity
                onPress={() => setShowConfirmPassword(!showConfirmPassword)}
              >
                <Ionicons
                  name={showConfirmPassword ? "eye-off-outline" : "eye-outline"}
                  size={20}
                  color="#888"
                />
              </TouchableOpacity>
            </View>

            {/* Sign Up Button */}
            <TouchableOpacity style={styles.button} onPress={handleSignUp}>
              <Text style={styles.buttonText}>Sign Up</Text>
            </TouchableOpacity>

            <Text style={styles.agreement}>By signing up, you agree to HausTap&apos;s</Text>

            <View style={styles.termsContainer}>
              <TouchableOpacity onPress={() => setShowTerms(true)}>
                <Text style={styles.link}>Terms & Conditions</Text>
              </TouchableOpacity>
              <Text style={styles.separator}> | </Text>
              <TouchableOpacity onPress={() => setShowPrivacy(true)}>
                <Text style={styles.link}>Privacy Policy</Text>
              </TouchableOpacity>
            </View>

            <Text style={styles.footerText}>
              Already have an account? <Text style={styles.link}>Log In</Text>
            </Text>

            <View style={styles.line} />
          </View>
        </ScrollView>
      </KeyboardAvoidingView>

      {/* Terms & Conditions Modal */}
      <Modal visible={showTerms} animationType="slide">
        <SafeAreaView style={styles.modalContainer}>
          <StatusBar barStyle="dark-content" backgroundColor="#fff" />
          <View style={styles.modalHeader}>
            <Text style={styles.modalTitle}>Terms & Agreement</Text>
            <TouchableOpacity
              style={styles.modalCloseButton}
              onPress={() => setShowTerms(false)}
            >
              <Ionicons name="close" size={24} color="#666" />
            </TouchableOpacity>
          </View>
          <ScrollView contentContainerStyle={styles.modalContent}>
            <Text style={styles.modalText}>
              Last Updated: October 2025{"\n\n"}
              These Terms and Conditions (&quot;Terms&quot;) govern your access to and use of the HausTap mobile and web platform (&quot;HausTap&quot;, &quot;we&quot;, &quot;our&quot;, or &quot;the Platform&quot;) for booking home services. By creating an account or using HausTap, you acknowledge that you have read, understood, and agreed to these Terms.{"\n\n"}
              If you do not agree with these Terms, please do not continue using the Platform.{"\n\n"}
              1. ELIGIBILITY & ACCOUNT REGISTRATION{"\n"}
              You must be at least 18 years old to use the platform.{"\n"}
              You agree to provide accurate and truthful information upon registration.{"\n"}
              You are fully responsible for all activities under your account.{"\n"}
              Sharing, renting, or allowing others to misuse your account is strictly prohibited.{"\n\n"}
              2. SERVICE REQUEST & BOOKING{"\n"}
              HausTap allows clients to book verified service providers for home services such as cleaning, repairs, beauty, wellness, etc.{"\n"}
              Service availability depends on the schedule and acceptance of the service provider.{"\n"}
              Once a booking is confirmed, it is your responsibility to be available and ready at the scheduled time and location.{"\n"}
              Any last-minute cancellations, no-shows, or delays on your part may result in penalties or account restrictions.{"\n\n"}
              3. PAYMENT TERMS{"\n"}
              All services booked through HausTap are cash payment only, directly paid to the service provider after job completion.{"\n"}
              HausTap does not collect or hold any payment from clients.{"\n"}
              Any pricing disputes or refund discussions must be settled between you and the provider.{"\n"}
              No advance payments, deposits, or electronic payments are done through HausTap.{"\n\n"}
              4. CLIENT RESPONSIBILITIES{"\n"}
              By using the platform, you agree to:{"\n"}
              Provide safe, respectful, and appropriate behavior toward service providers.{"\n"}
              Ensure the service location is accessible and safe for the provider to perform the job.{"\n"}
              Not request illegal, abusive, or harmful activities from any provider.{"\n"}
              Not bypass the HausTap platform by attempting direct, outside-app transactions.{"\n\n"}
              5. RATINGS & REVIEWS{"\n"}
              After service, you may rate and review providers honestly and fairly.{"\n"}
              False, abusive, or defamatory reviews are prohibited.{"\n"}
              HausTap may remove unfair or malicious feedback and take action accordingly.{"\n\n"}
              6. LIMITATION OF LIABILITY{"\n"}
              HausTap is only a booking platform, NOT the employer or agent of any provider.{"\n"}
              All services are directly between you and the service provider.{"\n"}
              HausTap is not responsible for:{"\n"}
              Damages to property or belongings{"\n"}
              Service outcomes or dissatisfaction{"\n"}
              Delays, cancellations, or emergencies{"\n"}
              Your safety and discretion are your responsibility during service appointments.{"\n\n"}
              7. SUSPENSION OR TERMINATION{"\n"}
              Your access to HausTap may be temporarily or permanently suspended if you:{"\n"}
              Violate these Terms{"\n"}
              Engage in abusive or fraudulent behavior{"\n"}
              Harass or disrespect service providers{"\n"}
              Attempt to bypass or exploit HausTap&apos;s system{"\n\n"}
              8. COMMUNICATION CONSENT{"\n"}
              By creating a HausTap account, you consent to receive booking updates, notifications, and important alerts via SMS, email, or in-app messages. You may disable promotional messages anytime.{"\n\n"}
              9. AMENDMENTS{"\n"}
              HausTap reserves the right to update or modify these Terms at any time. Continued use of the platform means you accept the updated Terms.
            </Text>
          </ScrollView>
          <TouchableOpacity
            style={styles.modalButton}
            onPress={() => setShowTerms(false)}
          >
            <Text style={styles.buttonText}>Close</Text>
          </TouchableOpacity>
        </SafeAreaView>
      </Modal>

      {/* Privacy Policy Modal */}
      <Modal visible={showPrivacy} animationType="slide">
        <SafeAreaView style={styles.modalContainer}>
          <StatusBar barStyle="dark-content" backgroundColor="#fff" />
          <View style={styles.modalHeader}>
            <Text style={styles.modalTitle}>Privacy Policy</Text>
            <TouchableOpacity
              style={styles.modalCloseButton}
              onPress={() => setShowPrivacy(false)}
            >
              <Ionicons name="close" size={24} color="#666" />
            </TouchableOpacity>
          </View>
          <ScrollView contentContainerStyle={styles.modalContent}>
            <Text style={styles.modalText}>
              Last Updated: October 2025{"\n\n"}
              This Privacy Policy explains how HausTap (&quot;we&quot;, &quot;our&quot;, or &quot;the Platform&quot;) collects, uses, stores, and protects your personal information when you create an account or use our services.{"\n\n"}
              By using HausTap, you confirm that you have read and understood this Privacy Policy.{"\n\n"}
              1. INFORMATION WE COLLECT{"\n"}
              We only collect information that is necessary for account creation, booking, and communication, including:{"\n"}
              • Full Name{"\n"}
              • Email Address & Mobile Number (for OTP verification and notifications){"\n"}
              • Location/Address (for service booking and provider matching){"\n"}
              • Booking history and feedback{"\n\n"}
              We do not collect or store payment card details because all payments are done in cash directly to the service provider.{"\n\n"}
              2. HOW WE USE YOUR INFORMATION{"\n\n"}
              Your data is used solely for platform operations, including:{"\n"}
              • Account registration and OTP identity verification{"\n"}
              • Sending booking confirmations and service notifications{"\n"}
              • Matching you with nearby verified service providers{"\n"}
              • Improving platform security and service experience{"\n"}
              • Providing support and resolving concerns{"\n\n"}
              3. SHARING OF INFORMATION{"\n"}
              HausTap respects your privacy and will not sell or disclose your personal data to unauthorized third parties. However, we may share limited information with:{"\n"}
              • Verified Service Providers only for booking and coordination{"\n"}
              • Legal authorities when required by law or for security purposes{"\n\n"}
              4. DATA SECURITY{"\n"}
              We implement reasonable security measures, including:{"\n"}
              • OTP verification and secure login access{"\n"}
              • Restricted access to sensitive data{"\n"}
              • Monitoring for suspicious or fraudulent activity{"\n\n"}
              However, no digital system is 100% secure. HausTap shall not be held liable for data breaches caused by hacking, third-party intrusion, or situations beyond our control.{"\n\n"}
              5. YOUR RIGHTS AS A USER{"\n"}
              You have the right to:{"\n"}
              • Update or correct your information{"\n"}
              • Request account deletion (subject to verification and pending transactions){"\n"}
              • Decline promotional messages (but essential system alerts cannot be disabled){"\n\n"}
              Requests may be sent through HausTap&apos;s official support channels.{"\n\n"}
              6. DATA RETENTION{"\n"}
              Your data will be stored only as long as necessary for your account usage, legal compliance, or dispute resolution. Terminated accounts may be securely archived or permanently deleted as needed.{"\n\n"}
              7. POLICY UPDATES{"\n"}
              HausTap may update this Privacy Policy at any time to improve safety and compliance. You will be notified of major changes. Continued use of the platform confirms your acceptance.{"\n\n"}
              8. CONTACT INFORMATION{"\n"}
              For any questions, support, or privacy-related concerns, you may reach us through our official communication channels: Email, Facebook, or Instagram.
            </Text>
          </ScrollView>
          <TouchableOpacity
            style={styles.modalButton}
            onPress={() => setShowPrivacy(false)}
          >
            <Text style={styles.buttonText}>Close</Text>
          </TouchableOpacity>
        </SafeAreaView>
      </Modal>

      {/* Email Verification Modal */}
      <Modal visible={showEmailVerification} animationType="slide">
        <SafeAreaView style={styles.modalContainer}>
          <StatusBar barStyle="dark-content" backgroundColor="#fff" />
          {/* Header with back button */}
          <View style={styles.modalHeader}>
            <TouchableOpacity
              style={styles.modalCloseButton}
              onPress={() => setShowEmailVerification(false)}
            >
              <Ionicons name="arrow-back" size={24} color="#666" />
            </TouchableOpacity>
            <Text style={styles.modalTitle}>Email Verification</Text>
            {/* placeholder to balance header layout */}
            <View style={{ width: 40 }} />
          </View>
          <View style={[styles.modalContent, { alignItems: 'center', padding: 20 }]}>
            <Text style={[styles.modalText, { textAlign: 'center', marginBottom: 20 }]}>
              We have sent a One-Time Password (OTP) to your registered email address.
              Please enter the code below to verify your email.
            </Text>
            
            <Text style={[styles.label, { alignSelf: 'flex-start' }]}>Enter Email</Text>
            <TextInput
              style={[styles.input, { width: '100%', marginBottom: 15 }]}
              value={email}
              editable={false}
            />
            
            <Text style={[styles.label, { alignSelf: 'flex-start' }]}>Enter OTP</Text>
            <TextInput
              style={[
                styles.input,
                { width: '100%', marginBottom: 5 },
                otpError ? { borderColor: '#ff3b30' } : {}
              ]}
              keyboardType="numeric"
              value={otp}
              onChangeText={(text) => {
                setOtp(text);
                setOtpError(""); // Clear error when user types
              }}
              maxLength={6}
            />
            
            {otpError ? (
              <Text style={styles.errorText}>{otpError}</Text>
            ) : null}

            <Text style={[styles.timerText, isOtpExpired ? styles.expiredText : {}]}>
              Time remaining: {formatTime(remainingTime)}
            </Text>

            <TouchableOpacity 
              style={[styles.button, { width: '100%', marginTop: 15 }]}
              onPress={verifyOtp}
            >
              <Text style={styles.buttonText}>Verify</Text>
            </TouchableOpacity>

            <TouchableOpacity 
              onPress={() => {
                void generateNewOtp();
              }}
              style={{ marginTop: 15 }}
              disabled={!isOtpExpired && remainingTime > 0}
            >
              <Text 
                style={[
                  styles.link, 
                  { fontSize: 14 },
                  (!isOtpExpired && remainingTime > 0) ? { opacity: 0.5 } : {}
                ]}
              >
                Resend OTP
              </Text>
            </TouchableOpacity>
          </View>
        </SafeAreaView>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  errorText: {
    color: '#ff3b30',
    fontSize: 12,
    marginBottom: 10,
    alignSelf: 'flex-start'
  },
  timerText: {
    fontSize: 14,
    color: '#666',
    marginTop: 5,
    marginBottom: 10
  },
  expiredText: {
    color: '#ff3b30'
  },
  container: {
    flexGrow: 1,
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#fff",
    padding: 20,
  },
  logo: {
    width: 120,
    height: 120,
    marginBottom: 10,
  },
  box: {
    backgroundColor: "#f2f2f2",
    borderRadius: 10,
    padding: 20,
    width: "100%",
  },
  boxTitle: {
    fontSize: 20,
    fontWeight: "bold",
    textAlign: "center",
    marginBottom: 15,
  },
  label: {
    fontSize: 12,
    color: "#666",
    marginBottom: 4,
  },
  input: {
    backgroundColor: "#fff",
    borderWidth: 1,
    borderColor: "#ccc",
    borderRadius: 8,
    height: 45,
    paddingHorizontal: 12,
    fontSize: 14,
    marginBottom: 10,
  },
  inputContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#fff",
    borderRadius: 8,
    height: 45,
    paddingHorizontal: 10,
    marginBottom: 10,
  },
  passwordInput: {
    flex: 1,
    color: "#000",
  },
  rowContainer: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 15,
    gap: 8,
  },
  flex1: { flex: 1 },
  flex2: { flex: 2 },
  noLabel: { marginTop: 22 },
  button: {
    backgroundColor: "#3DC1C6",
    borderRadius: 10,
    paddingVertical: 12,
    alignItems: "center",
    marginTop: 5,
  },
  buttonAlt: {
    backgroundColor: "#3DC1C6",
    borderRadius: 10,
    paddingVertical: 12,
    alignItems: "center",
    marginTop: 10,
  },
  buttonText: { color: "#fff", fontWeight: "bold", fontSize: 16 },
  agreement: { textAlign: "center", fontSize: 12, color: "#555", marginTop: 10 },
  termsContainer: { flexDirection: "row", justifyContent: "center", marginBottom: 5 },
  link: { color: "#3DC1C6", fontSize: 12 },
  separator: { color: "#555", fontSize: 12 },
  footerText: { textAlign: "center", fontSize: 13, marginTop: 10 },
  line: { height: 1, backgroundColor: "#ccc", marginVertical: 15 },
  modalContainer: {
    flex: 1,
    backgroundColor: "#fff",
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: "#eee",
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: "bold",
  },
  modalCloseButton: {
    padding: 8,
  },
  modalContent: {
    padding: 16,
  },
  modalText: {
    fontSize: 14,
    color: "#333",
    lineHeight: 22,
  },
  modalButton: {
    backgroundColor: "#3DC1C6",
    borderRadius: 10,
    padding: 12,
    alignItems: "center",
    margin: 16,
  },
});