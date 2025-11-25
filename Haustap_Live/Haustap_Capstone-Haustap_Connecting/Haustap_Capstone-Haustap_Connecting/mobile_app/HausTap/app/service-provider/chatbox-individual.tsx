// ChatScreen.js
import { Ionicons } from "@expo/vector-icons";
import { router, useLocalSearchParams } from "expo-router";
import React, { useRef, useState } from "react";
import {
    KeyboardAvoidingView,
    Platform,
    ScrollView,
    StatusBar,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";


export default function ChatScreen() {
  const params = useLocalSearchParams();
  const bookingId = params.bookingId as string | undefined;
  const clientName = params.clientName as string | undefined;
  const clientPhone = params.clientPhone as string | undefined;
  const [messages, setMessages] = useState([
    {
      id: 1,
      text:
        "Good day Ms. Jenn, Thank you for choosing Haustap.\n\nAddress Details:\nB1 L50 Mango St, Phase 1 Saint Joseph Village 10 Brgy. Langgam City of San Pedro, Laguna\n\nBooking Details:\nBasic Cleaning - 1 Cleaner\nDuration: 3 hrs\n\nSchedule Time:\nMay 21, 2025 / 8:00 AM",
      sender: "serviceprovider",
    },
    {
      id: 2,
      text: "Hi! This is Ana Santos\n09499226346",
      sender: "serviceprovider",
    },
    {
      id: 3,
      text: "See you!",
      sender: "other",
    },
  ]);


  const [inputText, setInputText] = useState("");
  const scrollViewRef = useRef<ScrollView | null>(null);


  const handleSend = () => {
    if (!inputText.trim()) return;
    const newMessage = {
      id: Date.now(),
      text: inputText.trim(),
      sender: "serviceprovider",
    };
    setMessages((prev) => [...prev, newMessage]);
    setInputText("");


    setTimeout(() => {
      scrollViewRef.current?.scrollToEnd({ animated: true });
    }, 100);
  };


  return (
    <KeyboardAvoidingView
      style={{ flex: 1, backgroundColor: "#fff" }}
      behavior={Platform.OS === "ios" ? "padding" : "height"}
      keyboardVerticalOffset={Platform.OS === "ios" ? 90 : 80} // ✅ Para umangat yung input kapag may keyboard
    >
      <StatusBar barStyle="dark-content" />


      {/* ✅ Header */}
      <View
        style={{
          flexDirection: "row",
          alignItems: "center",
          paddingVertical: 12,
          paddingHorizontal: 16,
          borderBottomWidth: 1,
          borderBottomColor: "#e5e5e5",
          marginTop: Platform.OS === "ios" ? 45 : (StatusBar.currentHeight ?? 0) + 10,
          backgroundColor: "#fff",
        }}
      >
        <TouchableOpacity onPress={() => router.push('/service-provider/chatbox')}>
          <Ionicons name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text
          style={{
            fontSize: 18,
            fontWeight: "600",
            marginLeft: 12,
            flex: 1,
          }}
        >
          {clientName || "Client"}
        </Text>
        <TouchableOpacity>
          <Ionicons name="call-outline" size={22} color="#000" />
        </TouchableOpacity>
      </View>


      {/* ✅ Chat Messages */}
      <ScrollView
        ref={scrollViewRef}
        style={{ flex: 1, paddingHorizontal: 16, paddingVertical: 10 }}
        contentContainerStyle={{ paddingBottom: 20 }}
        onContentSizeChange={() =>
          scrollViewRef.current?.scrollToEnd({ animated: true })
        }
      >
        {messages.map((msg) => (
          <View
            key={msg.id}
            style={{
              flexDirection: "row",
              justifyContent:
                msg.sender === "serviceprovider" ? "flex-end" : "flex-start",
              marginVertical: 6,
            }}
          >
            {/* Avatar (left or right) */}
            {msg.sender === "other" && (
              <View
                style={{
                  width: 28,
                  height: 28,
                  borderRadius: 14,
                  backgroundColor: "#e5e5e5",
                  alignItems: "center",
                  justifyContent: "center",
                  marginRight: 6,
                }}
              >
                <Ionicons name="person-outline" size={16} color="#555" />
              </View>
            )}


            <View
              style={{
                backgroundColor:
                  msg.sender === "serviceprovider" ? "#e8f0fe" : "#e5e5e5",
                borderRadius: 12,
                padding: 10,
                maxWidth: "75%",
              }}
            >
              <Text style={{ fontSize: 14, color: "#000" }}>{msg.text}</Text>
            </View>


            {msg.sender === "serviceprovider" && (
              <View
                style={{
                  width: 28,
                  height: 28,
                  borderRadius: 14,
                  backgroundColor: "#e5e5e5",
                  alignItems: "center",
                  justifyContent: "center",
                  marginLeft: 6,
                }}
              >
                <Ionicons name="person-outline" size={16} color="#555" />
              </View>
            )}
          </View>
        ))}
      </ScrollView>


      {/* ✅ Input Box */}
      <View
        style={{
          flexDirection: "row",
          alignItems: "center",
          borderTopWidth: 1,
          borderTopColor: "#e5e5e5",
          paddingHorizontal: 10,
          paddingVertical: 8,
          backgroundColor: "#fff",
        }}
      >
        <TouchableOpacity>
          <Ionicons name="add-circle-outline" size={26} color="#555" />
        </TouchableOpacity>


        <TextInput
          placeholder="Text Message"
          value={inputText}
          onChangeText={setInputText}
          style={{
            flex: 1,
            backgroundColor: "#f3f3f3",
            borderRadius: 20,
            paddingHorizontal: 14,
            paddingVertical: 8,
            marginHorizontal: 8,
            fontSize: 14,
          }}
          onFocus={() =>
            setTimeout(
              () => scrollViewRef.current?.scrollToEnd({ animated: true }),
              200
            )
          } // ✅ auto-scroll pag focus sa input
        />


        <TouchableOpacity onPress={handleSend}>
          <Ionicons name="send" size={24} color="#007bff" />
        </TouchableOpacity>
      </View>
    </KeyboardAvoidingView>
  );
}
