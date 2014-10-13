<?php
	# ###
	# Basic Example
	# ###

	require_once("iban.class.php");
	
	// Calculate IBAN 
	$account = "123";	
	$bankcode = "456";	
	$country = "AT";	// Currently only support DE,AT,CH
	
	$iban = iban::generate($account,$bankcode,$country);
	
	
	// Validate IBAN
	if (iban::validate($iban)) {
		echo "Valid!";
	}
	
	
	// Parse IBAN
	$array = iban::parse($iban);
	
	echo "Account No. ".$array["account"];
	
	

