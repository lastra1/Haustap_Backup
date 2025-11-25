import { Ionicons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
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
import { resetPassword } from '../../services/auth-api';

export default function CreateNewPassword() {
  const params = useLocalSearchParams();
  const router = useRouter();
  const email = Array.isArray(params.email) ? params.email[0] : (params.email || '');
  const otp = Array.isArray(params.otp) ? params.otp[0] : (params.otp || '');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  const rules = {
    hasLower: /[a-z]/.test(password),
    hasUpper: /[A-Z]/.test(password),
    validLength: password.length >= 8 && password.length <= 16,
    alnumOnly: /^[A-Za-z0-9]*$/.test(password),
  };

  const handleBack = () => router.back();

  const handleNext = () => {
    if (!rules.hasLower || !rules.hasUpper || !rules.validLength || !rules.alnumOnly) {
      Alert.alert('Invalid password', 'Please follow the password requirements');
      return;
    }
    if (!email || !otp) {
      Alert.alert('Missing data', 'Email or verification code missing. Please request a new code.');
      // navigate back to verification so user can resend
      router.replace(`/auth/verification-forgot-password?email=${encodeURIComponent(email)}`);
      return;
    }
    (async () => {
      // log the params to terminal only
      if (__DEV__) {
        console.log('Attempting password reset for:', { email });
        console.log('DEV OTP present:', !!otp);
      }
      setLoading(true);
      try {
        const res = await resetPassword(email, otp, password, password);
        console.log('resetPassword response:', res);
        Alert.alert('Success', 'Password changed successfully');
        router.replace('/auth/log-in');
      } catch (e: any) {
        console.error('resetPassword error:', e);
        // Try to extract useful message from server response
        const errMsg =
          (e && typeof e === 'string' && e) ||
          e?.message ||
          e?.error ||
          (e?.errors && JSON.stringify(e.errors)) ||
          JSON.stringify(e) ||
          'Failed to change password';
        Alert.alert('Error', errMsg);
      } finally {
        setLoading(false);
      }
    })();
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={handleBack} style={styles.backButton} accessibilityLabel="Go back">
          <Ionicons name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Change Password</Text>
      </View>

      <View style={styles.content}>
        <Text style={styles.title}>Create a new password</Text>

        <View style={styles.inputWrapper}>
          <TextInput
            value={password}
            onChangeText={setPassword}
            placeholder="Password"
            secureTextEntry={!showPassword}
            style={styles.input}
            autoCapitalize="none"
          />
          <TouchableOpacity style={styles.eye} onPress={() => setShowPassword((s) => !s)}>
            <Ionicons name={showPassword ? 'eye-off' : 'eye'} size={20} color="#666" />
          </TouchableOpacity>
        </View>

        <View style={styles.rulesContainer}>
          <Text style={styles.ruleText}>{rules.hasLower ? '✓' : '○'} At least one lowercase character</Text>
          <Text style={styles.ruleText}>{rules.hasUpper ? '✓' : '○'} At least one uppercase character</Text>
          <Text style={styles.ruleText}>{rules.validLength ? '✓' : '○'} 8-16 characters</Text>
          <Text style={styles.ruleText}>{rules.alnumOnly ? '✓' : '○'} Only letters and numbers can be used</Text>
        </View>
      </View>

      <TouchableOpacity style={styles.nextButton} onPress={handleNext} accessibilityLabel="Next">
        <Text style={styles.nextButtonText}>Next</Text>
      </TouchableOpacity>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff' },
  header: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 16, paddingTop: 12, paddingBottom: 10 },
  backButton: { paddingRight: 8, paddingVertical: 6 },
  headerTitle: { fontSize: 16, fontWeight: '600', marginLeft: 6 },
  content: { flex: 1, paddingHorizontal: 20, paddingTop: 24 },
  title: { textAlign: 'center', marginBottom: 20, fontSize: 14, color: '#333' },
  inputWrapper: { position: 'relative', marginBottom: 12 },
  input: {
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 6,
    paddingHorizontal: 12,
    paddingVertical: 12,
    fontSize: 14,
    backgroundColor: '#fff',
  },
  eye: { position: 'absolute', right: 12, top: 12 },
  rulesContainer: { marginTop: 12 },
  ruleText: { fontSize: 12, color: '#777', marginBottom: 6 },
  nextButton: { backgroundColor: '#3DC1C6', height: 52, alignItems: 'center', justifyContent: 'center' },
  nextButtonText: { color: '#000', fontSize: 16, fontWeight: '600' },
});
