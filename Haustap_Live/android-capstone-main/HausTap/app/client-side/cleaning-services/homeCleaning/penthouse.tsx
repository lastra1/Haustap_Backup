import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

const services = [
	{
		title: "Basic Cleaning – 3 Cleaner",
		price: "₱2,500",
		desc: "Inclusions:\nLiving room, dining, bedrooms: sweep, mop, dust, trash removal\nLarge windows & mirrors: wipe\nBalconies: sweep & mop",
	},
	{
		title: "Standard Cleaning – 3-4 Cleaners",
		price: "₱4,000",
		desc: "Inclusions:\nAll Basic tasks, Kitchen: stove, counters, cabinets wiped\nBathrooms: toilet, shower, sink scrubbing\nUnder furniture cleaning",
	},
	{
		title: "Deep Cleaning – 4 Cleaners",
		price: "₱6,500",
		desc: "Inclusions:\nAll Standard tasks, Scrub grout & tiles\nClean behind large appliances\nUpholstery/carpet deep cleaning\nFull disinfection",
	},
];

export default function PenthouseScreen() {
	return (
		<MultiSelectList items={services} mainCategory="Cleaning Services" serviceName="Penthouse" />
	);
}

const styles = StyleSheet.create({});
