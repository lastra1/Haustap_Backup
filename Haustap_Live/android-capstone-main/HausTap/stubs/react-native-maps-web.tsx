import React from 'react'
import { View } from 'react-native'

type MapViewProps = {
  style?: any
  children?: React.ReactNode
  initialRegion?: any
  onPress?: (e: any) => void
}

export default function MapView(props: MapViewProps) {
  return (
    <View
      style={[
        { width: '100%', height: '100%', backgroundColor: '#e6e6e6' },
        props.style,
      ]}
    >
      {props.children}
    </View>
  )
}

export function Marker({ children }: { children?: React.ReactNode }) {
  return <View>{children}</View>
}
