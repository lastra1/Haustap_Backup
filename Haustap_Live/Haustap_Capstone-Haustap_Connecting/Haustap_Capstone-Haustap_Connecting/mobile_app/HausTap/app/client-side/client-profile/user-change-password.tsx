import { Ionicons } from "@expo/vector-icons";
import { useRouter } from 'expo-router';
import React, { useState } from "react";
import { Platform, StyleSheet, Text, TextInput, TouchableOpacity, View } from "react-native";

export default function UserChangePassword() {
	const router = useRouter();
	const [password, setPassword] = useState("");
	const [secure, setSecure] = useState(true);

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

			<Text style={styles.label}>Enter your password</Text>
			<View style={styles.passwordRow}>
				<TextInput
					style={styles.passwordInput}
					value={password}
					onChangeText={setPassword}
					secureTextEntry={secure}
					placeholder="Password"
				/>
				<TouchableOpacity style={styles.eyeBtn} onPress={() => setSecure(!secure)}>
					<Ionicons name={secure ? 'eye-off' : 'eye'} size={18} color="#666" />
				</TouchableOpacity>
			</View>

			<TouchableOpacity style={styles.confirmBtn} onPress={() => router.push('/client-side/client-profile/user-otp-verification' as any)}>
				<Text style={styles.confirmText}>Confirm</Text>
			</TouchableOpacity>
		</View>
	);
}

const styles = StyleSheet.create({
	container: { flex: 1, paddingHorizontal: 16, paddingTop: Platform.OS === 'ios' ? 44 : 20, backgroundColor: '#fff' },
	headerRow: { flexDirection: 'row', alignItems: 'center' },
	backBtn: { padding: 6, marginRight: 8 },
	headerTitle: { fontSize: 16, fontWeight: '700' },
	label: { fontSize: 13, color: '#666', marginTop: 20, marginBottom: 8 },
	passwordRow: { flexDirection: 'row', alignItems: 'center' },
	passwordInput: { flex: 1, borderWidth: 1, borderColor: '#ccc', borderRadius: 6, paddingHorizontal: 12, paddingVertical: Platform.OS === 'ios' ? 12 : 8, backgroundColor: '#fff' },
	eyeBtn: { paddingHorizontal: 10 },
	confirmBtn: { backgroundColor: '#3DC1C6', paddingVertical: 12, borderRadius: 8, marginTop: 28, alignItems: 'center' },
	confirmText: { color: '#fff', fontWeight: '700' }
});
