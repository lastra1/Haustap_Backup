import * as SecureStore from 'expo-secure-store';

// -----------------------------
// API Base URL
// -----------------------------
export const DEFAULT_API_URL = 'http://localhost:8001';

export const setBaseUrl = async (url) => {
  global.BASE_URL = url;
  try {
    await SecureStore.setItemAsync('BASE_URL', url);
  } catch (e) {
    console.warn('Failed to save BASE_URL to SecureStore', e);
  }
};

export const loadBaseUrl = async () => {
  try {
    const savedUrl = await SecureStore.getItemAsync('BASE_URL');
    if (savedUrl) {
      global.BASE_URL = savedUrl;
    } else {
      global.BASE_URL = DEFAULT_API_URL;
    }
  } catch (e) {
    console.warn('Failed to load BASE_URL from SecureStore', e);
    global.BASE_URL = DEFAULT_API_URL;
  }
};


export const getBaseUrl = () => {
  return global.BASE_URL || DEFAULT_API_URL;
};


// -----------------------------
// Auth: Role
// -----------------------------
export const getUserRole = async () => {
  try {
    if (typeof window !== 'undefined' && window.localStorage) {
      return window.localStorage.getItem('USER_ROLE') || null;
    }
    return await SecureStore.getItemAsync('USER_ROLE') || null;
  } catch (e) {
    return null;
  }
}

export const setUserRole = async (role) => {
  try {
    if (typeof window !== 'undefined' && window.localStorage) {
      window.localStorage.setItem('USER_ROLE', role);
      return;
    }
    await SecureStore.setItemAsync('USER_ROLE', role);
  } catch (e) {
    console.warn('Failed to set USER_ROLE', e);
  }
}