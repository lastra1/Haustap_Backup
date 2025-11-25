import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

const services = [
	{
		title: "Basic Cleaning – 2 Cleaner",
		price: "₱1,500",
		desc:
			"Inclusions:\nLiving room & bedrooms: sweeping, mopping, dusting\nMirrors & windows: wipe\nTrash removal",
	},
	{
		title: "Standard Cleaning – 2-3 Cleaners",
		price: "₱2,500",
		desc:
			"Inclusions:\nAll Basic tasks: Kitchen: deep wipe & sink scrubbing\nBathroom: toilet, shower, sink, disinfect floors\nClean under beds & sofa",
	},
	{
		title: "Deep Cleaning – 3 Cleaners",
		price: "₱4,000",
		desc:
			"Inclusions:\nAll Standard tasks: Tile grout scrubbing\nBehind appliances cleaning\nCarpet shampoo/vacuum\nDisinfection of high-touch areas",
	},
];

export default function Condo2BRScreen() {
	return (
		<MultiSelectList
			items={services}
			mainCategory="Cleaning Services"
			serviceName="Condominium 2BR"
		/>
	);
}

const styles = StyleSheet.create({});

