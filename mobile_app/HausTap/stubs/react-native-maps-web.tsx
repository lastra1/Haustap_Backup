import React from 'react';
import { View, Pressable, Image } from 'react-native';

type LatLng = { latitude: number; longitude: number };
type Region = LatLng & { latitudeDelta: number; longitudeDelta: number };

type MapViewProps = {
  style?: any;
  initialRegion?: Region;
  onPress?: (e: { nativeEvent: { coordinate: LatLng } }) => void;
  children?: React.ReactNode;
};

export default function MapView({ style, initialRegion, onPress, children }: MapViewProps) {
  const [size, setSize] = React.useState<{ w: number; h: number }>({ w: 0, h: 0 });
  const containerRef = React.useRef<any>(null);

  const handleLayout = React.useCallback((e: any) => {
    const { width, height } = e.nativeEvent.layout || {};
    if (width && height) setSize({ w: width, h: height });
  }, []);

  const computeAndSend = React.useCallback((relX: number, relY: number) => {
    if (!onPress || !initialRegion) return;
    const lat = initialRegion.latitude + (0.5 - relY) * (initialRegion.latitudeDelta || 0.01);
    const lng = initialRegion.longitude + (relX - 0.5) * (initialRegion.longitudeDelta || 0.01);
    onPress({ nativeEvent: { coordinate: { latitude: lat, longitude: lng } } });
  }, [onPress, initialRegion]);

  const handleResponder = React.useCallback((e: any) => {
    if (!size.w || !size.h) return;
    const { locationX, locationY } = e.nativeEvent || {};
    const relX = Math.max(0, Math.min(1, (locationX ?? size.w / 2) / size.w));
    const relY = Math.max(0, Math.min(1, (locationY ?? size.h / 2) / size.h));
    computeAndSend(relX, relY);
  }, [size, computeAndSend]);

  const handleClick = React.useCallback((e: any) => {
    try {
      const target = e.currentTarget || e.target;
      const rect = target?.getBoundingClientRect?.();
      if (rect) {
        const x = (e.clientX - rect.left) / rect.width;
        const y = (e.clientY - rect.top) / rect.height;
        const relX = Math.max(0, Math.min(1, x));
        const relY = Math.max(0, Math.min(1, y));
        computeAndSend(relX, relY);
        return;
      }
    } catch {}
    handleResponder(e);
  }, [computeAndSend, handleResponder]);

  return (
    <Pressable
      ref={containerRef as any}
      style={[style, { cursor: 'crosshair', position: 'relative' }]}
      onLayout={handleLayout}
      onPress={(e: any) => {
        try {
          const { locationX, locationY } = e.nativeEvent || {};
          if (typeof locationX === 'number' && typeof locationY === 'number' && size.w && size.h) {
            const relX = Math.max(0, Math.min(1, locationX / size.w));
            const relY = Math.max(0, Math.min(1, locationY / size.h));
            computeAndSend(relX, relY);
            return;
          }
        } catch {}
        handleClick(e);
      }}
      onClick={handleClick}
    >
      {initialRegion ? (
        <Image
          source={{
            uri: `https://staticmap.openstreetmap.de/staticmap.php?center=${initialRegion.latitude},${initialRegion.longitude}&zoom=15&size=${Math.max(300, size.w || 600)}x${Math.max(200, size.h || 300)}&markers=${initialRegion.latitude},${initialRegion.longitude},lightblue1`,
          }}
          style={{ position: 'absolute', left: 0, top: 0, width: '100%', height: '100%' }}
          resizeMode="cover"
        />
      ) : null}
      {children}
    </Pressable>
  );
}

type MarkerProps = {
  coordinate: LatLng;
  anchor?: { x: number; y: number };
  children?: React.ReactNode;
};

export function Marker({ children }: MarkerProps) {
  return <View pointerEvents="none">{children}</View>;
}