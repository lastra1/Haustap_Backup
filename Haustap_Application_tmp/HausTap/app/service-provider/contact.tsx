import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
import React from "react";
import {
    Platform,
    ScrollView,
    StatusBar,
    Text,
    TouchableOpacity,
    View,
} from "react-native";


export default function ContactInfoScreen() {
  const contacts = [
    { name: "Carl Mirabueno", phone: "09499226346" },
    { name: "Mj Punzalan", phone: "09568738785" },
    { name: "Mc Organo", phone: "09984789812" },
  ];


  return (
    <View style={{ flex: 1, backgroundColor: "#f4f4f4" }}>
      <StatusBar barStyle="dark-content" />


      {/* HEADER */}
      <View
        style={{
          flexDirection: "row",
          alignItems: "center",
          paddingVertical: 14,
          paddingHorizontal: 18,
          backgroundColor: "#f4f4f4",
          marginTop: Platform.OS === "ios" ? 40 : (StatusBar.currentHeight ?? 0) + 10,
        }}
      >
        <TouchableOpacity onPress={() => router.push('/service-provider/chatbox-team')}>
          <Ionicons name="arrow-back" size={22} color="#000" />
        </TouchableOpacity>
        <Text
          style={{
            fontSize: 15,
            fontWeight: "700",
            marginLeft: 10,
            color: "#000",
          }}
        >
          Contact Information
        </Text>
      </View>


      {/* CONTACT LIST */}
      <ScrollView
        style={{ flex: 1 }}
        contentContainerStyle={{
          paddingHorizontal: 14,
          paddingBottom: 20,
        }}
      >
        {contacts.map((contact, index) => (
          <View
            key={index}
            style={{
              flexDirection: "row",
              alignItems: "center",
              backgroundColor: "#e4e4e4",
              borderRadius: 10,
              marginBottom: 14,
              paddingVertical: 8,
              paddingHorizontal: 10,
              shadowColor: "#000",
              shadowOpacity: 0.08,
              shadowOffset: { width: 0, height: 2 },
              shadowRadius: 3,
              elevation: 1,
            }}
          >
            {/* Avatar box */}
            <View
              style={{
                width: 60,
                height: 60,
                borderRadius: 8,
                backgroundColor: "#fff",
                alignItems: "center",
                justifyContent: "center",
                marginRight: 12,
              }}
            >
              <Ionicons name="person-outline" size={26} color="#000" />
            </View>


            {/* Text (Name + Phone) */}
            <View style={{ flex: 1 }}>
              <Text
                style={{
                  fontSize: 15,
                  fontWeight: "700",
                  color: "#000",
                  marginBottom: 2,
                }}
              >
                {contact.name}
              </Text>
              <Text style={{ fontSize: 13.5, color: "#000" }}>
                {contact.phone}
              </Text>
            </View>


            {/* Call Icon */}
            <TouchableOpacity style={{ padding: 6 }}>
              <Ionicons name="call-outline" size={24} color="#000" />
            </TouchableOpacity>
          </View>
        ))}
      </ScrollView>
    </View>
  );
}
