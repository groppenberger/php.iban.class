<?php

/* 
	Sources
	################################################################
	http://www.pruefziffernberechnung.de/I/IBAN.shtml
	http://en.wikipedia.org/wiki/International_Bank_Account_Number
*/

$iban_countries = array("AT" => array(
								 "iso" => "1029",
				   				 "format" => "ATkk bbbb bccc cccc cccc",
				   				 "length" => 20
				   				 ),
				   	   "DE" => array(
				   	   			 "iso" => "1314",
				   				 "format" => "DEkk bbbb bbbb cccc cccc cc",
				   				 "length" => 22
				   				  ),				 
				   	   "CH" => array(
				   	   		     "iso" => "1217",
				   				 "format" => "CHkk bbbb bccc cccc cccc c",
				   				 "length" => 21
				   				  ),
				   );


				  
class iban {

	/**
	 * validate - Validate IBAN 
	 * 
	 * @param String $iban
	 * @return void
	 */
	public static function validate($iban) {
		
		global $iban_countries;
		
		// Get country by iban 
		$country = substr($iban,0,2);

		
		// Check length
		if (strlen($country) == $iban_countries[$country]["length"]) {
			$err = "IBAN Length not valid.";
		}
		
		// Checksum Check
		$end = substr($iban,2,2);  $start  = substr($iban,4);
		$checksum = $start.$iban_countries[$country]["iso"].$end;
		
		if (bcmod($checksum, '97') != 1) {
			$err = "IBAN not valid.";
		}
		
		if (!isset($err)) {
			return true;
		}
		else
		{
			return $err;
		}
		
	} 
		
	/**
	 * getAccount - Return Account Number
	 * 
	 * @param String $iban
	 * @return String
	 */
	public static function getAccount($iban) {
		$array = iban::parse($iban);
		return $array["account"];
	}
	
	/**
	 * getBankcode - Return Bank Code
	 * 
	 * @param String $iban
	 * @return String
	 */
	public static function getBankcode($iban) {
		$array = iban::parse($iban);
		return $array["bankcode"];
	}
		
	/**
	 * parse - Parse IBAN to Account No and Bankcode
	 * 
	 * @param String $iban
	 * @return Array
	 */
	public static function parse($iban) {
		
		global $iban_countries;
		
		$array = array();
		
		if (iban::validate($iban)) {
		
			// Get country by iban 
			$country = substr($iban,0,2);
					
			$array["bankcode"] = substr($iban,strpos(str_replace(" ","",($iban_countries[$country]["format"])),"b"),substr_count($iban_countries[$country]["format"],"b"));
			$array["account"]  = substr($iban,strpos(str_replace(" ","",($iban_countries[$country]["format"])),"c"),substr_count($iban_countries[$country]["format"],"c"));		
			$array["country"]  = $country;
			
			return $array;
		}
		else
		{
			return false;
		}
	}	
	
	/**
	 * generate - Generate IBAN
	 * 
	 * @param Int $account
	 * @param Int $bankcode
	 * @param String $country
	 * @param string $additional (default: '00')
	 * @return string | void
	 */
	public static function generate($account,$bankcode,$country, $additional = '00') {
		
		global $iban_countries;
	
		if (isset($iban_countries[$country])) {
		
			// Fill Bankcode and Account Number with 000
			$bankcode 	= str_pad($bankcode, substr_count($iban_countries[$country]["format"],"b"), "0", STR_PAD_LEFT); 
			$account 	= str_pad($account, substr_count($iban_countries[$country]["format"],"c"), "0", STR_PAD_LEFT); 
			
			// Generate checksum
			$checksum = $bankcode.$account.$iban_countries[$country]["iso"].$additional;
			$checksum = 98 - (bcmod($checksum, '97') ); 
			$checksum = str_pad($checksum, substr_count($iban_countries[$country]["format"],"k"), "0", STR_PAD_LEFT); 
			
			// Generate iban
			$iban = $country.$checksum.$bankcode.$account;
			
			return $iban;
	
		}
		else
		{
			// Country not in the list
			return false;
		}
	
		
	}

}






