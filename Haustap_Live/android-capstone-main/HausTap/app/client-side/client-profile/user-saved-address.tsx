import { Ionicons } from "@expo/vector-icons";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useFocusEffect } from '@react-navigation/native';
import { useRouter } from 'expo-router';
import React from "react";
import { Platform, ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";

export interface SavedAddress {
	id: string;
	name: string;
	details?: string;
	street: string;
	barangay: string;
	municipal: string;
	province: string;
}

export default function UserSavedAddress() {
	const router = useRouter();
	const [addresses, setAddresses] = React.useState<SavedAddress[]>([]);

	// Load addresses when screen focuses
	useFocusEffect(
		React.useCallback(() => {
			loadAddresses();
		}, [])
	);

	// Fallback: also load on mount in case focus hook doesn't fire in some navigation setups
	React.useEffect(() => {
		loadAddresses();
	}, []);

	const loadAddresses = async () => {
		try {
			const saved = await AsyncStorage.getItem('savedAddresses');
			if (saved) {
				setAddresses(JSON.parse(saved));
			}
		} catch (error) {
			console.error('Error loading addresses:', error);
		}
	};

	return (
		<View style={styles.container}>

			{/* Header */}
			<View style={styles.headerRow}>
				<View style={styles.headerLeft}>
					<TouchableOpacity 
						style={styles.backBtn} 
						onPress={() => router.back()}
					>
						<Ionicons name="arrow-back" size={24} color="black" />
					</TouchableOpacity>
					<Text style={styles.headerTitle}>Addresses</Text>
				</View>
				<TouchableOpacity 
					style={styles.addBtn}
					onPress={() => router.push('/client-side/client-profile/user-add-address' as any)}
				>
					<Text style={styles.addText}>Add</Text>
				</TouchableOpacity>
			</View>
			{/* Address list */}
			<ScrollView style={styles.content} contentContainerStyle={addresses?.length === 0 && styles.emptyContent}>
				{!addresses || addresses.length === 0 ? (
					<Text style={styles.emptyText}>No saved addresses yet</Text>
				) : (
					addresses.map((address, index) => (
						<View key={address.id} style={styles.addressCard}>
							<View style={styles.addressHeader}>
								<View style={styles.addressTitleRow}>
									<View style={styles.iconContainer}>
										<Ionicons name="home-outline" size={20} color="#666" />
									</View>
									<Text style={styles.addressName}>{address.name}</Text>
								</View>
								<TouchableOpacity style={styles.editButton}>
									<Text style={styles.editText}>Edit</Text>
								</TouchableOpacity>
							</View>
							<Text style={styles.addressDetails}>
								{[address.street, address.barangay, address.municipal, address.province]
									.filter(Boolean)
									.join(', ')}
							</Text>
							<TouchableOpacity style={styles.setDefaultButton}>
								<Text style={styles.setDefaultText}>Set as Default</Text>
							</TouchableOpacity>
						</View>
					))
				)}
			</ScrollView>
		</View>
	);
}

const styles = StyleSheet.create({
	container: { 
		flex: 1, 
		backgroundColor: '#fff',
		paddingTop: Platform.OS === 'ios' ? 44 : 20 
	},
	headerRow: { 
		flexDirection: 'row', 
		alignItems: 'center',
		justifyContent: 'space-between',
		paddingHorizontal: 16,
		paddingBottom: 10,
		borderBottomWidth: 1,
		borderBottomColor: '#eee'
	},
	headerLeft: {
		flexDirection: 'row',
		alignItems: 'center'
	},
	backBtn: { 
		padding: 6,
		marginRight: 8
	},
	headerTitle: { 
		fontSize: 16, 
		fontWeight: '700' 
	},
	addBtn: {
		paddingVertical: 6,
		paddingHorizontal: 12
	},
	addText: {
		color: '#3DC1C6',
		fontSize: 15,
		fontWeight: '600'
	},
	content: {
		flex: 1,
		paddingHorizontal: 16,
		paddingTop: 12
	},
	emptyContent: {
		flex: 1,
		alignItems: 'center',
		justifyContent: 'center'
	},
	emptyText: {
		color: '#666',
		fontSize: 15
	},
	addressCard: {
		backgroundColor: '#fff',
		borderRadius: 8,
		padding: 16,
		marginBottom: 12,
		borderWidth: 1,
		borderColor: '#eee'
	},
	addressHeader: {
		flexDirection: 'row',
		justifyContent: 'space-between',
		alignItems: 'flex-start',
		marginBottom: 12
	},
	addressTitleRow: {
		flexDirection: 'row',
		alignItems: 'center'
	},
	iconContainer: {
		width: 32,
		height: 32,
		borderRadius: 16,
		backgroundColor: '#f5f5f5',
		alignItems: 'center',
		justifyContent: 'center',
		marginRight: 10
	},
	addressName: {
		fontSize: 15,
		fontWeight: '600',
		color: '#000'
	},
	editButton: {
		paddingHorizontal: 12,
		paddingVertical: 4
	},
	editText: {
		color: '#3DC1C6',
		fontSize: 14
	},
	addressDetails: {
		fontSize: 14,
		color: '#666',
		lineHeight: 20,
		paddingRight: 24,
		marginBottom: 12
	},
	setDefaultButton: {
		alignSelf: 'flex-start'
	},
	setDefaultText: {
		color: '#3DC1C6',
		fontSize: 14,
		fontWeight: '500'
	}
});
