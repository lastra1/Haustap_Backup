// ChatTeamScreen.js
import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
import React from "react";
import {
    Platform,
    ScrollView,
    StatusBar,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";


export default function ChatTeamScreen() {
  return (
    <View style={{ flex: 1, backgroundColor: "#fff" }}>
      <StatusBar barStyle="dark-content" />


      {/* Header */}
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
          Team San Pedro 1
        </Text>


        <TouchableOpacity onPress={() => router.push('/service-provider/contact')}>
          <Ionicons name="call-outline" size={22} color="#000" />
        </TouchableOpacity>
      </View>


      {/* Chat messages */}
      <ScrollView
        style={{ flex: 1, paddingHorizontal: 16, paddingVertical: 10 }}
        contentContainerStyle={{ paddingBottom: 20 }}
      >
        {/* first gray message (left side) */}
        <View style={{ alignItems: "flex-start", marginVertical: 6 }}>
          <View
            style={{
              backgroundColor: "#e5e5e5",
              borderRadius: 12,
              padding: 10,
              maxWidth: "80%",
            }}
          >
            <Text style={{ fontSize: 14, color: "#000" }}>
              Good day Sir/Mr. Cj, Thankyou for choosing Haustap.{"\n\n"}
              Address Details:{"\n"}
              B1 L50 Mango st. Phase 1 Saint Joseph Village 10 Brgy. Langgam
              City of San Pedro, Laguna{"\n\n"}
              Booking Details:{"\n"}
              Deep Cleaning - 3 Cleaner{"\n"}
              Duration: 3 hrs{"\n\n"}
              Schedule Time:{"\n"}
              May 21, 2025 / 8:00 AM
            </Text>
          </View>
        </View>


        {/* cleaners messages (right side) */}
        {[
          "Hi! This is Carl Mirabueno\nFrom Team San Pedro 1\n09499226346",
          "Hi! This is Mj Punzalan\nFrom Team San Pedro 1\n09568738785",
          "Hi! This is Mc Organo\nFrom Team San Pedro 1\n09984789812",
        ].map((msg, i) => (
          <View
            key={i}
            style={{
              flexDirection: "row",
              justifyContent: "flex-end",
              alignItems: "flex-end",
              marginVertical: 6,
            }}
          >
            <View
              style={{
                backgroundColor: "#f0f0f0",
                borderRadius: 12,
                padding: 10,
                maxWidth: "70%",
              }}
            >
              <Text style={{ fontSize: 14, color: "#000" }}>{msg}</Text>
            </View>


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
          </View>
        ))}


        {/* see you message (left side) */}
        <View
          style={{
            flexDirection: "row",
            alignItems: "flex-end",
            marginVertical: 6,
          }}
        >
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


          <View
            style={{
              backgroundColor: "#e5e5e5",
              borderRadius: 12,
              padding: 10,
              maxWidth: "70%",
            }}
          >
            <Text style={{ fontSize: 14, color: "#000" }}>See you!</Text>
          </View>
        </View>
      </ScrollView>


      {/* Input box */}
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
          style={{
            flex: 1,
            backgroundColor: "#f3f3f3",
            borderRadius: 20,
            paddingHorizontal: 14,
            paddingVertical: 8,
            marginHorizontal: 8,
            fontSize: 14,
          }}
          editable={false} // UI only
        />


        <TouchableOpacity>
          <Ionicons name="send" size={24} color="#000" />
        </TouchableOpacity>
      </View>
    </View>
  );
}
