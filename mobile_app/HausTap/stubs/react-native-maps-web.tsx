import React from 'react';
import { View } from 'react-native';

type LatLng = { latitude: number; longitude: number };
type Region = LatLng & { latitudeDelta: number; longitudeDelta: number };

type MapViewProps = {
  style?: any;
  initialRegion?: Region;
  onPress?: (e: { nativeEvent: { coordinate: LatLng } }) => void;
  children?: React.ReactNode;
};

export default function MapView({ style, children }: MapViewProps) {
  return <View style={style}>{children}</View>;
}

type MarkerProps = {
  coordinate: LatLng;
  anchor?: { x: number; y: number };
  children?: React.ReactNode;
};

export function Marker({ children }: MarkerProps) {
  return <View>{children}</View>;
}