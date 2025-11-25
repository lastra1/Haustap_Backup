import { Ionicons } from "@expo/vector-icons";
import { useRouter } from 'expo-router';
import React, { useState } from "react";
import { Platform, StyleSheet, Text, TextInput, TouchableOpacity, View } from "react-native";

export default function UserNewPassword() {
	const router = useRouter();
	const [currentPwd, setCurrentPwd] = useState("");
	const [newPwd, setNewPwd] = useState("");
	const [confirmPwd, setConfirmPwd] = useState("");
	const [showCurrent, setShowCurrent] = useState(true);
	const [showNew, setShowNew] = useState(true);
	const [showConfirm, setShowConfirm] = useState(true);

	return (
		<View style={styles.container}>
			{/* Header */}
			<View style={styles.headerRow}>
				<TouchableOpacity onPress={() => router.back()} style={styles.backBtn}>
					<Ionicons name="arrow-back" size={22} color="#000" />
				</TouchableOpacity>
				<Text style={styles.headerTitle}>Change Password</Text>
			</View>

			<View style={{ height: 18 }} />

			<Text style={styles.label}>Current Password</Text>
			<View style={styles.passwordRow}>
				<TextInput
					style={styles.passwordInput}
					value={currentPwd}
					onChangeText={setCurrentPwd}
					secureTextEntry={showCurrent}
					placeholder="Enter current password"
				/>
				<TouchableOpacity style={styles.eyeBtn} onPress={() => setShowCurrent(!showCurrent)}>
					<Ionicons name={showCurrent ? 'eye-off' : 'eye'} size={18} color="#666" />
				</TouchableOpacity>
			</View>

			<Text style={styles.label}>New Password</Text>
			<View style={styles.passwordRow}>
				<TextInput
					style={styles.passwordInput}
					value={newPwd}
					onChangeText={setNewPwd}
					secureTextEntry={showNew}
					placeholder="Enter new password"
				/>
				<TouchableOpacity style={styles.eyeBtn} onPress={() => setShowNew(!showNew)}>
					<Ionicons name={showNew ? 'eye-off' : 'eye'} size={18} color="#666" />
				</TouchableOpacity>
			</View>

			<Text style={styles.label}>Confirm New Password</Text>
			<View style={styles.passwordRow}>
				<TextInput
					style={styles.passwordInput}
					value={confirmPwd}
					onChangeText={setConfirmPwd}
					secureTextEntry={showConfirm}
					placeholder="Re-enter new password"
				/>
				<TouchableOpacity style={styles.eyeBtn} onPress={() => setShowConfirm(!showConfirm)}>
					<Ionicons name={showConfirm ? 'eye-off' : 'eye'} size={18} color="#666" />
				</TouchableOpacity>
			</View>

	<TouchableOpacity style={styles.saveBtn} onPress={() => { /* validate & save new password */ router.push('/client-side/client-profile' as any); }}>
			<Text style={styles.saveText}>Save</Text>
		</TouchableOpacity>
		</View>
	);
}

const styles = StyleSheet.create({
	container: { flex: 1, paddingHorizontal: 16, paddingTop: Platform.OS === 'ios' ? 44 : 20, backgroundColor: '#fff' },
	headerRow: { flexDirection: 'row', alignItems: 'center' },
	backBtn: { padding: 6, marginRight: 8 },
	headerTitle: { fontSize: 16, fontWeight: '700' },
	label: { fontSize: 13, color: '#666', marginTop: 12, marginBottom: 8 },
	passwordRow: { flexDirection: 'row', alignItems: 'center' },
	passwordInput: { flex: 1, borderWidth: 1, borderColor: '#ccc', borderRadius: 6, paddingHorizontal: 12, paddingVertical: Platform.OS === 'ios' ? 12 : 8, backgroundColor: '#fff' },
	eyeBtn: { paddingHorizontal: 10 },
	saveBtn: { backgroundColor: '#3DC1C6', paddingVertical: 12, borderRadius: 8, marginTop: 24, alignItems: 'center' },
	saveText: { color: '#fff', fontWeight: '700' }
});
