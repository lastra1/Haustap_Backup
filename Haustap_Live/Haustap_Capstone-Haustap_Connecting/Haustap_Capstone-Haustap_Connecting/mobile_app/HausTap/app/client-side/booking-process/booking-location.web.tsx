import { Ionicons } from "@expo/vector-icons";
import * as Location from 'expo-location';
import { useLocalSearchParams, useRouter } from "expo-router";
import React, { useState } from "react";
import {
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View,
} from "react-native";
import MapView, { Marker } from "../../../stubs/react-native-maps-web";

export default function BookingLocation() {
  const router = useRouter();
  const { categoryTitle, categoryPrice, categoryDesc, address, mainCategory, subCategory, service, selectedItems } = useLocalSearchParams();
  const [selectedAddress, setSelectedAddress] = useState("saved");
  const [setAddressText, setSetAddressText] = useState(
    `Blk 1 Lot 50 Mango St. Saint Joseph Village 10\nBrgy. Langgam San Pedro City Laguna`
  );
  const [pinLocation, setPinLocation] = useState("");
  const [pinAddress, setPinAddress] = useState<string | null>(null);

  const [marker, setMarker] = useState({
    latitude: 14.3589,
    longitude: 121.0583,
  });

  React.useEffect(() => {
    const coordsText = `${marker.latitude.toFixed(4)}, ${marker.longitude.toFixed(4)}`;
    setPinLocation(coordsText);
    setPinAddress(null);

    (async () => {
      try {
        const { status } = await Location.requestForegroundPermissionsAsync();
        if (status !== 'granted') {
          return;
        }
        const results = await Location.reverseGeocodeAsync({
          latitude: marker.latitude,
          longitude: marker.longitude,
        });
        if (results && results.length > 0) {
          const r = results[0];
          const parts = [r.name, r.street, r.subregion || r.city, r.region, r.postalCode];
          const addr = parts.filter(Boolean).join(', ');
          setPinAddress(addr || coordsText);
        }
      } catch (e) {}
    })();
  }, [marker]);

  React.useEffect(() => {
    if (address) {
      setSetAddressText(String(address));
    }
  }, [address]);

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Booking Location</Text>
      </View>

      <View style={styles.mapContainer}>
        <MapView
          style={styles.map}
          initialRegion={{
            latitude: 14.3589,
            longitude: 121.0583,
            latitudeDelta: 0.01,
            longitudeDelta: 0.01,
          }}
          onPress={(e: any) => setMarker(e.nativeEvent.coordinate)}
        >
          <Marker coordinate={marker} anchor={{ x: 0.5, y: 0.5 }}>
            <Ionicons name="person" size={36} color="#3DC1C6" />
          </Marker>
        </MapView>

        <View style={styles.overlayInputs}>
          <TextInput
            placeholder="Pin Location"
            style={styles.input}
            placeholderTextColor="#666"
            value={pinAddress ?? pinLocation}
            editable={false}
          />
          <TextInput
            placeholder="Insert Type of house #"
            style={styles.input}
            placeholderTextColor="#666"
          />
        </View>
      </View>

      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <Ionicons name="location-outline" size={18} color="#000" />
          <Text style={styles.cardTitle}>Set Address</Text>
        </View>
        <View style={styles.addressContainer}>
          <Text style={styles.addressText}>{setAddressText}</Text>
          <TouchableOpacity onPress={() => router.push({ 
            pathname: '/client-side/booking-process/booking-edit-address', 
            params: { 
              address: setAddressText,
              categoryTitle,
              categoryPrice,
              categoryDesc,
              mainCategory,
              subCategory,
              service,
              selectedItems,
            } 
          } as any)}>
            <Text style={styles.editText}>Edit</Text>
          </TouchableOpacity>
        </View>
      </View>

      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <Ionicons name="location-outline" size={18} color="#000" />
          <Text style={styles.cardTitle}>Saved Address</Text>
        </View>
        <TouchableOpacity
          style={styles.savedAddress}
          onPress={() => setSelectedAddress((prev) => (prev === "saved" ? "" : "saved"))}
        >
          <View style={{ flex: 1 }}>
            <Text style={styles.addressText}>
              Blk3 Lot1 Apple St. Saint Joseph Village 10{"\n"}
              Brgy. Laram San Pedro City Laguna
            </Text>
          </View>
          <View style={[styles.radioOuter, selectedAddress === "saved" && styles.radioOuterSelected]}>
            <View style={[styles.radioInner, selectedAddress === "saved" && styles.radioInnerVisible]} />
          </View>
        </TouchableOpacity>
      </View>

      <TouchableOpacity
        style={styles.nextBtn}
        onPress={() => router.push({
          pathname: "/client-side/booking-process/schedule-booking",
          params: {
            location: pinLocation,
            address: selectedAddress === "saved" ? "Blk3 Lot1 Apple St. Saint Joseph Village 10\nBrgy. Laram San Pedro City Laguna" : setAddressText,
            categoryTitle,
            categoryPrice,
            categoryDesc,
            mainCategory,
            subCategory,
            service,
            selectedItems,
          }
        } as any)}
      >
        <Text style={styles.nextText}>Next</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: "#fff", paddingHorizontal: 16, paddingTop: 40 },
  header: { flexDirection: "row", alignItems: "center", marginBottom: 12 },
  headerTitle: { fontSize: 20, fontWeight: "600", marginLeft: 10 },
  mapContainer: { height: 300, borderRadius: 12, overflow: "hidden", marginBottom: 20, position: "relative" },
  map: { width: "100%", height: "100%" },
  overlayInputs: { position: "absolute", top: 10, alignSelf: "center", width: "90%" },
  input: { backgroundColor: "#fff", borderRadius: 8, paddingHorizontal: 10, paddingVertical: 8, fontSize: 14, marginBottom: 8, borderWidth: 1, borderColor: "#ccc" },
  submitBtn: { backgroundColor: "#00ADB5", borderRadius: 8, paddingVertical: 10, alignItems: "center" },
  submitText: { color: "#fff", fontWeight: "600" },
  card: { backgroundColor: "#F5F5F5", borderRadius: 8, padding: 12, marginBottom: 12 },
  cardHeader: { flexDirection: "row", alignItems: "center", marginBottom: 6 },
  cardTitle: { marginLeft: 6, fontWeight: "600", fontSize: 14 },
  addressContainer: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  addressText: { fontSize: 13, flex: 1, color: "#333" },
  editText: { color: "#00ADB5", fontWeight: "600", marginLeft: 10 },
  savedAddress: { flexDirection: "row", alignItems: "center" },
  radioOuter: { width: 18, height: 18, borderRadius: 9, borderWidth: 2, borderColor: "#00ADB5", alignItems: "center", justifyContent: "center", marginLeft: 8, backgroundColor: "#fff" },
  radioOuterSelected: { borderColor: "#00ADB5", borderWidth: 2 },
  radioInner: { width: 10, height: 10, borderRadius: 5, backgroundColor: "#00ADB5", opacity: 0 },
  radioInnerVisible: { opacity: 1 },
  nextBtn: { backgroundColor: "#00ADB5", borderRadius: 8, paddingVertical: 14, alignItems: "center", marginVertical: 20 },
  nextText: { color: "#fff", fontSize: 16, fontWeight: "700" },
});