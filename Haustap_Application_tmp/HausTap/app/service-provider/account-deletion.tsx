import { useRouter } from 'expo-router';
import React from 'react';
import { SafeAreaView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
// Make sure to install react-native-vector-icons:
// npm install react-native-vector-icons
import { Ionicons as Icon } from '@expo/vector-icons';


const AccountDeletionScreen = () => {
  const router = useRouter();
  const handleDeleteAccount = () => {
    console.log('Delete Account button pressed!');
    // Dito mo ilalagay ang logic para sa account deletion.
  };


  return (
    <SafeAreaView style={styles.safeArea}>
      <View style={styles.container}>
        {/* Navigation Bar (FIXED: Inalis ang spacer at inilapit ang title) */}
        <View style={styles.navBar}>
          <TouchableOpacity onPress={() => router.push('/service-provider/my-account')} accessibilityLabel="Go back to My Account">
            <Icon name="arrow-back" size={28} color="#000" />
          </TouchableOpacity>
          {/* Nilagyan ng marginLeft para hindi dumikit sa arrow */}
          <Text style={styles.navTitle}>Account Deletion</Text> 
          <View style={{ width: 115 }} /> {/* Ibinabalik ang spacer para maayos ang layout */}
        </View>


        {/* Content Section */}
        <View style={styles.contentRow}>
          <Text style={styles.requestText}>Request Account Deletion</Text>
          <TouchableOpacity style={styles.deleteButton} onPress={handleDeleteAccount}>
            <Text style={styles.deleteButtonText}>Delete</Text>
          </TouchableOpacity>
        </View>
      </View>
    </SafeAreaView>
  );
};


const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: '#fff', 
  },
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  navBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between', 
    paddingHorizontal: 15,
    paddingTop: 50,
    paddingBottom: 20,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
    backgroundColor: '#fff',
  },
  navTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#000',
    marginRight: 10, 
    flex: 1, 
    textAlign: 'center', 
  },
  contentRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    paddingVertical: 20, 
    borderBottomWidth: 1, 
    borderBottomColor: '#eee',
  },
  requestText: {
    fontSize: 16,
    color: '#000',
  },
  deleteButton: {
    backgroundColor: '#3dc1c6', // Teal color
    paddingVertical: 8,
    paddingHorizontal: 15,
    borderRadius: 5,
    alignItems: 'center',
    justifyContent: 'center',
  },
  deleteButtonText: {
    color: '#000',
    fontSize: 14,
    fontWeight: 'bold',
  },
});


export default AccountDeletionScreen;
