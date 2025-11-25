import React, { useState } from "react";
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  ScrollView,
  StyleSheet,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { handleSignUp } from "../controllers/AuthController";

export default function SignUpScreen() {
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const [form, setForm] = useState({
    firstName: "",
    lastName: "",
    middleInitial: "",
    birthMonth: "",
    birthDay: "",
    birthYear: "",
    email: "",
    phone: "",
    password: "",
    confirmPassword: "",
  });

  const handleChange = (field, value) => {
    setForm({ ...form, [field]: value });
  };

  return (
    <ScrollView contentContainerStyle={styles.container}>
      {/* Logo */}
      <Text style={styles.logo}>🏠 HausTap</Text>
      <Text style={styles.title}>Create your account</Text>

      {/* Name Inputs */}
      <View style={styles.row}>
        <TextInput
          style={[styles.input, { flex: 1 }]}
          placeholder="First Name"
          value={form.firstName}
          onChangeText={(t) => handleChange("firstName", t)}
        />
        <TextInput
          style={[styles.input, { flex: 1, marginLeft: 5 }]}
          placeholder="Last Name"
          value={form.lastName}
          onChangeText={(t) => handleChange("lastName", t)}
        />
        <TextInput
          style={[styles.input, { width: 50, marginLeft: 5 }]}
          placeholder="M.I."
          maxLength={1}
          value={form.middleInitial}
          onChangeText={(t) => handleChange("middleInitial", t)}
        />
      </View>

      {/* Birthday */}
      <View style={styles.row}>
        <TextInput
          style={[styles.input, { flex: 1 }]}
          placeholder="MM"
          keyboardType="numeric"
          maxLength={2}
          value={form.birthMonth}
          onChangeText={(t) => handleChange("birthMonth", t)}
        />
        <TextInput
          style={[styles.input, { flex: 1, marginLeft: 5 }]}
          placeholder="DD"
          keyboardType="numeric"
          maxLength={2}
          value={form.birthDay}
          onChangeText={(t) => handleChange("birthDay", t)}
        />
        <TextInput
          style={[styles.input, { flex: 2, marginLeft: 5 }]}
          placeholder="YYYY"
          keyboardType="numeric"
          maxLength={4}
          value={form.birthYear}
          onChangeText={(t) => handleChange("birthYear", t)}
        />
      </View>

      {/* Email */}
      <TextInput
        style={styles.input}
        placeholder="Email Address"
        keyboardType="email-address"
        value={form.email}
        onChangeText={(t) => handleChange("email", t)}
      />

      {/* Phone */}
      <TextInput
        style={styles.input}
        placeholder="Mobile Number"
        keyboardType="phone-pad"
        value={form.phone}
        onChangeText={(t) => handleChange("phone", t)}
      />

      {/* Password */}
      <View style={styles.passwordContainer}>
        <TextInput
          style={[styles.input, { flex: 1, borderWidth: 0 }]}
          placeholder="Password"
          secureTextEntry={!showPassword}
          value={form.password}
          onChangeText={(t) => handleChange("password", t)}
        />
        <TouchableOpacity onPress={() => setShowPassword(!showPassword)}>
          <Ionicons
            name={showPassword ? "eye" : "eye-off"}
            size={22}
            color="#666"
          />
        </TouchableOpacity>
      </View>

      {/* Confirm Password */}
      <View style={styles.passwordContainer}>
        <TextInput
          style={[styles.input, { flex: 1, borderWidth: 0 }]}
          placeholder="Confirm Password"
          secureTextEntry={!showConfirmPassword}
          value={form.confirmPassword}
          onChangeText={(t) => handleChange("confirmPassword", t)}
        />
        <TouchableOpacity
          onPress={() => setShowConfirmPassword(!showConfirmPassword)}
        >
          <Ionicons
            name={showConfirmPassword ? "eye" : "eye-off"}
            size={22}
            color="#666"
          />
        </TouchableOpacity>
      </View>

      {/* Sign Up Button */}
      <TouchableOpacity
        style={styles.signUpButton}
        onPress={() => handleSignUp(form)}
      >
        <Text style={styles.signUpText}>Sign Up</Text>
      </TouchableOpacity>

      {/* Footer */}
      <Text style={styles.footerText}>
        By signing up, you agree to our{" "}
        <Text style={styles.link}>Terms & Conditions</Text> and{" "}
        <Text style={styles.link}>Privacy Policy</Text>.
      </Text>

      <TouchableOpacity style={styles.partnerButton}>
        <Text style={styles.partnerText}>Become a HausTap Partner</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flexGrow: 1,
    padding: 20,
    backgroundColor: "#F7F9FA",
    alignItems: "center",
  },
  logo: {
    fontSize: 28,
    color: "#00BFA6",
    marginTop: 40,
    fontWeight: "bold",
  },
  title: {
    fontSize: 18,
    marginVertical: 10,
    color: "#00BFA6",
    fontWeight: "600",
  },
  row: {
    flexDirection: "row",
    width: "100%",
    marginBottom: 10,
  },
  input: {
    borderWidth: 1,
    borderColor: "#CCC",
    borderRadius: 8,
    padding: 10,
    backgroundColor: "#FFF",
    width: "100%",
    marginBottom: 10,
  },
  passwordContainer: {
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#CCC",
    borderRadius: 8,
    paddingHorizontal: 10,
    backgroundColor: "#FFF",
    marginBottom: 10,
    width: "100%",
  },
  signUpButton: {
    backgroundColor: "#00BFA6",
    paddingVertical: 12,
    borderRadius: 8,
    width: "100%",
    alignItems: "center",
    marginTop: 10,
  },
  signUpText: {
    color: "#FFF",
    fontWeight: "bold",
    fontSize: 16,
  },
  footerText: {
    textAlign: "center",
    color: "#888",
    fontSize: 12,
    marginVertical: 15,
    paddingHorizontal: 10,
  },
  link: {
    color: "#00BFA6",
    fontWeight: "500",
  },
  partnerButton: {
    backgroundColor: "#D8F6F1",
    paddingVertical: 12,
    borderRadius: 8,
    width: "100%",
    alignItems: "center",
  },
  partnerText: {
    color: "#00BFA6",
    fontWeight: "bold",
  },
});
