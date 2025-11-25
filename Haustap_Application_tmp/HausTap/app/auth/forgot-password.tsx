import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import {
  Alert,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { sendOtp } from '../../services/auth-api';

export default function ForgotPasswordScreen() {
  const router = useRouter();
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);

  const handleBack = () => {
    router.back();
  };

  const handleNext = async () => {
    if (!email) {
      Alert.alert('Validation', 'Please enter your email');
      return;
    }
    setLoading(true);
    try {
      const res = await sendOtp(email);
      // If backend returns the OTP for debugging/testing, log it to terminal only in dev
      const otpValue = res?.otp || res?.data?.otp;
      if (__DEV__ && otpValue) {
        console.log(`DEV OTP for ${email}:`, otpValue);
      }
      // Redact any OTP from logs to avoid exposing it on device
      const safeRes: any = { ...res };
      if (safeRes?.otp) delete safeRes.otp;
      if (safeRes?.data?.otp) delete safeRes.data.otp;
      console.log('sendOtp response (redacted):', safeRes);
      // show server message if available (without OTP)
      const msg = res?.message || res?.data?.message || 'Verification code sent';
      Alert.alert('Success', String(msg));
      router.push(`/auth/verification-forgot-password?email=${encodeURIComponent(email)}`);
    } catch (e: any) {
      console.error('sendOtp error:', e);
      const msg = e?.message || (e?.error ? JSON.stringify(e.error) : 'Failed to send OTP');
      Alert.alert('Error', msg.toString());
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={handleBack} style={styles.backButton} accessibilityLabel="Go back">
          <Ionicons name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Forgot password</Text>
      </View>

      <View style={styles.content}>
        <Text style={styles.helpText}>
          Enter your email associated with your account to reset your password.
        </Text>

        <TextInput
          value={email}
          onChangeText={setEmail}
          placeholder="Email"
          keyboardType="email-address"
          autoCapitalize="none"
          style={styles.input}
        />
      </View>

      <TouchableOpacity style={styles.nextButton} onPress={handleNext} accessibilityLabel="Next">
        <Text style={styles.nextButtonText}>Next</Text>
      </TouchableOpacity>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingTop: 12,
    paddingBottom: 10,
  },
  backButton: {
    paddingRight: 8,
    paddingVertical: 6,
  },
  headerTitle: {
    fontSize: 16,
    fontWeight: '600',
    marginLeft: 6,
  },
  content: {
    flex: 1,
    paddingHorizontal: 20,
    paddingTop: 20,
  },
  helpText: {
    fontSize: 14,
    color: '#333',
    marginBottom: 20,
    lineHeight: 20,
  },
  input: {
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 6,
    paddingHorizontal: 12,
    paddingVertical: 10,
    backgroundColor: '#fff',
  },
  nextButton: {
    backgroundColor: '#3DC1C6',
    height: 52,
    alignItems: 'center',
    justifyContent: 'center',
  },
  nextButtonText: {
    color: '#000',
    fontSize: 16,
    fontWeight: '600',
  },
});
