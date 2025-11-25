import AsyncStorage from '@react-native-async-storage/async-storage';

const STORAGE_KEY = 'HT_ringtone';

type Listener = () => void;

class SettingsStore {
  private ringtone: string = 'Honk (Default)';
  private listeners: Listener[] = [];

  constructor() {
    // load persisted value
    this.load();
  }

  private async load() {
    try {
      const raw = await AsyncStorage.getItem(STORAGE_KEY);
      if (raw) this.ringtone = raw;
    } catch (e) {
      // ignore
    } finally {
      this.notify();
    }
  }

  private notify() {
    this.listeners.forEach((l) => {
      try {
        l();
      } catch (e) {
        // ignore listener errors
      }
    });
  }

  getRingtone() {
    return this.ringtone;
  }

  async setRingtone(name: string) {
    this.ringtone = name;
    try {
      await AsyncStorage.setItem(STORAGE_KEY, name);
    } catch (e) {
      // ignore
    }
    this.notify();
  }

  subscribe(fn: Listener) {
    this.listeners.push(fn);
    return () => {
      this.listeners = this.listeners.filter((l) => l !== fn);
    };
  }
}

export const settingsStore = new SettingsStore();

export default settingsStore;
