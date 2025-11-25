import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

const services = [
	{
		title: "Basic Cleaning – 2 Cleaner",
		price: "₱2,000",
		desc: "Inclusions:\nSweep, mop, dust all rooms\nTrash removal",
	},
	{
		title: "Standard Cleaning – 2-3 Cleaners",
		price: "₱3,500",
		desc: "Inclusions:\nAll Basic tasks, Kitchen & bathroom deep clean\nUnder furniture cleaning",
	},
	{
		title: "Deep Cleaning – 3 Cleaners",
		price: "₱6,000",
		desc: "Inclusions:\nAll Standard tasks, Full tile scrubbing\nDisinfection high-touch areas\nCarpet cleaning",
	},
];

export default function StiltHouseLargeScreen() {
	return (
		<MultiSelectList
			items={services}
			mainCategory="Cleaning Services"
			serviceName="Stilt House — Large"
		/>
	);
}

const styles = StyleSheet.create({});

