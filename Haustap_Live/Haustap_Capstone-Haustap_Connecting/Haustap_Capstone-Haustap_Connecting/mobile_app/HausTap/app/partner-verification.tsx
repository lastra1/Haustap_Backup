import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useEffect, useState } from 'react';
import { Alert, StatusBar, StyleSheet, Text, TextInput, TouchableOpacity, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { accountsStore } from '../src/services/accountsStore';
import { authService } from '../src/services/auth.service';
import { flowStore } from '../src/services/flowStore';
import { useAuth } from './context/AuthContext';

export default function PartnerVerification() {
  const { services, email } = useLocalSearchParams();
  const router = useRouter();
  const { setApplicationPending, user } = useAuth();
  const [otp, setOtp] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [otpError, setOtpError] = useState('');
  const [remainingTime, setRemainingTime] = useState(300); // 5 minutes in seconds
  const [isOtpExpired, setIsOtpExpired] = useState(false);

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

  // Send initial OTP on mount
  // decoded email for display and sending
  const decodedEmail = email ? decodeURIComponent(email as string) : '';

  // Send initial OTP when email becomes available (try query param first, then flowStore)
  useEffect(() => {
    if (decodedEmail) {
      console.log('[partner-verification] received email param:', decodedEmail);
      void sendOTP(decodedEmail);
      return;
    }

    const fallbackEmail = flowStore.getEmail();
    if (fallbackEmail) {
      console.log('[partner-verification] no email in params, using flowStore email:', fallbackEmail);
      void sendOTP(fallbackEmail);
    } else {
      console.log('[partner-verification] no email param received and flowStore empty');
    }
  }, [decodedEmail]);

  // accept an optional email param (decoded) to ensure we send to the correct address
  const sendOTP = async (decodedEmail?: string) => {
    const targetEmail = decodedEmail || (email ? decodeURIComponent(email as string) : '');
    if (!targetEmail) {
      Alert.alert('Error', 'Email address is required');
      return;
    }

    console.log('[partner-verification] sending OTP to:', targetEmail);
    setIsLoading(true);
    try {
      const success = await authService.sendOTP(targetEmail);
      if (success) {
        startOtpTimer();
        setOtpError('');
      } else {
        setOtpError('Failed to send verification code. Please try again.');
      }
    } catch (error) {
      console.error('Error sending OTP:', error);
      setOtpError('An unexpected error occurred. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  const verifyOtp = async () => {
    if (!otp || otp.length !== 6) {
      setOtpError('Please enter a valid 6-digit code');
      return;
    }

    if (isOtpExpired) {
      setOtpError('OTP has expired. Please request a new one.');
      return;
    }

    setIsLoading(true);
    try {
      const targetEmail = decodedEmail || flowStore.getEmail();
      const isValid = await authService.verifyOTP(targetEmail, otp);
      if (isValid) {
        Alert.alert(
          'Success',
          'Email verified successfully. Please complete your service provider profile.',
          [
            {
              text: 'OK',
                onPress: async () => {
                try {
                  // mark the account as having a pending application
                  const target = decodedEmail || flowStore.getEmail();
                  if (target) {
                    await accountsStore.updateAccount(target, { isApplicationPending: true });
                    // if the currently-authenticated user matches, update in-memory auth state as well
                    try {
                      if (user && user.email === target && setApplicationPending) {
                        await setApplicationPending(true);
                      }
                    } catch (e) {
                      // ignore
                    }
                  }
                  // if the current app has an authenticated user matching this email, update auth state immediately
                } catch (e) {
                  // ignore update failure
                }
                router.replace('/partner-onboarding-success');
              }
            }
          ]
        );
      } else {
        setOtpError('Invalid verification code. Please try again.');
      }
    } catch (error) {
      setOtpError('Failed to verify code. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="#fff" />
      
      <View style={styles.modalHeader}>
        <TouchableOpacity
          style={styles.modalCloseButton}
          onPress={() => router.back()}
        >
          <Ionicons name="arrow-back" size={24} color="#666" />
        </TouchableOpacity>
        <Text style={styles.modalTitle}>Email Verification</Text>
        {/* placeholder to balance header layout */}
        <View style={{ width: 40 }} />
      </View>

      <View style={[styles.content, { alignItems: 'center', padding: 20 }]}>
        <Text style={[styles.description, { textAlign: 'center', marginBottom: 20 }]}>
          We have sent a One-Time Password (OTP) to your registered email address.
          Please enter the code below to verify your email.
        </Text>
        
        <Text style={[styles.label, { alignSelf: 'flex-start' }]}>Enter Email</Text>
        <TextInput
          style={[styles.input, { width: '100%', marginBottom: 15 }]}
          value={(decodedEmail || flowStore.getEmail()) as string}
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
            setOtpError('');
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
          disabled={isLoading}
        >
          <Text style={styles.buttonText}>Verify</Text>
        </TouchableOpacity>

        <TouchableOpacity 
          onPress={() => {
            void sendOTP();
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
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff'
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  modalCloseButton: {
    padding: 8,
  },
  content: {
    flex: 1,
  },
  description: {
    fontSize: 16,
    color: '#666',
    lineHeight: 22,
  },
  label: {
    fontSize: 12,
    color: '#666',
    marginBottom: 4,
  },
  input: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    height: 45,
    paddingHorizontal: 12,
    fontSize: 14,
  },
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
  button: {
    backgroundColor: '#3DC1C6',
    borderRadius: 10,
    paddingVertical: 12,
    alignItems: 'center',
  },
  buttonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16
  },
  link: {
    color: '#3DC1C6',
    fontSize: 14,
  },
});