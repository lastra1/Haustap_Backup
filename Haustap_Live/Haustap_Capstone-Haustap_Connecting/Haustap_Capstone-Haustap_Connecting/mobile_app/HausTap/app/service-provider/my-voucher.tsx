import { Ionicons } from '@expo/vector-icons'; // For icons like the back arrow
import { useRouter } from 'expo-router';
import React from 'react';
import {
    ScrollView,
    StyleSheet,
    Text,
    TouchableOpacity,
    View,
} from 'react-native';


export default function App() {
  const router = useRouter();
  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.push('/service-provider/my-account')} accessibilityLabel="Go back to My Account">
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>My Vouchers</Text>
      </View>


      <ScrollView style={styles.scrollView}>
        {/* Unlock Monthly Milestone Section */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Unlock your Monthly Milestone</Text>
          <Text style={styles.sectionDescription}>
            Once you complete 20 bookings, you'll receive a ₱50 voucher as a
            reward for your dedication and hard work.
          </Text>


          <View style={styles.milestoneCard}>
            <Text style={styles.milestoneCardTitle}>Complete 20 Bookings</Text>
            <View style={styles.milestoneGrid}>
              {[...Array(20)].map((_, i) => (
                <View
                  key={i}
                  style={[
                    styles.milestoneCircle,
                    i >= 19 && styles.milestoneCircleActive, // Example for the last circle
                  ]}
                >
                  {i >= 19 && (
                    <Text style={styles.milestoneCircleText}>₱50{'\n'}VOUCHER</Text>
                  )}
                </View>
              ))}
            </View>
            <Text style={styles.milestoneNote}>Must be completed within 2 months.</Text>
          </View>
          <Text style={styles.milestoneFinePrint}>
            You can apply your ₱50 voucher to your next subscription payment.
          </Text>
        </View>


        {/* Enjoy Exclusive Haustap Vouchers Section */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Enjoy Exclusive Haustap Vouchers</Text>


          {/* Welcome Voucher */}
          <View style={styles.voucherCard}>
            <Text style={styles.voucherCardTitle}>Welcome Voucher</Text>
            <Text style={styles.voucherCardSubtitle}>₱50 Bonus for New Service Providers</Text>
            <Text style={styles.voucherCardDescription}>
              New to Haustap? Complete your first month service and get a ₱50
              bonus as a welcome gift.
            </Text>
            <Text style={styles.voucherCardCondition}>Condition: First time users only</Text>
          </View>


          {/* Monthly Milestone Voucher */}
          <View style={styles.voucherCard}>
            <Text style={styles.voucherCardTitle}>Monthly Milestone</Text>
            <Text style={styles.voucherCardSubtitle}>₱50 Voucher after 20 Completed Bookings</Text>
            <Text style={styles.voucherCardDescription}>
              Stay active! Get a ₱50 reward after completing 20 bookings in a
              month.
            </Text>
            <Text style={styles.voucherCardCondition}>Condition: Must be completed within 2 months.</Text>
          </View>


          {/* Referral Bonus */}
          <View style={styles.voucherCard}>
            <Text style={styles.voucherCardTitle}>Referral Bonus</Text>
            <Text style={styles.voucherCardSubtitle}>Earn ₱5 for Every Successful Provider Referral</Text>
            <Text style={styles.voucherCardDescription}>
              Invite your fellow workers! When they complete their first job, you
              get ₱5 in credits.
            </Text>
            <Text style={styles.voucherCardCondition}>Condition: Referred provider must use your referral code and finish one booking.</Text>
          </View>
        </View>
      </ScrollView>
    </View>
  );
}


const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#ff', // Light gray background
    paddingTop: 40, // To account for status bar
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 15,
    backgroundColor: 'white',
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    marginLeft: 15,
  },
  scrollView: {
    flex: 1,
  },
  section: {
    padding: 15,
    marginBottom: 10,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  sectionDescription: {
    fontSize: 13,
    color: '#666',
    marginBottom: 15,
  },
  milestoneCard: {
    backgroundColor: 'white',
    borderRadius: 10,
    padding: 15,
    marginBottom: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.08,
    shadowRadius: 2,
    elevation: 2,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  milestoneCardTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  milestoneGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'flex-start',
    marginBottom: 10,
  },
  milestoneCircle: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: '#d9d9d9', // Light gray for inactive
    justifyContent: 'center',
    alignItems: 'center',
    margin: 5,
  },
  milestoneCircleActive: {
    backgroundColor: '#3dc1c6', // Green for active/voucher
  },
  milestoneCircleText: {
    color: 'white',
    fontSize: 10,
    fontWeight: 'bold',
    textAlign: 'center',
  },
  milestoneNote: {
    fontSize: 11,
    color: '#999',
    textAlign: 'right',
    marginTop: 5,
  },
  milestoneFinePrint: {
    fontSize: 11,
    color: '#999',
    marginTop: 5,
    textAlign: 'center',
  },
  voucherCard: {
    backgroundColor: 'white',
    borderRadius: 10,
    padding: 15,
    marginBottom: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.08,
    shadowRadius: 2,
    elevation: 2,
  },
  voucherCardTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    marginBottom: 3,
  },
  voucherCardSubtitle: {
    fontSize: 13,
    color: '#000', // Green color for bonus text
    fontWeight: 'bold',
    marginBottom: 5,
  },
  voucherCardDescription: {
    fontSize: 12,
    color: '#555',
    marginBottom: 5,
  },
  voucherCardCondition: {
    fontSize: 11,
    color: '#999',
    fontStyle: 'italic',
  },
});
