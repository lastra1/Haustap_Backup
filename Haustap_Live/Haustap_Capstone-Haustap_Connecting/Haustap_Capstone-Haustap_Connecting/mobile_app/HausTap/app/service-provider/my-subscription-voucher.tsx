import { Ionicons, MaterialCommunityIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import {
    ScrollView,
    StyleSheet,
    Text,
    TouchableOpacity,
    View,
} from 'react-native';


// --- DEFINED COLORS ---
const HAUSTAP_TURQUOISE = '#39ceb8';
const HAUSTAP_GRAY = '#666'; 
const HAUSTAP_CYAN = '#3dc1c6'; // Pinalitan ang HAUSTAP_GREEN ng bagong kulay: #3dc1c6
const HAUSTAP_RED = '#f44336'; 
// ----------------------


// Base Price
const SUB_TOTAL = 499.00;


// Voucher type and Data List 
type Voucher = {
  id: string;
  title: string;
  subtitle: string;
  description: string;
  condition: string;
  discount: number;
};

const VOUCHERS: Voucher[] = [
    { id: 'V001', title: 'Welcome Voucher', subtitle: '₱50 Bonus for New Service Providers', description: 'New to Haustap? Complete your first month service and get a ₱50 bonus as a welcome gift.', condition: 'Condition: First time users only', discount: 50.00 },
    { id: 'V002', title: 'Monthly Milestone', subtitle: '₱50 Voucher after 20 Completed Bookings', description: 'Stay active! Get a ₱50 reward after completing 20 bookings in a month.', condition: 'Condition: Must be completed within 2 months.', discount: 50.00 }, 
    { 
        id: 'V003', 
        title: 'Referral Bonus', 
        subtitle: '₱5 Discount for Referrals', 
        description: 'Invite your fellow workers! When they complete their first job, you get ₱5 in credits.', 
        condition: 'Condition: Referred provider must use your referral code and finish one booking.', 
        discount: 5.00 
    },
];


// --- Voucher Card Component ---
type VoucherCardProps = {
  voucher: Voucher;
  onSelect: (v: Voucher) => void;
  isSelected: boolean;
};

const VoucherCard: React.FC<VoucherCardProps> = ({ voucher, onSelect, isSelected }) => (
  <TouchableOpacity 
    style={[
      VoucherStyles.voucherCard, 
      isSelected && VoucherStyles.voucherCardSelected 
    ]}
    onPress={() => onSelect(voucher)}
  >
    <View style={VoucherStyles.voucherCardContent}>
        <View style={{ flex: 1 }}>
            <Text style={VoucherStyles.voucherCardTitle}>{voucher.title}</Text>
            {/* Ginamit ang HAUSTAP_CYAN dito */}
            <Text style={[VoucherStyles.voucherCardSubtitle, { color: HAUSTAP_CYAN }]}>{voucher.subtitle}</Text> 
        </View>
        {isSelected ? (
            <Ionicons name="checkmark-circle" size={24} color={HAUSTAP_TURQUOISE} />
        ) : (
            <View style={{ width: 24, height: 24 }} /> 
        )}
    </View>
    <Text style={VoucherStyles.voucherCardDescription}>{voucher.description}</Text>
    <Text style={VoucherStyles.voucherCardCondition}>{voucher.condition}</Text>
  </TouchableOpacity>
);
// -------------------------------------------------------------


export default function App() {
  const [showVouchers, setShowVouchers] = useState(false);
  const [selectedVoucher, setSelectedVoucher] = useState<Voucher | null>(null);
  
  // Computed values
  const discountAmount = selectedVoucher ? selectedVoucher.discount : 0.0;
  const finalTotal = SUB_TOTAL - discountAmount;


  const toggleVouchers = () => {
    if (!selectedVoucher) {
        setShowVouchers(!showVouchers);
    }
  };
  
  const handleVoucherSelect = (voucher: Voucher) => {
    setSelectedVoucher(voucher);
    setShowVouchers(false);
  };


  const handleVoucherRemove = () => {
      setSelectedVoucher(null);
      setShowVouchers(false);
  };


  const router = useRouter();

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => router.push('/service-provider/payment-method')}
          accessibilityLabel="Go back to Payment Methods"
        >
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>My Subscription</Text>
      </View>


      <ScrollView style={styles.scrollView}>
        
        {/* === Subscription Details Panel === */}
        <View style={styles.subscriptionPanel}>
          
          {/* Top Info */}
          <Text style={styles.planLabel}>Plan Name: <Text style={styles.planDetail}>Haustap Partner Plan</Text></Text>
          <Text style={styles.planLabel}>Subscription ID: <Text style={styles.planDetail}>0123</Text></Text>
          <Text style={styles.planLabel}>Duration: <Text style={styles.planDetail}>30 Days</Text></Text>
          <Text style={styles.planLabel}>Start Date: <Text style={styles.planDetail}>October 1, 2025</Text></Text>
          <Text style={styles.planLabel}>Expiration Date: <Text style={styles.planDetail}>October 31, 202ET</Text></Text>


          <View style={styles.horizontalSpacer} />


          {/* Payment Method */}
          <View style={styles.paymentMethodRow}>
            <MaterialCommunityIcons name="google" size={18} color="#4285F4" />
            <Text style={styles.paymentMethodText}>63-09***36246</Text>
          </View>
          
          {/* ADD A VOUCHER BOX */}
          <TouchableOpacity 
            style={[
                styles.addVoucherBox,
                selectedVoucher && styles.addVoucherBoxActive 
            ]}
            onPress={selectedVoucher ? handleVoucherRemove : toggleVouchers} 
          >
            {selectedVoucher ? (
                // Display Active Voucher Info
                <View style={styles.addVoucherContent}>
                    <MaterialCommunityIcons name="tag" size={20} color={HAUSTAP_TURQUOISE} />
                    <View style={{marginLeft: 10}}>
                        <Text style={[styles.addVoucherText, { color: HAUSTAP_TURQUOISE }]}>
                            {selectedVoucher.title}
                        </Text>
                        <Text style={{fontSize: 12, color: HAUSTAP_TURQUOISE}}>
                            (Less ₱{selectedVoucher.discount.toFixed(2)})
                        </Text>
                    </View>
                </View>
            ) : (
                // Display Default "Add a Voucher"
                <View style={styles.addVoucherContent}>
                    <MaterialCommunityIcons name="tag-outline" size={20} color="#333" />
                    <Text style={styles.addVoucherText}>Add a Voucher</Text>
                </View>
            )}


            {/* Icon for Action: Trash/Remove or Arrow */}
            {selectedVoucher ? (
                <Ionicons name="trash-bin-outline" size={20} color={HAUSTAP_RED} />
            ) : (
                <Ionicons 
                    name={showVouchers ? "chevron-up" : "chevron-forward"} 
                    size={20} 
                    color="#777" 
                />
            )}
            
          </TouchableOpacity>


          {/* === CONDITIONAL VOUCHER LIST === */}
          {showVouchers && !selectedVoucher && (
            <View style={styles.voucherListContainer}>
                <Text style={VoucherStyles.vouchersTitle}>Vouchers</Text>
                
                {VOUCHERS.map((voucher) => (
                    <VoucherCard
                        key={voucher.id}
                        voucher={voucher}
                        onSelect={handleVoucherSelect}
                        isSelected={false} 
                    />
                ))}
            </View>
          )}
          {/* === END CONDITIONAL VOUCHER LIST === */}


          {/* Separator line sa ibaba ng Voucher Box / Voucher List */}
          <View style={styles.voucherSeparator} />


          {/* Pricing Breakdown */}
          <View style={styles.pricingRow}>
            <Text style={styles.pricingLabel}>Sub Total</Text>
            <Text style={styles.pricingValue}>₱{SUB_TOTAL.toFixed(2)}</Text>
          </View>
          <View style={styles.pricingRow}>
            <Text style={styles.pricingLabel}>Less Voucher Discount</Text>
            <Text style={styles.pricingDiscount}>- ₱{discountAmount.toFixed(2)}</Text>
          </View>


          <View style={styles.totalRow}>
            <Text style={styles.totalLabel}>TOTAL</Text>
            <Text style={styles.totalValue}>₱{finalTotal.toFixed(2)}</Text>
          </View>
          
          {/* PAY NOW BUTTON */}
          <View style={styles.payNowContainerInside}>
              <TouchableOpacity style={styles.payNowButtonNarrow}>
                <Text style={styles.payNowButtonText}>Pay Now</Text>
              </TouchableOpacity>
          </View>
          
        </View>
        
      </ScrollView>
    </View>
  );
}


