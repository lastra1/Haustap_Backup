import { useLocalSearchParams } from 'expo-router';
import React from 'react';
import ChatInbox from '../user-chat/chat-inbox';
import UserChatBox from '../user-chat/user-chat-box';

export default function ChatIndex() {
  const params = useLocalSearchParams() as { conversationId?: string };
  const conversationId = params.conversationId;

  if (conversationId) {
    return <ChatInbox />;
  }
  return <UserChatBox />;
}
