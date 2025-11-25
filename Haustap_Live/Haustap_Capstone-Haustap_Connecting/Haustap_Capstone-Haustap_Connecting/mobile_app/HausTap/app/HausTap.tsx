import { Ionicons } from "@expo/vector-icons";
import { Image } from "expo-image";
import * as SecureStore from 'expo-secure-store';
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
import { setAuthToken } from '../services/api-client';
import { login as apiLogin, register } from '../services/auth-api';
import { setUserRole } from '../src/config/apiConfig';
import { sendEmailOtp } from '../src/utils/otp';
import * as Validation from '../src/utils/validation';

import { useLocalSearchParams, useRouter } from 'expo-router';

export default function HausTap() {
  const router = useRouter();
  const params = useLocalSearchParams() as Record<string, any>;
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [month, setMonth] = useState("");
  const [day, setDay] = useState("");
  const [year, setYear] = useState("");
  const [showTerms, setShowTerms] = useState(false);
  const [showPrivacy, setShowPrivacy] = useState(false);
  const [acceptedPolicies, setAcceptedPolicies] = useState(false);
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

  const validateForm = (): boolean => {
    const errors: string[] = [];

    if (!Validation.isNonEmptyString(firstName)) errors.push("First Name is required");
    if (!Validation.isNonEmptyString(lastName)) errors.push("Last Name is required");
    if (!Validation.isNonEmptyString(email)) errors.push("Email is required");
    if (!Validation.isNonEmptyString(mobileNumber)) errors.push("Mobile Number is required");
    if (!Validation.isNonEmptyString(password)) errors.push("Password is required");
    if (!Validation.isNonEmptyString(confirmPassword)) errors.push("Confirm Password is required");
    if (!month || !day || !year) errors.push("Complete Birthdate is required");

    if (Validation.isNonEmptyString(email) && !Validation.isValidEmail(email)) {
        errors.push("Please enter a valid email address");
    }
    
    if (Validation.isNonEmptyString(mobileNumber) && !Validation.isValidPhoneNumber(mobileNumber)) {
        errors.push("Please enter a valid mobile number");
    }


    if (Validation.isNonEmptyString(password) && Validation.isNonEmptyString(confirmPassword) && password !== confirmPassword) {
        errors.push("Passwords do not match");
    }

    if (Validation.isNonEmptyString(password) && !Validation.isStrongPassword(password)) {
        errors.push("Password must be at least 8 characters long and include uppercase, lowercase, number, and special character");
    }
	  // Construct DOB and validate
    const dob = new Date(Number(year), Number(month) - 1, Number(day));
    if (isNaN(dob.getTime())) {
      errors.push('Please enter a valid birthdate');
      return false;
    }
	 // Calculate age
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
      age -= 1;
    }
    if (age < 18) {
      errors.push('You must be at least 18 years old to sign up.');
      return false;
    }

    if (errors.length > 0) {
        alert(errors.join("\n"));
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

    // Generate new OTP
    const generateNewOtp = async () => {
        setOtpError("");
        setOtp("");
        
        try {
            const otpResponse = await sendEmailOtp(email);

            if (otpResponse) {
                setGeneratedOtp(otpResponse.otp);
                console.log('OTP sent successfully:', otpResponse.otp);
                startOtpTimer();
            } 
            else {
                setOtpError("Failed to send OTP email. Please try again.");
            }

        } catch (error) {

            console.error("Error sending OTP:", error);
            setOtpError("Failed to send OTP email. Please try again.");
        }
    };

    // Verify OTP
    const handleValidOtp = () => {
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
        alert("Email verified successfully!");
        handleSaveUser();
    };

    const handleSaveUser = async () => {

        console.log("Saving user:", {
            firstName,
            middleInitial,
            lastName,
            email,
            mobileNumber,
            password,
            confirmPassword
        });
        try {
            const name = firstName + " " + middleInitial + " " + lastName;
            const response = await register(name, email, mobileNumber, password, confirmPassword);

            if (response.success) {
                // Attempt automatic login after successful registration so user returns to browsing flow
                try {
                  const loginRes = await apiLogin(email, password);
                  const tokenFromRes = loginRes?.token || loginRes?.data?.token || loginRes?.access_token || loginRes?.data?.access_token || loginRes?.meta?.token;
                  const userFromRes = loginRes?.user || loginRes?.data?.user || loginRes?.data || loginRes;
                  if (tokenFromRes) {
                    setAuthToken(tokenFromRes);
                  }
                  // Persist minimal auth data for other parts of the app
                  try {
                    await SecureStore.setItemAsync('HT_auth', JSON.stringify({ user: userFromRes || null, token: tokenFromRes || null, mode: (userFromRes && userFromRes.role) || 'client' }));
                  } catch (e) {
                    // ignore storage errors
                  }
                  // set role flag used elsewhere
                  try { await setUserRole('client'); } catch (e) { }

                  // If a redirect param was provided, go there; otherwise go to client home
                  const redirect = params?.redirect ? String(params.redirect) : null;
                  if (redirect) {
                    const dest = decodeURIComponent(redirect);
                    router.replace(dest as any);
                  } else {
                    router.replace('/client-side');
                  }
                  return;
                } catch (err) {
                  // If auto-login fails, fall back to sending user to login screen
                  alert("Registration successful. Please log in.");
                  router.replace('/auth/log-in');
                  return;
                }
            }
            else {
                alert("Registration failed: " + (response.message || "Unknown error"));
            }
        } catch (error) {
            console.error("Error signing up:", error);
            alert("Failed to sign up. Please try again later.");
        }
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

            {/* Full Name Section (no M.I.) */}
            <View style={styles.rowContainer}>
              <View style={styles.flex1}>
                <Text style={styles.label}>First Name <Text style={{ color: 'red' }}>*</Text></Text>
                <TextInput 
                  style={styles.input} 
                  placeholder="First Name"
                  value={firstName}
                  onChangeText={setFirstName}
                />
              </View>
              <View style={styles.flex1}>
                <Text style={styles.label}>Last Name <Text style={{ color: 'red' }}>*</Text></Text>
                <TextInput 
                  style={styles.input} 
                  placeholder="Last Name"
                  value={lastName}
                  onChangeText={setLastName}
                />
              </View>
            </View>

            {/* Birthdate */}
            <Text style={styles.label}>Birthdate <Text style={{ color: 'red' }}>*</Text></Text>
            <View style={styles.rowContainer}>
              <View style={styles.flex1}>
                <TextInput
                  style={[styles.input, styles.birthInput]}
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
              <View style={[styles.flex1, styles.birthSpacer]}>
                <TextInput
                  style={[styles.input, styles.birthInput]}
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
              <View style={styles.flex1}>
                <TextInput
                  style={[styles.input, styles.birthInput]}
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
            <Text style={styles.label}>Email <Text style={{ color: 'red' }}>*</Text></Text>
            <TextInput 
              style={styles.input} 
              keyboardType="email-address"
              value={email}
              onChangeText={setEmail}
              autoCapitalize="none"
            />

            {/* Mobile Number */}
            <Text style={styles.label}>Mobile Number <Text style={{ color: 'red' }}>*</Text></Text>
            <TextInput 
              style={styles.input} 
              keyboardType="phone-pad"
              value={mobileNumber}
              onChangeText={(t) => setMobileNumber(String(t).replace(/\D/g, '').slice(0, 11))}
              maxLength={11}
            />

            {/* Password */}
            <Text style={styles.label}>Password <Text style={{ color: 'red' }}>*</Text></Text>
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
            <Text style={styles.label}>Confirm Password <Text style={{ color: 'red' }}>*</Text></Text>
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


            <Text style={styles.footerText}>
              Already have an account? <Text style={styles.link} onPress={() => router.push('/auth/log-in')}>Log In</Text>
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
              These Terms and Conditions ("Terms") govern your access to and use of the HausTap mobile and web platform ("HausTap", "we", "our", or "the Platform") for booking home services. By creating an account or using HausTap, you acknowledge that you have read, understood, and agreed to these Terms.{"\n\n"}
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
              Attempt to bypass or exploit HausTap's system{"\n\n"}
              8. COMMUNICATION CONSENT{"\n"}
              By creating a HausTap account, you consent to receive booking updates, notifications, and important alerts via SMS, email, or in-app messages. You may disable promotional messages anytime.{"\n\n"}
              9. AMENDMENTS{"\n"}
              HausTap reserves the right to update or modify these Terms at any time. Continued use of the platform means you accept the updated Terms.{"\n\n"}

              Privacy Policy{"\n\n"}
              Last Updated: October 2025{"\n\n"}
              This Privacy Policy explains how HausTap ("we", "our", or "the Platform") collects, uses, stores, and protects your personal information when you create an account or use our services. By using HausTap, you confirm that you have read and understood this Privacy Policy.{"\n\n"}
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
              5. YOUR RIGHTS AS A USER{"\n"}
              You have the right to:{"\n"}
              • Update or correct your information{"\n"}
              • Request account deletion (subject to verification and pending transactions){"\n"}
              • Decline promotional messages (but essential system alerts cannot be disabled){"\n\n"}
              Requests may be sent through HausTap's official support channels.{"\n\n"}
              6. DATA RETENTION{"\n"}
              Your data will be stored only as long as necessary for your account usage, legal compliance, or dispute resolution. Terminated accounts may be securely archived or permanently deleted as needed.{"\n\n"}
              7. POLICY UPDATES{"\n"}
              HausTap may update this Privacy Policy at any time to improve safety and compliance. You will be notified of major changes. Continued use of the platform confirms your acceptance.{"\n\n"}
              8. CONTACT INFORMATION{"\n"}
              For any questions, support, or privacy-related concerns, you may reach us through our official communication channels: Email, Facebook, or Instagram.{"\n\n"}
            </Text>
          </ScrollView>

          <View style={styles.checkboxContainer}>
            <TouchableOpacity
              onPress={() => setAcceptedPolicies(!acceptedPolicies)}
              style={[styles.checkbox, acceptedPolicies ? styles.checkboxChecked : {}]}
            >
              {acceptedPolicies ? <Ionicons name="checkmark" size={16} color="#fff" /> : null}
            </TouchableOpacity>

            <Text style={styles.checkboxLabel}>
              I, the undersigned, confirm that I have read, understood, and agree to the Terms & Conditions and the Privacy Policy set forth by HausTap.
            </Text>
          </View>
          <View style={styles.agreeButtonContainer}>
            <TouchableOpacity
              disabled={!acceptedPolicies}
              style={[styles.agreeButton, !acceptedPolicies ? styles.agreeButtonDisabled : {}]}
              onPress={() => {
                if (!acceptedPolicies) {
                  alert('Please confirm that you have read and agree to the Terms & Privacy Policy.');
                  return;
                }
                // proceed to OTP flow
                setShowTerms(false);
                setShowEmailVerification(true);
                void generateNewOtp();
              }}
            >
              <Text style={styles.buttonText}>Agree</Text>
            </TouchableOpacity>
          </View>
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
              This Privacy Policy explains how HausTap ("we", "our", or "the Platform") collects, uses, stores, and protects your personal information when you create an account or use our services.{"\n\n"}
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
              Requests may be sent through HausTap's official support channels.{"\n\n"}
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
              onPress={handleValidOtp}
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
    marginBottom: 8,
  },
  box: {
    backgroundColor: "#ececec",
    borderRadius: 18,
    padding: 22,
    width: "92%",
    maxWidth: 420,
    alignSelf: 'center',
    // subtle shadow
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.06,
    shadowRadius: 8,
    elevation: 4,
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
    borderColor: "#d9d9d9",
    borderRadius: 8,
    height: 44,
    paddingHorizontal: 12,
    fontSize: 14,
    marginBottom: 10,
  },
  birthInput: {
    textAlign: 'center',
    paddingVertical: 4,
  },
  birthSpacer: {
    marginHorizontal: 6,
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
    marginBottom: 14,
    gap: 8,
  },
  flex1: { flex: 1 },
  flex2: { flex: 2 },
  noLabel: { marginTop: 22 },
  button: {
    backgroundColor: "#00ADB5",
    borderRadius: 24,
    paddingVertical: 12,
    alignItems: "center",
    marginTop: 12,
    width: '64%',
    alignSelf: 'center',
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
  modalCancelButton: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 10,
    padding: 12,
    alignItems: 'center',
    flex: 1,
    marginRight: 8,
  },
  checkboxContainer: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    paddingHorizontal: 16,
    paddingBottom: 20,
  },
  checkbox: {
    width: 22,
    height: 22,
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 4,
    marginRight: 10,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 2,
  },
  checkboxChecked: {
    backgroundColor: '#3DC1C6',
    borderColor: '#3DC1C6',
  },
  checkboxLabel: {
    flex: 1,
    fontSize: 13,
    color: '#333',
    lineHeight: 18,
  },
  agreeButtonContainer: {
    paddingHorizontal: 16,
    alignItems: 'flex-end',
    marginBottom: 20,
  },
  agreeButton: {
    backgroundColor: '#00ADB5',
    borderRadius: 20,
    paddingVertical: 10,
    paddingHorizontal: 18,
    minWidth: 90,
    alignItems: 'center',
  },
  agreeButtonDisabled: {
    backgroundColor: '#9fd1d2',
  },
});