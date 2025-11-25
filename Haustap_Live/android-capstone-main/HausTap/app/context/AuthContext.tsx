import * as SecureStore from 'expo-secure-store';
import React, { createContext, useContext, useEffect, useState } from 'react';
import { setAuthToken } from '../../services/api-client';
import { login as apiLogin, register as apiRegister } from '../../services/auth-api';

type Role = 'client' | 'provider';
type User = any | null;

type AuthContextValue = {
	user: User;
	loading: boolean;
	login: (email: string, password: string) => Promise<User>;
	signup: (name: string, email: string, phone: string, password: string, confirmPassword: string) => Promise<any>;
	logout: () => Promise<void>;
	setPartnerStatus: (value: boolean) => Promise<void>;
	setApplicationPending: (value: boolean) => Promise<void>;
	setMode: (mode: Role) => Promise<void>;
	mode: Role;
};

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

const STORAGE_KEY = 'HT_auth';

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
	const [user, setUser] = useState<User>(null);
	const [token, setToken] = useState<string | null>(null);
	const [loading, setLoading] = useState(true);
	const [mode, setModeState] = useState<Role>('client');

	useEffect(() => {
		(async () => {
			try {
				const raw = await SecureStore.getItemAsync(STORAGE_KEY);
				if (raw) {
					const parsed = JSON.parse(raw);
					setUser(parsed.user || null);
					setToken(parsed.token || null);
					setModeState(parsed.mode || 'client');
					if (parsed.token) setAuthToken(parsed.token);
				}
			} catch (e) {
				// ignore
			} finally {
				setLoading(false);
			}
		})();
	}, []);

	const persist = async (u: User | null, t: string | null, m: Role) => {
		if (u || t) {
			try {
				await SecureStore.setItemAsync(STORAGE_KEY, JSON.stringify({ user: u, token: t, mode: m }));
			} catch (e) {
				// ignore
			}
		} else {
			try {
				await SecureStore.deleteItemAsync(STORAGE_KEY);
			} catch (e) {
				// ignore
			}
		}
	};

	const login = async (email: string, password: string) => {
		const res = await apiLogin(email, password);
		// response shape may vary; try common keys
		const tokenFromRes = res?.token || res?.data?.token || res?.access_token || res?.data?.access_token || res?.meta?.token;
		const userFromRes = res?.user || res?.data?.user || res?.data || res;

		setUser(userFromRes || null);
		setToken(tokenFromRes || null);
		if (tokenFromRes) setAuthToken(tokenFromRes);
		await persist(userFromRes || null, tokenFromRes || null, (userFromRes && userFromRes.role) || 'client');
		setModeState((userFromRes && userFromRes.role) || 'client');
		return userFromRes;
	};

	const signup = async (name: string, email: string, phone: string, password: string, confirmPassword: string) => {
		const res = await apiRegister(name, email, phone, password, confirmPassword);
		return res;
	};

	const logout = async () => {
		setUser(null);
		setToken(null);
		setModeState('client');
		setAuthToken(null);
		await persist(null, null, 'client');
	};

	const setPartnerStatus = async (value: boolean) => {
		if (!user) return;
		const updated = { ...user, isHausTapPartner: value };
		setUser(updated);
		await persist(updated, token, mode);
	};

	const setApplicationPending = async (value: boolean) => {
		if (!user) return;
		const updated = { ...user, isApplicationPending: value };
		setUser(updated);
		await persist(updated, token, mode);
	};

	const setMode = async (m: Role) => {
		setModeState(m);
		await persist(user, token, m);
	};

	return (
		<AuthContext.Provider value={{ user, loading, login, signup, logout, setPartnerStatus, setApplicationPending, setMode, mode }}>
			{children}
		</AuthContext.Provider>
	);
};

export const useAuth = () => {
	const ctx = useContext(AuthContext);
	if (!ctx) throw new Error('useAuth must be used within AuthProvider');
	return ctx;
};

export default AuthContext;
