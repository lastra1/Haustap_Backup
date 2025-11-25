/**
 * Booking Store
 * In-memory store for managing bookings (available and pending)
 * In production, this would be replaced with API calls
 */

export interface Booking {
  id: string;
  bookingId?: string; // HT-prefixed ID assigned when accepted
  clientName: string;
  clientEmail: string;
  clientPhone: string;
  service: string;
  dateTime: string;
  address: string;
  price: number;
  notes?: string;
  status: 'available' | 'pending' | 'ongoing' | 'completed' | 'cancelled'; // available = not yet accepted, pending = accepted, ongoing = in progress
  createdAt: string;
  acceptedAt?: string;
  beforePhoto?: string; // Photo URL or data from before service
  afterPhoto?: string;  // Photo URL or data from after service
}

class BookingStore {
  private bookings: Booking[] = [];
  private listeners: Set<(bookings: Booking[]) => void> = new Set();

  constructor() {
    this.initializeDefaultBookings();
  }

  private initializeDefaultBookings() {
    this.bookings = [
      {
        id: '1',
        clientName: 'Maria Santos',
        clientEmail: 'maria@example.com',
        clientPhone: '+63 912 345 6789',
        service: 'Bungalow 80–150 sqm',
        dateTime: 'May 21, 2025 - 8:00 AM',
        address: '1234 Palm Grove, Brgy. San Miguel, City, Province',
        price: 1050,
        notes: '',
        status: 'available',
        createdAt: new Date().toISOString(),
      },
      {
        id: '2',
        bookingId: 'HT-452981',
        clientName: 'Jenn Bornilla',
        clientEmail: 'jenn@example.com',
        clientPhone: '+63 917 234 5678',
        service: 'Bungalow 80–150 sqm',
        dateTime: 'May 21, 2025 - 8:00 AM',
        address: 'B1 L50 Mango st. Phase 1 Saint Joseph Village 10, Barangay Langgam, City of San Pedro, Laguna 4023',
        price: 1050,
        notes: 'No additional notes',
        status: 'cancelled',
        createdAt: new Date().toISOString(),
        acceptedAt: new Date(Date.now() - 86400000).toISOString(),
      },
      {
        id: '3',
        bookingId: 'HT-789456',
        clientName: 'Ana Rodriguez',
        clientEmail: 'ana@example.com',
        clientPhone: '+63 916 789 0123',
        service: 'Bungalow 80–150 sqm',
        dateTime: 'May 25, 2025 - 10:00 AM',
        address: '456 Sunset Avenue, Manila',
        price: 1200,
        notes: 'Prefer morning service',
        status: 'cancelled',
        createdAt: new Date().toISOString(),
        acceptedAt: new Date(Date.now() - 172800000).toISOString(),
      },
    ];
  }

  // Get all available bookings
  getAvailableBookings(): Booking[] {
    return this.bookings.filter(b => b.status === 'available');
  }

  // Get all pending bookings
  getPendingBookings(): Booking[] {
    return this.bookings.filter(b => b.status === 'pending');
  }

  // Get all bookings
  getAllBookings(): Booking[] {
    return [...this.bookings];
  }

  // Accept a booking and move it to pending
  acceptBooking(bookingId: string): Booking | null {
    const booking = this.bookings.find(b => b.id === bookingId);
    if (booking) {
      const htBookingId = 'HT' + Math.floor(100000 + Math.random() * 900000);
      booking.status = 'pending';
      booking.bookingId = htBookingId;
      booking.acceptedAt = new Date().toISOString();
      this.notifyListeners();
      return booking;
    }
    return null;
  }

  // Cancel a booking
  cancelBooking(bookingId: string): Booking | null {
    const booking = this.bookings.find(b => b.id === bookingId);
    if (booking) {
      booking.status = 'cancelled';
      this.notifyListeners();
      return booking;
    }
    return null;
  }

  // Mark a booking as ongoing (service in progress)
  markAsOngoing(bookingId: string): Booking | null {
    const booking = this.bookings.find(b => b.id === bookingId);
    if (booking) {
      console.log(`[BookingStore] Marking booking ${bookingId} as ongoing`);
      booking.status = 'ongoing';
      this.notifyListeners();
      console.log(`[BookingStore] Notified listeners. Total bookings:`, this.bookings.length);
      console.log(`[BookingStore] Ongoing bookings:`, this.bookings.filter(b => b.status === 'ongoing'));
      return booking;
    }
    console.log(`[BookingStore] Booking ${bookingId} not found`);
    return null;
  }

  // Mark a booking as completed with photos
  completeBooking(bookingId: string, beforePhoto?: string, afterPhoto?: string): Booking | null {
    const booking = this.bookings.find(b => b.id === bookingId);
    if (booking) {
      booking.status = 'completed';
      if (beforePhoto) booking.beforePhoto = beforePhoto;
      if (afterPhoto) booking.afterPhoto = afterPhoto;
      this.notifyListeners();
      return booking;
    }
    return null;
  }

  // Subscribe to changes
  subscribe(listener: (bookings: Booking[]) => void): () => void {
    this.listeners.add(listener);
    // Call listener immediately with current bookings
    listener([...this.bookings]);
    return () => this.listeners.delete(listener);
  }

  private notifyListeners() {
    this.listeners.forEach(listener => listener([...this.bookings]));
  }
}

export const bookingStore = new BookingStore();
