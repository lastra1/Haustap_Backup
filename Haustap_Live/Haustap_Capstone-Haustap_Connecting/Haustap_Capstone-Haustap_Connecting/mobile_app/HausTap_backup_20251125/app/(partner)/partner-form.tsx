import { Picker } from '@react-native-picker/picker';
import { Image } from 'react-native';
import { useRouter } from 'expo-router';
import React, { useState } from 'react';
import {
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { flowStore } from '../../src/services/flowStore';
const logo = require('../../assets/images/logo.png');

// Sample cascading data for pickers
const PROVINCES = [
  { label: 'Province 1', value: 'province1' },
  { label: 'Province 2', value: 'province2' },
];

const MUNICIPALITIES: Record<string, { label: string; value: string }[]> = {
  province1: [
    { label: 'Municipality 1-1', value: 'municipality1' },
    { label: 'Municipality 1-2', value: 'municipality2' },
  ],
  province2: [
    { label: 'Municipality 2-1', value: 'municipality3' },
    { label: 'Municipality 2-2', value: 'municipality4' },
  ],
};

const BARANGAYS: Record<string, { label: string; value: string }[]> = {
  municipality1: [
    { label: 'Barangay 1-A', value: 'barangay1' },
    { label: 'Barangay 1-B', value: 'barangay2' },
  ],
  municipality2: [
    { label: 'Barangay 2-A', value: 'barangay3' },
    { label: 'Barangay 2-B', value: 'barangay4' },
  ],
  municipality3: [
    { label: 'Barangay 3-A', value: 'barangay5' },
  ],
  municipality4: [
    { label: 'Barangay 4-A', value: 'barangay6' },
  ],
};

export default function PartnerForm() {
  const router = useRouter();

  const [accountType, setAccountType] = useState('');

  // Individual info
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [middleInitial, setMiddleInitial] = useState('');
  const [localEmail, setLocalEmail] = useState('');
  const [mobileNumber, setMobileNumber] = useState('');

  // Team info
  const [teamName, setTeamName] = useState('');
  const [teamMembers, setTeamMembers] = useState<string[]>(['']);
  const [teamLeadName, setTeamLeadName] = useState('');
  const [teamLeadEmail, setTeamLeadEmail] = useState('');
  const [teamLeadPhone, setTeamLeadPhone] = useState('');

  // Address
  const [houseNo, setHouseNo] = useState('');
  const [streetName, setStreetName] = useState('');
  const [province, setProvince] = useState('');
  const [municipality, setMunicipality] = useState('');
  const [barangay, setBarangay] = useState('');

  const [municipalitiesList, setMunicipalitiesList] = useState<{ label: string; value: string }[]>([]);
  const [barangaysList, setBarangaysList] = useState<{ label: string; value: string }[]>([]);

  // Update dependent dropdowns
  React.useEffect(() => {
    if (province && MUNICIPALITIES[province]) {
      setMunicipalitiesList(MUNICIPALITIES[province]);
    } else setMunicipalitiesList([]);
    setMunicipality('');
    setBarangay('');
    setBarangaysList([]);
  }, [province]);

  React.useEffect(() => {
    if (municipality && BARANGAYS[municipality]) {
      setBarangaysList(BARANGAYS[municipality]);
    } else setBarangaysList([]);
    setBarangay('');
  }, [municipality]);

  const handleNext = () => {
    console.log({ accountType, firstName, lastName, teamName, teamMembers, teamLeadName });
    // Pass email forward so later screens (terms -> verification) can show and use it
    // choose which email to pass depending on account type
    const selectedEmail = accountType === 'Team' ? teamLeadEmail : localEmail;
    // store email in flowStore as a fallback
    if (selectedEmail) flowStore.setEmail(selectedEmail);
    const encodedEmail = encodeURIComponent(selectedEmail || '');
    router.push(`/partner-services?email=${encodedEmail}`);
  };

  const handleTeamMemberChange = (text: string, index: number) => {
    const updated = [...teamMembers];
    updated[index] = text;
    setTeamMembers(updated);
  };

  const addTeamMember = () => setTeamMembers([...teamMembers, '']);

  return (
    <SafeAreaView style={styles.safeArea}>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={{ flex: 1 }}
        keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 80}
      >
        <ScrollView contentContainerStyle={styles.container} keyboardShouldPersistTaps="handled">
          
          <Image source={logo} style={styles.logo} resizeMode="contain" />
          <Text style={styles.header}>Application Form</Text>

          {/* Account Type */}
          <Text style={styles.label}>Choose account type:</Text>
          <View style={styles.accountTypeContainer}>
            {['Individual', 'Team'].map((type) => (
              <TouchableOpacity
                key={type}
                style={[styles.accountTypeButton, accountType === type && styles.selectedAccountType]}
                onPress={() => setAccountType(type)}
              >
                <View style={styles.radioButton}>
                  {accountType === type && <View style={styles.radioButtonSelected} />}
                </View>
                <Text style={styles.accountTypeLabel}>{type}</Text>
              </TouchableOpacity>
            ))}
          </View>

          {/* Individual Form */}
          {accountType === 'Individual' && (
            <>
              <Text style={styles.sectionLabel}>Basic Information</Text>
              <View style={styles.row}>
                <View style={[styles.column, { flex: 2 }]}> 
                  <TextInput
                    style={styles.input}
                    placeholder="First Name"
                    value={firstName}
                    onChangeText={setFirstName}
                    placeholderTextColor="#666"
                  />
                </View>
                <View style={[styles.column, { flex: 2 }]}> 
                  <TextInput
                    style={styles.input}
                    placeholder="Last Name"
                    value={lastName}
                    onChangeText={setLastName}
                    placeholderTextColor="#666"
                  />
                </View>
                <View style={[styles.column, { flex: 1 }]}> 
                  <TextInput
                    style={styles.input}
                    placeholder="M.I."
                    value={middleInitial}
                    onChangeText={setMiddleInitial}
                    maxLength={1}
                    placeholderTextColor="#666"
                  />
                </View>
              </View>
              <View style={styles.row}>
                <View style={[styles.column, { flex: 1 }]}> 
                  <TextInput
                    style={styles.input}
                    placeholder="Email"
                    value={localEmail}
                    onChangeText={setLocalEmail}
                    keyboardType="email-address"
                    autoCapitalize="none"
                    placeholderTextColor="#666"
                  />
                </View>
                <View style={[styles.column, { flex: 1 }]}> 
                  <TextInput
                    style={styles.input}
                    placeholder="Mobile number"
                    value={mobileNumber}
                    onChangeText={setMobileNumber}
                    keyboardType="phone-pad"
                    placeholderTextColor="#666"
                  />
                </View>
              </View>
            </>
          )}

          {/* Team Form */}
          {accountType === 'Team' && (
            <>
              <Text style={styles.sectionLabel}>Team Information</Text>
              <TextInput
                style={styles.input}
                placeholder="Team Name"
                value={teamName}
                onChangeText={setTeamName}
                placeholderTextColor="#666"
              />
              <Text style={[styles.sectionLabel, { fontSize: 12 }]}>Team Members</Text>
              {teamMembers.map((member, i) => (
                <TextInput
                  key={i}
                  style={styles.input}
                  placeholder={`Member ${i + 1} Name`}
                  value={member}
                  onChangeText={(text) => handleTeamMemberChange(text, i)}
                  placeholderTextColor="#666"
                />
              ))}
              <TouchableOpacity style={styles.addButton} onPress={addTeamMember}>
                <Text style={styles.addButtonText}>+ Add Member</Text>
              </TouchableOpacity>

              <Text style={styles.sectionLabel}>Team Lead Contact</Text>
              
              {/* Full Name */}
              <TextInput
                style={styles.input}
                placeholder="Full Name"
                value={teamLeadName}
                onChangeText={setTeamLeadName}
                placeholderTextColor="#666"
              />

              <TextInput
                style={styles.input}
                placeholder="Email"
                value={teamLeadEmail}
                onChangeText={setTeamLeadEmail}
                keyboardType="email-address"
                autoCapitalize="none"
                placeholderTextColor="#666"
              />
              <TextInput
                style={styles.input}
                placeholder="Mobile Number"
                value={teamLeadPhone}
                onChangeText={setTeamLeadPhone}
                keyboardType="phone-pad"
                placeholderTextColor="#666"
              />
            </>
          )}

          {/* Full Address */}
          <Text style={styles.sectionLabel}>Full Address</Text>
          <TextInput
            style={styles.input}
            placeholder="House no. & Street Name"
            value={`${houseNo}${streetName ? ` ${streetName}` : ''}`}
            onChangeText={(text) => {
              const match = text.match(/^(\d*)\s*(.*)/);
              if (match) {
                setHouseNo(match[1]);
                setStreetName(match[2]);
              }
            }}
            placeholderTextColor="#666"
          />

          {/* Province / Municipality / Barangay Pickers */}
          {[{
            label: 'Province', value: province, setter: setProvince, list: PROVINCES
          },{
            label: 'Municipality', value: municipality, setter: setMunicipality, list: municipalitiesList
          },{
            label: 'Barangay', value: barangay, setter: setBarangay, list: barangaysList
          }].map((item) => (
            <View style={styles.fullInputRow} key={item.label}>
              <View style={styles.inputWithPicker}>
                <TextInput
                  style={styles.inputInner}
                  placeholder={item.label}
                  value={item.value}
                  onChangeText={item.setter}
                  placeholderTextColor="#666"
                />
                <Picker
                  selectedValue={item.value}
                  onValueChange={item.setter}
                  style={styles.pickerInline}
                >
                  <Picker.Item label="" value="" />
                  {item.list.map((p: any) => (
                    <Picker.Item key={p.value} label={p.label} value={p.value} />
                  ))}
                </Picker>
              </View>
            </View>
          ))}

          {/* Next Button */}
          <TouchableOpacity style={styles.button} onPress={handleNext}>
            <Text style={styles.buttonText}>Next</Text>
          </TouchableOpacity>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: '#fff' },
  container: { flexGrow: 1, padding: 20, alignItems: 'center', backgroundColor: '#fff' },
  logo: { width: 120, height: 120, marginBottom: 10 },
  header: { fontSize: 20, fontWeight: 'bold', marginBottom: 15, textAlign: 'center' },
  sectionLabel: { fontSize: 14, fontWeight: 'bold', marginTop: 15, marginBottom: 5, alignSelf: 'flex-start' },
  label: { fontSize: 12, color: '#666', marginBottom: 4, alignSelf: 'flex-start' },
  accountTypeContainer: { flexDirection: 'row', marginBottom: 15, gap: 10 },
  accountTypeButton: { flexDirection: 'row', alignItems: 'center', paddingVertical: 8, paddingHorizontal: 12, borderWidth: 1, borderColor: '#ccc', borderRadius: 8, backgroundColor: '#fff', flex: 1 },
  selectedAccountType: { borderColor: '#3DC1C6' },
  radioButton: { width: 20, height: 20, borderRadius: 10, borderWidth: 2, borderColor: '#3DC1C6', marginRight: 8, alignItems: 'center', justifyContent: 'center' },
  radioButtonSelected: { width: 10, height: 10, borderRadius: 5, backgroundColor: '#3DC1C6' },
  accountTypeLabel: { fontSize: 12, color: '#000' },
  row: { flexDirection: 'row', gap: 10, width: '100%' },
  column: { flex: 1 },
  input: { width: '100%', backgroundColor: '#fff', borderWidth: 1, borderColor: '#ccc', borderRadius: 8, paddingHorizontal: 10, height: 45, marginBottom: 10 },
  fullInputRow: { width: '100%' },
  inputWithPicker: { width: '100%', flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: '#ccc', borderRadius: 8, height: 45, paddingHorizontal: 8, marginBottom: 10, backgroundColor: '#fff' },
  inputInner: { flex: 1, height: '100%', paddingHorizontal: 6, color: '#000' },
  pickerInline: { width: 120, height: 45, justifyContent: 'center' },
  button: { backgroundColor: '#3DC1C6', borderRadius: 10, paddingVertical: 12, alignItems: 'center', marginTop: 15, width: '100%' },
  buttonText: { color: '#fff', fontWeight: 'bold', fontSize: 16 },
  addButton: { paddingVertical: 8, alignItems: 'center', marginBottom: 10 },
  addButtonText: { color: '#3DC1C6', fontWeight: 'bold', fontSize: 14 },
});
