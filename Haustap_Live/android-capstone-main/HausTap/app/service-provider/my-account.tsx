import {
    AntDesign,
    Entypo,
    FontAwesome,
    Ionicons,
    MaterialCommunityIcons,
    MaterialIcons
} from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import {
    LayoutAnimation,
    Platform,
    ScrollView,
    StyleSheet,
    Switch,
    Text,
    TouchableOpacity,
    UIManager,
    View
} from 'react-native';
import { useAuth } from '../context/AuthContext';

// Enable LayoutAnimation on Android
if (Platform.OS === 'android') {
  if (UIManager.setLayoutAnimationEnabledExperimental) {
    UIManager.setLayoutAnimationEnabledExperimental(true);
  }
}

export default function App() {
  const router = useRouter();
  const { logout, setMode } = useAuth();
  // Dropdown toggle states
  const [showProfile, setShowProfile] = useState(false);
  const [showSkills, setShowSkills] = useState(false);
  const [showPrivacy, setShowPrivacy] = useState(false);
  const [showSettings, setShowSettings] = useState(false);
  // HausTap Partner toggle state
  const [isHausTapPartner, setIsHausTapPartner] = useState(true);

  const toggle = (
    setter: React.Dispatch<React.SetStateAction<boolean>>,
    current: boolean
  ) => {
    LayoutAnimation.configureNext(LayoutAnimation.Presets.easeInEaseOut);
    setter(!current);
  };

  const handlePartnerToggle = async (value: boolean) => {
    setIsHausTapPartner(value);
    if (!value) {
      // Turn off - switch to client mode
      await setMode('client');
      router.replace('/client-side');
    }
  };

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity
          onPress={() => router.push('/service-provider')}
          accessibilityLabel="Go back to home"
          style={{ padding: 6 }}
        >
          <MaterialIcons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>My Account</Text>
      </View>

      {/* Profile Section */}
      <View style={styles.profileSection}>
        <View style={styles.avatarContainer}>
          <MaterialIcons name="person" size={45} color="black" />
        </View>
        <Text style={styles.profileName}>Ana Santos</Text>
        <TouchableOpacity
          style={styles.editProfile}
          onPress={() => router.push('/service-provider/profile')}
          accessibilityLabel="Edit profile"
        >
          <MaterialIcons name="edit" size={14} color="gray" />
          <Text style={styles.editText}>Edit Profile</Text>
        </TouchableOpacity>
      </View>

      {/* Light Blue Bar */}
      <View style={styles.highlightBar} />

      {/* HausTap Partner Toggle */}
      <View style={styles.hausTapPartnerContainer}>
        <View style={styles.hausTapPartnerContent}>
          <View style={styles.hausTapPartnerTextContainer}>
            <Text style={styles.hausTapPartnerTitle}>HausTap Partner</Text>
            <Text style={styles.hausTapPartnerSubtitle}>
              {isHausTapPartner ? 'Active' : 'Inactive'}
            </Text>
          </View>
        </View>
        <Switch
          value={isHausTapPartner}
          onValueChange={handlePartnerToggle}
          trackColor={{ false: '#E0E0E0', true: '#B2DFD8' }}
          thumbColor={isHausTapPartner ? '#3DC1C6' : '#999'}
        />
      </View>

      {/* Menu Section */}
      <ScrollView style={styles.menuContainer} showsVerticalScrollIndicator={false}>
        {/* My Profile */}
        <View style={styles.menuGroup}>
          <TouchableOpacity
            style={styles.menuHeader}
            onPress={() => toggle(setShowProfile, showProfile)}
          >
            <Ionicons name="person-outline" size={20} color="black" />
            <Text style={styles.menuTitle}>My Profile</Text>
            <Entypo
              name={showProfile ? 'chevron-up' : 'chevron-down'}
              size={18}
              color="black"
              style={styles.chevronIcon}
            />
          </TouchableOpacity>
          {showProfile && (
            <View>
              <Text style={styles.subItem}>Profile</Text>
              <TouchableOpacity
                onPress={() => router.push('/service-provider/address')}
                accessibilityLabel="Addresses"
              >
                <Text style={styles.subItem}>Addresses</Text>
              </TouchableOpacity>
            </View>
          )}
        </View>

        {/* Skills */}
        <View style={styles.menuGroup}>
          <TouchableOpacity
            style={styles.menuHeader}
            onPress={() => router.push('/service-provider/skills')}
          >
            <MaterialCommunityIcons name="briefcase-outline" size={20} color="black" />
            <Text style={styles.menuTitle}>Skills</Text>
            <Entypo
              name={showSkills ? 'chevron-up' : 'chevron-down'}
              size={18}
              color="black"
              style={styles.chevronIcon}
            />
          </TouchableOpacity>
          {showSkills && <Text style={styles.subItem}>Home Cleaning</Text>}
        </View>

        {/* Referral */}
        <TouchableOpacity
          style={styles.menuRow}
          onPress={() => router.push('/service-provider/referral')}
          accessibilityLabel="Referral"
        >
          <MaterialCommunityIcons name="account-group-outline" size={20} color="black" />
          <Text style={styles.menuTitle}>Referral</Text>
        </TouchableOpacity>

        {/* My Vouchers */}
        <TouchableOpacity
          style={styles.menuRow}
          onPress={() => router.push('/service-provider/my-voucher')}
          accessibilityLabel="My Vouchers"
        >
          <MaterialCommunityIcons name="ticket-percent-outline" size={20} color="black" />
          <Text style={styles.menuTitle}>My Vouchers</Text>
        </TouchableOpacity>

        {/* My Subscription */}
        <TouchableOpacity
          style={styles.menuRow}
          onPress={() => router.push('/service-provider/my-subscription')}
          accessibilityLabel="My Subscription"
        >
          <MaterialCommunityIcons name="wallet-outline" size={20} color="black" />
          <Text style={styles.menuTitle}>My Subscription</Text>
        </TouchableOpacity>

        {/* Connect Haustap */}
        <TouchableOpacity
          style={styles.menuRow}
          onPress={() => router.push('/service-provider/connect-haustap')}
          accessibilityLabel="Connect Haustap"
        >
          <MaterialCommunityIcons name="email-outline" size={20} color="black" />
          <Text style={styles.menuTitle}>Connect Haustap</Text>
        </TouchableOpacity>

        {/* Privacy Settings */}
        <View style={styles.menuGroup}>
          <TouchableOpacity
            style={styles.menuHeader}
            onPress={() => toggle(setShowPrivacy, showPrivacy)}
          >
            <MaterialCommunityIcons name="shield-outline" size={20} color="black" />
            <Text style={styles.menuTitle}>Privacy Settings</Text>
            <Entypo
              name={showPrivacy ? 'chevron-up' : 'chevron-down'}
              size={18}
              color="black"
              style={styles.chevronIcon}
            />
          </TouchableOpacity>
          {showPrivacy && (
            <View>
              <TouchableOpacity onPress={() => router.push('/service-provider/terms-condition')} accessibilityLabel="Terms and Conditions">
                <Text style={styles.subItem}>Terms and Conditions</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => router.push('/service-provider/privacy-policy')} accessibilityLabel="Privacy Policy">
                <Text style={styles.subItem}>Privacy Policy</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => router.push('/service-provider/account-deletion')} accessibilityLabel="Account Deletion">
                <Text style={styles.subItem}>Account Deletion</Text>
              </TouchableOpacity>
            </View>
          )}
        </View>

        {/* Settings */}
        <View style={styles.menuGroup}>
          <TouchableOpacity
            style={styles.menuHeader}
            onPress={() => toggle(setShowSettings, showSettings)}
          >
            <Ionicons name="settings-outline" size={20} color="black" />
            <Text style={styles.menuTitle}>Settings</Text>
            <Entypo
              name={showSettings ? 'chevron-up' : 'chevron-down'}
              size={18}
              color="black"
              style={styles.chevronIcon}
            />
          </TouchableOpacity>
          {showSettings && (
            <View>
              <TouchableOpacity onPress={() => router.push('/service-provider/language-settings')} accessibilityLabel="Language Settings">
                <Text style={styles.subItem}>Language</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => router.push('/service-provider/sound-settings')} accessibilityLabel="Sound Settings">
                <Text style={styles.subItem}>Sound</Text>
              </TouchableOpacity>
            </View>
          )}
        </View>

        {/* Rate Haustap */}
        <View style={styles.menuRow}>
          <FontAwesome name="star-o" size={20} color="black" />
          <Text style={styles.menuTitle}>Rate Haustap</Text>
        </View>

        {/* About Us */}
        <TouchableOpacity style={styles.menuRow} onPress={() => router.push('/service-provider/about-us')} accessibilityLabel="About Us">
          <AntDesign name="info-circle" size={20} color="black" />
          <Text style={styles.menuTitle}>About us</Text>
          <Text style={styles.version}>v1</Text>
        </TouchableOpacity>
      </ScrollView>

      {/* Logout Button */}
      <TouchableOpacity
        style={styles.logoutButton}
        onPress={async () => {
          try {
            await logout();
          } catch (e) {
            // ignore
          }
          router.replace('/auth/log-in');
        }}
      >
        <Text style={styles.logoutText}>Log out</Text>
      </TouchableOpacity>
    </View>
  );
}

