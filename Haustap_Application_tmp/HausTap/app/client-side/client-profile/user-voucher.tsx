import { Ionicons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import React from "react";
import { Platform, ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";

export default function UserVoucher() {
    const router = useRouter();

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
                <Text style={styles.headerTitle}>My Vouchers</Text>
            </View>

            {/* (removed top progress dots to match design) */}

            {/* Content */}
            <ScrollView style={styles.content} showsVerticalScrollIndicator={false}>
                {/* Loyalty Bonus Header / Progress Card (from design) */}
                <View style={styles.loyaltyContainer}>
                    <Text style={styles.loyaltyTitle}>Unlock your Loyalty Bonus</Text>
                    <Text style={styles.loyaltySubtext}>Once you complete the remaining bookings you can enjoy a ₱50 voucher</Text>

                    <View style={styles.loyaltyCard}>
                        <Text style={styles.loyaltyCardTitle}>Complete 10 Bookings</Text>
                        <View style={styles.dotsRow}>
                            <View style={[styles.dot, styles.dotFilled]} />
                            <View style={[styles.dot, styles.dotFilled]} />
                            <View style={[styles.dot, styles.dotFilled]} />
                            <View style={styles.dot} />
                            <View style={styles.dot} />
                            <View style={styles.dot} />
                            <View style={styles.dot} />
                            <View style={styles.dot} />
                            <View style={styles.dot} />
                            <View style={styles.dot} />
                        </View>
                        <Text style={styles.loyaltyCardFooter}>Complete within 3 months to earn your ₱50 voucher</Text>
                    </View>

                    <Text style={styles.loyaltyBottomText}>Complete your 10 remaining bookings to earn a ₱50 voucher</Text>
                </View>

                <Text style={styles.subtitle}>Enjoy Exclusive HAUSTAP Vouchers</Text>

                {/* P50 OFF First-Time Users */}
                <View style={styles.voucherCard}>
                    <Text style={styles.voucherTitle}>P50 OFF for First-Time Users</Text>
                    <Text style={styles.voucherDescription}>
                        New here? Book your first service today and Enjoy P50 OFF
                    </Text>
                    <Text style={styles.voucherCondition}>
                        Get P50 Voucher when you complete first booking
                    </Text>
                </View>

                {/* Loyalty Bonus */}
                <View style={styles.voucherCard}>
                    <Text style={styles.voucherTitle}>Loyalty Bonus</Text>
                    <Text style={styles.voucherDescription}>
                        Loyal Customer gets to save after 10 completed bookings
                    </Text>
                    <Text style={styles.voucherCondition}>
                        Book + P50 bonus when at least you hit the mark with bookings
                    </Text>
                </View>

                {/* Referral Bonus */}
                <View style={styles.voucherCard}>
                    <Text style={styles.voucherTitle}>Referral Bonus</Text>
                    <Text style={styles.voucherDescription}>
                        Save HAUSTAP with friends! Deal your friend complete their booking, you can P50 voucher
                    </Text>
                    <Text style={styles.voucherCondition}>
                        Condition: You will be notified after you r friend's first successful booking
                    </Text>
                </View>
            </ScrollView>
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
    /* top progress dots were removed */
    content: {
        flex: 1,
        paddingHorizontal: 16,
    },
    subtitle: {
        fontSize: 18,
        fontWeight: '600',
        marginBottom: 20,
        textAlign: 'center',
    },
    voucherCard: {
        backgroundColor: '#fff',
        borderRadius: 8,
        padding: 16,
        marginBottom: 16,
        shadowColor: "#000",
        shadowOffset: {
            width: 0,
            height: 2,
        },
        shadowOpacity: 0.1,
        shadowRadius: 4,
        elevation: 2,
        borderWidth: 1,
        borderColor: '#eee',
    },
    voucherTitle: {
        fontSize: 16,
        fontWeight: '600',
        marginBottom: 8,
    },
    voucherDescription: {
        fontSize: 14,
        color: '#333',
        marginBottom: 8,
    },
    voucherCondition: {
        fontSize: 12,
        color: '#666',
        fontStyle: 'italic',
    },
    loyaltyContainer: {
        marginBottom: 20,
        paddingHorizontal: 4,
        alignItems: 'center',
    },
    loyaltyTitle: {
        fontSize: 18,
        fontWeight: '700',
        marginBottom: 6,
        alignSelf: 'flex-start',
    },
    loyaltySubtext: {
        fontSize: 13,
        color: '#666',
        marginBottom: 12,
        alignSelf: 'flex-start',
    },
    loyaltyCard: {
        width: '100%',
        backgroundColor: '#fff',
        borderRadius: 12,
        paddingVertical: 18,
        paddingHorizontal: 16,
        borderWidth: 1,
        borderColor: '#eee',
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.08,
        shadowRadius: 6,
        elevation: 2,
        marginBottom: 12,
        alignItems: 'center',
    },
    loyaltyCardTitle: {
        fontSize: 16,
        fontWeight: '700',
        marginBottom: 12,
    },
    dotsRow: {
        flexDirection: 'row',
        justifyContent: 'center',
        alignItems: 'center',
        flexWrap: 'wrap',
        marginBottom: 12,
        paddingHorizontal: 6,
    },
    dot: {
        width: 16,
        height: 16,
        borderRadius: 8,
        borderWidth: 2,
        borderColor: '#D9D9D9',
        backgroundColor: 'transparent',
        marginHorizontal: 6,
        marginVertical: 6,
    },
    dotFilled: {
        backgroundColor: '#00BDB2',
        borderColor: '#00BDB2',
    },
    loyaltyCardFooter: {
        fontSize: 12,
        color: '#666',
    },
    loyaltyBottomText: {
        marginTop: 8,
        fontSize: 14,
        color: '#333',
        alignSelf: 'flex-start',
    },
});
