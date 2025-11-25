import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useFocusEffect, useLocalSearchParams, useRouter } from 'expo-router';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

type Notification = {
  id: string;
  title: string;
  message: string;
  type: 'promotion' | 'update' | 'booking' | 'discount' | 'message';
  timestamp: string;
  isRead: boolean;
};

export default function UserNotification() {
  const router = useRouter();
  const params = useLocalSearchParams() as { notificationId?: string };
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [focusedId, setFocusedId] = useState<string | null>(null);
  const scrollRef = useRef<any>(null);
  const itemOffsets = useRef<Record<string, number>>({});

  useFocusEffect(
    useCallback(() => {
      const loadNotifications = async () => {
        try {
          const stored = await AsyncStorage.getItem('HT_notifications');
          if (stored) {
            const parsed: Notification[] = JSON.parse(stored);
            // if a specific notification was requested, mark it read and focus it
            if (params?.notificationId) {
              const updated = parsed.map(n => n.id === params.notificationId ? { ...n, isRead: true } : n);
              await AsyncStorage.setItem('HT_notifications', JSON.stringify(updated));
              setNotifications(updated);
              setFocusedId(params.notificationId ?? null);
              return;
            }
            setNotifications(parsed);
          }
        } catch (e) {
          console.warn('Failed to load notifications', e);
        }
      };
      loadNotifications();
    }, [params?.notificationId])
  );

  useEffect(() => {
    if (focusedId && scrollRef.current) {
      const y = itemOffsets.current[focusedId];
      if (typeof y === 'number') {
        try {
          scrollRef.current.scrollTo({ y: Math.max(0, y - 16), animated: true });
        } catch (e) {
          // ignore
        }
      }
    }
  }, [focusedId, notifications]);

  const markAllAsRead = async () => {
    try {
      const updatedNotifications = notifications.map(n => ({ ...n, isRead: true }));
      setNotifications(updatedNotifications);
      await AsyncStorage.setItem('HT_notifications', JSON.stringify(updatedNotifications));
    } catch (e) {
      console.warn('Failed to mark notifications as read', e);
    }
  };

  const clearAll = async () => {
    try {
      setNotifications([]);
      await AsyncStorage.setItem('HT_notifications', JSON.stringify([]));
    } catch (e) {
      console.warn('Failed to clear notifications', e);
    }
  };

  const getNotificationIcon = (type: Notification['type']) => {
    switch (type) {
      case 'promotion':
        return <Ionicons name="pricetag" size={24} color="#FF6B6B" />;
      case 'update':
        return <Ionicons name="sync" size={24} color="#4ECDC4" />;
      case 'booking':
        return <Ionicons name="calendar" size={24} color="#3DC1C6" />;
      case 'discount':
        return <Ionicons name="cash" size={24} color="#FFA500" />;
      case 'message':
        return <Ionicons name="chatbubble" size={24} color="#007AFF" />;
    }
  };

  const fetchNotifications = async () => {
    // Fetch notifications from your API or data source
    const response = await fetch('YOUR_API_ENDPOINT');
    const data = await response.json();
    setNotifications(data);
  };

  useEffect(() => {
    fetchNotifications();
  }, []);

  return (
    <View style={styles.page}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
          <Ionicons name="chevron-back" size={24} color="#333" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Notifications</Text>
        <View style={styles.headerActions}>
          <TouchableOpacity onPress={markAllAsRead} style={styles.headerButton}>
            <Text style={styles.headerButtonText}>Mark all as read</Text>
          </TouchableOpacity>
          <TouchableOpacity onPress={clearAll} style={[styles.headerButton, { marginLeft: 8 }]}>
            <Text style={styles.headerButtonText}>Clear all</Text>
          </TouchableOpacity>
        </View>
      </View>

      <ScrollView style={styles.content} ref={scrollRef}>
        {notifications.length === 0 ? (
          <View style={styles.emptyState}>
            <Ionicons name="notifications-off" size={48} color="#ccc" />
            <Text style={styles.emptyText}>No notifications</Text>
          </View>
        ) : (
          notifications.map(notification => (
            <TouchableOpacity
              key={notification.id}
              activeOpacity={0.85}
              onPress={async () => {
                try {
                  const raw = await AsyncStorage.getItem('HT_notifications');
                  if (raw) {
                    const parsed: Notification[] = JSON.parse(raw);
                    const updated = parsed.map(n => n.id === notification.id ? { ...n, isRead: true } : n);
                    await AsyncStorage.setItem('HT_notifications', JSON.stringify(updated));
                    setNotifications(updated);
                  }
                } catch (e) {
                  console.warn('Failed to mark notification read', e);
                }
                // open detail screen
                router.push({ pathname: '/client-side/notification/user-notification-detail', params: { notificationId: notification.id } } as any);
              }}
            >
              <View
                onLayout={(ev) => {
                  const y = ev.nativeEvent.layout.y;
                  itemOffsets.current[notification.id] = y;
                }}
                style={[
                  styles.notificationItem,
                  notification.isRead && styles.notificationRead,
                  notification.id === focusedId && styles.focusedNotification,
                ]}
              >
                <View style={styles.notifIconContainer}>
                  {getNotificationIcon(notification.type)}
                </View>
                <View style={styles.notifContent}>
                  <Text style={styles.notifTitle}>{notification.title}</Text>
                  <Text style={styles.notifMessage}>{notification.message}</Text>
                  <Text style={styles.notifTime}>
                    {new Date(notification.timestamp).toLocaleDateString('en-US', {
                      month: 'short',
                      day: '2-digit',
                      hour: '2-digit',
                      minute: '2-digit',
                    })}
                  </Text>
                </View>
                {!notification.isRead && <View style={styles.unreadDot} />}
              </View>
            </TouchableOpacity>
          ))
        )}
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  page: {
    flex: 1,
    backgroundColor: '#fff',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  backButton: {
    padding: 4,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: '700',
    marginLeft: 8,
    flex: 1,
  },
  headerActions: {
    flexDirection: 'row',
  },
  headerButton: {
    paddingVertical: 6,
    paddingHorizontal: 12,
    backgroundColor: '#f0f0f0',
    borderRadius: 16,
  },
  headerButtonText: {
    fontSize: 12,
    color: '#666',
  },
  content: {
    flex: 1,
  },
  emptyState: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: 32,
  },
  emptyText: {
    fontSize: 16,
    color: '#666',
    marginTop: 8,
  },
  notificationItem: {
    flexDirection: 'row',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
    backgroundColor: '#fff',
  },
  notificationRead: {
    backgroundColor: '#fafafa',
  },
  focusedNotification: {
    borderLeftWidth: 4,
    borderLeftColor: '#3DC1C6',
    backgroundColor: '#f7fffe',
  },
  notifIconContainer: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: '#f8f8f8',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  notifContent: {
    flex: 1,
  },
  notifTitle: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 4,
  },
  notifMessage: {
    fontSize: 14,
    color: '#666',
    marginBottom: 4,
  },
  notifTime: {
    fontSize: 12,
    color: '#999',
  },
  unreadDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#3DC1C6',
    marginLeft: 8,
    alignSelf: 'center',
  },
});
