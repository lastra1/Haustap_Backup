// SkillsScreen.js
import { Ionicons, MaterialCommunityIcons } from "@expo/vector-icons";
import { router } from "expo-router";
import React, { useEffect, useState } from "react";
import {
  LayoutAnimation,
  Platform,
  StyleSheet,
  Text,
  TouchableOpacity,
  UIManager,
  View,
} from "react-native";

export default function SkillsScreen({ navigation }: { navigation?: any }) {
  const [expanded, setExpanded] = useState(false);

  // Enable LayoutAnimation on Android
  useEffect(() => {
    if (Platform.OS === "android" && UIManager.setLayoutAnimationEnabledExperimental) {
      UIManager.setLayoutAnimationEnabledExperimental(true);
    }
  }, []);

  const toggleExpand = () => {
    // nice smooth open/close
    LayoutAnimation.configureNext(LayoutAnimation.Presets.easeInEaseOut);
    setExpanded((v) => !v);
  };

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity
          style={styles.backBtn}
          onPress={() => router.push('/service-provider/my-account')}
          accessibilityLabel="Go back to My Account"
        >
          <Ionicons name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Skills</Text>
      </View>

      {/* Section */}
      <View style={styles.section}>
        <TouchableOpacity
          style={styles.sectionHeader}
          onPress={toggleExpand}
          activeOpacity={0.7}
          accessibilityRole="button"
          accessibilityState={{ expanded }}
        >
          <Text style={styles.sectionTitle}>Cleaning Services</Text>
          <Ionicons
            name={expanded ? "chevron-up" : "chevron-down"}
            size={20}
            color="#444"
          />
        </TouchableOpacity>

        {/* Expanded content */}
        {expanded && (
          <View style={styles.itemsContainer}>
            <TouchableOpacity
              style={styles.itemRow}
              activeOpacity={0.7}
              onPress={() => {
                // handle item press here (navigate, toggle selected, etc.)
                console.log("Home Cleaning pressed");
              }}
            >
              <View style={styles.itemIcon}>
                <MaterialCommunityIcons name="broom" size={18} color="#000" />
              </View>
              <Text style={styles.itemText}>Home Cleaning</Text>
            </TouchableOpacity>
          </View>
        )}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
    paddingTop:  Platform.OS === "ios" ? 52 : 20,
    paddingHorizontal: 16,
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 14,
  },
  backBtn: {
    padding: 6,
    marginRight: 8,
  },
  headerTitle: {
    fontSize:20,
    fontWeight: "bold",
    marginLeft: 1,
    color: "#000",
  },

  section: {
    marginTop: 6,
  },
  sectionHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    // mimic screenshot spacing
    paddingVertical: 6,
    paddingRight: 6,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: "500",
    color: "#111",
    marginLeft: 42,
  },

  itemsContainer: {
    marginTop: 6,
    // keep left padding so item lines up under title
    paddingLeft: 40,
  },
  itemRow: {
    flexDirection: "row",
    alignItems: "center",
    paddingVertical: 10,
    // make touch area comfortable
  },
  itemIcon: {
    width: 28,
    alignItems: "center",
    justifyContent: "center",
    marginRight: 8,
  },
  itemText: {
    fontSize: 14,
    color: "#111",
  },
});
