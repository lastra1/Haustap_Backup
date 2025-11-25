import React from 'react';
import { View, Pressable, StyleSheet } from 'react-native';

type Coordinate = { latitude: number; longitude: number };
type Region = { latitude: number; longitude: number; latitudeDelta?: number; longitudeDelta?: number };

type MapViewProps = { style?: any; initialRegion?: Region; onPress?: (e: any) => void; children?: React.ReactNode };

const MapView: React.FC<MapViewProps> = ({ style, initialRegion, onPress, children }) => {
  const handlePress = () => {
    if (onPress) {
      const coord = { latitude: initialRegion?.latitude ?? 0, longitude: initialRegion?.longitude ?? 0 };
      onPress({ nativeEvent: { coordinate: coord } });
    }
  };
  return (
    <Pressable onPress={handlePress} style={[styles.map, style]}>
      <View style={StyleSheet.absoluteFill} />
      {children}
    </Pressable>
  );
};

type MarkerProps = { coordinate: Coordinate; children?: React.ReactNode };

const Marker: React.FC<MarkerProps> = ({ children }) => {
  return <View style={styles.marker}>{children}</View>;
};

export default MapView;
export { Marker };

const styles = StyleSheet.create({
  map: {
    width: '100%',
    height: '100%',
    backgroundColor: '#e6e6e6',
  },
  marker: {
    position: 'absolute',
    left: '50%',
    top: '50%',
    transform: [{ translateX: -18 }, { translateY: -18 }],
    pointerEvents: 'none',
  },
});