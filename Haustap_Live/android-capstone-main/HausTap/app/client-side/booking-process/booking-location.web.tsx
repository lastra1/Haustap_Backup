import { Ionicons } from "@expo/vector-icons"
import * as Location from "expo-location"
import { useLocalSearchParams, useRouter } from "expo-router"
import React from "react"
import { ScrollView, StyleSheet, Text, TextInput, TouchableOpacity, View } from "react-native"

export default function BookingLocation() {
  const router = useRouter()
  const { categoryTitle, categoryPrice, categoryDesc, address, mainCategory, subCategory, service, selectedItems } = useLocalSearchParams()
  const [selectedAddress, setSelectedAddress] = React.useState("saved")
  const [setAddressText, setSetAddressText] = React.useState(
    `Blk 1 Lot 50 Mango St. Saint Joseph Village 10\nBrgy. Langgam San Pedro City Laguna`
  )
  const [pinLocation, setPinLocation] = React.useState("")
  const [pinAddress, setPinAddress] = React.useState<string | null>(null)
  const [coords, setCoords] = React.useState({ latitude: 14.3589, longitude: 121.0583 })
  const mapRef = React.useRef<any>(null)
  const markerRef = React.useRef<any>(null)

  function ensureLeafletAssets() {
    return new Promise<void>((resolve) => {
      const w: any = window
      if (w.L) {
        resolve()
        return
      }
      const cssId = 'leaflet-css'
      const jsId = 'leaflet-js'
      if (!document.getElementById(cssId)) {
        const link = document.createElement('link')
        link.id = cssId
        link.rel = 'stylesheet'
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
        document.head.appendChild(link)
      }
      const script = document.getElementById(jsId) as HTMLScriptElement | null
      if (script && (window as any).L) {
        resolve()
        return
      }
      const s = document.createElement('script')
      s.id = jsId
      s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
      s.onload = () => resolve()
      document.body.appendChild(s)
    })
  }

  React.useEffect(() => {
    let cancelled = false
    ;(async () => {
      await ensureLeafletAssets()
      if (cancelled) return
      const L: any = (window as any).L
      if (!L) return
      const map = L.map('leaflet-map').setView([coords.latitude, coords.longitude], 16)
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
      }).addTo(map)
      const marker = L.marker([coords.latitude, coords.longitude], { draggable: true }).addTo(map)
      marker.on('dragend', () => {
        const p = marker.getLatLng()
        setCoords({ latitude: p.lat, longitude: p.lng })
      })
      map.on('click', (e: any) => {
        marker.setLatLng(e.latlng)
        setCoords({ latitude: e.latlng.lat, longitude: e.latlng.lng })
      })
      mapRef.current = map
      markerRef.current = marker
    })()
    return () => {
      cancelled = true
      if (mapRef.current) {
        mapRef.current.remove()
        mapRef.current = null
      }
      markerRef.current = null
    }
  }, [])

  React.useEffect(() => {
    const t = `${coords.latitude.toFixed(4)}, ${coords.longitude.toFixed(4)}`
    setPinLocation(t)
    setPinAddress(null)
    ;(async () => {
      try {
        const { status } = await Location.requestForegroundPermissionsAsync()
        if (status !== "granted") return
        const results = await Location.reverseGeocodeAsync({ latitude: coords.latitude, longitude: coords.longitude })
        if (results && results.length > 0) {
          const r = results[0]
          const parts = [r.name, r.street, r.subregion || r.city, r.region, r.postalCode]
          const a = parts.filter(Boolean).join(", ")
          setPinAddress(a || t)
          return
        }
      } catch {}

      try {
        const resp = await fetch(
          `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${coords.latitude}&lon=${coords.longitude}`,
          { headers: { Accept: 'application/json' } }
        )
        if (resp.ok) {
          const data = await resp.json()
          const dn = (data && (data.display_name as string)) || ''
          if (dn) setPinAddress(dn)
        }
      } catch {}
    })()
  }, [coords])

  React.useEffect(() => {
    if (address) setSetAddressText(String(address))
  }, [address])

  async function useCurrentLocation() {
    try {
      const { status } = await Location.requestForegroundPermissionsAsync()
      if (status !== "granted") return
      const pos = await Location.getCurrentPositionAsync({})
      setCoords({ latitude: pos.coords.latitude, longitude: pos.coords.longitude })
      if (mapRef.current && markerRef.current) {
        markerRef.current.setLatLng([pos.coords.latitude, pos.coords.longitude])
        mapRef.current.setView([pos.coords.latitude, pos.coords.longitude], 16)
      }
    } catch {}
  }

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Booking Location</Text>
      </View>

      <View style={styles.mapContainer}>
        <View nativeID="leaflet-map" style={styles.webMap} />
        <View style={styles.mapControls}>
          <TouchableOpacity style={styles.stubButton} onPress={useCurrentLocation}>
            <Text style={styles.stubButtonText}>Use Current Location</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.overlayInputs}>
          <TextInput
            placeholder="Pin Location"
            style={styles.input}
            placeholderTextColor="#666"
            value={pinAddress ?? 'Fetching address…'}
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
          <TouchableOpacity
            onPress={() =>
              router.push({
                pathname: "/client-side/booking-process/booking-edit-address",
                params: {
                  address: setAddressText,
                  categoryTitle,
                  categoryPrice,
                  categoryDesc,
                  mainCategory,
                  subCategory,
                  service,
                  selectedItems,
                },
              } as any)
            }
          >
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
        onPress={() =>
          router.push({
            pathname: "/client-side/booking-process/schedule-booking",
            params: {
              location: pinLocation,
              address:
                selectedAddress === "saved"
                  ? "Blk3 Lot1 Apple St. Saint Joseph Village 10\nBrgy. Laram San Pedro City Laguna"
                  : setAddressText,
              categoryTitle,
              categoryPrice,
              categoryDesc,
              mainCategory,
              subCategory,
              service,
              selectedItems,
            },
          } as any)
        }
      >
        <Text style={styles.nextText}>Next</Text>
      </TouchableOpacity>
    </ScrollView>
  )
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
    marginBottom: 12,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "600",
    marginLeft: 10,
  },
  mapContainer: {
    height: 300,
    borderRadius: 12,
    overflow: "hidden",
    marginBottom: 20,
    position: "relative",
  },
  webMap: {
    flex: 1,
    backgroundColor: "#F5F5F5",
  },
  mapControls: {
    position: "absolute",
    bottom: 10,
    right: 10,
  },
  stubButton: {
    marginTop: 12,
    backgroundColor: "#00ADB5",
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 16,
  },
  stubButtonText: {
    color: "#fff",
    fontWeight: "600",
  },
  overlayInputs: {
    position: "absolute",
    top: 10,
    alignSelf: "center",
    width: "90%",
  },
  input: {
    backgroundColor: "#fff",
    borderRadius: 8,
    paddingHorizontal: 10,
    paddingVertical: 8,
    fontSize: 14,
    marginBottom: 8,
    borderWidth: 1,
    borderColor: "#ccc",
  },
  card: {
    backgroundColor: "#F5F5F5",
    borderRadius: 8,
    padding: 12,
    marginBottom: 12,
  },
  cardHeader: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 6,
  },
  cardTitle: {
    marginLeft: 6,
    fontWeight: "600",
    fontSize: 14,
  },
  addressContainer: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  addressText: {
    fontSize: 13,
    flex: 1,
    color: "#333",
  },
  editText: {
    color: "#00ADB5",
    fontWeight: "600",
    marginLeft: 10,
  },
  savedAddress: {
    flexDirection: "row",
    alignItems: "center",
  },
  radioOuter: {
    width: 18,
    height: 18,
    borderRadius: 9,
    borderWidth: 2,
    borderColor: "#00ADB5",
    alignItems: "center",
    justifyContent: "center",
    marginLeft: 8,
    backgroundColor: "#fff",
  },
  radioOuterSelected: {
    borderColor: "#00ADB5",
    borderWidth: 2,
  },
  radioInner: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: "#00ADB5",
    opacity: 0,
  },
  radioInnerVisible: {
    opacity: 1,
  },
  nextBtn: {
    backgroundColor: "#00ADB5",
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: "center",
    marginVertical: 20,
  },
  nextText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "700",
  },
})
