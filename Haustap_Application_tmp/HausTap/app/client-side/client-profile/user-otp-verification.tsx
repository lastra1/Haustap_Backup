import { Ionicons } from "@expo/vector-icons";
import { useRouter } from 'expo-router';
import React, { useRef, useState } from "react";
import { Platform, StyleSheet, Text, TextInput, TouchableOpacity, View } from "react-native";

export default function UserOtpVerification() {
	const router = useRouter();
	const [code, setCode] = useState<string[]>(['', '', '', '', '', '']);
	const inputs = useRef<Array<TextInput | null>>([]);
	const [timer, setTimer] = useState<number>(60); // seconds remaining

	// Simple countdown (not robust) — you can improve with useEffect
	React.useEffect(() => {
		let t: any = null;
		if (timer > 0) {
			t = setTimeout(() => setTimer(timer - 1), 1000);
		}
		return () => clearTimeout(t);
	}, [timer]);

	const onChange = (text: string, idx: number) => {
		if (!text) {
			const next = [...code];
			next[idx] = '';
			setCode(next);
			return;
		}
		const ch = text.slice(-1);
		const next = [...code];
		next[idx] = ch;
		setCode(next);
		// focus next
		if (idx < inputs.current.length - 1 && ch) {
			inputs.current[idx + 1]?.focus();
		}
		// if last and full code
		if (idx === code.length - 1) {
			const joined = next.join('');
			// TODO: auto-submit or validate
			console.log('code entered', joined);
		}
	};

	const onKeyPress = (e: any, idx: number) => {
		if (e.nativeEvent.key === 'Backspace' && !code[idx] && idx > 0) {
			inputs.current[idx - 1]?.focus();
		}
	};

	return (
		<View style={styles.container}>
			{/* Header */}
			<View style={styles.headerRow}>
				<TouchableOpacity onPress={() => router.back()} style={styles.backBtn}>
					<Ionicons name="arrow-back" size={22} color="#000" />
				</TouchableOpacity>
				<Text style={styles.headerTitle}>Change Password</Text>
			</View>

			<View style={{ height: 24 }} />

			<Text style={styles.title}>Enter Verification Code</Text>
			<Text style={styles.subtitle}>Please enter the verification code sent to your email address</Text>

			<View style={styles.otpRow}>
				{code.map((c, i) => (
					<TextInput
						key={i}
						ref={(ref) => { inputs.current[i] = ref; }}
						style={styles.otpInput}
						keyboardType="number-pad"
						maxLength={1}
						value={c}
						onChangeText={(t) => onChange(t, i)}
						onKeyPress={(e) => onKeyPress(e, i)}
						returnKeyType={i === code.length - 1 ? 'done' : 'next'}
					/>
				))}
			</View>

			<View style={{ alignItems: 'center', marginTop: 8 }}>
				<Text style={{ color: '#666' }}>Didn't receive the code? <Text style={{ color: '#3DC1C6' }}>Resend Code</Text></Text>
				<Text style={{ color: '#999', marginTop: 8 }}>This code will expire in 0:{String(timer).padStart(2, '0')} minute</Text>
			</View>

			<TouchableOpacity style={styles.nextBtn} onPress={() => router.push('/client-side/client-profile/user-new-password' as any)}>
				<Text style={styles.nextText}>Next</Text>
			</TouchableOpacity>
		</View>
	);
}

const styles = StyleSheet.create({
	container: { flex: 1, paddingHorizontal: 16, paddingTop: Platform.OS === 'ios' ? 44 : 20, backgroundColor: '#fff' },
	headerRow: { flexDirection: 'row', alignItems: 'center' },
	backBtn: { padding: 6, marginRight: 8 },
	headerTitle: { fontSize: 16, fontWeight: '700' },
	title: { fontSize: 16, fontWeight: '700', textAlign: 'center', marginBottom: 6 },
	subtitle: { textAlign: 'center', color: '#666', marginBottom: 18, paddingHorizontal: 10 },
	otpRow: { flexDirection: 'row', justifyContent: 'space-between', paddingHorizontal: 24, marginTop: 6 },
	otpInput: { width: 40, height: 44, borderWidth: 1, borderColor: '#ccc', borderRadius: 6, textAlign: 'center', fontSize: 18, backgroundColor: '#fff' },
	nextBtn: { backgroundColor: '#3DC1C6', paddingVertical: 12, borderRadius: 8, marginTop: 26, alignItems: 'center', marginHorizontal: 40 },
	nextText: { color: '#fff', fontWeight: '700' }
});
