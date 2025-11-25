import { Ionicons } from "@expo/vector-icons";
import DateTimePicker from '@react-native-community/datetimepicker';
import { useRouter } from 'expo-router';
import React, { useState } from "react";
import { Modal, Platform, StyleSheet, Text, TextInput, TouchableOpacity, View } from "react-native";

export default function UserProfile() {
	const [fullName, setFullName] = useState("Jenn Bornilla");
	const [mobile, setMobile] = useState("09499129312");
	const [email, setEmail] = useState("JennBornilla@gmail.com");
	const [gender, setGender] = useState("female");
	const [dob, setDob] = useState("");

	// UI state for pickers
	const [showGenderPicker, setShowGenderPicker] = useState(false);
	const [showDatePicker, setShowDatePicker] = useState(false);
	const [date, setDate] = useState<Date | null>(null);

	const router = useRouter();

	return (
		<View style={styles.container}>
			<View style={styles.avatarRow}>
				<View style={styles.avatarCircle}>
					<Ionicons name="person" size={48} color="#333" />
				</View>
				<TouchableOpacity style={styles.cameraBtn}>
					<Ionicons name="camera" size={18} color="#fff" />
				</TouchableOpacity>
			</View>

			<View style={styles.field}>
				<Text style={styles.label}>Full Name</Text>
				<TextInput style={styles.input} value={fullName} onChangeText={setFullName} />
			</View>

			<View style={styles.field}>
				<Text style={styles.label}>Mobile Number</Text>
				<TextInput style={styles.input} value={mobile} onChangeText={setMobile} keyboardType="phone-pad" />
			</View>

			<View style={styles.field}>
				<Text style={styles.label}>Email</Text>
				<TextInput style={styles.input} value={email} onChangeText={setEmail} keyboardType="email-address" autoCapitalize="none" />
			</View>

			<View style={styles.field}>
				<Text style={styles.label}>Gender</Text>
				<TouchableOpacity style={styles.input} onPress={() => setShowGenderPicker(true)}>
					<Text style={{ color: gender ? '#000' : '#888' }}>{gender ? (gender === 'male' ? 'Male' : gender === 'female' ? 'Female' : gender) : 'Select gender'}</Text>
				</TouchableOpacity>

				{/* Gender selection modal */}
				<Modal visible={showGenderPicker} transparent animationType="fade" onRequestClose={() => setShowGenderPicker(false)}>
					<View style={styles.modalOverlay}>
						<View style={styles.modalContent}>
							<TouchableOpacity style={styles.modalItem} onPress={() => { setGender('male'); setShowGenderPicker(false); }}>
								<Text>Male</Text>
							</TouchableOpacity>
							<TouchableOpacity style={styles.modalItem} onPress={() => { setGender('female'); setShowGenderPicker(false); }}>
								<Text>Female</Text>
							</TouchableOpacity>
							<TouchableOpacity style={styles.modalItem} onPress={() => { setGender('other'); setShowGenderPicker(false); }}>
								<Text>Other</Text>
							</TouchableOpacity>
							<TouchableOpacity style={[styles.modalItem, { marginTop: 8 }]} onPress={() => setShowGenderPicker(false)}>
								<Text style={{ color: '#888' }}>Cancel</Text>
							</TouchableOpacity>
						</View>
					</View>
				</Modal>
			</View>

			<View style={styles.field}>
				<Text style={styles.label}>Date of Birth</Text>
				<TouchableOpacity style={styles.input} onPress={() => setShowDatePicker(true)}>
					<Text style={{ color: dob ? '#000' : '#888' }}>{dob || 'Select date of birth'}</Text>
				</TouchableOpacity>

				{showDatePicker && (
					<DateTimePicker
						value={date || new Date(1990, 0, 1)}
						mode="date"
						display={Platform.OS === 'ios' ? 'spinner' : 'default'}
						maximumDate={new Date()}
						onChange={(event: any, selectedDate?: Date | null) => {
								setShowDatePicker(Platform.OS === 'ios');
								if (selectedDate) {
									setDate(selectedDate);
								// format dd/mm/yyyy
								const d = selectedDate;
								const formatted = `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
								setDob(formatted);
							}
						}}
					/>
				)}
			</View>

			<TouchableOpacity style={styles.saveBtn}>
				<Text style={styles.saveText}>Save</Text>
			</TouchableOpacity>

			<TouchableOpacity style={styles.changePwdBtn} onPress={() => router.push('/client-side/client-profile/user-change-password' as any)}>
				<Text style={styles.changePwdText}>Change Password</Text>
			</TouchableOpacity>
		</View>
	);
}

const styles = StyleSheet.create({
	container: { paddingVertical: 8, paddingHorizontal: 12 },
	avatarRow: { alignItems: 'center', marginBottom: 12 },
	avatarCircle: { width: 90, height: 90, borderRadius: 45, backgroundColor: '#fff', borderWidth: 1, borderColor: '#000', alignItems: 'center', justifyContent: 'center' },
	cameraBtn: { position: 'absolute', right: 14, top: 52, backgroundColor: '#3DC1C6', padding: 8, borderRadius: 18 },
	field: { marginBottom: 10 },
	label: { fontSize: 12, color: '#666', marginBottom: 6 },
	input: { borderWidth: 1, borderColor: '#ccc', borderRadius: 6, paddingHorizontal: 10, paddingVertical: Platform.OS === 'ios' ? 10 : 6, backgroundColor: '#fff' },
	saveBtn: { backgroundColor: '#3DC1C6', paddingVertical: 12, borderRadius: 8, marginTop: 14, alignItems: 'center' },
	saveText: { color: '#fff', fontWeight: '700' },
	changePwdBtn: { backgroundColor: '#ddd', paddingVertical: 12, borderRadius: 8, marginTop: 10, alignItems: 'center' },
	changePwdText: { color: '#333', fontWeight: '700' },
 	modalOverlay: { flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'center', alignItems: 'center' },
 	modalContent: { backgroundColor: '#fff', padding: 12, borderRadius: 8, minWidth: 220, alignItems: 'stretch' },
 	modalItem: { paddingVertical: 10, paddingHorizontal: 8, borderBottomWidth: 1, borderBottomColor: '#eee', alignItems: 'center' },
});

