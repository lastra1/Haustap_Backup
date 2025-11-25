import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React from 'react';
import { FlatList, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

type ChatItem = {
	id: string;
	name: string;
	lastMessage: string;
	time?: string;
	unread?: boolean;
};

const MOCK_CHATS: ChatItem[] = [
	{ id: '1', name: 'Ana Santos', lastMessage: 'See you!', time: '11:24', unread: true },
	{ id: '2', name: 'Team San Pedro 1', lastMessage: 'See you!', time: '09:05', unread: false },
];

export default function UserChatBox() {
	const router = useRouter();

	const renderItem = ({ item }: { item: ChatItem }) => (
		<TouchableOpacity
			style={styles.row}
			activeOpacity={0.8}
			onPress={() => {
				// Navigate to a conversation screen; a route can be added later.
				// For now push to `/client-side/chat` conversation with id param.
				router.push({ pathname: '/client-side/chat', params: { conversationId: item.id } } as any);
			}}
		>
			<View style={styles.avatar}>
				<Ionicons name="person" size={20} color="#fff" />
			</View>

			<View style={styles.content}>
				<View style={styles.titleRow}>
					<Text style={styles.name}>{item.name}</Text>
					<Text style={styles.time}>{item.time}</Text>
				</View>
				<View style={styles.subtitleRow}>
					<Text style={styles.lastMessage}>{item.lastMessage}</Text>
					{item.unread ? <View style={styles.unreadDot} /> : null}
				</View>
			</View>
		</TouchableOpacity>
	);

	return (
		<View style={styles.page}>
			<View style={styles.header}>
				<Text style={styles.headerTitle}>Chats</Text>
			</View>

			<FlatList
				data={MOCK_CHATS}
				keyExtractor={(i) => i.id}
				renderItem={renderItem}
				contentContainerStyle={styles.list}
				ItemSeparatorComponent={() => <View style={styles.sep} />}
			/>
		</View>
	);
}

const styles = StyleSheet.create({
	page: { flex: 1, backgroundColor: '#fff' },
	header: { paddingTop: 18, paddingBottom: 8, paddingHorizontal: 16 },
	headerTitle: { fontSize: 20, fontWeight: '700' },
	list: { paddingHorizontal: 12, paddingTop: 8, paddingBottom: 120 },
	row: { flexDirection: 'row', alignItems: 'center', paddingVertical: 12 },
	avatar: {
		width: 44,
		height: 44,
		borderRadius: 22,
		backgroundColor: '#eee',
		justifyContent: 'center',
		alignItems: 'center',
		marginRight: 12,
	},
	content: { flex: 1, justifyContent: 'center' },
	titleRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
	name: { fontSize: 16, fontWeight: '700' },
	time: { fontSize: 12, color: '#888' },
	subtitleRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
	lastMessage: { color: '#666', flex: 1 },
	unreadDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: '#3DC1C6', marginLeft: 8 },
	sep: { height: 1, backgroundColor: '#F0F0F0' },
});

