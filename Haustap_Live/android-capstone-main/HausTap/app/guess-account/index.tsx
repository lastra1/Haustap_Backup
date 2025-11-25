import { useRouter } from 'expo-router';
import React from 'react';
import { Image, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

export default function GuestAccount() {
  const router = useRouter();

  return (
    <ScrollView contentContainerStyle={styles.container} showsVerticalScrollIndicator={false}>
      <Image source={require('../../assets/images/logo.png')} style={styles.logo} />
      <Text style={styles.title}>Continue as Guest</Text>
      <Text style={styles.subtitle}>
        You can browse services and add items to your booking. To complete a booking or access Bookings, Chat, and My Account, please sign up or log in.
      </Text>

      <TouchableOpacity style={styles.primary} onPress={() => router.push('/signup') }>
        <Text style={styles.primaryText}>Sign Up / Log In</Text>
      </TouchableOpacity>

      <TouchableOpacity style={styles.secondary} onPress={() => router.replace('/client-side') }>
        <Text style={styles.secondaryText}>Continue Browsing</Text>
      </TouchableOpacity>

      <View style={styles.hintBox}>
        <Text style={styles.hintTitle}>Guest browsing</Text>
        <Text style={styles.hintText}>As a guest you can browse categories, select services, and view prices. When you try to confirm a booking we'll ask you to sign up.</Text>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    padding: 24,
    paddingTop: 80,
    alignItems: 'center',
  },
  logo: {
    width: 120,
    height: 120,
    marginBottom: 20,
    resizeMode: 'contain',
  },
  title: {
    fontSize: 22,
    fontWeight: '700',
    marginBottom: 8,
  },
  subtitle: {
    textAlign: 'center',
    color: '#666',
    marginBottom: 20,
    lineHeight: 20,
  },
  primary: {
    backgroundColor: '#00ADB5',
    paddingVertical: 14,
    paddingHorizontal: 28,
    borderRadius: 8,
    marginBottom: 12,
    width: '100%',
    alignItems: 'center',
  },
  primaryText: {
    color: '#fff',
    fontWeight: '700',
    fontSize: 16,
  },
  secondary: {
    borderWidth: 1,
    borderColor: '#00ADB5',
    paddingVertical: 12,
    paddingHorizontal: 28,
    borderRadius: 8,
    width: '100%',
    alignItems: 'center',
    marginBottom: 24,
  },
  secondaryText: {
    color: '#00ADB5',
    fontWeight: '700',
  },
  hintBox: {
    backgroundColor: '#F5F5F5',
    padding: 16,
    borderRadius: 8,
    width: '100%'
  },
  hintTitle: { fontWeight: '700', marginBottom: 6 },
  hintText: { color: '#666', lineHeight: 20 }
});
