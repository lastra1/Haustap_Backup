import { Ionicons } from "@expo/vector-icons";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';
import React, { useState } from "react";
import { Modal, Platform, ScrollView, StyleSheet, Text, TextInput, TouchableOpacity, View } from "react-native";

export default function UserAddAddress() {
	const router = useRouter();
	const [addressName, setAddressName] = useState("");
	const [addressDetails, setAddressDetails] = useState("");
	const [street, setStreet] = useState("");
	const [barangay, setBarangay] = useState("");
	const [municipal, setMunicipal] = useState("");
	const [province, setProvince] = useState("");

	// Selection modal state
	const [showPicker, setShowPicker] = useState(false);
	const [pickerTitle, setPickerTitle] = useState("");
	const [pickerOptions, setPickerOptions] = useState<string[]>([]);
	const [activeField, setActiveField] = useState<'barangay' | 'municipal' | 'province' | null>(null);

	const openPicker = (field: 'barangay' | 'municipal' | 'province', title: string, options: string[]) => {
		setActiveField(field);
		setPickerTitle(title);
		setPickerOptions(options);
		setShowPicker(true);
	};

	const onSelect = (value: string) => {
		if (activeField === 'barangay') setBarangay(value);
		if (activeField === 'municipal') setMunicipal(value);
		if (activeField === 'province') setProvince(value);
		setShowPicker(false);
	};

	// Mock data - replace with your actual data
	const mockOptions = {
		barangay: ["Barangay 1", "Barangay 2", "Barangay 3"],
		municipal: ["Municipal 1", "Municipal 2", "Municipal 3"],
		province: ["Province 1", "Province 2", "Province 3"]
	};

	return (
		<View style={styles.container}>
			{/* Header */}
			<View style={styles.headerRow}>
				<TouchableOpacity onPress={() => router.back()} style={styles.backBtn}>
					<Ionicons name="arrow-back" size={22} color="#000" />
				</TouchableOpacity>
				<Text style={styles.headerTitle}>Add Address</Text>
				<TouchableOpacity style={styles.deleteBtn}>
					<Text style={styles.deleteText}>Delete</Text>
				</TouchableOpacity>
			</View>

			<ScrollView style={styles.form} showsVerticalScrollIndicator={false}>
				<Text style={styles.label}>Address Name</Text>
				<TextInput
					style={styles.input}
					value={addressName}
					onChangeText={setAddressName}
					placeholder="Home, Office, etc."
				/>

				<Text style={styles.label}>Address Details</Text>
				<TextInput
					style={styles.input}
					value={addressDetails}
					onChangeText={setAddressDetails}
					placeholder="Building, Floor, Unit no."
				/>

				<Text style={styles.label}>Street</Text>
				<TextInput
					style={styles.input}
					value={street}
					onChangeText={setStreet}
					placeholder="Street name"
				/>

				<Text style={styles.label}>Barangay</Text>
				<TouchableOpacity 
					style={styles.pickerButton}
					onPress={() => openPicker('barangay', 'Select Barangay', mockOptions.barangay)}
				>
					<Text style={barangay ? styles.pickerText : styles.placeholderText}>
						{barangay || 'Select barangay'}
					</Text>
					<Ionicons name="chevron-down" size={20} color="#666" />
				</TouchableOpacity>

				<Text style={styles.label}>Municipal</Text>
				<TouchableOpacity 
					style={styles.pickerButton}
					onPress={() => openPicker('municipal', 'Select Municipal', mockOptions.municipal)}
				>
					<Text style={municipal ? styles.pickerText : styles.placeholderText}>
						{municipal || 'Select municipal'}
					</Text>
					<Ionicons name="chevron-down" size={20} color="#666" />
				</TouchableOpacity>

				<Text style={styles.label}>Province</Text>
				<TouchableOpacity 
					style={styles.pickerButton}
					onPress={() => openPicker('province', 'Select Province', mockOptions.province)}
				>
					<Text style={province ? styles.pickerText : styles.placeholderText}>
						{province || 'Select province'}
					</Text>
					<Ionicons name="chevron-down" size={20} color="#666" />
				</TouchableOpacity>

				<TouchableOpacity 
					style={[styles.addBtn, (!addressName || !street || !barangay || !municipal || !province) && styles.addBtnDisabled]}
					onPress={async () => {
						if (addressName && street && barangay && municipal && province) {
							try {
								// Create new address object
								const newAddress = {
									id: Date.now().toString(),
									name: addressName,
									details: addressDetails,
									street,
									barangay,
									municipal,
									province
								};

								// Get existing addresses
								const existingJSON = await AsyncStorage.getItem('savedAddresses');
								const existing = existingJSON ? JSON.parse(existingJSON) : [];

								// Add new address
								const updated = [...existing, newAddress];
								await AsyncStorage.setItem('savedAddresses', JSON.stringify(updated));

								// Go back to list
								router.back();
							} catch (error) {
								console.error('Error saving address:', error);
								// You could show an error toast here
							}
						}
					}}
				>
					<Text style={styles.addText}>Add</Text>
				</TouchableOpacity>
			</ScrollView>

			{/* Selection Modal */}
			<Modal
				visible={showPicker}
				transparent
				animationType="fade"
				onRequestClose={() => setShowPicker(false)}
			>
				<View style={styles.modalOverlay}>
					<View style={styles.modalContent}>
						<View style={styles.modalHeader}>
							<Text style={styles.modalTitle}>{pickerTitle}</Text>
							<TouchableOpacity onPress={() => setShowPicker(false)}>
								<Ionicons name="close" size={24} color="#666" />
							</TouchableOpacity>
						</View>
						<ScrollView style={styles.optionsList}>
							{pickerOptions.map((option, index) => (
								<TouchableOpacity
									key={index}
									style={styles.optionItem}
									onPress={() => onSelect(option)}
								>
									<Text style={styles.optionText}>{option}</Text>
								</TouchableOpacity>
							))}
						</ScrollView>
					</View>
				</View>
			</Modal>
		</View>
	);
}

