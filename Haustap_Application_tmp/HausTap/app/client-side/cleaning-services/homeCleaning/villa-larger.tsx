import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

const services = [
	{
		title: "Basic Cleaning – 4 Cleaner",
		price: "₱7,000",
		desc: "Inclusions:\nAll rooms swept, mopped, dusted\nTrash collection\nWindows & mirrors wiped",
	},
	{
		title: "Standard Cleaning – 5 Cleaners",
		price: "₱12,000",
		desc: "Inclusions:\nAll Basic tasks, Kitchen deep clean\nBathroom full scrub\nUnder furniture cleaning",
	},
	{
		title: "Deep Cleaning – 6 Cleaners",
		price: "₱20,000",
		desc: "Inclusions:\nAll Standard tasks, Tile scrubbing & grout cleaning\nBehind appliances/furniture\nCarpet shampoo\nFull disinfection",
	},
];

export default function VillaLargerScreen() {
	return (
		<MultiSelectList
			items={services}
			mainCategory="Cleaning Services"
			serviceName="Villa — Larger"
		/>
	);
}

const styles = StyleSheet.create({});

