// ChatsScreen.js
import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
import React from "react";
import { FlatList, Text, TouchableOpacity, View } from "react-native";


type ChatItem = { id: string; name: string; message: string };

const chats: ChatItem[] = [
  { id: "1", name: "Jenn Bornilla", message: "See you!" },
  { id: "2", name: "Cj Garcia", message: "See you!" },
];


export default function ChatsScreen() {
  const renderItem = ({ item }: { item: ChatItem }) => (
    <TouchableOpacity
      style={{
        flexDirection: "row",
        alignItems: "center",
        paddingVertical: 12,
        borderBottomWidth: 1,
        borderBottomColor: "#e5e5e5",
      }}
      key={item.id}
      onPress={() =>
        item.name === 'Cj Garcia'
          ? router.push('/service-provider/chatbox-team')
          : router.push('/service-provider/chatbox-individual')
      }
    >
      <View
        style={{
          width: 42,
          height: 42,
          borderRadius: 21,
          backgroundColor: "#f0f0f0",
          alignItems: "center",
          justifyContent: "center",
          marginRight: 12,
        }}
      >
        <Ionicons name="person-outline" size={22} color="#555" />
      </View>


      <View>
        <Text style={{ fontWeight: "600", fontSize: 16 }}>{item.name}</Text>
        <Text style={{ color: "#777" }}>{item.message}</Text>
      </View>
    </TouchableOpacity>
  );


  return (
    <View style={{ flex: 1, backgroundColor: "#fff" }}>
      {/* Header */}
      <View style={{ padding: 16 }}>
        <Text style={{ fontSize: 18, fontWeight: "700", color: "#333" }}>
          Chats
        </Text>
      </View>


      {/* Chat List */}
      <FlatList
        data={chats}
        keyExtractor={(item) => item.id}
        renderItem={renderItem}
        contentContainerStyle={{ paddingHorizontal: 16 }}
      />
    </View>
  );
}