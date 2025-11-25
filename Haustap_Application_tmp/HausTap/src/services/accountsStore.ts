import AsyncStorage from '@react-native-async-storage/async-storage';

type Account = {
  email: string;
  password: string;
  isHausTapPartner?: boolean;
  // true when the user has submitted a partner application and is awaiting review
  isApplicationPending?: boolean;
};

const STORAGE_KEY = 'HT_accounts';

class AccountsStore {
  private accounts: Account[] = [];

  constructor() {
    this.load();
  }

  private async load() {
    try {
      const raw = await AsyncStorage.getItem(STORAGE_KEY);
      if (raw) this.accounts = JSON.parse(raw);
    } catch (e) {
      // ignore
    }
  }

  private async persist() {
    try {
      await AsyncStorage.setItem(STORAGE_KEY, JSON.stringify(this.accounts));
    } catch (e) {
      // ignore
    }
  }

  getAccountByEmail(email: string) {
    return this.accounts.find((a) => a.email === email) || null;
  }

  async addAccount(account: Account) {
    // replace if exists
    const idx = this.accounts.findIndex((a) => a.email === account.email);
    if (idx !== -1) this.accounts[idx] = account;
    else this.accounts.unshift(account);
    await this.persist();
  }

  async updateAccount(email: string, patch: Partial<Account>) {
    const idx = this.accounts.findIndex((a) => a.email === email);
    if (idx !== -1) {
      this.accounts[idx] = { ...this.accounts[idx], ...patch };
      await this.persist();
    }
  }
}

export const accountsStore = new AccountsStore();
export default accountsStore;
