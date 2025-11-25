import { Ionicons } from "@expo/vector-icons";
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useFocusEffect } from '@react-navigation/native';
import { LinearGradient } from "expo-linear-gradient";
import { useRouter } from "expo-router";
import React from "react";
import {
  Image,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from "react-native";
import { useBookingSelection } from '../context/BookingSelectionContext';
import CategoryButton from "./components/CategoryButton";
import NotificationPopup from './notification/user-notif-popup';

export default function ClientHomeScreen() {
  const router = useRouter();
  const [search, setSearch] = React.useState("");
  const [showNotifications, setShowNotifications] = React.useState(false);
  const [unreadCount, setUnreadCount] = React.useState(0);
  const { clearAll } = useBookingSelection();

  const searchIndex = [
    // Top-level categories
    { title: "Cleaning Services", route: "/client-side/cleaning-services" },
    { title: "Indoor Services", route: "/client-side/indoor-services" },
    { title: "Outdoor Services", route: "/client-side/outdoor-services" },
    { title: "Beauty Services", route: "/client-side/beauty-services" },
    { title: "Wellness Services", route: "/client-side/wellness-services" },
    { title: "Tech & Gadget Services", route: "/client-side/tech-gadget-services" },

    // Home cleaning subpages
    { title: "Bungalow", route: "/client-side/cleaning-services/homeCleaning/bungalow" },
    { title: "Condominium Studio / 1BR", route: "/client-side/cleaning-services/homeCleaning/condo-studio" },
    { title: "Condominium 2BR", route: "/client-side/cleaning-services/homeCleaning/condominium2br" },
    { title: "Condominium Penthouse", route: "/client-side/cleaning-services/homeCleaning/penthouse" },
    { title: "Duplex Smaller", route: "/client-side/cleaning-services/homeCleaning/duplex-smaller" },
    { title: "Duplex Larger", route: "/client-side/cleaning-services/homeCleaning/duplex-larger" },
    { title: "Container House Single", route: "/client-side/cleaning-services/homeCleaning/container-house-single" },
    { title: "Container House Multiple", route: "/client-side/cleaning-services/homeCleaning/container-house-multiple" },
    { title: "Stilt House Small", route: "/client-side/cleaning-services/homeCleaning/stilt-house-small" },
    { title: "Stilt House Large", route: "/client-side/cleaning-services/homeCleaning/stilt-house-large" },
    { title: "Mansion Smaller", route: "/client-side/cleaning-services/homeCleaning/mansion-smaller" },
    { title: "Mansion Larger", route: "/client-side/cleaning-services/homeCleaning/mansion-larger" },
    { title: "Villa Smaller", route: "/client-side/cleaning-services/homeCleaning/villa-smaller" },
    { title: "Villa Larger", route: "/client-side/cleaning-services/homeCleaning/villa-larger" },

    // AC cleaning
    { title: "AC Cleaning", route: "/client-side/cleaning-services/ACcleaning" },
    { title: "AC Deep Cleaning", route: "/client-side/cleaning-services/ACdeepCleaning" },

    // Beauty
    { title: "Hair Services", route: "/client-side/beauty-services/hair" },
    { title: "Nail Care", route: "/client-side/beauty-services/nailCare" },
    { title: "Nails", route: "/client-side/beauty-services/nails" },
    { title: "Make-up", route: "/client-side/beauty-services/makeup" },
    { title: "Lashes", route: "/client-side/beauty-services/lashes" },
    { title: "Beauty Packages", route: "/client-side/beauty-services/packages" },

    // Wellness
    { title: "Massage", route: "/client-side/wellness-services/massage" },
    { title: "Wellness Packages", route: "/client-side/wellness-services/packages" },
    { title: "Spa", route: "/client-side/wellness-services/spa" },
    { title: "Therapy", route: "/client-side/wellness-services/therapy" },

    // Tech & Gadget
    { title: "Mobile Phone", route: "/client-side/tech-gadget-services/mobile" },
    { title: "Laptop & Desktop PC", route: "/client-side/tech-gadget-services/computer" },
    { title: "Tablet & iPad", route: "/client-side/tech-gadget-services/tablet" },
    { title: "Game & Console", route: "/client-side/tech-gadget-services/gaming" },

    // Indoor services (examples)
    { title: "Appliance Repair", route: "/client-side/indoor-services/applianceRepair" },
    { title: "Electrical", route: "/client-side/indoor-services/electrical" },
    { title: "Handyman", route: "/client-side/indoor-services/handyman" },
    { title: "Pest Control (Indoor)", route: "/client-side/indoor-services/pestControl" },
    { title: "Plumbing", route: "/client-side/indoor-services/plumbing" },
  ];

  const results = search.trim()
    ? searchIndex.filter(item => item.title.toLowerCase().includes(search.trim().toLowerCase()))
    : [];

  useFocusEffect(
    React.useCallback(() => {
      let mounted = true;
      const loadCount = async () => {
        try {
          const raw = await AsyncStorage.getItem('HT_notifications');
          if (!raw) {
            setUnreadCount(0);
            return;
          }
          const parsed = JSON.parse(raw) as Array<{ isRead?: boolean }>;
          if (mounted) setUnreadCount(parsed.filter(n => !n.isRead).length);
        } catch (e) {
          console.warn('Failed to load notifications count', e);
        }
      };
      // Clear any in-memory selected services when returning to the client home
      try {
        clearAll();
      } catch (e) {
        // ignore if provider not mounted
      }
      loadCount();
      return () => { mounted = false; };
    }, [])
  );
  return (
    <View style={{ flex: 1, backgroundColor: "#f8f9fa" }}>
      <ScrollView contentContainerStyle={styles.container}>
        {/* Header Section */}
        <View style={styles.headerWrapper}>
          <Image
            source={require("../../assets/images/homepage.jpg")}
            style={styles.headerImage}
          />

          {/* Notification Icon */}
          <TouchableOpacity style={styles.notificationIcon} onPress={() => setShowNotifications(true)}>
            <Ionicons name="notifications-outline" size={24} color="#3DC1C6" />
            {unreadCount > 0 ? (
              <View style={styles.notificationBadge}>
                <Text style={styles.notificationBadgeText}>{unreadCount > 99 ? '99+' : String(unreadCount)}</Text>
              </View>
            ) : (
              <View style={styles.notificationDot} />
            )}
          </TouchableOpacity>
          <NotificationPopup visible={showNotifications} onClose={() => setShowNotifications(false)} />

          {/* Search Bar Overlay */}
          <View style={styles.searchContainer}>
            <TextInput
              placeholder="Search services"
              placeholderTextColor="#888"
              style={styles.searchInput}
              value={search}
              onChangeText={setSearch}
              returnKeyType="search"
            />
            <Ionicons name="search" size={20} color="#888" />
          </View>

          {/* Search results dropdown */}
          {results.length > 0 && (
            <View style={styles.searchResults}>
              {results.slice(0, 8).map((r) => (
                <TouchableOpacity
                  key={r.route}
                  style={styles.resultItem}
                  onPress={() => {
                    setSearch("");
                    router.push(r.route as any);
                  }}
                >
                  <Text style={styles.resultText}>{r.title}</Text>
                </TouchableOpacity>
              ))}
            </View>
          )}
        </View>

        {/* Categories */}
        <Text style={styles.sectionTitle}>Categories</Text>
        <View style={styles.categoriesContainer}>
          {[
            "Cleaning Services",
            "Indoor Services",
            "Outdoor Services",
            "Beauty Services",
            "Wellness Services",
            "Tech & Gadget Services",
          ].map((category) => (
            <CategoryButton
              key={category}
              title={category}
              onPress={() => {
                switch(category) {
                  case "Cleaning Services":
                    router.push("/client-side/cleaning-services");
                    break;
                  case "Indoor Services":
                    router.push("/client-side/indoor-services");
                    break;
                  case "Outdoor Services":
                    router.push("/client-side/outdoor-services");
                    break;
                  case "Beauty Services":
                    router.push("/client-side/beauty-services");
                    break;
                  case "Tech & Gadget Services":
                    router.push("/client-side/tech-gadget-services");
                    break;
                  case "Wellness Services":
                    router.push("/client-side/wellness-services");
                    break;
                }
              }}
            />
          ))}
        </View>

        {/* Loyalty Section */}
        <Text style={styles.sectionTitle}>Unlock your Loyalty Bonus</Text>
        <Text style={styles.description}>
          Once you complete the remaining bookings you can enjoy a ₱50 voucher
        </Text>

        <View style={styles.loyaltyBox}>
          <Text style={styles.loyaltyTitle}>Complete 10 Bookings</Text>
          <View style={styles.loyaltyContainer}>
            {Array.from({ length: 10 }).map((_, i) => (
              <View
                key={i}
                style={i < 3 ? styles.loyaltyFilled : styles.loyaltyCircle}
              />
            ))}
          </View>
          <Text style={styles.loyaltyCondition}>
            Complete within 3 months to earn your ₱50 voucher
          </Text>
        </View>

        <Text style={styles.remainingText}>
          Complete your 10 remaining bookings to earn a ₱50 voucher
        </Text>

        {/* Reward Cards Section */}
        <View style={styles.rewardsWrapper}>
          <View style={styles.rowCards}>
            {/* Welcome Voucher */}
            <LinearGradient
              colors={["#ffffff", "#A0EBF3"]}
              start={{ x: 0, y: 0 }}
              end={{ x: 1, y: 1 }}
              style={styles.card}
            >
              <Text style={styles.cardTitle}>WELCOME VOUCHER</Text>
              <Text style={styles.cardSub}>{"\n\n"}AND GET ₱50 VOUCHER</Text>
              <Text style={styles.cardText}>
                {"\n\n"}New Here? Book your first service today and enjoy ₱50
                voucher as our welcome gift.
              </Text>
              <Text style={styles.cardCondition}>
                Conditions: First time users only
              </Text>
            </LinearGradient>

            {/* Referral Bonus */}
            <LinearGradient
              colors={["#ffffff", "#A0EBF3"]}
              start={{ x: 0, y: 0 }}
              end={{ x: 1, y: 1 }}
              style={styles.card}
            >
              <Text style={styles.cardTitle}>REFERRAL BONUS</Text>
              <Text style={styles.cardSub}>{"\n\n"}AND GET ₱10 VOUCHER</Text>
              <Text style={styles.cardText}>
                {"\n\n"}Share HAUSTAP with friends! Once your friend completes
                their first booking, you earn ₱10 voucher.
              </Text>
              <Text style={styles.cardCondition}>
                Conditions: Voucher will be credited after your friend's first
                complete booking.
              </Text>
            </LinearGradient>
          </View>

          {/* Loyalty Bonus (square card same size as others) */}
          <View style={styles.singleCardWrapper}>
            <LinearGradient
              colors={["#ffffff", "#A0EBF3"]}
              start={{ x: 0, y: 0 }}
              end={{ x: 1, y: 1 }}
              style={[styles.card, styles.singleCard]}
            >
              <Text style={styles.cardTitle}>UNLOCK YOUR{"\n"}LOYALTY BONUS</Text>
              <Text style={styles.cardSub}>{"\n\n"}AND GET ₱50 VOUCHER</Text>
              <Text style={styles.cardText}>
                {"\n\n"}After 10 completed bookings, enjoy a ₱50 voucher as a
                thank you for staying with HAUSTAP.
              </Text>
              <Text style={styles.cardCondition}>
                Conditions: Must be completed within 3 months
              </Text>
            </LinearGradient>
          </View>
        </View>

        {/* Features above footer */}
        <View style={styles.featuresContainer}>
          <View style={styles.featureItem}>
            <Ionicons name="diamond-outline" size={18} color="#000" />
            <Text style={styles.featureText}>Fair and upfront prices</Text>
          </View>
          <View style={styles.featureItem}>
            <Ionicons name="checkmark-circle-outline" size={18} color="#000" />
            <Text style={styles.featureText}>Verified and trusted providers</Text>
          </View>
        </View>
      </ScrollView>

      {/* Footer handled by layout */}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    padding: 15,
    alignItems: "center",
  },
  headerWrapper: {
    width: "100%",
    marginBottom: 25,
    position: "relative",
  },
  headerImage: {
    width: "100%",
    height: 180,
    borderRadius: 15,
  },
  notificationIcon: {
    position: "absolute",
    top: 15,
    right: 15,
    backgroundColor: "#fff",
    borderRadius: 20,
    padding: 5,
    elevation: 4,
  },
  notificationDot: {
    position: "absolute",
    top: 5,
    right: 5,
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: "red",
  },
  notificationBadge: {
    position: 'absolute',
    top: 2,
    right: 2,
    minWidth: 18,
    height: 18,
    borderRadius: 9,
    backgroundColor: '#FF3B30',
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 4,
  },
  notificationBadgeText: {
    color: '#fff',
    fontSize: 10,
    fontWeight: '700',
  },
  searchContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#fff",
    width: "90%",
    borderRadius: 10,
    paddingHorizontal: 10,
    paddingVertical: 8,
    elevation: 4,
    position: "absolute",
    bottom: -20,
    alignSelf: "center",
  },
  searchInput: {
    flex: 1,
    marginRight: 8,
  },
  searchResults: {
    position: "absolute",
    top: 170,
    left: "5%",
    width: "90%",
    backgroundColor: "#fff",
    borderRadius: 8,
    elevation: 6,
    zIndex: 1000,
    maxHeight: 240,
  },
  resultItem: {
    padding: 12,
    borderBottomWidth: 1,
    borderBottomColor: "#eee",
  },
  resultText: {
    fontSize: 16,
  },
  sectionTitle: {
    fontWeight: "bold",
    fontSize: 16,
    width: "100%",
    marginTop: 30,
    marginBottom: 10,
  },
  categoriesContainer: {
    flexDirection: "row",
    flexWrap: "wrap",
    justifyContent: "space-between",
    marginBottom: 25,
  },
  description: {
    fontSize: 12,
    color: "#666",
    marginBottom: 10,
    textAlign: "center",
    paddingHorizontal: 10,
  },
  loyaltyBox: {
    backgroundColor: "#fff",
    width: "100%",
    borderRadius: 10,
    padding: 15,
    elevation: 2,
    marginBottom: 10,
  },
  loyaltyTitle: {
    fontWeight: "bold",
    marginBottom: 10,
    color: "#333",
    textAlign: "center",
  },
  loyaltyContainer: {
    flexDirection: "row",
    justifyContent: "center",
    marginBottom: 8,
  },
  loyaltyCircle: {
    width: 22,
    height: 22,
    borderRadius: 11,
    borderWidth: 1,
    borderColor: "#3DC1C6",
    marginHorizontal: 3,
  },
  loyaltyFilled: {
    width: 22,
    height: 22,
    borderRadius: 11,
    backgroundColor: "#3DC1C6",
    marginHorizontal: 3,
  },
  loyaltyCondition: {
    fontSize: 11,
    color: "#666",
    textAlign: "center",
  },
  remainingText: {
    fontSize: 12,
    color: "#000",
    textAlign: "center",
    marginBottom: 20,
  },
  rewardsWrapper: {
    width: "100%",
    alignItems: "center",
  },
  rowCards: {
    flexDirection: "row",
    justifyContent: "space-between",
    width: "100%",
  },
  singleCardWrapper: {
    alignItems: "center",
    marginTop: 15,
    width: "100%",
  },
  card: {
    flex: 1,
    margin: 10,
    padding: 20,
    borderRadius: 16,
    shadowColor: "#555",
    shadowOffset: { width: 5, height: 7 },
    shadowOpacity: 0.6,
    shadowRadius: 6,
    elevation: 8,
  },
  singleCard: {
    width: "47%",
  },
  cardTitle: {
    fontSize: 19,
    fontWeight: "bold",
    color: "#3DC1C6",
    textAlign: "left",
  },
  cardSub: {
    fontSize: 14,
    color: "#000",
    textAlign: "left",
  },
  cardText: {
    fontSize: 13,
    color: "#000",
    textAlign: "left",
  },
  cardCondition: {
    fontSize: 12,
    color: "#000",
    marginTop: 10,
    textAlign: "left",
    opacity: 0.9,
  },

  // Features above footer
  featuresContainer: {
    marginTop: 30,
    width: "100%",
    padding: 15,
    backgroundColor: "#D9F7FA",
  },
  featureItem: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 8,
  },
  featureText: {
    fontSize: 13,
    color: "#000",
    marginLeft: 6,
  },

  // footer is provided by layout
});
