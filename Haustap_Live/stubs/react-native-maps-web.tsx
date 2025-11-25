import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

type MapViewProps = {
  children?: React.ReactNode;
  style?: any;
};

export default function MapView(props: MapViewProps) {
  return (
    <View style={[styles.placeholder, props.style]}> 
      <Text style={styles.text}>MapView is stubbed on web.</Text>
    </View>
  );
}

export function Marker() {
  return (
    <View style={styles.marker}>
      <Text style={styles.text}>Marker</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  placeholder: {
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#eef3f6',
    width: '100%',
    height: '100%'
  },
  marker: {
    padding: 4,
    backgroundColor: '#ddd',
    borderRadius: 4,
  },
  text: {
    color: '#666',
    fontSize: 12,
  }
});