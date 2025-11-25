import AsyncStorage from '@react-native-async-storage/async-storage';

type Address = {
  id: string;
  houseNumber?: string;
  street?: string;
  barangayName?: string;
  municipal?: string;
  province?: string;
};

const STORAGE_KEY = 'HT_addresses';

class AddressStore {
  private addresses: Address[] = [];

  constructor() {
    this.load();
  }

  async load() {
    try {
      const raw = await AsyncStorage.getItem(STORAGE_KEY);
      if (raw) this.addresses = JSON.parse(raw);
    } catch (e) {
      // ignore
    }
  }

  async persist() {
    try {
      await AsyncStorage.setItem(STORAGE_KEY, JSON.stringify(this.addresses));
    } catch (e) {
      // ignore
    }
  }

  getAddresses() {
    return this.addresses.slice();
  }

  async addAddress(a: Address) {
    this.addresses.push(a);
    await this.persist();
  }

  async updateAddress(id: string, patch: Partial<Address>) {
    const idx = this.addresses.findIndex((r) => r.id === id);
    if (idx !== -1) {
      this.addresses[idx] = { ...this.addresses[idx], ...patch };
      await this.persist();
    }
  }

  async deleteAddress(id: string) {
    this.addresses = this.addresses.filter((r) => r.id !== id);
    await this.persist();
  }
}

export const addressStore = new AddressStore();

export default addressStore;
