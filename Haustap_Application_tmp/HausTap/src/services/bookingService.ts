import AsyncStorage from '@react-native-async-storage/async-storage';

export type Booking = {
  id: string;
  mainCategory: string;
  subCategory: string;
  serviceTitle?: string;
  providerId: string;
  providerName: string;
  providerRating?: number;
  date: string;
  time: string;
  address: string;
  total: number;
  desc: string;
  notes: string;
  voucherCode: string;
  voucherValue: number;
  status: 'pending' | 'ongoing' | 'completed' | 'cancelled';
  // whether chat between user and provider is allowed for this booking
  chatAllowed?: boolean;
  // identifier for the conversation between user and provider
  conversationId?: string;
  completedAt?: string;
  isRated: boolean;
};

export const completeBooking = async (bookingId: string): Promise<boolean> => {
  try {
    const raw = await AsyncStorage.getItem('HT_bookings');
    if (!raw) return false;

    const bookings = JSON.parse(raw) as Booking[];
    const bookingIndex = bookings.findIndex(b => b.id === bookingId);
    
    if (bookingIndex === -1) return false;

    // Update the booking status and set completion timestamp
    bookings[bookingIndex] = {
      ...bookings[bookingIndex],
      status: 'completed',
      completedAt: new Date().toISOString()
    };

    // Save updated bookings
    await AsyncStorage.setItem('HT_bookings', JSON.stringify(bookings));
    return true;
  } catch (err) {
    console.error('Failed to complete booking:', err);
    return false;
  }
};

export const getBooking = async (bookingId: string): Promise<Booking | null> => {
  try {
    const raw = await AsyncStorage.getItem('HT_bookings');
    if (!raw) return null;

    const bookings = JSON.parse(raw) as Booking[];
    return bookings.find(b => b.id === bookingId) ?? null;
  } catch (err) {
    console.error('Failed to get booking:', err);
    return null;
  }
};

export const updateBooking = async (bookingId: string, updates: Partial<Booking>): Promise<boolean> => {
  try {
    const raw = await AsyncStorage.getItem('HT_bookings');
    if (!raw) return false;

    const bookings = JSON.parse(raw) as Booking[];
    const bookingIndex = bookings.findIndex(b => b.id === bookingId);
    
    if (bookingIndex === -1) return false;

    // If provider accepts the booking (status becomes 'ongoing'), enable chat
    const merged = { ...bookings[bookingIndex], ...updates } as Booking;
    if (updates.status === 'ongoing' || merged.status === 'ongoing') {
      merged.chatAllowed = true;
      // create a conversation id if not present
      if (!merged.conversationId) merged.conversationId = `conv-${bookingId}`;
    }

    // Update the booking with new values
    bookings[bookingIndex] = merged;

    // Save updated bookings
    await AsyncStorage.setItem('HT_bookings', JSON.stringify(bookings));
    return true;
  } catch (err) {
    console.error('Failed to update booking:', err);
    return false;
  }
};