import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useEffect, useState } from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

type Notification = {
  id: string;
  title: string;
  message: string;
  type: 'promotion' | 'update' | 'booking' | 'new_type_1' | 'new_type_2'; // Added new types here
  timestamp: string;
  isRead: boolean;
};

export default function NotificationDetail() {
  const params = useLocalSearchParams() as { notificationId?: string };
  const router = useRouter();
  const [notif, setNotif] = useState<Notification | null>(null);

  useEffect(() => {
    const load = async () => {
      try {
        const raw = await AsyncStorage.getItem('HT_notifications');
        if (!raw) return;
        const parsed: Notification[] = JSON.parse(raw);
        const found = parsed.find(n => n.id === params.notificationId) ?? null;
        if (found) {
          // mark as read
          const updated = parsed.map(n => n.id === found.id ? { ...n, isRead: true } : n);
          await AsyncStorage.setItem('HT_notifications', JSON.stringify(updated));
          setNotif({ ...found, isRead: true });
        }
      } catch (e) {
        console.warn('Failed to load notification detail', e);
      }
    };
    load();
  }, [params.notificationId]);

  if (!notif) {
    return (
      <View style={styles.page}>
        <View style={styles.header}>
          <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
            <Ionicons name="chevron-back" size={24} color="#333" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Notification</Text>
        </View>
        <View style={styles.emptyState}>
          <Text style={styles.emptyText}>Notification not found</Text>
        </View>
      </View>
    );
  }

  return (
    <View style={styles.page}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
          <Ionicons name="chevron-back" size={24} color="#333" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Notification</Text>
      </View>

      <ScrollView style={styles.content} contentContainerStyle={{ padding: 16 }}>
        <Text style={styles.title}>{notif.title}</Text>
        <Text style={styles.time}>{new Date(notif.timestamp).toLocaleString()}</Text>
        <Text style={styles.message}>{notif.message}</Text>
        {notif.type === 'new_type_1' && (
          <Text style={styles.additionalInfo}>This is some additional information for new type 1 notifications.</Text>
        )}
        {notif.type === 'new_type_2' && (
          <Text style={styles.additionalInfo}>This is some additional information for new type 2 notifications.</Text>
        )}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  page: { flex: 1, backgroundColor: '#fff' },
  header: { flexDirection: 'row', alignItems: 'center', padding: 16, borderBottomWidth: 1, borderBottomColor: '#eee' },
  backButton: { padding: 4 },
  headerTitle: { fontSize: 18, fontWeight: '700', marginLeft: 8 },
  content: { flex: 1 },
  title: { fontSize: 20, fontWeight: '700', marginBottom: 8 },
  time: { fontSize: 12, color: '#999', marginBottom: 16 },
  message: { fontSize: 16, color: '#333' },
  additionalInfo: { fontSize: 14, color: '#007bff', marginTop: 12 },
  emptyState: { flex: 1, justifyContent: 'center', alignItems: 'center' },
  emptyText: { color: '#666' },
});
