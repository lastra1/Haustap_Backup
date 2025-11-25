import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

const services = [
	{
		title: "Basic Cleaning – 6 Cleaner",
		price: "₱15,000",
		desc: "Inclusions:\nAll areas swept, mopped, dusted\nTrash collection\nWindow/glass wipe",
	},
	{
		title: "Standard Cleaning – 6-7 Cleaners",
		price: "₱25,000",
		desc: "Inclusions:\nAll Basic tasks, Kitchen & multiple bathrooms deep clean\nFurniture base & under cleaning",
	},
	{
		title: "Deep Cleaning – 8 Cleaners",
		price: "₱40,000",
		desc: "Inclusions:\nAll Standard tasks, Floor polishing & grout cleaning\nBehind appliances/furniture\nCarpet shampoo\nDisinfection full house",
	},
];

export default function MansionLargerScreen() {
	return (
		<MultiSelectList
			items={services}
			mainCategory="Cleaning Services"
			serviceName="Mansion — Larger"
		/>
	);
}

const styles = StyleSheet.create({});

