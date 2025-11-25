import { MaterialIcons } from '@expo/vector-icons';
import { useRouter, useSegments } from 'expo-router';
import React, { useEffect, useRef, useState } from 'react';
import {
  NativeSyntheticEvent,
  StyleSheet,
  Text,
  TextInput,
  TextInputKeyPressEventData,
  TouchableOpacity,
  View,
} from 'react-native';

export default function App() {
  const [code, setCode] = useState<string[]>(['', '', '', '', '', '']);
  const router = useRouter();
  const segments = useSegments();

  useEffect(() => {
    console.log('[email-verification] mounted, segments:', segments);
    // log router object shallowly
    try {
      // router can be circular; stringify selectively
      console.log('[email-verification] router keys:', Object.keys(Object.getPrototypeOf(router)).slice(0, 20));
    } catch (err) {
      console.log('[email-verification] router (raw):', router);
    }
  }, [router, segments]);

  // Keep refs to the TextInput elements; typed so TS knows what methods exist (e.g. focus)
  const inputRefs = useRef<Array<React.ElementRef<typeof TextInput> | null>>([]);

  const handleChangeText = (text: string, index: number) => {
    const newCode = [...code];
    newCode[index] = text;
    setCode(newCode);

    // Move to next input when text is entered
    if (text && index < 5 && inputRefs.current[index + 1]) {
      inputRefs.current[index + 1]?.focus();
    }
  };

  const handleKeyPress = (
    e: NativeSyntheticEvent<TextInputKeyPressEventData>,
    index: number
  ) => {
    const { key } = e.nativeEvent;

    // If Backspace pressed on empty field, go back
    if (key === 'Backspace' && code[index] === '' && index > 0) {
      const prevRef = inputRefs.current[index - 1];
      if (prevRef) prevRef.focus();
    }
  };

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <MaterialIcons name="arrow-back" size={24} color="black" />
        <Text style={styles.headerTitle}>Change Password</Text>
      </View>

      <View style={styles.content}>
        <Text style={styles.mainTitle}>Enter Verification Code</Text>
        <Text style={styles.description}>
          Please enter the verification code {'\n'}sent to your email address
        </Text>

        {/* OTP Input Fields */}
        <View style={styles.otpContainer}>
          {code.map((digit, index) => (
            <TextInput
              key={index}
              ref={(ref) => {
                inputRefs.current[index] = ref;
              }}
              style={styles.otpInput}
              keyboardType="number-pad"
              maxLength={1}
              value={digit}
              onChangeText={(text) => handleChangeText(text, index)}
              onKeyPress={(e) => handleKeyPress(e, index)}
              caretHidden={true}
              returnKeyType="done"
            />
          ))}
        </View>

        <View style={styles.resendCodeContainer}>
          <Text style={styles.resendCodeText}>
            Didn't receive the code?{' '}
            <Text style={styles.resendCodeLink}>Resend Code</Text>
          </Text>
          <Text style={styles.timerText}>This code will expire in 01:00 minute</Text>
        </View>

        {/* Next Button */}
        <TouchableOpacity
          style={styles.nextButton}
          onPress={() => {
            console.log('Next pressed — navigating to:', '/service-provider/new-password');
            router.replace('/service-provider/new-password');
          }}
        >
          <Text style={styles.nextButtonText}>Next</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingTop: 50,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 15,
    paddingBottom: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    marginLeft: 10,
  },
  content: {
    flex: 1,
    paddingHorizontal: 15,
    paddingTop: 70,
    alignItems: 'center',
  },
  mainTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  description: {
    fontSize: 15,
    color: 'gray',
    textAlign: 'center',
    marginBottom: 40,
    lineHeight: 22,
  },
  otpContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    width: '80%',
    marginBottom: 30,
  },
  otpInput: {
    width: 40,
    height: 40,
    borderBottomWidth: 2,
    borderColor: 'gray',
    textAlign: 'center',
    fontSize: 20,
    fontWeight: 'bold',
  },
  resendCodeContainer: {
    alignItems: 'center',
    marginBottom: 50,
  },
  resendCodeText: {
    fontSize: 14,
    color: 'gray',
    marginBottom: 5,
  },
  resendCodeLink: {
    color: '#3dc1c6',
    fontWeight: 'bold',
  },
  timerText: {
    fontSize: 13,
    color: 'gray',
    marginBottom: 35,
  },
  nextButton: {
    backgroundColor: '#3dc1c6',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    width: '100%',
    maxWidth: 300,
  },
  nextButtonText: {
    color: 'black',
    fontSize: 18,
    fontWeight: 'bold',
  },
});
