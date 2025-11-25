import { Ionicons } from "@expo/vector-icons";
import * as Clipboard from 'expo-clipboard';
import { useRouter } from "expo-router";
import React, { useState } from "react";
import { Alert, Platform, StyleSheet, Text, TextInput, TouchableOpacity, View } from "react-native";

export default function UserReferral() {
    const router = useRouter();
    const [referralCode, setReferralCode] = useState("");
    const userReferralCode = "6AYI6F"; // This would come from your user data/backend

    const copyToClipboard = async () => {
        await Clipboard.setStringAsync(userReferralCode);
        // You could show a toast/alert here to confirm copy
        Alert.alert("Copied", `Referral code ${userReferralCode} copied to clipboard`);
    };

    const handleSubmit = () => {
        // Handle submitting the referral code to your backend
        if (referralCode) {
            // Submit the code
            console.log('Submitting referral code:', referralCode);
            // You would make an API call here
            Alert.alert(
                "SUCCESS!!!",
                "You'll get your referral reward once the booking of your invitee is completed.",
                [{ text: "OK", onPress: () => setReferralCode("") }]
            );
        }
    };

    return (
        <View style={styles.container}>
            {/* Header */}
            <View style={styles.headerRow}>
                <TouchableOpacity 
                    style={styles.backBtn} 
                    onPress={() => router.back()}
                >
                    <Ionicons name="arrow-back" size={24} color="black" />
                </TouchableOpacity>
                <Text style={styles.headerTitle}>Referral</Text>
            </View>

            {/* Content */}
            <View style={styles.content}>
                <Text style={styles.label}>Your code:</Text>
                <View style={styles.codeContainer}>
                    <Text style={styles.codeText}>{userReferralCode}</Text>
                    <TouchableOpacity 
                        style={styles.copyButton}
                        onPress={copyToClipboard}
                    >
                        <Text style={styles.copyButtonText}>Copy</Text>
                    </TouchableOpacity>
                </View>

                <View style={styles.receivedSection}>
                    <Text style={styles.receivedText}>Received an invitation from a friend?</Text>
                    <Text style={styles.helperText}>Add the referral code you have received from your friend</Text>
                    <TextInput
                        style={styles.input}
                        value={referralCode}
                        onChangeText={setReferralCode}
                        placeholder="Enter referral code"
                    />
                    <TouchableOpacity 
                        style={[
                            styles.submitButton,
                            !referralCode && styles.submitButtonDisabled
                        ]}
                        onPress={handleSubmit}
                        disabled={!referralCode}
                    >
                        <Text style={[
                            styles.submitButtonText,
                            !referralCode && styles.submitButtonTextDisabled
                        ]}>Submit</Text>
                    </TouchableOpacity>
                </View>
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#fff',
    },
    headerRow: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingHorizontal: 16,
        paddingTop: Platform.OS === 'ios' ? 44 : 20,
        paddingBottom: 10,
        borderBottomWidth: 1,
        borderBottomColor: '#eee',
    },
    backBtn: {
        padding: 8,
        marginRight: 8,
    },
    headerTitle: {
        fontSize: 16,
        fontWeight: '600',
    },
    content: {
        padding: 16,
    },
    label: {
        fontSize: 14,
        color: '#000',
        marginBottom: 8,
    },
    codeContainer: {
        backgroundColor: '#E0F7F9',
        borderRadius: 8,
        padding: 16,
        marginBottom: 24,
    },
    codeText: {
        fontSize: 24,
        fontWeight: '600',
        color: '#000',
        textAlign: 'center',
        marginBottom: 12,
    },
    copyButton: {
        backgroundColor: '#fff',
        paddingVertical: 8,
        paddingHorizontal: 16,
        borderRadius: 4,
        alignSelf: 'center',
    },
    copyButtonText: {
        color: '#000',
        fontSize: 14,
    },
    receivedSection: {
        marginTop: 24,
    },
    receivedText: {
        fontSize: 14,
        color: '#000',
        marginBottom: 8,
    },
    helperText: {
        fontSize: 12,
        color: '#666',
        marginBottom: 16,
    },
    input: {
        borderWidth: 1,
        borderColor: '#ccc',
        borderRadius: 8,
        padding: 12,
        fontSize: 14,
        marginBottom: 16,
    },
    submitButton: {
        backgroundColor: '#3DC1C6',
        paddingVertical: 12,
        borderRadius: 8,
        alignItems: 'center',
    },
    submitButtonDisabled: {
        backgroundColor: '#E0F7F9',
    },
    submitButtonText: {
        color: '#fff',
        fontSize: 14,
        fontWeight: '600',
    },
    submitButtonTextDisabled: {
        color: '#666',
    },
});