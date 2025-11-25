import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

export default function ContainerHouseMultipleScreen() {
	const services = [
		{
			title: "Basic Cleaning – 1 Cleaner",
			price: "₱900",
			desc: "Inclusions:\nLiving, bedroom area sweep, mop, dust\nTrash removal",
		},
		{
			title: "Standard Cleaning – 1-2 Cleaners",
			price: "₱1,400",
			desc: "Inclusions:\nAll Basic tasks, Kitchenette wipe\nBathroom scrub",
		},
		{
			title: "Deep Cleaning – 2 Cleaners",
			price: "₱2,500",
			desc: "Inclusions:\nAll Standard tasks, Deep floor scrubbing\nDisinfection & carpet vacuum",
		},
	];

	return <MultiSelectList items={services} mainCategory="Cleaning Services" serviceName="Container House — Multiple" />;
}

const styles = StyleSheet.create({});

