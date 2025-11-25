import { Ionicons } from "@expo/vector-icons";
import { useLocalSearchParams, useRouter } from "expo-router";
import React, { useState } from "react";
import {
  Modal,
  ScrollView,
  StyleSheet,
  Switch,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from "react-native";
import { getUserRole } from '../../../src/config/apiConfig';

export default function BookingOverview() {
  const router = useRouter();
  const loading = false;
  const { categoryTitle, categoryPrice, categoryDesc, address, location, date, time, mainCategory, subCategory, voucherCode, voucherValue, selectedItems } = useLocalSearchParams();
  const [notes, setNotes] = useState("");
  const [agreement, setAgreement] = useState(false);
  const [showAgreementModal, setShowAgreementModal] = useState(false);
  // voucherValue comes as string param (e.g. "50"); parse to number
  const parsedVoucherValue = React.useMemo(() => {
    if (!voucherValue) return 0;
    try {
      const n = String(voucherValue).replace(/[^0-9.]/g, "");
      return Number(n) || 0;
    } catch {
      return 0;
    }
  }, [voucherValue]);

  const [voucherEnabled, setVoucherEnabled] = useState(Boolean(parsedVoucherValue));
  // keep selected voucher label in state so we can show its name in the overview
  const [selectedVoucherCode, setSelectedVoucherCode] = useState<string | null>(voucherCode ? String(voucherCode) : null);

  // sync when params change (e.g., after returning from voucher screen)
  React.useEffect(() => {
    setVoucherEnabled(Boolean(parsedVoucherValue));
    setSelectedVoucherCode(voucherCode ? String(voucherCode) : null);
  }, [parsedVoucherValue, voucherCode]);

  // when user toggles the switch on, open the voucher picker so they can choose
  const handleVoucherToggle = (value: boolean) => {
    if (value) {
      // open voucher selector; when user applies one, it will return voucherCode/voucherValue
      router.push({
          pathname: "/client-side/booking-process/booking-voucher",
          params: { categoryTitle, categoryPrice, categoryDesc, address, location, date, time, mainCategory, subCategory, selectedItems },
        } as any);
      return;
    }

    // turning off simply disables the voucher discount (keeps selection visible)
    // remove selected voucher when toggled off per request
    setVoucherEnabled(false);
    setSelectedVoucherCode(null);
  };

  // Placeholder subtotal calculation: use categoryPrice if available (strip currency)
  const parsePrice = (p: any) => {
    if (!p) return 0;
    try {
      const num = String(p).replace(/[^0-9.]/g, "");
      return Number(num) || 0;
    } catch {
      return 0;
    }
  };

  const subtotal = parsePrice(categoryPrice);
  const voucherDiscount = voucherEnabled ? parsedVoucherValue : 0;
  const transpoFee = 100;
  const total = Math.max(0, subtotal + transpoFee - voucherDiscount);

  const formatCurrency = (v: number) => `₱${v.toFixed(2)}`;

  return (
  <ScrollView style={styles.container} contentContainerStyle={styles.contentContainer} showsVerticalScrollIndicator={false}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Booking Overview</Text>
      </View>

      <Text style={styles.subtitle}>Your Booking overview</Text>

  <View style={styles.card}>
        <View style={styles.cardHeaderRow}>
          <View style={{ flex: 1 }}>
            <Text style={styles.mainCategory}>{mainCategory || "Indoor Services"}</Text>
            <Text style={styles.subcategory}>{(subCategory ? `${subCategory} - ` : "") + (categoryTitle || "Service")}</Text>
          </View>
          <View>
            <Text style={styles.priceText}>{categoryPrice ? `${categoryPrice}` : ""}</Text>
          </View>
        </View>

        <View style={styles.divider} />

  <View style={styles.rowSmall}>
          <View style={styles.rowCol}>
            <Text style={styles.metaLabel}>Date</Text>
            <Text style={styles.metaValue}>{date || "--"}</Text>
          </View>
          <View style={styles.vertSeparator} />
          <View style={styles.rowCol}>
            <Text style={styles.metaLabel}>Time</Text>
            <Text style={styles.metaValue}>{time || "--"}</Text>
          </View>
  </View>

        <View style={[styles.addressBox, { marginTop: 12 }]}>
          <Text style={styles.metaLabel}>Address</Text>
          <Text style={styles.addressText}>{address || location || "No address provided"}</Text>
        </View>

        <View style={[styles.selectedBox, { marginTop: 12 }]}>
          <Text style={[styles.metaLabel, { marginBottom: 6 }]}>You selected</Text>
          <Text style={styles.selectedTitle}>{categoryTitle || "Selected service"}</Text>
          {categoryDesc && (
            <Text style={styles.inclusions}>{categoryDesc}</Text>
          )}
        </View>

        <View style={{ marginTop: 12 }}>
          <Text style={styles.metaLabel}>Notes:</Text>
          <TextInput
            placeholder="Add notes for the provider"
            value={notes}
            onChangeText={setNotes}
            style={styles.notesInput}
            multiline
          />
        </View>
      </View>

      <View style={styles.voucherRow}>
        <TouchableOpacity
          style={styles.voucherLeft}
          onPress={() =>
            router.push({
              pathname: "/client-side/booking-process/booking-voucher",
              params: { categoryTitle, categoryPrice, categoryDesc, address, location, date, time, mainCategory, subCategory },
            } as any)
          }
        >
          <Ionicons name="pricetag-outline" size={18} color="#333" style={{ marginRight: 8 }} />
          <Text style={styles.voucherText}>{selectedVoucherCode ? selectedVoucherCode : "Add a Voucher"}</Text>
          {selectedVoucherCode && parsedVoucherValue > 0 && (
            <View style={styles.voucherBadge}>
              <Text style={styles.voucherBadgeText}>{formatCurrency(parsedVoucherValue)}</Text>
            </View>
          )}
        </TouchableOpacity>
        <Switch value={voucherEnabled} onValueChange={handleVoucherToggle} />
      </View>

      <View style={styles.totalsRow}>
        <View>
          <Text style={styles.smallLabel}>Sub Total</Text>
          <Text style={styles.smallLabel}>Voucher Discount</Text>
        </View>
        <View style={{ alignItems: "flex-end" }}>
          <Text style={styles.smallLabel}>{formatCurrency(subtotal)}</Text>
          <Text style={styles.smallLabel}>{voucherEnabled ? formatCurrency(voucherDiscount) : "₱0.00"}</Text>
        </View>
      </View>

      <View style={[styles.totalsRow, { marginTop: 6 }]}> 
        <Text style={styles.smallLabel}>Transpo fee</Text>
        <Text style={styles.smallLabel}>+₱100.00</Text>
      </View>

      <View style={styles.totalRow}>
        <Text style={styles.totalLabel}>TOTAL</Text>
        <Text style={styles.totalValue}>{formatCurrency(total)}</Text>
      </View>

      <Text style={styles.payNoteSmall}>Full payment will be collected directly by the service provider upon completion of the service.</Text>

      <View style={styles.agreementBlock}>
        <TouchableOpacity
          style={styles.checkboxRow}
          onPress={() => {
            // If not yet agreed, show the agreement UI/modal first
            if (!agreement) {
              setShowAgreementModal(true);
              return;
            }
            // If already agreed, allow unchecking
            setAgreement(false);
          }}
        >
          <View style={[styles.checkboxBox, agreement && styles.checkboxChecked]}>
            {agreement && <Ionicons name="checkmark" size={14} color="#fff" />}
          </View>
          <Text style={styles.agreementText}>Service Agreement</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.nextBtn, { opacity: agreement && !loading ? 1 : 0.5, alignSelf: 'stretch' }]}
          disabled={!agreement || loading}
          onPress={() => {
            (async () => {
              const role = await getUserRole();
              if (!role) {
                const s = (v?: string | string[]) => (Array.isArray(v) ? String(v[0]) : String(v ?? ""));
                const redirectPath =
                  `/client-side/booking-process/booking-choose-sp?categoryTitle=${encodeURIComponent(s(categoryTitle))}` +
                  `&categoryPrice=${encodeURIComponent(s(categoryPrice))}` +
                  `&categoryDesc=${encodeURIComponent(s(categoryDesc))}` +
                  `&address=${encodeURIComponent(s(address))}` +
                  `&location=${encodeURIComponent(s(location))}` +
                  `&date=${encodeURIComponent(s(date))}` +
                  `&time=${encodeURIComponent(s(time))}` +
                  `&mainCategory=${encodeURIComponent(s(mainCategory))}` +
                  `&subCategory=${encodeURIComponent(s(subCategory))}` +
                  (selectedItems ? `&selectedItems=${encodeURIComponent(String(selectedItems))}` : "");

                router.push(`/signup?redirect=${encodeURIComponent(redirectPath)}` as any);
                return;
              }

              router.push({
                pathname: '/client-side/booking-process/booking-choose-sp',
                params: {
                  categoryTitle,
                  categoryPrice,
                  categoryDesc,
                  address,
                  location,
                  date,
                  time,
                  mainCategory,
                  subCategory,
                  selectedItems,
                },
              } as any);
            })();
          }}
        >
          <Text style={styles.nextText}>Next</Text>
        </TouchableOpacity>
      </View>
      {/* Agreement Modal shown when user first taps the checkbox */}
      <Modal visible={showAgreementModal} animationType="slide">
        <View style={styles.modalContainer}>
          <View style={styles.modalHeader}>
            <Text style={styles.modalTitle}>HAUSTAP BOOKING SERVICE AGREEMENT</Text>
            <TouchableOpacity onPress={() => {
              // Closing the modal counts as agreeing
              setShowAgreementModal(false);
              setAgreement(true);
            }}>
              <Ionicons name="close" size={24} color="#666" />
            </TouchableOpacity>
          </View>
          <ScrollView contentContainerStyle={styles.modalContent}>
            <Text style={styles.modalText}>
              (Applicable to both Client and Service Provider){"\n\n"}
              1. Scope and Acknowledgment{"\n"}
              - By proceeding with a booking through the Haustap platform, both the Client and Service Provider acknowledge and agree to comply with the terms stated in this Service Agreement.{"\n\n"}
              - This agreement serves as a mutual understanding of responsibilities, service scope, and conduct throughout the booking process — from confirmation to completion.{"\n\n"}
              2. Additional Services During Inspection{"\n"}
              If the Client requests additional service(s) during the inspection, the following applies:{"\n"}
              a. The original inspection fee (if any) will be waived.{"\n"}
              b. The Client will only be charged the service fee for the additional service(s).{"\n"}
              The SP must accept and perform the additional service only if both parties agree, and the charge will follow the platform’s standard rate for that service.{"\n\n"}
              3. Outside Transactions{"\n"}
              - Haustap shall not be held liable for any transactions, agreements, or activities conducted outside the Haustap platform.{"\n"}
              - Any service arranged outside the system is considered unauthorized and not covered by Haustap’s protection, policies, or support.{"\n\n"}
              4. Accuracy of Booking Details{"\n"}
              - All information entered in the booking form must be true, accurate, and complete.{"\n"}
              - Any misinformation may result in service delays, invalid warranties, or cancellation of the booking prior to service.{"\n\n"}
              5. Communication{"\n"}
              - Both the Client and Service Provider must communicate clearly and respectfully regarding any concerns through Haustap’s messaging or contact channels.{"\n\n"}
              6. Transportation Fee{"\n"}
              - A Transportation Fee is charged to cover the Service Provider’s travel cost to and from the service location. This fee is visible upon booking confirmation and is included in the total bill.{"\n\n"}
              7. Payments{"\n"}
              - All payments are strictly handled in cash between the Client and the Service Provider after the service is completed.{"\n"}
              - The Client must pay the full agreed amount, including the transportation fee, directly to the Service Provider once the job is completed.{"\n\n"}
              8. Cancellation Policy{"\n"}
              Bookings may only be cancelled under specific conditions:{"\n"}
              - Clients may cancel a booking only while it is in “Pending” status. Once the booking is “Ongoing,” the Client no longer has the right to cancel.{"\n"}
              - Service Providers must not cancel a confirmed booking except under exceptional circumstances. Any such cancellation may affect their rating and/or result in penalties as outlined in this agreement.{"\n\n"}
              Cancellation Consequences{"\n"}
              To maintain fairness and reliability on the Haustap platform:{"\n\n"}
              - For Clients:{"\n"}
              1st–2nd cancellation: Recorded warning{"\n"}
              3rd cancellation: Temporary suspension (up to 3 days){"\n"}
              5th cancellation: Permanent account deactivation{"\n\n"}
              - For Service Providers:{"\n"}
              1st–2nd cancellation: Recorded warning and minor rating deduction{"\n"}
              3rd cancellation: Temporary suspension (up to 3 days){"\n"}
              5th cancellation: Permanent account deactivation{"\n\n"}
              9. Ongoing Service{"\n"}
              - The Service Provider must upload photo evidence (before and after) of the service performed.{"\n\n"}
              - Both parties are expected to act professionally and ensure that the service is conducted safely and satisfactorily.{"\n\n"}
              10. Completion and Reporting{"\n"}
              - When marking a booking as “Completed,” both the Client and Service Provider confirm that the report or feedback submitted is true and fair.{"\n"}
              - All reports or complaints will undergo review by Haustap’s admin before any actions are taken.{"\n\n"}
              11. Reporting Policy{"\n"}
              - Reports against either Client or Service Provider must be truthful and fair.{"\n"}
              - Haustap will investigate each report before making any decision or penalty.{"\n"}
              Any false report intended to damage another user’s reputation may result in penalties or suspension.{"\n\n"}
              12. Return Requests{"\n"}
              - Clients may request a return or re-service within 24 hours after service completion free of charge.{"\n"}
              - Requests made after 24 hours will incur a charge equal to 50% of the service fee.{"\n"}
              - All return requests will be reviewed by Haustap’s admin before approval.{"\n\n"}
              Once approved:{"\n"}
              - The Service Provider must complete the approved return within 7 days from the admin’s approval date.{"\n\n"}
              - Failure to comply within the given period will result in penalties as outlined in the agreement (warnings, suspension, or termination depending on severity).{"\n\n"}
              Haustap reserves the right to determine the severity of each case based on the frequency and nature of non-compliance.
            </Text>
          </ScrollView>
          <View style={styles.modalFooter}>
            <TouchableOpacity style={styles.modalCloseBtn} onPress={() => { setShowAgreementModal(false); setAgreement(true); }}>
              <Text style={styles.modalCloseText}>Close</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
    paddingHorizontal: 16,
    paddingTop: 40,
  },
  contentContainer: {
    paddingBottom: 100,
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 8,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "600",
    marginLeft: 10,
  },
  subtitle: {
    fontSize: 14,
    color: "#666",
    marginBottom: 12,
  },
  card: {
    backgroundColor: "#F5F5F5",
    borderRadius: 8,
    padding: 12,
    marginBottom: 12,
  },
  cardHeaderRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 8,
  },
  // serviceMain/serviceSub kept for backward compatibility; prefer mainCategory/subcategory styles
  serviceMain: {
    fontSize: 18,
    fontWeight: "700",
    color: "#111",
  },
  serviceSub: {
    fontSize: 14,
    color: "#666",
  },
  mainCategory: {
    fontSize: 20,
    fontWeight: "800",
    color: "#111",
  },
  subcategory: {
    fontSize: 16,
    color: "#666",
    marginTop: 4,
  },
  priceText: {
    fontSize: 16,
    fontWeight: "700",
    color: "#00ADB5",
  },
  divider: {
    height: 1,
    backgroundColor: "#E0E0E0",
    marginVertical: 12,
  },
  rowSmall: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  vertSeparator: {
    width: 1,
    backgroundColor: "#E0E0E0",
    marginHorizontal: 12,
    alignSelf: "stretch",
  },
  rowCol: {
    flex: 1,
  },
  metaLabel: {
    fontSize: 12,
    color: "#666",
  },
  metaValue: {
    fontSize: 14,
    fontWeight: "600",
    marginTop: 4,
  },
  addressBox: {
    backgroundColor: "#fff",
    padding: 10,
    borderRadius: 6,
    borderWidth: 1,
    borderColor: "#EAEAEA",
  },
  addressText: {
    fontSize: 13,
    color: "#333",
    marginTop: 6,
  },
  selectedBox: {
    backgroundColor: "#fff",
    padding: 10,
    borderRadius: 6,
    borderWidth: 1,
    borderColor: "#EAEAEA",
  },
  selectedTitle: {
    fontSize: 14,
    fontWeight: "700",
    marginBottom: 6,
  },
  inclusions: {
    fontSize: 13,
    color: "#444",
    lineHeight: 18,
  },
  notesInput: {
    backgroundColor: "#fff",
    borderRadius: 6,
    borderWidth: 1,
    borderColor: "#EAEAEA",
    padding: 10,
    minHeight: 60,
    marginTop: 6,
  },
  voucherBtn: {
    backgroundColor: "#fff",
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#E0E0E0",
    padding: 12,
    alignItems: "center",
    marginBottom: 12,
  },
  voucherText: {
    color: "#333",
    fontWeight: "600",
  },
  voucherRow: {
    backgroundColor: "#fff",
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#E0E0E0",
    paddingVertical: 8,
    paddingHorizontal: 12,
    marginBottom: 12,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
  },
  voucherLeft: {
    flexDirection: "row",
    alignItems: "center",
  },
  voucherBadge: {
    backgroundColor: "#00ADB5",
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
    marginLeft: 8,
  },
  voucherBadgeText: {
    color: "#fff",
    fontWeight: "700",
    fontSize: 12,
  },
  totalsRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingHorizontal: 4,
    marginBottom: 8,
  },
  smallLabel: {
    color: "#444",
    fontSize: 14,
    marginBottom: 6,
  },
  totalRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 4,
    marginBottom: 12,
  },
  totalLabel: {
    fontSize: 16,
    fontWeight: "700",
  },
  totalValue: {
    fontSize: 18,
    fontWeight: "700",
    color: "#111",
  },
  payNote: {
    fontSize: 12,
    color: "#666",
    marginBottom: 12,
  },
  payNoteSmall: {
    fontSize: 11,
    color: "#666",
    marginBottom: 12,
  },
  agreementRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginTop: 12,
    marginBottom: 24,
  },
  agreementBlock: {
    flexDirection: 'column',
    alignItems: 'flex-start',
    marginTop: 12,
    marginBottom: 24,
    width: '100%'
  },
  checkboxRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  checkbox: {
    flexDirection: "row",
    alignItems: "center",
  },
  checkboxBox: {
    width: 20,
    height: 20,
    borderWidth: 1,
    borderColor: "#CCC",
    borderRadius: 4,
    justifyContent: "center",
    alignItems: "center",
    marginRight: 8,
  },
  checkboxChecked: {
    backgroundColor: "#00ADB5",
    borderColor: "#00ADB5",
  },
  agreementText: {
    fontSize: 14,
    color: "#333",
  },
  nextBtn: {
    backgroundColor: "#00ADB5",
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: "center",
    marginBottom: 24,
  },
  nextText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "700",
  },
  /* Modal styles for agreement */
  modalContainer: {
    flex: 1,
    backgroundColor: '#fff',
  },
  modalHeader: {
    padding: 16,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: '700',
  },
  modalContent: {
    padding: 16,
  },
  modalText: {
    fontSize: 14,
    color: '#333',
    lineHeight: 20,
  },
  modalFooter: {
    padding: 16,
    borderTopWidth: 1,
    borderTopColor: '#eee',
  },
  modalCloseBtn: {
    alignSelf: 'flex-end',
    paddingVertical: 10,
    paddingHorizontal: 16,
  },
  modalCloseText: {
    color: '#00ADB5',
    fontWeight: '700',
  },
});
