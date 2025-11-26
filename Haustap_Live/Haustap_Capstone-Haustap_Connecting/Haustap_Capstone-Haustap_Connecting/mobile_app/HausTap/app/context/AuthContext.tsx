import * as SecureStore from 'expo-secure-store';
import React, { createContext, useContext, useEffect, useState } from 'react';
import { setAuthToken } from '../../services/api-client';
import { auth } from '../../src/lib/firebase';
import { signInWithEmailAndPassword, createUserWithEmailAndPassword, updateProfile } from 'firebase/auth';

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
        const cred = await signInWithEmailAndPassword(auth, email, password);
        const idToken = await cred.user.getIdToken();
        const u: any = { uid: cred.user.uid, email: cred.user.email, displayName: cred.user.displayName, role: 'client' };
        setUser(u);
        setToken(idToken);
        setAuthToken(idToken);
        await persist(u, idToken, 'client');
        setModeState('client');
        return u;
    };

    const signup = async (name: string, email: string, phone: string, password: string, confirmPassword: string) => {
        const cred = await createUserWithEmailAndPassword(auth, email, password);
        try { await updateProfile(cred.user, { displayName: name }); } catch {}
        const idToken = await cred.user.getIdToken();
        const u: any = { uid: cred.user.uid, email: cred.user.email, displayName: name || cred.user.displayName, role: 'client' };
        setUser(u);
        setToken(idToken);
        setAuthToken(idToken);
        await persist(u, idToken, 'client');
        setModeState('client');
        return u;
    };

    const logout = async () => {
        try { await auth.signOut(); } catch {}
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