const styles = StyleSheet.create({
	container: {
		flex: 1,
		backgroundColor: '#fff'
	},
	headerRow: {
		flexDirection: 'row',
		alignItems: 'center',
		justifyContent: 'space-between',
		paddingHorizontal: 16,
		paddingTop: Platform.OS === 'ios' ? 44 : 20,
		paddingBottom: 10,
		borderBottomWidth: 1,
		borderBottomColor: '#eee'
	},
	backBtn: {
		padding: 6
	},
	headerTitle: {
		fontSize: 16,
		fontWeight: '700'
	},
	deleteBtn: {
		paddingVertical: 6,
		paddingHorizontal: 12
	},
	deleteText: {
		color: '#FF3B30',
		fontSize: 15
	},
	form: {
		flex: 1,
		padding: 16
	},
	label: {
		fontSize: 13,
		color: '#666',
		marginTop: 12,
		marginBottom: 6
	},
	input: {
		borderWidth: 1,
		borderColor: '#ccc',
		borderRadius: 6,
		paddingHorizontal: 12,
		paddingVertical: Platform.OS === 'ios' ? 12 : 8,
		backgroundColor: '#fff'
	},
	pickerButton: {
		flexDirection: 'row',
		alignItems: 'center',
		justifyContent: 'space-between',
		borderWidth: 1,
		borderColor: '#ccc',
		borderRadius: 6,
		paddingHorizontal: 12,
		paddingVertical: Platform.OS === 'ios' ? 12 : 8,
		backgroundColor: '#fff'
	},
	pickerText: {
		color: '#000'
	},
	placeholderText: {
		color: '#999'
	},
	addBtn: {
		backgroundColor: '#3DC1C6',
		paddingVertical: 14,
		borderRadius: 8,
		alignItems: 'center',
		marginTop: 24,
		marginBottom: 30
	},
	addBtnDisabled: {
		opacity: 0.6
	},
	addText: {
		color: '#fff',
		fontSize: 16,
		fontWeight: '600'
	},
	modalOverlay: {
		flex: 1,
		backgroundColor: 'rgba(0,0,0,0.4)',
		justifyContent: 'center',
		alignItems: 'center'
	},
	modalContent: {
		backgroundColor: '#fff',
		borderRadius: 12,
		width: '85%',
		maxHeight: '70%'
	},
	modalHeader: {
		flexDirection: 'row',
		alignItems: 'center',
		justifyContent: 'space-between',
		padding: 16,
		borderBottomWidth: 1,
		borderBottomColor: '#eee'
	},
	modalTitle: {
		fontSize: 16,
		fontWeight: '600'
	},
	optionsList: {
		maxHeight: 300
	},
	optionItem: {
		paddingVertical: 12,
		paddingHorizontal: 16,
		borderBottomWidth: 1,
		borderBottomColor: '#eee'
	},
	optionText: {
		fontSize: 15
	}
});
