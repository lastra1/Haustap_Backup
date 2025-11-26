import React from 'react';

type LatLng = { latitude: number; longitude: number };
type Region = LatLng & { latitudeDelta?: number; longitudeDelta?: number };

export default function WebMap({ style, initialRegion, marker, onPress }: { style?: any; initialRegion: Region; marker: LatLng; onPress?: (e: { nativeEvent: { coordinate: LatLng } }) => void }) {
  const mapRef = React.useRef<any>(null);
  const markerRef = React.useRef<any>(null);
  const containerRef = React.useRef<HTMLDivElement | null>(null);

  const loadLeaflet = React.useCallback(async () => {
    if ((window as any).L) return (window as any).L;
    const cssId = 'leaflet-css';
    if (!document.getElementById(cssId)) {
      const link = document.createElement('link');
      link.id = cssId;
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      document.head.appendChild(link);
    }
    await new Promise<void>((resolve) => {
      const s = document.createElement('script');
      s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
      s.onload = () => resolve();
      document.body.appendChild(s);
    });
    return (window as any).L;
  }, []);

  React.useEffect(() => {
    let disposed = false;
    (async () => {
      const L = await loadLeaflet();
      if (disposed || !containerRef.current) return;
      mapRef.current = L.map(containerRef.current).setView([initialRegion.latitude, initialRegion.longitude], 15);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(mapRef.current);
      markerRef.current = L.marker([marker.latitude, marker.longitude]).addTo(mapRef.current);
      mapRef.current.on('click', (ev: any) => {
        const latlng = ev.latlng;
        if (onPress) onPress({ nativeEvent: { coordinate: { latitude: latlng.lat, longitude: latlng.lng } } });
      });
    })();
    return () => { disposed = true; if (mapRef.current) { mapRef.current.remove(); mapRef.current = null; } };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  React.useEffect(() => {
    const L = (window as any).L;
    if (L && mapRef.current && markerRef.current) {
      markerRef.current.setLatLng([marker.latitude, marker.longitude]);
    }
  }, [marker]);

  return <div ref={containerRef} style={{ width: '100%', height: '100%', ...(style || {}) }} />;
}