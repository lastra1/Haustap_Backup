import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';
import React, { useCallback, useEffect, useState } from 'react';
import { Modal, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

type Notification = {
  id: string;
  title: string;
  message: string;
  type: 'promotion' | 'update' | 'booking';
  timestamp: string;
  isRead: boolean;
};

interface NotificationPopupProps {
  visible: boolean;
  onClose: () => void;
}

export default function NotificationPopup({ visible, onClose }: NotificationPopupProps) {
  const router = useRouter();
  const [notifications, setNotifications] = useState<Notification[]>([]);

  const loadNotifications = useCallback(async () => {
    try {
      const stored = await AsyncStorage.getItem('HT_notifications');
      if (stored) {
        const parsed = JSON.parse(stored);
        setNotifications(parsed.slice(0, 5)); // Show only 5 most recent notifications
      } else {
        // Demo notifications
        const demo: Notification[] = [
          {
            id: '1',
            title: 'Haustap Update',
            message: 'New features available! Check it out.',
            type: 'update',
            timestamp: new Date().toISOString(),
            isRead: false,
          },
          {
            id: '2',
            title: 'Promotions',
            message: 'Get P100 Off for First-Time Bookings',
            type: 'promotion',
            timestamp: new Date().toISOString(),
            isRead: false,
          },
        ];
        setNotifications(demo);
        await AsyncStorage.setItem('HT_notifications', JSON.stringify(demo));
      }
    } catch (e) {
      console.warn('Failed to load notifications', e);
    }
  }, []);

  useEffect(() => {
    if (visible) {
      (async () => {
        await loadNotifications();
        // mark all loaded notifications as read when popup opens
        try {
          const raw = await AsyncStorage.getItem('HT_notifications');
          if (raw) {
            const parsed: Notification[] = JSON.parse(raw);
            const updated = parsed.map(n => ({ ...n, isRead: true }));
            await AsyncStorage.setItem('HT_notifications', JSON.stringify(updated));
            // update local slice as well
            setNotifications(updated.slice(0, 5));
          }
        } catch (e) {
          console.warn('Failed to mark notifications as read on open', e);
        }
      })();
    }
  }, [visible, loadNotifications]);

  const handleViewAll = () => {
    onClose();
    router.push('/client-side/notification/user-notification');
  };

  const getNotificationIcon = (type: Notification['type']) => {
    switch (type) {
      case 'promotion':
        return <Ionicons name="pricetag" size={24} color="#FF6B6B" />;
      case 'update':
        return <Ionicons name="sync" size={24} color="#4ECDC4" />;
      case 'booking':
        return <Ionicons name="calendar" size={24} color="#3DC1C6" />;
    }
  };

  return (
    <Modal
      visible={visible}
      transparent
      animationType="fade"
      onRequestClose={onClose}
    >
      <TouchableOpacity 
        style={styles.overlay} 
        activeOpacity={1} 
        onPress={onClose}
      >
        <View style={styles.popup}>
          <TouchableOpacity 
            activeOpacity={1}
            onPress={e => e.stopPropagation()}
          >
            <View style={styles.header}>
              <Text style={styles.headerTitle}>Notifications</Text>
              <TouchableOpacity onPress={onClose}>
                <Ionicons name="close" size={24} color="#666" />
              </TouchableOpacity>
            </View>

            <ScrollView style={styles.notificationList}>
              {notifications.map(notif => (
                <TouchableOpacity
                  key={notif.id}
                  style={styles.notificationItem}
                  activeOpacity={0.8}
                  onPress={async () => {
                    try {
                      // mark the notification as read in storage
                      const raw = await AsyncStorage.getItem('HT_notifications');
                      if (raw) {
                        const parsed: Notification[] = JSON.parse(raw);
                        const updated = parsed.map(n => n.id === notif.id ? { ...n, isRead: true } : n);
                        await AsyncStorage.setItem('HT_notifications', JSON.stringify(updated));
                      }
                    } catch (e) {
                      console.warn('Failed to mark notification as read', e);
                    }
                    onClose();
                    // navigate to notification detail page directly
                    router.push({ pathname: '/client-side/notification/user-notification-detail', params: { notificationId: notif.id } } as any);
                  }}
                >
                  <View style={styles.notifIconContainer}>
                    {getNotificationIcon(notif.type)}
                  </View>
                  <View style={styles.notifContent}>
                    <Text style={styles.notifTitle}>{notif.title}</Text>
                    <Text style={styles.notifMessage}>{notif.message}</Text>
                    <Text style={styles.notifTime}>
                      {new Date(notif.timestamp).toLocaleTimeString([], { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                      })}
                    </Text>
                  </View>
                </TouchableOpacity>
              ))}
            </ScrollView>

            <TouchableOpacity 
              style={styles.viewAllButton} 
              onPress={handleViewAll}
            >
              <Text style={styles.viewAllText}>View All Notifications</Text>
              <Ionicons name="chevron-forward" size={20} color="#3DC1C6" />
            </TouchableOpacity>
          </TouchableOpacity>
        </View>
      </TouchableOpacity>
    </Modal>
  );
}

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'flex-start',
  },
  popup: {
    backgroundColor: '#fff',
    marginTop: 60,
    marginHorizontal: 16,
    borderRadius: 12,
    maxHeight: '80%',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: '700',
  },
  notificationList: {
    maxHeight: 400,
  },
  notificationItem: {
    flexDirection: 'row',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
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
  viewAllButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 16,
    borderTopWidth: 1,
    borderTopColor: '#eee',
  },
  viewAllText: {
    color: '#3DC1C6',
    fontSize: 16,
    fontWeight: '600',
    marginRight: 4,
  },
});