// --- Stylesheet for Main UI ---
const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: 'white',
    paddingTop: 40,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 15,
    paddingVertical: 10,
    borderBottomColor: '#eee',
  },
  backButton: {
    paddingRight: 15,
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: 'bold',
  },
  scrollView: {
    flex: 1,
  },
  
  subscriptionPanel: {
    margin: 20,
    padding: 15,
    borderWidth: 2,
    borderColor: '#00abb1',
    backgroundColor: '#d9d9d9',
    borderRadius: 8,
    marginBottom: 10,
  },
  planLabel: {
    fontSize: 14,
    color: HAUSTAP_GRAY,
    marginBottom: 3,
  },
  planDetail: {
    color: 'black',
    fontWeight: 'bold',
  },
  horizontalSpacer: {
    height: 1,
    backgroundColor: '#a6a6a6',
    marginVertical: 10,
  },
  paymentMethodRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 5,
  },
  paymentMethodText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#000',
    marginLeft: 8,
  },
  addVoucherBox: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: 'white',
    borderWidth: 1,
    borderColor: '#a6a6a6',
    borderRadius: 8,
    padding: 10,
    marginVertical: 10,
  },
  addVoucherBoxActive: {
      borderColor: HAUSTAP_TURQUOISE, 
      borderWidth: 2,
  },
  addVoucherContent: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  addVoucherText: {
    fontSize: 14,
    color: '#333',
    fontWeight: 'bold',
    marginLeft: 10,
  },
  voucherListContainer: {
    marginTop: 10,
    paddingHorizontal: 5,
  },
  voucherSeparator: {
    height: 1,
    backgroundColor: '#d9d9d9',
    marginBottom: 5,
    marginTop: 5,
  },
  pricingRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 5,
  },
  pricingLabel: {
    fontSize: 14,
    color: HAUSTAP_GRAY,
  },
  pricingValue: {
    fontSize: 14,
    color: HAUSTAP_GRAY,
    textAlign: 'right',
  },
  pricingDiscount: {
    fontSize: 14,
    color: 'red',
    textAlign: 'right',
  },
  totalRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 10,
    borderTopWidth: 1,
    borderTopColor: '#a6a6a6',
    paddingTop: 8,
    marginBottom: 15,
  },
  totalLabel: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  totalValue: {
    fontSize: 18,
    fontWeight: 'bold',
    textAlign: 'right',
  },
  payNowContainerInside: {
    alignItems: 'flex-end',
    marginTop: 5,
  },
  payNowButtonNarrow: {
    backgroundColor: '#3dc1c6',
    borderRadius: 8,
    paddingVertical: 10, 
    paddingHorizontal: 20, 
    alignItems: 'center',
  },
  payNowButtonText: {
    color: 'black',
    fontSize: 14,
    fontWeight: 'bold',
  },
});


// --- Stylesheet for Voucher Cards (Pinaghiwalay para sa clarity) ---
const VoucherStyles = StyleSheet.create({
    vouchersTitle: {
        fontSize: 16,
        fontWeight: 'bold',
        marginBottom: 10,
    },
    voucherCard: {
      backgroundColor: 'white',
      borderRadius: 8,
      borderWidth: 1,
      borderColor: '#eee',
      padding: 15,
      marginBottom: 10,
      shadowColor: '#000',
      shadowOffset: { width: 0, height: 1 },
      shadowOpacity: 0.08,
      shadowRadius: 2,
      elevation: 2,
    },
    voucherCardSelected: {
        borderColor: HAUSTAP_TURQUOISE, 
        borderWidth: 2,
    },
    voucherCardContent: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'flex-start',
    },
    voucherCardTitle: {
      fontSize: 14,
      fontWeight: 'bold',
      marginBottom: 3,
    },
    voucherCardSubtitle: {
      fontSize: 13,
      fontWeight: 'bold',
      marginBottom: 5,
      // Ang kulay ay nanggagaling sa inline style na gumagamit ng HAUSTAP_CYAN
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
