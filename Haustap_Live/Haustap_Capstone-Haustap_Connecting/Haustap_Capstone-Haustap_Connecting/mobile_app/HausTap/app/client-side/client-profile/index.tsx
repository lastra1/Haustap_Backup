// app/client-profile/index.tsx
import { Entypo, FontAwesome5, Ionicons, MaterialIcons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import React, { useState } from "react";
import {
    ScrollView,
    StyleSheet,
    Switch,
    Text,
    TouchableOpacity,
    View
} from "react-native";
import { useAuth } from '../../context/AuthContext';

export default function MyAccount() {
  const router = useRouter();
  const [expanded, setExpanded] = useState<string | null>(null);
  const { logout } = useAuth();
  const { user, setPartnerStatus, setMode, mode } = useAuth();

  const isPartnerMode = mode === 'provider';

  const onTogglePartner = async (value: boolean) => {
    // if application is pending, do nothing
    if (user?.isApplicationPending) return;

    if (value) {
      // turning ON
      if (!user?.isHausTapPartner) {
        // Not yet a partner - go to application form
        router.push('/client-profile/application-form');
        return;
      }
      // already a verified partner - switch to provider mode
      await setMode('provider');
      await setPartnerStatus(true);
      router.replace('/service-provider');
    } else {
      // turning OFF - switch back to client mode
      await setMode('client');
      router.replace('/client-side');
    }
  };

  const toggleExpand = (section: string) => {
    setExpanded(expanded === section ? null : section);
  };

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>My Account</Text>
      </View>

      {/* Profile Section */}
      <View style={styles.profileContainer}>
        <View style={styles.profileCircle}>
          <Ionicons name="person" size={50} color="#333" />
        </View>
        <Text style={styles.profileName}>Jenn Bornilla</Text>
        <TouchableOpacity onPress={() => router.push('/client-side/client-profile/user-profile')} accessibilityLabel="Edit profile">
          <Text style={styles.editProfileText}>Edit Profile</Text>
        </TouchableOpacity>
        {/* Partner toggle */}
        <View style={[styles.partnerRow, user?.isApplicationPending ? { opacity: 0.6 } : {}]}>
          <Text style={styles.partnerLabel}>{user?.isApplicationPending ? 'Pending HausTap Partner' : 'HausTap Partner'}</Text>
          <Switch
            value={isPartnerMode}
            onValueChange={onTogglePartner}
            disabled={!!user?.isApplicationPending}
          />
        </View>
      </View>

      {/* Account Sections */}
      <View style={styles.sectionContainer}>
        {/* My Account */}
        <TouchableOpacity style={styles.sectionHeader} onPress={() => toggleExpand("account")}>
          <Ionicons name="person-outline" size={18} color="#000" />
          <Text style={styles.sectionTitle}>My Account</Text>
          <Ionicons
            name={expanded === "account" ? "chevron-up" : "chevron-down"}
            size={18}
            color="#000"
            style={{ marginLeft: "auto" }}
          />
        </TouchableOpacity>
        {expanded === "account" && (
          <View style={styles.dropdown}>
            <TouchableOpacity style={styles.dropdownButton} onPress={() => { setExpanded(null); router.push('/client-side/client-profile/user-profile'); }}>
              <Text style={styles.dropdownItem}>Profile</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.dropdownButton} onPress={() => { setExpanded(null); router.push('/client-side/client-profile/user-saved-address' as any); }}>
              <Text style={styles.dropdownItem}>Addresses</Text>
            </TouchableOpacity>
          </View>
        )}

        {/* Referral */}
        <TouchableOpacity 
          style={styles.sectionHeader}
          onPress={() => router.push('/client-side/client-profile/user-referral' as any)}
        >
          <Ionicons name="gift-outline" size={18} color="#000" />
          <Text style={styles.sectionTitle}>Referral</Text>
        </TouchableOpacity>

        {/* My Vouchers */}
          <TouchableOpacity 
            style={styles.sectionHeader}
            onPress={() => router.push('/client-side/client-profile/user-voucher' as any)}
          >
            <MaterialIcons name="confirmation-number" size={18} color="#000" />
            <Text style={styles.sectionTitle}>My Vouchers</Text>
          </TouchableOpacity>

        {/* Connect Haustap */}
        <TouchableOpacity 
          style={styles.sectionHeader}
          onPress={() => router.push('/client-side/client-profile/user-connect-haustap' as any)}
        >
          <Entypo name="link" size={18} color="#000" />
          <Text style={styles.sectionTitle}>Connect Haustap</Text>
        </TouchableOpacity>

        {/* Privacy Settings */}
        <TouchableOpacity style={styles.sectionHeader} onPress={() => toggleExpand("privacy")}>
          <Ionicons name="document-text-outline" size={18} color="#000" />
          <Text style={styles.sectionTitle}>Privacy Settings</Text>
          <Ionicons
            name={expanded === "privacy" ? "chevron-up" : "chevron-down"}
            size={18}
            color="#000"
            style={{ marginLeft: "auto" }}
          />
        </TouchableOpacity>
        {expanded === "privacy" && (
          <View style={styles.dropdown}>
              <TouchableOpacity style={styles.dropdownButton} onPress={() => { setExpanded(null); router.push('/client-side/client-profile/terms-conditions' as any); }}>
                <Text style={styles.dropdownItem}>Terms and Conditions</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.dropdownButton} onPress={() => { setExpanded(null); router.push('/client-side/client-profile/privacy-policy' as any); }}>
                <Text style={styles.dropdownItem}>Privacy Policy</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.dropdownButton} onPress={() => { setExpanded(null); router.push('/client-side/client-profile/user-account-deletion' as any); }}>
                <Text style={styles.dropdownItem}>Account Deletion</Text>
              </TouchableOpacity>
          </View>
        )}

        {/* Settings */}
        <TouchableOpacity style={styles.sectionHeader} onPress={() => toggleExpand("settings")}>
          <Ionicons name="settings-outline" size={18} color="#000" />
          <Text style={styles.sectionTitle}>Settings</Text>
          <Ionicons
            name={expanded === "settings" ? "chevron-up" : "chevron-down"}
            size={18}
            color="#000"
            style={{ marginLeft: "auto" }}
          />
        </TouchableOpacity>
        {expanded === "settings" && (
          <View style={styles.dropdown}>
            <TouchableOpacity style={styles.dropdownButton} onPress={() => { setExpanded(null); router.push('/client-side/client-profile/language-setting' as any); }}>
              <Text style={styles.dropdownItem}>Language</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.dropdownButton} onPress={() => { setExpanded(null); router.push('/client-side/client-profile/user-sound' as any); }}>
              <Text style={styles.dropdownItem}>Sound</Text>
            </TouchableOpacity>
          </View>
        )}

        {/* Rate Haustap */}
        <TouchableOpacity style={styles.sectionHeader}>
          <FontAwesome5 name="star" size={16} color="#000" />
          <Text style={styles.sectionTitle}>Rate Haustap</Text>
        </TouchableOpacity>

        {/* About Us */}
        <TouchableOpacity 
          style={styles.sectionHeader}
          onPress={() => router.push('/client-side/client-profile/user-about')}
        >
          <Ionicons name="information-circle-outline" size={18} color="#000" />
          <Text style={styles.sectionTitle}>About us</Text>
          <Text style={styles.versionText}>v1</Text>
        </TouchableOpacity>
      </View>

      {/* Logout Button */}
      <TouchableOpacity
        style={styles.logoutBtn}
        onPress={async () => {
          try {
            await logout();
            router.replace('/auth/log-in');
          } catch (e) {
            // fallback: still navigate
            router.replace('/auth/log-in');
          }
        }}
      >
        <Text style={styles.logoutText}>Log out</Text>
      </TouchableOpacity>
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
  header: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 20,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "600",
    marginLeft: 10,
  },
  profileContainer: {
    alignItems: "center",
    marginBottom: 20,
  },
  profileCircle: {
    width: 90,
    height: 90,
    borderRadius: 45,
    backgroundColor: "#EAEAEA",
    alignItems: "center",
    justifyContent: "center",
  },
  profileName: {
    fontSize: 18,
    fontWeight: "600",
    marginTop: 8,
  },
  editProfileText: {
    color: "#00ADB5",
    marginTop: 4,
    fontSize: 13,
  },
  sectionContainer: {
    backgroundColor: "#F9F9F9",
    borderRadius: 12,
    paddingVertical: 10,
    paddingHorizontal: 8,
  },
  sectionHeader: {
    flexDirection: "row",
    alignItems: "center",
    paddingVertical: 10,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: "500",
    marginLeft: 8,
  },
  dropdown: {
    paddingLeft: 28,
    marginBottom: 6,
  },
  dropdownItem: {
    paddingVertical: 4,
    color: "#444",
  },
  dropdownButton: {
    paddingVertical: 8,
  },
  partnerRow: {
    marginTop: 12,
    width: '100%',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 8,
    paddingVertical: 10,
    backgroundColor: '#fff',
    borderRadius: 8,
    marginBottom: 10,
  },
  partnerLabel: {
    fontSize: 16,
    fontWeight: '500',
  },
  backText: {
    color: '#3DC1C6',
    fontWeight: '600',
  },
  versionText: {
    marginLeft: "auto",
    color: "#888",
    fontSize: 12,
  },
  logoutBtn: {
    backgroundColor: "#FF3B30",
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: "center",
    marginVertical: 30,
  },
  logoutText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "700",
  },
});
