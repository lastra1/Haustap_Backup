import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

const services = [
	{
		title: "Basic Cleaning – 1 Cleaner",
		price: "₱1,000",
		desc: "Inclusions:\nSweep, mop, dust, trash disposal\nMirror/window wipe",
	},
	{
		title: "Standard Cleaning – 1-2 Cleaners",
		price: "₱1,800",
		desc: "Inclusions:\nAll Basic tasks, Kitchen cleaning\nBathroom scrubbing",
	},
	{
		title: "Deep Cleaning – 2 Cleaners",
		price: "₱3,000",
		desc: "Inclusions:\nAll Standard tasks, Floor scrubbing, grout cleaning\nBehind furniture/appliances",
	},
];

export default function StiltHouseSmallScreen() {
	return (
		<MultiSelectList
			items={services}
			mainCategory="Cleaning Services"
			serviceName="Stilt House — Small"
		/>
	);
}

const styles = StyleSheet.create({});

