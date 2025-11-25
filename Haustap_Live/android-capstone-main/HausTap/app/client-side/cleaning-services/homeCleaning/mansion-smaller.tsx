import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

const services = [
	{
		title: "Basic Cleaning – 4-5 Cleaner",
		price: "₱8,000",
		desc: "Inclusions:\nAll rooms swept, mopped, dusted\nTrash collection\nLarge windows wiped",
	},
	{
		title: "Standard Cleaning – 5 Cleaners",
		price: "₱12,000",
		desc: "Inclusions:\nAll Basic tasks, Kitchen deep clean (multiple areas)\nBathrooms scrubbed & sanitized\nFurniture base cleaning",
	},
	{
		title: "Deep Cleaning – 6 Cleaners",
		price: "₱20,000",
		desc: "Inclusions:\nAll Standard tasks, Grout & tile scrubbing\nBehind appliances & furniture\nUpholstery & carpet deep clean\nFull disinfection",
	},
];

export default function MansionSmallerScreen() {
	return (
		<MultiSelectList
			items={services}
			mainCategory="Cleaning Services"
			serviceName="Mansion — Smaller"
		/>
	);
}

const styles = StyleSheet.create({});

