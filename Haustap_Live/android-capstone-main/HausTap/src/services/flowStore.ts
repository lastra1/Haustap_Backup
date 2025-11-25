// Simple in-memory store for passing small pieces of data between screens during flow
// NOTE: This is volatile (resets on reload). For production, use secure storage or a backend.
class FlowStore {
  private _email: string = '';

  setEmail(email: string) {
    this._email = email;
    console.log('[flowStore] setEmail:', email);
  }

  getEmail() {
    return this._email;
  }

  clear() {
    this._email = '';
  }
}

export const flowStore = new FlowStore();
