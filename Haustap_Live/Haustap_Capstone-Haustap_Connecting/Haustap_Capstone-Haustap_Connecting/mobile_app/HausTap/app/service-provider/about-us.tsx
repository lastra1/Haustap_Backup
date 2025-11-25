import { Ionicons } from "@expo/vector-icons";
import { useRouter } from 'expo-router';
import React from "react";
import { SafeAreaView, ScrollView, StyleSheet, Text, TouchableOpacity, View } from "react-native";


export default function AboutUs() {
  const router = useRouter();
  return (
    // SafeAreaView ensures content doesn't overlap the status bar area
    <SafeAreaView style={styles.safeArea}>
      <View style={styles.container}>
        
        {/* Navigation Bar (White Background) - ADJUSTED PADDING */}
        <View style={styles.navBar}>
          <TouchableOpacity
            style={styles.backButtonNav}
            onPress={() => router.push('/service-provider/my-account')}
            accessibilityLabel="Go back to My Account"
          >
            <Ionicons name="arrow-back" size={24} color="black" />
          </TouchableOpacity>
          <Text style={styles.navTitle}>About us</Text>
          {/* Spacer */}
          <View style={{ width: 24 }} /> 
        </View>


        {/* Header Content (Blue Box) */}
        <View style={styles.headerBlueBox}>
          <Text style={styles.headerTitle}>About HausTap</Text>
          <Text style={styles.headerSubtitle}>
            Your trusted partner for everyday services.
          </Text>
        </View>


        {/* Scrollable Content */}
        <ScrollView contentContainerStyle={styles.scrollContainer}>
          <Text style={styles.paragraph}>
            Haustap is an innovative platform that connects homeowners with
            skilled, verified professionals for all kinds of home services.
            Whether you need cleaning, plumbing, electrical or appliance repair,
            Haustap makes it easy, secure, and convenient to book trusted service
            providers right from your home.
          </Text>


          {/* OUR MISSION */}
          <Text style={styles.sectionTitle}>OUR MISSION</Text>
          <View style={styles.sectionLine} />
          <Text style={styles.paragraph}>
            HausTap is committed to providing a seamless and dependable online
            booking platform that connects individuals with verified and skilled
            professionals for home, personal, wellness, and technology services.
            Our mission is to deliver convenience, quality, and secure service
            experiences through innovative solutions that make daily life easier
            and more efficient— anytime, anywhere.
          </Text>


          {/* OUR VISION */}
          <Text style={styles.sectionTitle}>OUR VISION</Text>
          <View style={styles.sectionLine} />
          <Text style={styles.paragraph}>
            HausTap envisions becoming the leading and most trusted all-in-one
            service booking platform in the Philippines and beyond. We aim to
            empower individuals with easy access to essential services while
            fostering growth and opportunities for service professionals through
            technology-driven, reliable, and customer-focused innovations.
          </Text>


          {/* OUR CORE VALUES */}
          <Text style={styles.sectionTitle}>OUR CORE VALUES</Text>
          <View style={styles.sectionLine} />
          <Text style={styles.paragraph}>
            Trust & Reliability{"\n"}
            Quality Service{"\n"}
            Convenience{"\n"}
            Innovation
          </Text>


          {/* WHY CHOOSE HAUSTAP */}
          <Text style={styles.sectionTitle}>WHY CHOOSE HAUSTAP?</Text>
          <View style={styles.sectionLine} />
          <Text style={styles.paragraph}>
            <Text style={styles.bold}>Expert & Trusted Professionals{"\n"}</Text>
            Every service provider is verified and skilled to handle a wide range
            of home services.{"\n\n"}


            <Text style={styles.bold}>Flexible Scheduling{"\n"}</Text>
            Book when it’s most convenient for you, from one-time to regular
            appointments.{"\n\n"}


            <Text style={styles.bold}>All-in-One Home & Lifestyle Care{"\n"}</Text>
            From home cleaning to personal care, we’ve got your everyday needs
            covered.{"\n\n"}


            <Text style={styles.bold}>Satisfaction Guarantee{"\n"}</Text>
            Driven by customer satisfaction, we ensure top-quality service in
            every experience.
          </Text>


          <Text style={styles.footer}>
            With Haustap, trusted services are just a tap away.{"\n"}
            <Text style={styles.bookNow}>Book Now!</Text>
          </Text>
        </ScrollView>
      </View>
    </SafeAreaView>
  );
}


const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: "#fff",
  },
  container: {
    flex: 1,
    backgroundColor: "#fff",
  },
  
  // ADJUSTED: White Navigation Bar PADDING
  navBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 15,
    paddingVertical: 10,
    backgroundColor: '#fff',
    // FIX: Dinagdagan ng paddingTop: 50 para bumaba ang arrow at title
    paddingTop: 50, 
  },
  navTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#000',
    flex: 1, 
    marginLeft: 15, 
  },
  backButtonNav: {
    padding: 5, 
  },


  // Header Content (Blue Box)
  headerBlueBox: {
    backgroundColor: "#a0ebf3",
    paddingVertical: 40,
    paddingHorizontal: 20,
    paddingTop: 40, 
    marginBottom: 20, 
  },
  headerTitle: {
    fontSize: 28, 
    fontWeight: "bold",
    color: "#000",
    marginBottom: 5,
  },
  headerSubtitle: {
    fontSize: 14,
    color: "#000",
  },
  
  // Scrollable Content
  scrollContainer: {
    paddingHorizontal: 20,
    paddingBottom: 40,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: "700",
    marginTop: 25,
    textAlign: "center",
  },
  sectionLine: {
    height: 2,
    width: 60,
    backgroundColor: "#A8E6F2",
    alignSelf: "center",
    marginVertical: 10,
    borderRadius: 2,
  },
  paragraph: {
    fontSize: 14,
    color: "#333",
    lineHeight: 22,
    textAlign: "justify",
  },
  bold: {
    fontWeight: "700",
  },
  footer: {
    marginTop: 20,
    textAlign: "center",
    fontSize: 14,
    lineHeight: 22,
  },
  bookNow: {
    color: "#000", 
    fontWeight: "bold",
    fontSize: 16, 
  },
});
