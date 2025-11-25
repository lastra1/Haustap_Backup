import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React from 'react';
import { ScrollView, StyleSheet, Switch, Text, TouchableOpacity, View } from 'react-native';

interface RingtoneOption {
  id: string;
  name: string;
}

export default function SoundSettings() {
  const router = useRouter();
  const [isEnabled, setIsEnabled] = React.useState(false);
  const [showRingtones, setShowRingtones] = React.useState(false);
  const [selectedRingtone, setSelectedRingtone] = React.useState('honk');
  
  const toggleSwitch = () => setIsEnabled(previousState => !previousState);

  const ringtones: RingtoneOption[] = [
    { id: 'honk', name: 'Honk (Default)' },
    { id: 'bottle', name: 'Bottle' },
    { id: 'bubble', name: 'Bubble' },
    { id: 'bullfrog', name: 'Bullfrog' },
    { id: 'burst', name: 'Burst' },
    { id: 'chirp', name: 'Chirp' },
    { id: 'clank', name: 'Clank' },
    { id: 'crystal', name: 'Crystal' },
    { id: 'fadein', name: 'FadeIn' },
  ];

  const handleRingtoneSelect = (id: string) => {
    setSelectedRingtone(id);
  };

  if (showRingtones) {
    return (
      <View style={styles.container}>
        {/* Header */}
        <View style={styles.header}>
          <TouchableOpacity onPress={() => setShowRingtones(false)} style={styles.backButton}>
            <Ionicons name="arrow-back" size={24} color="black" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Select Ringtone</Text>
        </View>

        <ScrollView style={styles.content}>
          {ringtones.map((ringtone) => (
            <TouchableOpacity
              key={ringtone.id}
              style={styles.ringtoneItem}
              onPress={() => {
                handleRingtoneSelect(ringtone.id);
                setShowRingtones(false);
              }}
            >
              <Text style={styles.ringtoneName}>{ringtone.name}</Text>
              <View style={styles.radioButton}>
                {selectedRingtone === ringtone.id && <View style={styles.radioButtonInner} />}
              </View>
            </TouchableOpacity>
          ))}
        </ScrollView>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Sound</Text>
      </View>

      <ScrollView style={styles.content}>
        {/* Section Title */}
        <Text style={styles.sectionTitle}>General</Text>

        {/* Settings Item */}
        <View style={styles.settingsCard}>
          <View style={styles.settingRow}>
            <View>
              <Text style={styles.settingTitle}>Enable Notification Sound</Text>
              <Text style={styles.settingDescription}>Turn on/off notification sound and push notifications</Text>
            </View>
            <Switch
              trackColor={{ false: '#D1D1D1', true: '#00ADB5' }}
              thumbColor={'#FFFFFF'}
              ios_backgroundColor="#D1D1D1"
              onValueChange={toggleSwitch}
              value={isEnabled}
            />
          </View>
          <View style={styles.soundOption}>
            <Text style={styles.soundOptionTitle}>General</Text>
            <TouchableOpacity 
              style={styles.settingRow}
              onPress={() => setShowRingtones(true)}
            >
              <Text style={styles.soundText}>
                {ringtones.find(r => r.id === selectedRingtone)?.name || 'Honk (Default)'}
              </Text>
            </TouchableOpacity>
          </View>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingTop: 60,
    paddingHorizontal: 16,
    paddingBottom: 20,
    backgroundColor: '#FFFFFF',
  },
  backButton: {
    padding: 4,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: '600',
    marginLeft: 12,
    fontFamily: 'DMSans-Bold',
  },
  content: {
    flex: 1,
    backgroundColor: '#F6F6F6',
  },
  sectionTitle: {
    fontSize: 14,
    color: '#666666',
    marginTop: 16,
    marginBottom: 8,
    marginLeft: 16,
    fontFamily: 'DMSans-Medium',
  },
  settingsCard: {
    backgroundColor: '#FFFFFF',
    marginHorizontal: 16,
    borderRadius: 12,
    padding: 16,
  },
  settingRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  settingTitle: {
    fontSize: 16,
    color: '#000000',
    marginBottom: 4,
    fontFamily: 'DMSans-Regular',
  },
  settingDescription: {
    fontSize: 12,
    color: '#666666',
    fontFamily: 'DMSans-Regular',
  },
  soundOption: {
    marginTop: 16,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#F0F0F0',
  },
  soundText: {
    fontSize: 15,
    color: '#000000',
    fontFamily: 'DMSans-Regular',
  },
  soundOptionTitle: {
    fontSize: 14,
    color: '#666666',
    fontFamily: 'DMSans-Medium',
    marginBottom: 8,
  },
  ringtoneItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 16,
    paddingHorizontal: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#F0F0F0',
    backgroundColor: '#FFFFFF',
  },
  ringtoneName: {
    fontSize: 16,
    color: '#000000',
    fontFamily: 'DMSans-Regular',
  },
  radioButton: {
    width: 20,
    height: 20,
    borderRadius: 10,
    borderWidth: 2,
    borderColor: '#00ADB5',
    justifyContent: 'center',
    alignItems: 'center',
  },
  radioButtonInner: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: '#00ADB5',
  },
});
