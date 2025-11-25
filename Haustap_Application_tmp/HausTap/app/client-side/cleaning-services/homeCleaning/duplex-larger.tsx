import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

const services = [
	{
		title: "Basic Cleaning – 3 Cleaner",
		price: "₱3,500",
		desc: "Inclusions:\nAll main areas swept, mopped, dusted\nTrash collection\nWindows/mirrors wiped",
	},
	{
		title: "Standard Cleaning – 3-4 Cleaners",
		price: "₱5,500",
		desc: "Inclusions:\nAll Basic tasks, Kitchen deep clean\nBathrooms scrubbed & disinfected\nFurniture base & underside cleaning",
	},
	{
		title: "Deep Cleaning – 4 Cleaners",
		price: "₱8,000",
		desc: "Inclusions:\nAll Standard tasks, Grout & tile scrubbing\nBehind appliances cleaning\nCarpets vacuum/shampoo\nDisinfection high-touch",
	},
];

export default function DuplexLargerScreen() {
	return (
		<MultiSelectList
			items={services}
			mainCategory="Cleaning Services"
			serviceName="Duplex — Larger"
		/>
	);
}

const styles = StyleSheet.create({});

