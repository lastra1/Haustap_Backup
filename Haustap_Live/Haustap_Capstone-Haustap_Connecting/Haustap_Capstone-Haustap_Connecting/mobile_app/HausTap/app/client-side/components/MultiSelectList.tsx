import { useRouter } from 'expo-router';
import React, { useEffect, useState } from 'react';
import { ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { useBookingSelection } from '../../context/BookingSelectionContext';

type Item = { title: string; price?: string; desc?: string; section?: string };

export default function MultiSelectList({
  items,
  mainCategory,
  serviceName,
}: {
  items: Item[];
  mainCategory: string;
  serviceName: string;
}) {
  const router = useRouter();
  const [selected, setSelected] = useState<string[]>([]);
  const { getForCategory, setForCategory } = useBookingSelection();

  useEffect(() => {
    // initialize from context when component mounts or mainCategory changes
    try {
      const existing = getForCategory(mainCategory) || [];
      setSelected(existing);
    } catch (e) {
      // ignore if hook not available
    }
  }, [mainCategory]);

  const toggle = (title: string) => {
    setSelected((p) => {
      const next = p.includes(title) ? p.filter((x) => x !== title) : [...p, title];
      try {
        // persist to context per main category
        setForCategory(mainCategory, next);
      } catch (e) {
        // ignore if hook not available
      }
      return next;
    });
  };

  const handleNext = () => {
    router.push({
      pathname: '/client-side/booking-summary',
      params: {
        mainCategory,
        service: serviceName,
        selectedItems: JSON.stringify(selected),
      },
    } as any);
  };

  return (
    <View style={{ flex: 1 }}>
      <ScrollView contentContainerStyle={styles.container}>
        {selected.length > 0 ? (
          <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.selectedBar} contentContainerStyle={styles.selectedBarContent}>
            {selected.map((title) => (
              <TouchableOpacity key={title} style={styles.chip} onPress={() => toggle(title)}>
                <Text style={styles.chipText}>{title}</Text>
                <Text style={styles.chipRemove}>✕</Text>
              </TouchableOpacity>
            ))}
          </ScrollView>
        ) : null}
        {items.map((service, idx) => {
          if (service.section === 'header') {
            return (
              <View key={idx} style={styles.sectionHeader}>
                <Text style={styles.sectionTitle}>{service.title}</Text>
                {service.desc ? <Text style={styles.sectionDesc}>{service.desc}</Text> : null}
              </View>
            );
          }

          const isSelected = selected.includes(service.title);

          return (
            <TouchableOpacity key={idx} style={[styles.categoryBox, isSelected && styles.selectedBox]} onPress={() => toggle(service.title)}>
              <View style={styles.categoryContent}>
                <Text style={styles.categoryTitle}>{service.title}</Text>
                {service.price ? <Text style={styles.categoryPrice}>{service.price}</Text> : null}
                <Text style={styles.categoryDesc}>{service.desc}</Text>
              </View>
              <View style={styles.checkboxBox}>{isSelected ? <View style={styles.checkboxInner} /> : null}</View>
            </TouchableOpacity>
          );
        })}
      </ScrollView>

      <View style={styles.footer}>
        <TouchableOpacity style={[styles.nextButton, { opacity: selected.length ? 1 : 0.5 }]} disabled={!selected.length} onPress={handleNext}>
          <Text style={styles.nextText}>Next</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { padding: 16 },
  categoryBox: {
    backgroundColor: '#DEE1E0',
    borderRadius: 10,
    padding: 15,
    marginBottom: 15,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 3,
    elevation: 3,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  selectedBox: { borderWidth: 2, borderColor: 'cyan' },
  categoryContent: { flex: 1, marginRight: 10 },
  categoryTitle: { fontSize: 16, fontWeight: 'bold', color: '#000', marginBottom: 4 },
  categoryPrice: { fontSize: 14, color: '#444', marginBottom: 4 },
  categoryDesc: { fontSize: 14, color: '#444', lineHeight: 20 },
  checkboxBox: { height: 22, width: 22, borderRadius: 6, borderWidth: 2, borderColor: '#666', alignItems: 'center', justifyContent: 'center', backgroundColor: '#fff' },
  checkboxInner: { height: 12, width: 12, backgroundColor: '#00ADB5', borderRadius: 3 },

  footer: { padding: 16, borderTopWidth: 1, borderTopColor: '#eee', backgroundColor: '#fff' },
  nextButton: { backgroundColor: '#3DC1C6', paddingVertical: 14, borderRadius: 10, alignItems: 'center' },
  nextText: { color: '#fff', fontSize: 18, fontWeight: 'bold' },

  sectionHeader: { marginVertical: 12 },
  sectionTitle: { fontSize: 18, fontWeight: '700', color: '#222', marginBottom: 4 },
  sectionDesc: { fontSize: 14, color: '#666', marginBottom: 8 },
  selectedBar: { maxHeight: 52, marginBottom: 12 },
  selectedBarContent: { paddingHorizontal: 8, alignItems: 'center' },
  chip: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', paddingVertical: 8, paddingHorizontal: 12, borderRadius: 20, marginRight: 8, borderWidth: 1, borderColor: '#ddd' },
  chipText: { fontSize: 14, marginRight: 8, color: '#222' },
  chipRemove: { fontSize: 12, color: '#888' },
});
