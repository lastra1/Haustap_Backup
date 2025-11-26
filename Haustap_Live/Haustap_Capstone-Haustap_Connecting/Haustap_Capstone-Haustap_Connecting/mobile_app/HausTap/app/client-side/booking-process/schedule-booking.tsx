import { Ionicons } from "@expo/vector-icons";
import DateTimePicker from '@react-native-community/datetimepicker';
import { useLocalSearchParams, useRouter } from "expo-router";
import React, { useMemo, useState } from "react";
import {
    Platform,
    ScrollView,
    StyleSheet,
    Text,
    TouchableOpacity,
    View,
} from "react-native";

export default function ScheduleBooking() {
  const router = useRouter();
  const { categoryTitle, categoryPrice, categoryDesc, address, location, mainCategory, subCategory, service, selectedItems } = useLocalSearchParams();
  const [selectedDate, setSelectedDate] = useState<string | null>(null);
  const [selectedTime, setSelectedTime] = useState<string>("");
  const [isFullyBooked, setIsFullyBooked] = useState(false);
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [showTimePicker, setShowTimePicker] = useState(false);

  // We now use a calendar/date picker instead of a fixed 7-day grid.

  // (We no longer use a static time list; user picks a time via time picker)

  const formatTime = (d: Date) => d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

  // Check if date is fully booked - Saturday and Sunday are fully booked
  const checkAvailability = (date: Date) => {
    const dayOfWeek = date.getDay();
    return dayOfWeek !== 0 && dayOfWeek !== 6;
  };

  // minimum selectable date: today (start of day)
  const minDate = new Date();
  minDate.setHours(0,0,0,0);

  // No need for useEffect since we handle availability in handleDateSelect

  const handleDateSelect = (date: Date) => {
    // guard: do not accept past dates
    const picked = new Date(date);
    picked.setHours(0,0,0,0);
    if (picked < minDate) {
      // ignore and keep picker open (or show message)
      return;
    }
    const formatted = date.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" });
    setSelectedDate(formatted);
    const isAvailable = checkAvailability(date);
    setIsFullyBooked(!isAvailable);
    if (!isAvailable) setSelectedTime("");
  };

  const timesWeb = useMemo(() => [
    '08:00 AM','09:00 AM','10:00 AM','11:00 AM','12:00 PM','01:00 PM','02:00 PM','03:00 PM','04:00 PM','05:00 PM'
  ], []);

  const [calendarMonth, setCalendarMonth] = useState(() => {
    const d = new Date();
    return new Date(d.getFullYear(), d.getMonth(), 1);
  });

  const monthDays = useMemo(() => {
    const year = calendarMonth.getFullYear();
    const month = calendarMonth.getMonth();
    const firstDay = new Date(year, month, 1);
    const startOffset = firstDay.getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const grid: Array<Date | null> = [];
    for (let i = 0; i < startOffset; i++) grid.push(null);
    for (let d = 1; d <= daysInMonth; d++) grid.push(new Date(year, month, d));
    while (grid.length % 7 !== 0) grid.push(null);
    return grid;
  }, [calendarMonth]);

  return (
    <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()}>
          <Ionicons name="arrow-back" size={24} color="black" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Schedule Booking</Text>
      </View>

      <Text style={styles.sectionTitle}>Set your Date & Time</Text>

      {/* Calendar / Date Picker */}
      <TouchableOpacity style={styles.calendarPicker} onPress={() => setShowDatePicker(true)}>
        <Text style={{ color: selectedDate ? '#000' : '#888' }}>{selectedDate ?? 'Choose a date'}</Text>
      </TouchableOpacity>
      {showDatePicker && (
        Platform.OS === 'web' ? (
          <View style={styles.webCalendar}>
            <View style={styles.webCalHeader}>
              <TouchableOpacity onPress={() => setCalendarMonth(new Date(calendarMonth.getFullYear(), calendarMonth.getMonth() - 1, 1))}>
                <Ionicons name="chevron-back" size={20} color="#000" />
              </TouchableOpacity>
              <Text style={styles.webCalTitle}>
                {calendarMonth.toLocaleDateString(undefined, { month: 'long', year: 'numeric' })}
              </Text>
              <TouchableOpacity onPress={() => setCalendarMonth(new Date(calendarMonth.getFullYear(), calendarMonth.getMonth() + 1, 1))}>
                <Ionicons name="chevron-forward" size={20} color="#000" />
              </TouchableOpacity>
            </View>
            <View style={styles.webCalWeek}>
              {['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].map((d) => (
                <Text key={d} style={styles.webCalWeekDay}>{d}</Text>
              ))}
            </View>
            <View style={styles.webCalGrid}>
              {monthDays.map((d, i) => {
                const disabled = !d || d < minDate;
                const fully = !!d && !checkAvailability(d);
                return (
                  <TouchableOpacity
                    key={i}
                    style={[styles.webCalCell, disabled && styles.webCalCellDisabled, fully && styles.webCalCellFully]}
                    disabled={disabled}
                    onPress={() => {
                      if (!d) return;
                      handleDateSelect(d);
                      setShowDatePicker(false);
                    }}
                  >
                    <Text style={styles.webCalCellText}>{d ? d.getDate() : ''}</Text>
                  </TouchableOpacity>
                );
              })}
            </View>
          </View>
        ) : (
          <DateTimePicker
            value={new Date()}
            minimumDate={minDate}
            mode="date"
            display={Platform.OS === 'ios' ? 'spinner' : 'calendar'}
            onChange={(_e: any, d: Date | undefined) => {
              if (Platform.OS !== 'ios') setShowDatePicker(false);
              if (d) handleDateSelect(d);
            }}
          />
        )
      )}

      {/* Time Selection (native time picker trigger) */}
      {selectedDate && !isFullyBooked && (
        Platform.OS === 'web' ? (
          <View>
            <Text style={styles.label}>Time</Text>
            <View style={styles.timeGrid}>
              {timesWeb.map((t) => (
                <TouchableOpacity key={t} style={[styles.timeChip, selectedTime === t && styles.timeChipSelected]} onPress={() => setSelectedTime(t)}>
                  <Text style={[styles.timeChipText, selectedTime === t && styles.timeChipTextSelected]}>{t}</Text>
                </TouchableOpacity>
              ))}
            </View>
          </View>
        ) : (
          <>
            <Text style={styles.label}>Time</Text>
            <TouchableOpacity style={styles.calendarPicker} onPress={() => setShowTimePicker(true)}>
              <Text style={{ color: selectedTime ? '#000' : '#888' }}>{selectedTime ?? 'Choose a time'}</Text>
            </TouchableOpacity>
            {showTimePicker && (
              <DateTimePicker
                value={new Date()}
                mode="time"
                is24Hour={false}
                display={Platform.OS === 'ios' ? 'spinner' : 'clock'}
                onChange={(_e: any, d: Date | undefined) => {
                  if (Platform.OS !== 'ios') setShowTimePicker(false);
                  if (d) setSelectedTime(formatTime(d));
                }}
              />
            )}
          </>
        )
      )}

      {/* Fully Booked Warning */}
      {isFullyBooked && selectedDate && (
        <Text style={styles.fullyBookedText}>
          Sorry, we're fully booked for today.
        </Text>
      )}

      {/* Buttons */}
      <View style={styles.buttonContainer}>
        <TouchableOpacity
          style={[
            styles.scheduleBtn,
            // cancel button always enabled (just navigate back)
          ]}
          onPress={() => router.back()}
        >
          <Text style={styles.scheduleText}>Cancel</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.nextBtn, { opacity: selectedDate && selectedTime && !isFullyBooked ? 1 : 0.5 }]}
          disabled={!selectedDate || !selectedTime || isFullyBooked}
          onPress={() =>
            router.push({
              pathname: "/client-side/booking-process/booking-overview",
              params: {
                date: selectedDate,
                time: selectedTime,
                categoryTitle,
                categoryPrice,
                categoryDesc,
                address,
                location,
                mainCategory,
                subCategory,
                service,
                selectedItems,
              },
            } as any)
          }
        >
          <Text style={styles.nextText}>Schedule</Text>
        </TouchableOpacity>
      </View>
    </ScrollView>
  );
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
    marginBottom: 20,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "600",
    marginLeft: 10,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: "700",
    marginBottom: 16,
  },
  dateGrid: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 8,
    marginBottom: 24,
  },
  dateCard: {
    width: "48%",
    backgroundColor: "#F5F5F5",
    borderRadius: 8,
    padding: 12,
    alignItems: "center",
  },
  dateCardSelected: {
    backgroundColor: "#00ADB5",
  },
  dateDay: {
    fontSize: 12,
    color: "#666",
    marginBottom: 4,
  },
  dateText: {
    fontSize: 14,
    fontWeight: "600",
    color: "#000",
  },
  calendarPicker: {
    backgroundColor: '#F5F5F5',
    borderRadius: 8,
    paddingVertical: 12,
    paddingHorizontal: 14,
    marginBottom: 24,
    alignItems: 'center',
  },
  webCalendar: {
    backgroundColor: '#F5F5F5',
    borderRadius: 8,
    padding: 12,
    marginBottom: 24,
  },
  webCalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  webCalTitle: {
    fontSize: 16,
    fontWeight: '600',
  },
  webCalWeek: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  webCalWeekDay: {
    width: `${100/7}%`,
    textAlign: 'center',
    fontSize: 12,
    color: '#666',
  },
  webCalGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
  },
  webCalCell: {
    width: `${100/7}%`,
    paddingVertical: 10,
    alignItems: 'center',
  },
  webCalCellDisabled: {
    opacity: 0.3,
  },
  webCalCellFully: {
    backgroundColor: '#FDECEC',
  },
  webCalCellText: {
    fontSize: 14,
  },
  timeGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  timeChip: {
    paddingVertical: 8,
    paddingHorizontal: 12,
    borderRadius: 20,
    borderWidth: 1,
    borderColor: '#ddd',
    backgroundColor: '#fff',
    marginBottom: 8,
    marginRight: 8,
  },
  timeChipSelected: {
    borderColor: '#00ADB5',
    backgroundColor: '#E6F7F8',
  },
  timeChipText: {
    fontSize: 14,
    color: '#222',
  },
  timeChipTextSelected: {
    color: '#00ADB5',
    fontWeight: '600',
  },
  label: {
    fontSize: 14,
    color: "#444",
    marginBottom: 8,
  },
  pickerWrapper: {
    backgroundColor: "#F5F5F5",
    borderRadius: 8,
    marginBottom: 24,
    overflow: "hidden",
  },
  picker: {
    height: 48,
  },
  fullyBookedText: {
    color: "#FF3B30",
    textAlign: "center",
    marginVertical: 16,
    fontSize: 14,
  },
  buttonContainer: {
    flexDirection: "row",
    gap: 12,
    marginTop: "auto",
    paddingVertical: 20,
  },
  scheduleBtn: {
    flex: 1,
    backgroundColor: "#00ADB5",
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: "center",
  },
  scheduleBtnDisabled: {
    backgroundColor: "#ccc",
  },
  scheduleText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "700",
  },
  nextBtn: {
    flex: 1,
    backgroundColor: "#333",
    borderRadius: 8,
    paddingVertical: 14,
    alignItems: "center",
  },
  nextText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "700",
  },
});
