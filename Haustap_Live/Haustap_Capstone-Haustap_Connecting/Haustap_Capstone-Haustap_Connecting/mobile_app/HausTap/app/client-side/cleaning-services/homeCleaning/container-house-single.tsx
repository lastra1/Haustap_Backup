import React from "react";
import { StyleSheet } from "react-native";
import MultiSelectList from "../../components/MultiSelectList";

export default function ContainerHouseSingleScreen() {
  const services = [
	{
	  title: "Basic Cleaning – 1 Cleaner",
	  price: "₱500",
	  desc: "Inclusions:\nLiving space swept, mopped, dusted\nTrash disposal",
	},
	{
	  title: "Standard Cleaning – 1 Cleaners",
	  price: "₱800",
	  desc: "Inclusions:\nAll Basic tasks, Bathroom cleaning\nSmall kitchen wipe",
	},
	{
	  title: "Deep Cleaning – 1 Cleaners",
	  price: "₱1,200",
	  desc: "Inclusions:\nAll Standard tasks, Floor scrubbing & disinfection",
	},
  ];

  return <MultiSelectList items={services} mainCategory="Cleaning Services" serviceName="Container House — Single" />;
}

const styles = StyleSheet.create({});

