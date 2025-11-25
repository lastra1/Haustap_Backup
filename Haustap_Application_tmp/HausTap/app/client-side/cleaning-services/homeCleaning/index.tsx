import { useRouter } from "expo-router";
import React from "react";
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";
import { homeCleaningCategories } from "../../data/cleaning";

// map data titles to folder filenames under homeCleaning
const routeMap: Record<string, string> = {
  "Bungalow": "bungalow",
  "Condominium Studio / 1BR": "condo-studio",
  "Condominium 2BR": "condominium2br",
  "Condominium Penthouse": "penthouse",
  "Duplex": "duplex",
  "Container House Single": "container-house-single",
  "Container House Multiple": "container-house-multiple",
  "Stilt House Small": "stilt-house-small",
  "Stilt House Large": "stilt-house-large",
  "Mansion Smaller": "mansion-smaller",
  "Mansion Larger": "mansion-larger",
  "Villa Smaller": "villa-smaller",
  "Villa Larger": "villa-larger",
};

export default function HomeCleaningScreen() {
  const router = useRouter();
  const categories = homeCleaningCategories;

  return (
    <View style={styles.container}>
      <ScrollView>
        <Text style={styles.header}>Home Cleaning Services</Text>
        <View style={styles.categoriesContainer}>
          {categories.map((category, index) => {
            const route = routeMap[category.title];
            return (
              <TouchableOpacity
                key={index}
                style={styles.categoryBox}
                onPress={() => {
                  if (route) {
                    router.push(`/client-side/cleaning-services/homeCleaning/${route}` as any);
                  } else {
                    router.push({
                      pathname: "/client-side/booking-summary",
                      params: {
                        categoryTitle: category.title,
                        categoryDesc: category.desc,
                        service: "Home Cleaning",
                        mainCategory: "Cleaning Services",
                      },
                    });
                  }
                }}
              >
                <View style={styles.categoryContent}>
                  <Text style={styles.categoryTitle}>{category.title}</Text>
                  {category.size && (
                    <Text style={styles.categorySize}>{category.size}</Text>
                  )}
                  <Text style={styles.categoryDesc}>{category.desc}</Text>
                </View>
              </TouchableOpacity>
            );
          })}
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
  },
  header: {
    fontSize: 24,
    fontWeight: "bold",
    padding: 16,
    color: "#000",
  },
  categoriesContainer: {
    padding: 16,
  },
  
  
  categoryBox: {
    backgroundColor: "#DEE1E0",
    borderRadius: 10,
    padding: 15,
    marginBottom: 15,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 3,
    elevation: 3,
  },
  categoryContent: {
    flex: 1,
  },
  categoryTitle: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#000",
    marginBottom: 4,
  },
  categorySize: {
    fontSize: 14,
    color: "#444",
    marginBottom: 4,
  },
  categoryDesc: {
    fontSize: 14,
    color: "#444",
    lineHeight: 20,
  },
});
