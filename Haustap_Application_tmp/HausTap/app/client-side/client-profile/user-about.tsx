import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import React from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';

export default function AboutUs() {
  const router = useRouter();

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>About us</Text>
      </View>

      <ScrollView style={styles.content} showsVerticalScrollIndicator={false}>
        {/* Banner */}
        <View style={styles.banner}>
          <View style={styles.bannerContent}>
            <Text style={styles.bannerTitleBold}>About HausTap</Text>
            <Text style={styles.bannerDescription}>Your trusted partner for everyday services.</Text>
          </View>
        </View>
        
        <View style={styles.section}>
          <Text style={styles.paragraph}>
            Haustap is an innovative platform that connects homeowners with skilled, verified professionals for all kinds of home services. Whether you need cleaning, plumbing, electrical or appliance repair, Haustap makes it easy, secure, and convenient to book trusted service providers right from your home.
          </Text>
        </View>

        {/* Our Mission */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>OUR MISSION</Text>
          <Text style={styles.paragraph}>
            HausTap is committed to providing a seamless and comfortable online booking platform that connects individuals with verified and skilled professionals for home, personal, wellness, and technology services. Our mission is to deliver convenience, quality, and secure service experiences through innovative solutions that make daily life easier and more efficient — anytime, anywhere.
          </Text>
        </View>

        {/* Our Vision */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>OUR VISION</Text>
          <Text style={styles.paragraph}>
            HausTap envisions becoming the leading and most trusted all-in-one platform for connecting individuals with high-quality services in the Philippines and beyond. We aim to empower individuals with easy access to essential services while fostering growth and opportunities for skilled professionals through technology-driven, reliable, and customer-focused innovations.
          </Text>
        </View>

        {/* Our Core Values */}
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>OUR CORE VALUES</Text>
          <View style={styles.valueContainer}>
            <View style={styles.valueItem}>
              <Text style={styles.valueTitle}>Trust & Reliability</Text>
              <Text style={styles.valueDescription}>Expert & Trusted Professionals</Text>
            </View>
            <View style={styles.valueItem}>
              <Text style={styles.valueTitle}>All-In-One Home & Lifestyle Care</Text>
              <Text style={styles.valueDescription}>Satisfaction Guarantee</Text>
            </View>
            <View style={styles.valueItem}>
              <Text style={styles.valueTitle}>Quality Service</Text>
              <Text style={styles.valueDescription}>Flexible Scheduling</Text>
            </View>
            <View style={styles.valueItem}>
              <Text style={styles.valueTitle}>Innovation</Text>
              <Text style={styles.valueDescription}>Book Now!</Text>
            </View>
          </View>
        </View>

        {/* CTA / Divider */}
        <View style={styles.ctaContainer}>
          <View style={styles.ctaDivider} />
          <Text style={styles.ctaText}>With HausTap, trusted services are just a tap away.</Text>
          <TouchableOpacity style={styles.ctaButton} onPress={() => router.push('/') }>
            <Text style={styles.ctaButtonText}>Book Now!</Text>
          </TouchableOpacity>
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
  },
  banner: {
    backgroundColor: '#A8E5E9',
    padding: 24,
    width: '100%',
  },
  bannerContent: {
    alignItems: 'flex-start',
  },
  bannerTitleBold: {
    fontSize: 32,
    color: '#000000',
    fontFamily: 'DMSans-Bold',
    marginBottom: 8,
  },
  bannerDescription: {
    fontSize: 18,
    color: '#000000',
    fontFamily: 'DMSans-Medium',
  },
  section: {
    paddingHorizontal: 16,
    paddingVertical: 20,
    alignItems: 'center',
  },
  description: {
    fontSize: 18,
    color: '#000000',
    marginBottom: 12,
    fontFamily: 'DMSans-Medium',
    textAlign: 'center',
  },
  paragraph: {
    fontSize: 14,
    color: '#666666',
    lineHeight: 22,
    marginBottom: 16,
    fontFamily: 'DMSans-Regular',
    textAlign: 'center',
  },
  sectionTitle: {
    fontSize: 16,
    color: '#00ADB5',
    marginBottom: 12,
    fontFamily: 'DMSans-Bold',
    textAlign: 'center',
  },
  valueContainer: {
    marginTop: 8,
    alignItems: 'center',
    width: '100%',
  },
  valueItem: {
    marginBottom: 16,
    alignItems: 'center',
  },
  valueTitle: {
    fontSize: 15,
    color: '#000000',
    marginBottom: 4,
    fontFamily: 'DMSans-Medium',
    textAlign: 'center',
  },
  valueDescription: {
    fontSize: 14,
    color: '#666666',
    fontFamily: 'DMSans-Regular',
    textAlign: 'center',
  },
  ctaContainer: {
    paddingHorizontal: 16,
    paddingVertical: 24,
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
  },
  ctaDivider: {
    width: '80%',
    height: 4,
    backgroundColor: '#00ADB5',
    borderRadius: 2,
    marginBottom: 16,
  },
  ctaText: {
    fontSize: 14,
    color: '#333333',
    marginBottom: 12,
    fontFamily: 'DMSans-Medium',
    textAlign: 'center',
  },
  ctaButton: {
    backgroundColor: '#00ADB5',
    paddingHorizontal: 28,
    paddingVertical: 10,
    borderRadius: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  ctaButtonText: {
    color: '#FFFFFF',
    fontSize: 14,
    fontFamily: 'DMSans-Bold',
  },
});