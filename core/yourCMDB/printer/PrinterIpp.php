<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2016 Michael Batz
*
*
* yourCMDB is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* yourCMDB is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with yourCMDB.  If not, see <http://www.gnu.org/licenses/>.
*
*********************************************************************/
namespace yourCMDB\printer;

/**
* Printer using the Internet Printing Protocol (IPP)
* @author Michael Batz <michael@yourcmdb.org>
*/
class PrinterIpp extends Printer
{
	public function printData($data)
	{
		//read configuration
		$configUrl = $this->printerOptions->getOption("url");
		if($configUrl == "")
		{
			throw new PrintException("Printer URL was not configured");
		}

		//ToDo: Error handling, document-format
		//print data
		$this->ippPrint($data, $configUrl);
	}

	/**
	* creates a print job for the given data and sends it to a printer using IPP
	* @param string $data		the data to print
	* @param string $url		HTTP URL for the printer, e.g. http://localhost:631/printers/PDF
	*/
	private function ippPrint($data, $url)
	{
		//ipp operation layer
		$ippData = "";
		//ipp: version
		$ippData .= chr(0x01).chr(0x01);
		//ipp: operationID PrintJob
		$ippData .= chr(0x00).chr(0x02);
		//ipp: requestID
		$ippData .= chr(0x00).chr(0x00).chr(0x00).chr(0x01);
	
		//ipp: operation attributes
		$ippData .= chr(0x01);
		$ippData .= $this->ippEncodeAttribute(0x47, "attributes-charset", "utf-8");
		$ippData .= $this->ippEncodeAttribute(0x48, "attributes-natural-language", "en-us");
		$ippData .= $this->ippEncodeAttribute(0x45, "printer-uri", $this->ippCreateIppUrl($url));
		$ippData .= $this->ippEncodeAttribute(0x44, "document-format", "application/pdf");
		$ippData .= chr(0x03);
		$ippData .= $data;

		//create curl request
		$curl = curl_init();
		$curlOptions = array
		(
			CURLOPT_URL		=> $url,
			CURLOPT_CUSTOMREQUEST	=> "POST",
			CURLOPT_POSTFIELDS	=> $ippData,
			CURLOPT_HTTPHEADER	=> array('Content-Type: application/ipp', "Content-Length: ".strlen($ippData)),
			CURLOPT_RETURNTRANSFER	=> true
		);
		curl_setopt_array($curl, $curlOptions);
		$result = curl_exec($curl);
		curl_close($curl);
	}

	/**
	* IPP helper function: create IPP URL from HTTP URL
	* @param string $httpUrl	HTTP URL for the printer
	* @return string		IPP URL for the printer
	*/
	private function ippCreateIppUrl($httpUrl)
	{
		$urlParsed = parse_url($httpUrl);

		//create IPP URL: schema
		$urlOutput = "ipp://";
		$urlOutput .= $urlParsed['host'];

		//create IPP URL: set port
		if(isset($urlParsed['port']))
		{
			//if port is set and non IPP default, set port
			if($urlParsed['port'] != 631)
			{
				$urlOutput .= ":";
				$urlOutput .= $urlParsed['port'];
			}
		}
		else
		{
			//if no port is set, use the HTTP default port (80)
			$urlOutput .= ":80";
		}

		//create IPP URL: set path
		if(isset($urlParsed['path']))
		{
			$urlOutput .= $urlParsed['path'];
		}
		else
		{
			$urlOutput .= "/";
		}
		
		//create IPP URL: set query
		if(isset($urlParsed['query']))
		{
			$urlOutput .= "?";
			$urlOutput .= $urlParsed['query'];
		}
		
		return $urlOutput;
	}

	/**
	* IPP helper function: encodes an operation attribute
	* @param string	$valueTag	value tag to encode
	* @param string	$key		attribute key
	* @param string	$value		attribute value
	* @return string		the encoded attribute
	*/
	private function ippEncodeAttribute($valueTag, $key, $value)
	{
		$ippData = "";

		//value tag
		$ippData .= chr($valueTag);

		//key
		$ippData .= $this->ippCalculateStringLength($key);
		$ippData .= $key;

		//value
		$ippData .= $this->ippCalculateStringLength($value);
		$ippData .= $value;
		return $ippData;
	}

	/**
	* IPP helper function: calculates the string length of $input
	* @param string	$input		String to calculate the length for encoding in IPP
	* @return string		calculated string length encoded for IPP
	*/
	private function ippCalculateStringLength($input)
	{
		$length = strlen($input);

		$int1 = $length & 0xFF;
		$length -= $int1;
		$length = $length >> 8;
		$int2 = $length & 0xFF;

		$output = chr($int2) . chr($int1);
		return $output;
	}

}
?>
