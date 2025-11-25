import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useEffect, useRef, useState } from 'react';
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

export default function VerificationForgotPassword() {
  const params = useLocalSearchParams();
  const router = useRouter();
  const email = Array.isArray(params.email) ? params.email[0] : (params.email || '');
  const [code, setCode] = useState<string[]>(['', '', '', '', '', '']);
  const inputs = useRef<Array<TextInput | null>>([]);
  const [secondsLeft, setSecondsLeft] = useState(20);
  const [canResend, setCanResend] = useState(false);

  useEffect(() => {
    setCanResend(false);
    setSecondsLeft(20);
    const t = setInterval(() => {
      setSecondsLeft((s) => {
        if (s <= 1) {
          clearInterval(t);
          setCanResend(true);
          return 0;
        }
        return s - 1;
      });
    }, 1000);
    return () => clearInterval(t);
  }, []);

  const handleChange = (text: string, idx: number) => {
    if (text.length > 1) text = text.slice(-1);
    const next = [...code];
    next[idx] = text;
    setCode(next);
    if (text && idx < inputs.current.length - 1) {
      inputs.current[idx + 1]?.focus();
    }
  };

  const handleKeyPress = ({ nativeEvent }: any, idx: number) => {
    if (nativeEvent.key === 'Backspace' && !code[idx] && idx > 0) {
      inputs.current[idx - 1]?.focus();
    }
  };

  const handleResend = async () => {
    if (!canResend) return;
    try {
      const res = await sendOtp(email);
      // Log OTP to terminal only in dev for debugging (do not show on-device)
      const otpValue = res?.otp || res?.data?.otp;
      if (__DEV__ && otpValue) {
        console.log(`DEV OTP for ${email}:`, otpValue);
      }
      Alert.alert('Resend', 'Verification code resent');
    } catch (e: any) {
      Alert.alert('Error', e?.message || 'Failed to resend');
    }
    // restart timer
    setCanResend(false);
    setSecondsLeft(20);
    const t = setInterval(() => {
      setSecondsLeft((s) => {
        if (s <= 1) {
          clearInterval(t);
          setCanResend(true);
          return 0;
        }
        return s - 1;
      });
    }, 1000);
  };

  const handleNext = () => {
    const entered = code.join('');
    if (entered.length < 6) {
      Alert.alert('Validation', 'Please enter the 6-digit code');
      return;
    }
    // Navigate to create-new-password screen and pass email + otp
    router.push(`/auth/create-new-password?email=${encodeURIComponent(email)}&otp=${encodeURIComponent(entered)}`);
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton} accessibilityLabel="Go back">
          <Ionicons name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Forgot password</Text>
      </View>

      <View style={styles.content}>
        <Text style={styles.infoText}>Your verification code is sent via Email to</Text>
        <Text style={styles.emailText}>{email}</Text>

        <View style={styles.codeContainer}>
          {code.map((c, i) => (
            <TextInput
              key={i}
              ref={(ref) => { inputs.current[i] = ref; }}
              value={c}
              onChangeText={(t) => handleChange(t, i)}
              onKeyPress={(e) => handleKeyPress(e, i)}
              keyboardType="number-pad"
              maxLength={1}
              style={styles.codeInput}
              textAlign="center"
              returnKeyType="done"
            />
          ))}
        </View>

        <Text style={styles.waitText}>Pls wait until {secondsLeft} seconds to resend</Text>
        <TouchableOpacity onPress={handleResend} disabled={!canResend} accessibilityLabel="Resend Code">
          <Text style={[styles.resendText, !canResend && { opacity: 0.4 }]}>Resend Code</Text>
        </TouchableOpacity>
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
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 24,
  },
  infoText: {
    fontSize: 13,
    color: '#333',
    textAlign: 'center',
    marginBottom: 8,
  },
  emailText: {
    fontSize: 14,
    fontWeight: '700',
    marginBottom: 24,
  },
  codeContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    width: '80%',
    marginBottom: 18,
  },
  codeInput: {
    width: 36,
    height: 36,
    borderBottomWidth: 2,
    borderColor: '#ccc',
    fontSize: 18,
    padding: 0,
  },
  waitText: {
    fontSize: 12,
    color: '#999',
    marginTop: 8,
  },
  resendText: {
    color: '#3DC1C6',
    marginTop: 8,
    fontSize: 13,
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
