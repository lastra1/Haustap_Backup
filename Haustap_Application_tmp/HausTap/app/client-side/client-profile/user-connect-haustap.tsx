import { Ionicons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import React, { useState } from "react";
import {
    Linking,
    Platform,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";

export default function UserConnectHaustap() {
  const router = useRouter();
  const [firstName, setFirstName] = useState("");
  const [middleInitial, setMiddleInitial] = useState("");
  const [lastName, setLastName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [message, setMessage] = useState("");
  const [agree, setAgree] = useState(false);

  return (
    <ScrollView style={styles.container} contentContainerStyle={{ flexGrow: 1 }}>
      {/* Header */}
      <View style={styles.headerRow}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Connect Haustap</Text>
      </View>

      <Text style={styles.title}>Get in touch</Text>

      {/* Form */}
      <View style={styles.formRow}>
        <TextInput
          style={[styles.input, { flex: 2 }]}
          placeholder="First name"
          value={firstName}
          onChangeText={setFirstName}
        />
        <TextInput
          style={[styles.input, { flex: 1, marginHorizontal: 8 }]}
          placeholder="M.I"
          value={middleInitial}
          onChangeText={setMiddleInitial}
        />
        <TextInput
          style={[styles.input, { flex: 2 }]}
          placeholder="Last name"
          value={lastName}
          onChangeText={setLastName}
        />
      </View>
      <TextInput
        style={styles.input}
        placeholder="Email"
        value={email}
        onChangeText={setEmail}
        keyboardType="email-address"
        autoCapitalize="none"
      />
      <TextInput
        style={styles.input}
        placeholder="Phone Number"
        value={phone}
        onChangeText={setPhone}
        keyboardType="phone-pad"
      />
      <TextInput
        style={[styles.input, styles.messageInput]}
        placeholder="Message"
        value={message}
        onChangeText={setMessage}
        multiline
      />
      <View style={styles.checkboxRow}>
        <TouchableOpacity
          style={styles.customCheckbox}
          onPress={() => setAgree(!agree)}
          activeOpacity={0.7}
        >
          {agree && <View style={styles.checkboxTick} />}
        </TouchableOpacity>
        <Text style={styles.checkboxText}>
          I agree to our friendly{' '}
          <Text style={styles.link} onPress={() => Linking.openURL('https://your-privacy-policy-url.com')}>
            privacy policy
          </Text>
        </Text>
      </View>
      <TouchableOpacity style={styles.sendBtn} disabled={!agree}>
        <Text style={styles.sendBtnText}>Send Message</Text>
      </TouchableOpacity>

      {/* Social Icons */}
      <View style={styles.socialRow}>
        <TouchableOpacity onPress={() => Linking.openURL('https://facebook.com')}> 
          <Ionicons name="logo-facebook" size={40} color="#1877F3" />
        </TouchableOpacity>
        <TouchableOpacity onPress={() => Linking.openURL('https://instagram.com')} style={{ marginLeft: 32 }}>
          <Ionicons name="logo-instagram" size={40} color="#E1306C" />
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingHorizontal: 20,
    paddingTop: Platform.OS === 'ios' ? 44 : 20,
  },
  headerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 10,
  },
  backBtn: {
    padding: 8,
    marginRight: 8,
  },
  headerTitle: {
    fontSize: 16,
    fontWeight: '600',
  },
  title: {
    fontSize: 22,
    fontWeight: '700',
    marginBottom: 18,
    marginTop: 8,
  },
  formRow: {
    flexDirection: 'row',
    marginBottom: 12,
  },
  input: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#eee',
    borderRadius: 6,
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontSize: 15,
    marginBottom: 12,
  },
  messageInput: {
    minHeight: 80,
    textAlignVertical: 'top',
  },
  checkboxRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 18,
  },
  customCheckbox: {
    width: 20,
    height: 20,
    borderWidth: 1.5,
    borderColor: '#888',
    borderRadius: 4,
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
  },
  checkboxTick: {
    width: 12,
    height: 12,
    backgroundColor: '#00BDB2',
    borderRadius: 2,
  },
  checkboxText: {
    marginLeft: 8,
    fontSize: 13,
    color: '#333',
  },
  link: {
    color: '#00BDB2',
    textDecorationLine: 'underline',
  },
  sendBtn: {
    backgroundColor: '#00BDB2',
    borderRadius: 6,
    paddingVertical: 14,
    alignItems: 'center',
    marginBottom: 32,
    marginTop: 8,
  },
  sendBtnText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  socialRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 16,
    marginBottom: 32,
  },
});
