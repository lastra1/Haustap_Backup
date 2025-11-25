import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useFocusEffect } from '@react-navigation/native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import { FlatList, KeyboardAvoidingView, Platform, StyleSheet, Text, TextInput, TouchableOpacity, View } from 'react-native';

type Message = {
	id: string;
	sender: 'user' | 'provider';
	text: string;
	createdAt: string;
	status?: 'sending' | 'sent' | 'seen';
};

export default function ChatInbox() {
	const router = useRouter();
	const params = useLocalSearchParams() as { 
		conversationId?: string; 
		providerId?: string; 
		providerName?: string;
		role?: string;
	};
	const conversationId = params.conversationId ?? 'conv-example';
	const providerId = params.providerId ?? 'provider-1';
	// this inbox is user-side only; messages sent from this screen are from 'user'
	const [/* role */ , setUnused] = useState<'user' | 'provider'>('user');
	const [messages, setMessages] = useState<Message[]>([]);
	const [text, setText] = useState('');
	const listRef = useRef<FlatList<Message> | null>(null);

	const storageKey = `HT_conv_${conversationId}`;

	const loadMessages = useCallback(async () => {
		try {
			const raw = await AsyncStorage.getItem(storageKey);
			if (raw) setMessages(JSON.parse(raw));
			else {
				// seed a couple of messages for demo
				const seed: Message[] = [
					{ id: 'm1', sender: 'provider', text: 'Hello! I accepted your booking.', createdAt: new Date().toISOString() },
					{ id: 'm2', sender: 'user', text: 'Thanks! When will you arrive?', createdAt: new Date().toISOString(), status: 'seen' },
				];
				setMessages(seed);
				await AsyncStorage.setItem(storageKey, JSON.stringify(seed));
			}
		} catch (e) {
			console.warn('Failed to load messages', e);
		}
	}, [storageKey]);

	useFocusEffect(
		useCallback(() => {
			loadMessages();
		}, [loadMessages])
	);

		useEffect(() => {
			// scroll to end on messages change
			if (listRef.current && messages.length) {
				// small timeout to allow layout to settle
				setTimeout(() => listRef.current?.scrollToEnd({ animated: true }), 60);
			}
		}, [messages]);

	const persist = async (next: Message[]) => {
		try {
			await AsyncStorage.setItem(storageKey, JSON.stringify(next));
		} catch (e) {
			console.warn('Failed to save messages', e);
		}
	};

	const updateMessageStatus = useCallback(async (messageId: string, status: Message['status']) => {
		const updatedMessages = messages.map(msg => 
			msg.id === messageId ? { ...msg, status } : msg
		);
		setMessages(updatedMessages);
		await persist(updatedMessages);
	}, [messages, persist]);

	const handleSend = async () => {
		if (!text.trim()) return;
		// always send as 'user' from the app's user-side inbox
		const m: Message = { 
			id: String(Date.now()), 
			sender: 'user', 
			text: text.trim(), 
			createdAt: new Date().toISOString(),
			status: 'sending'
		};
		const next = [...messages, m];
		setMessages(next);
		setText('');
		
		try {
			await persist(next);
			// Update the message status to 'sent' after successful persistence
			await updateMessageStatus(m.id, 'sent');

			// Simulate provider seeing the message after 2 seconds
			setTimeout(async () => {
				const seenMessages = next.map(msg => 
					msg.sender === 'user' ? { ...msg, status: 'seen' as const } : msg
				);
				setMessages(seenMessages);
				await persist(seenMessages);
			}, 2000);
		} catch (e) {
			console.warn('Failed to send message', e);
		}
	};

		const renderItem = ({ item }: { item: Message }) => {
			// user-side inbox: messages with sender === 'user' are mine
			const isMine = item.sender === 'user';
		return (
			<View style={[styles.msgRow, isMine ? styles.msgRowRight : styles.msgRowLeft]}>
				<View style={[styles.bubble, isMine ? styles.bubbleMine : styles.bubbleOther]}>
					<Text style={[styles.msgText, isMine ? styles.msgTextMine : styles.msgTextOther]}>{item.text}</Text>
					<View style={styles.msgFooter}>
						<Text style={styles.msgTime}>{new Date(item.createdAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</Text>
						{isMine && item.status && (
							<Text style={[styles.msgStatus, item.status === 'seen' && styles.msgStatusSeen]}>
								{item.status === 'sending' ? 'sending...' : item.status === 'seen' ? '✓✓' : '✓'}
							</Text>
						)}
					</View>
				</View>
			</View>
		);
	};

		return (
			<KeyboardAvoidingView
				style={styles.page}
				behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
				keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 80}
				contentContainerStyle={{ flex: 1 }}
			>
				<View style={styles.header}>
					<TouchableOpacity onPress={() => router.back()} style={{ paddingRight: 12 }}>
						<Ionicons name="chevron-back" size={22} color="#333" />
					</TouchableOpacity>
					<View style={{ flex: 1 }}>
						<Text style={styles.headerTitle}>
							{params.providerName ? params.providerName : 'Chat'}
						</Text>
						<Text style={styles.headerSub}>Booking ID: {conversationId}</Text>
					</View>
					{/* user-only inbox: no role toggle */}
				</View>

				<FlatList
					ref={listRef}
					data={messages}
					keyExtractor={(i) => i.id}
					renderItem={renderItem}
					contentContainerStyle={styles.list}
					keyboardShouldPersistTaps="handled"
					onContentSizeChange={() => listRef.current?.scrollToEnd({ animated: true })}
				/>

				<View style={styles.composerWrapper}>
					<View style={styles.composer}>
						<TextInput
							value={text}
							onChangeText={setText}
							placeholder="Type a message"
							style={styles.input}
							multiline
							returnKeyType="send"
							onSubmitEditing={() => handleSend()}
							blurOnSubmit={false}
						/>
						<TouchableOpacity style={styles.sendBtn} onPress={handleSend}>
							<Ionicons name="send" size={20} color="#fff" />
						</TouchableOpacity>
					</View>
				</View>
			</KeyboardAvoidingView>
		);
}

const styles = StyleSheet.create({
	page: { flex: 1, backgroundColor: '#fff' },
	header: { flexDirection: 'row', alignItems: 'center', padding: 12, borderBottomWidth: 1, borderBottomColor: '#eee' },
	headerTitle: { fontSize: 16, fontWeight: '700' },
	headerSub: { fontSize: 11, color: '#666' },
	roleToggle: { backgroundColor: '#3DC1C6', paddingHorizontal: 12, paddingVertical: 6, borderRadius: 8 },
	list: { padding: 12, paddingBottom: 20 },
	msgRow: { marginVertical: 6, flexDirection: 'row' },
	msgRowLeft: { justifyContent: 'flex-start' },
	msgRowRight: { justifyContent: 'flex-end' },
	bubble: { maxWidth: '80%', padding: 10, borderRadius: 12 },
	bubbleMine: { backgroundColor: '#3DC1C6', borderBottomRightRadius: 2 },
	bubbleOther: { backgroundColor: '#F0F0F0', borderBottomLeftRadius: 2 },
	msgText: { fontSize: 14 },
	msgTextMine: { color: '#fff' },
	msgTextOther: { color: '#333' },
	msgTime: { fontSize: 10, color: '#666', textAlign: 'right' },
	msgFooter: { flexDirection: 'row', alignItems: 'center', justifyContent: 'flex-end', marginTop: 6 },
	msgStatus: { fontSize: 10, color: '#666', marginLeft: 4 },
	msgStatusSeen: { color: '#3DC1C6' },
	composerWrapper: { borderTopWidth: 1, borderTopColor: '#eee' },
	composer: { flexDirection: 'row', padding: 10, alignItems: 'flex-end' },
	input: { flex: 1, minHeight: 40, maxHeight: 120, paddingHorizontal: 12, paddingVertical: 8, backgroundColor: '#fafafa', borderRadius: 8, borderWidth: 1, borderColor: '#eee' },
	sendBtn: { backgroundColor: '#3DC1C6', padding: 10, borderRadius: 8, marginLeft: 8, justifyContent: 'center', alignItems: 'center' },
});

