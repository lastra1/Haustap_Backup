import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

const services = [
	{
		title: "Basic Cleaning – 2 Cleaner",
		price: "₱2,000",
		desc: "Inclusions:\nLiving room, bedrooms, dining: sweep, mop, dust\nTrash removal\nWindows: quick wipe",
	},
	{
		title: "Standard Cleaning – 3 Cleaners",
		price: "₱3,500",
		desc: "Inclusions:\nAll Basic tasks, Detailed tile scrubbing\nClean behind appliances\nUpholstery & carpet cleaning\nDisinfection",
	},
	{
		title: "Deep Cleaning – 3 Cleaners",
		price: "₱5,500",
		desc: "Inclusions:\nAll Basic tasks, Kitchen: stove top, counters, sink\nBathrooms: toilet, sink, shower scrubbing\nFurniture under cleaning",
	},
];

export default function DuplexSmallerScreen() {
	return (
		<MultiSelectList
			items={services}
			mainCategory="Cleaning Services"
			serviceName="Duplex — Smaller"
		/>
	);
}

const styles = StyleSheet.create({});