/* ---------------- STYLES ---------------- */
const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff', paddingTop: 50 },
  header: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 15 },
  headerTitle: { fontSize: 20, fontWeight: 'bold', marginLeft: 10 },
  profileSection: { alignItems: 'center', marginTop: 20 },
  avatarContainer: {
    width: 70,
    height: 70,
    borderRadius: 35,
    backgroundColor: '#f2f2f2',
    justifyContent: 'center',
    alignItems: 'center',
  },
  profileName: { fontSize: 20, fontWeight: 'bold', marginTop: 10 },
  editProfile: { flexDirection: 'row', alignItems: 'center', marginTop: 5 },
  editText: { color: 'gray', fontSize: 13, marginLeft: 4 },
  highlightBar: { backgroundColor: '#d6f3fa', height: 20, width: '100%', marginTop: 15 },
  hausTapPartnerContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 15,
    paddingVertical: 12,
    marginHorizontal: 15,
    marginVertical: 10,
    backgroundColor: '#F0F8F9',
    borderRadius: 8,
    borderLeftWidth: 3,
    borderLeftColor: '#3DC1C6',
  },
  hausTapPartnerContent: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  hausTapPartnerTextContainer: {
    marginLeft: 12,
    flex: 1,
  },
  hausTapPartnerTitle: {
    fontSize: 15,
    fontWeight: '600',
    color: '#000',
  },
  hausTapPartnerSubtitle: {
    fontSize: 12,
    color: '#666',
    marginTop: 2,
  },
  menuContainer: { flex: 1, paddingHorizontal: 15, marginTop: 10 },
  menuGroup: { marginBottom: 10 },
  menuHeader: { flexDirection: 'row', alignItems: 'center' },
  menuTitle: { fontSize: 15, fontWeight: 'bold', marginLeft: 8 },
  chevronIcon: { marginLeft: 'auto' },
  subItem: { marginLeft: 28, fontSize: 14, color: '#555', marginTop: 3 },
  menuRow: { flexDirection: 'row', alignItems: 'center', marginVertical: 10 },
  version: { marginLeft: 5, color: 'gray', fontSize: 12 },
  logoutButton: {
    backgroundColor: '#ff3b30',
    marginHorizontal: 15,
    marginVertical: 25,
    borderRadius: 6,
    alignItems: 'center',
    paddingVertical: 10,
  },
  logoutText: { color: '#fff', fontSize: 17, fontWeight: 'bold' },
});