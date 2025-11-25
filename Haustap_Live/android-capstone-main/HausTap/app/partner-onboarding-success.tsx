import { useRouter } from 'expo-router';
import React from 'react';
import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';

export default function PartnerOnboardingSuccess() {
  const router = useRouter();

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.card}>
        <Text style={styles.header}>Hello Future Haustap Partner!</Text>
        <Text style={styles.body}>
          We'll be sending the schedule of your face-to-face application straight to your email, so kindly keep an eye on it.
        </Text>
        <Text style={styles.subheader}>Please prepare the following requirements:</Text>
        <View style={styles.list}>
          <Text style={styles.listItem}>• Valid ID with 3 specimen signature</Text>
          <Text style={styles.listItem}>• Resume</Text>
          <Text style={styles.listItem}>• Certificate of Training Credential/Tesda/NC II</Text>
          <Text style={styles.listItem}>  *Based on your choosing services</Text>
          <Text style={styles.listItem}>• NBI Clearance</Text>
          <Text style={styles.listItem}>• Police Clearance</Text>
          <Text style={styles.listItem}>• Barangay Clearance</Text>
          <Text style={styles.listItem}>• Business Permit (if applying as a service team)</Text>
        </View>
        <Text style={styles.reminder}>
          Kind Reminder: Having your documents ready will help us process your application faster and welcome you as one of our Haustap's trusted partners. 😊
        </Text>
        <Text style={styles.footer}>We're excited to have you on board and grow with us!</Text>
      </View>
      <TouchableOpacity style={styles.button} onPress={() => router.replace('/client-side/client-profile')}> {/* Route back to My Account */}
        <Text style={styles.buttonText}>Done</Text>
      </TouchableOpacity>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
    justifyContent: 'center',
    alignItems: 'center',
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 24,
    margin: 16,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
    width: '90%',
  },
  header: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 12,
    textAlign: 'center',
    color: '#0099A8',
  },
  body: {
    fontSize: 14,
    marginBottom: 12,
    textAlign: 'center',
    color: '#333',
  },
  subheader: {
    fontSize: 15,
    fontWeight: 'bold',
    marginBottom: 8,
    color: '#0099A8',
  },
  list: {
    marginBottom: 12,
    marginLeft: 8,
  },
  listItem: {
    fontSize: 13,
    color: '#333',
    marginBottom: 2,
  },
  reminder: {
    fontSize: 13,
    color: '#666',
    marginBottom: 8,
    marginTop: 8,
  },
  footer: {
    fontSize: 14,
    color: '#0099A8',
    textAlign: 'center',
    marginTop: 8,
  },
  button: {
    backgroundColor: '#0099A8',
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 32,
    alignSelf: 'center',
    marginTop: 16,
  },
  buttonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
});
