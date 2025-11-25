import AsyncStorage from '@react-native-async-storage/async-storage';
import apiClient from '../../../services/api-client';
import { router, useLocalSearchParams } from 'expo-router';
import { useAuth } from '../../context/AuthContext';
import React from 'react';
import { Alert, Image, StyleSheet, View } from 'react-native';
import { TouchableOpacity } from 'react-native-gesture-handler';
import { ThemedText } from '../../../components/themed-text';
import { ThemedView } from '../../../components/themed-view';
import { Colors } from '../../../constants/theme';
import { useBookingSelection } from '../../context/BookingSelectionContext';
import { applianceRepairCategories } from '../data/applianceRepair';
import { acCleaningCategories, acDeepCleaningCategories, homeCleaningCategories } from '../data/cleaning';
import { electricalCategories } from '../data/electrical';
import { gardeningCategories } from '../data/gardening';
import { hairCategories } from '../data/hair';
import { handymanCategories } from '../data/handyman';
import { makeupCategories } from '../data/makeup';
import { nailCategories } from '../data/nails';
import { pestControlCategories } from '../data/pestControl';
import { pestControlOutdoorCategories } from '../data/pestControlOutdoor';
import { plumbingCategories } from '../data/plumbing';
import { wellnessCategories } from '../data/wellness';

export default function ConfirmBookingScreen() {
  const params = useLocalSearchParams();
  const { clearAll } = useBookingSelection();
  const { user } = useAuth();

  const handleBackToHome = async () => {
    // Save the complete booking details from the flow
    const id = `bkg-${Date.now()}`;
    // build price map to compute subtotal when multiple services selected
    const lists = [
      ...nailCategories,
      ...hairCategories,
      ...makeupCategories,
      ...handymanCategories,
      ...plumbingCategories,
      ...pestControlCategories,
      ...pestControlOutdoorCategories,
      ...gardeningCategories,
      ...wellnessCategories,
      ...applianceRepairCategories,
      ...electricalCategories,
      ...homeCleaningCategories,
      ...acCleaningCategories,
      ...acDeepCleaningCategories,
    ];
    const priceMap: Record<string, string> = {};
    lists.forEach((it: any) => {
      if (it && it.title && it.price) priceMap[it.title] = it.price;
    });

    const parsePriceNumber = (priceStr?: string) => {
      if (!priceStr) return NaN;
      const cleaned = String(priceStr).replace(/[^0-9.]/g, "");
      const v = parseFloat(cleaned);
      return Number.isFinite(v) ? v : NaN;
    };

    const parsedSelected = (() => {
      const raw = params.selectedItems as string | undefined;
      if (!raw) return [] as string[];
      try {
        const p = JSON.parse(String(raw));
        if (Array.isArray(p)) return p.map(String);
        if (typeof p === 'string') return [p];
        return [] as string[];
      } catch (e) {
        return [String(raw)];
      }
    })();

    // compute subtotal
    const subtotal = (() => {
      if (parsedSelected.length) {
        let sum = 0;
        let any = false;
        parsedSelected.forEach((t) => {
          const p = priceMap[t];
          const n = parsePriceNumber(p);
          if (!Number.isNaN(n)) {
            sum += n;
            any = true;
          }
        });
        if (any) return sum;
      }
      const cp = parsePriceNumber(params.categoryPrice as string | undefined);
      if (!Number.isNaN(cp)) return cp;
      return 0;
    })();

    const transpoFee = 100;
    const voucherValue = parsePriceNumber(params.voucher as string | undefined) || (params.voucherValue ? Number(params.voucherValue) : 0);
    const total = Math.max(0, subtotal + transpoFee - voucherValue);

    const booking = {
      id,
      mainCategory: (params.mainCategory as string) || (params.category as string) || 'Service',
      subCategory: (params.subCategory as string) || (params.service as string) || 'Service',
      serviceTitle: (params.categoryTitle as string) || (params.service as string) || '',
      providerId: params.providerId as string,
      providerName: (params.providerName as string) || '',
      date: (params.date as string) || new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
      time: (params.time as string) || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
      address: (params.address as string) || (params.location as string) || '—',
      subtotal,
      transpoFee,
      voucherValue,
      total,
      desc: (params.categoryDesc as string) || '',
      notes: (params.notes as string) || '',
      voucherCode: (params.voucherCode as string) || '',
      status: 'pending'
    };
    // attach selected items (if any)
    if (Array.isArray(parsedSelected) && parsedSelected.length) {
      (booking as any).selectedItems = parsedSelected;
    }

    try {
      const raw = await AsyncStorage.getItem('HT_bookings');
      const existing = raw ? JSON.parse(raw) : [];
      const updated = Array.isArray(existing) ? [booking, ...existing] : [booking];
      await AsyncStorage.setItem('HT_bookings', JSON.stringify(updated));
    } catch (err) {
      console.warn('Failed to save booking', err);
      Alert.alert('Error', 'Could not save booking locally.');
    }

    try {
      const sched = `${booking.date} ${booking.time}`;
      const resp = await apiClient.post('/api/bookings', {
        client_id: (user && (user.id || (user.user && user.user.id) || user.user_id)) || null,
        provider_id: null,
        service_id: null,
        service_name: booking.serviceTitle,
        status: booking.status,
        scheduled_at: sched,
        address: booking.address,
        notes: booking.notes,
      });
      if (resp && resp.data && resp.data.id) {
        // Optionally update local booking id with DB id
        try {
          const rawLocal = await AsyncStorage.getItem('HT_bookings');
          const arr = rawLocal ? JSON.parse(rawLocal) : [];
          if (Array.isArray(arr)) {
            arr[0] = { ...(arr[0] || booking), id: String(resp.data.id) };
            await AsyncStorage.setItem('HT_bookings', JSON.stringify(arr));
          }
        } catch {}
      }
    } catch (err) {
      console.warn('Failed to send booking to API', err);
    }

    // Clear selections and navigate back to the client-side home page
    try {
      clearAll();
    } catch (e) {
      // ignore if provider missing
    }
    router.replace('/client-side');
  };

  return (
    <ThemedView style={styles.container}>
      <View style={styles.content}>
        {/* Logo header */}
        <Image source={require('../../../assets/images/logo.png')} style={styles.logo} resizeMode="contain" />

        {/* Success Icon */}
        <View style={styles.iconContainer}>
          <View style={styles.checkCircle}>
            <ThemedText style={styles.checkmark}>✓</ThemedText>
          </View>
        </View>

        {/* Success Message */}
        <ThemedText style={styles.title}>Thank you for booking</ThemedText>

        {/* Back to Home Button */}
        <TouchableOpacity
          style={styles.button}
          onPress={handleBackToHome}
        >
          <ThemedText style={styles.buttonText}>Back to Home</ThemedText>
        </TouchableOpacity>
      </View>
    </ThemedView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.light.background,
  },
  content: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 20,
  },
  iconContainer: {
    marginBottom: 24,
  },
  checkCircle: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: '#4CAF50',
    alignItems: 'center',
    justifyContent: 'center',
  },
  checkmark: {
    color: 'white',
    fontSize: 40,
  },
  logo: {
    width: 260,
    height: 120,
    marginBottom: 24,
    alignSelf: 'center',
  },
  title: {
    fontSize: 30,
    fontWeight: '800',
    marginBottom: 32,
    textAlign: 'center',
    color: Colors.light.text,
  },
  button: {
    backgroundColor: Colors.light.tint,
    paddingHorizontal: 32,
    paddingVertical: 12,
    borderRadius: 8,
    marginTop: 16,
  },
  buttonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: '600',
  },
});
