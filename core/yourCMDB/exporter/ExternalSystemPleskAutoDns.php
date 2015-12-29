<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2015 Michael Batz
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
namespace yourCMDB\exporter;

use yourCMDB\entities\CmdbObject;

/**
* Export API - External System: DNS of Plesk Automation
* Exports DNS A Records to Plesk Automation
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExternalSystemPleskAutoDns implements ExternalSystem
{
	//ExportVariables
	private $variables;

	//URL of Plesk Automation API
	private $pleskApiUrl;

	//User of Plesk Automation API
	private $pleskApiUser;

	//Password of Plesk Automation API
	private $pleskApiPassword;

	//SSL verify during connection to Plesk Automation API
	private $pleskApiSslVerify;

	//Domin name for export
	private $domainName;

	//store for existing records in Plesk Automation
	private $existingRecords;

	//store for created records in Plesk Automation
	private $createdRecords;

	public function setUp(ExportDestination $destination, ExportVariables $variables)
	{
		//get variables
		$this->variables = $variables;

		//check, if parameters are set
		$parameterKeys = $destination->getParameterKeys();
		if(!(	in_array("pleskApiUrl", $parameterKeys) && 
			in_array("pleskApiUser", $parameterKeys) && 
			in_array("pleskApiPassword", $parameterKeys) &&
			in_array("domainName", $parameterKeys)))
		{
			throw new ExportExternalSystemException("Parameters for ExternalSystem not set correctly");
		}

		//get parameters for Plesk automation access
		$this->pleskApiUrl = $destination->getParameterValue("pleskApiUrl");
		$this->pleskApiUser = $destination->getParameterValue("pleskApiUser");
		$this->pleskApiPassword = $destination->getParameterValue("pleskApiPassword");
		$this->domainName = $destination->getParameterValue("domainName");

		//SSL verify option
		$this->pleskApiSslVerify = "true";
		if(in_array("pleskApiSslVerify", $parameterKeys))
		{
			$this->pleskApiSslVerify = $destination->getParameterValue("pleskApiSslVerify");
		}

		//get all existing DNS records from Plesk Automation
		$this->existingRecords = $this->getARecords($this->domainName);
		$this->createdRecords = Array();
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object)
	{
		//get variables from object
		$hostname = $this->formatHostname($this->variables->getVariable("hostname")->getValue($object));
		$ip = $this->formatIp($this->variables->getVariable("ip")->getValue($object));

		//check if a DNS record exist for object
		if(isset($this->existingRecords[$hostname]))
		{
			//check if entry has changed
			if($this->existingRecords[$hostname]["data"] != $ip)
			{
				if(!isset($this->createdRecords[$hostname]))
				{
					//recreate entry
					$this->deleteRecord($this->existingRecords[$hostname]["id"]);
					$this->addARecord($this->domainName, $hostname, $ip);
				}
			}

			//delete entry from exitsing records array
			unset($this->existingRecords[$hostname]);
		}
		//if not create a new one
		else
		{
			if(!isset($this->createdRecords[$hostname]))
			{
				$this->addARecord($this->domainName, $hostname, $ip);
			}
		}

		//save to created records
		$this->createdRecords[$hostname] = $ip;

	}

	public function finishExport()
	{
		//delete all DNS records that does not exist in CMDB
		foreach($this->existingRecords as $record)
		{
			$this->deleteRecord($record["id"]);
		}
	}

	/**
	* Get all A Records for the given domain 
	* @param string $domain		domain name
	* @return Array			Array of a records
	*/
	private function getARecords($domain)
	{
		//xml definition
		$xml = "<?xml version=\"1.0\"?>";
		$xml.= "<methodCall>";
		$xml.= "<methodName>pem.getDNSRecords</methodName>";
		$xml.= "<params>";
		$xml.= "<param>";
		$xml.= "<value>";
		$xml.= "<struct>";
		//xml definition params
		$xml.= "<member>";
		$xml.= "<name>name</name>";
		$xml.= "<value><string>$domain</string></value>";
		$xml.= "</member>";
		$xml.= "<member>";
		$xml.= "<name>rr_type</name>";
		$xml.= "<value><string>A</string></value>";
		$xml.= "</member>";
		//xml definition: footer
		$xml.= "</struct>";
		$xml.= "</value>";
		$xml.= "</param>";
		$xml.= "</params>";
		$xml.= "</methodCall>";
	
		//get XML output
		$outputXml = $this->getData($xml);
		$xmlobject = simplexml_load_string($outputXml);

		//create array with resourceRecords
		$records = Array();
		foreach($xmlobject->xpath('//data/value/struct') as $record)
		{
			$recordId;
			$recordValue;
			$recordHost;
			foreach($record  as $recordData)
			{
				$name = (string)$recordData[0]->name;
				$value = (string) $recordData->value->children()[0];
				
				switch($name)
				{
					case "host":
						$recordHost = $value;
						if(preg_match("/(.*?).$domain\./", $value, $matches) === 1)
						{
							$recordHost = $matches[1];
						}
						break;
					case "record_id":
						$recordId = $value;
						break;
					case "data":
						$recordValue = $value;
						break;
				}
			}
			if(!isset($records[$recordHost]))
			{
				$records[$recordHost] = Array("id"  => $recordId, "data" => $recordValue);
			}
			else
			{
				//delete dupicate entries
				$this->deleteRecord($recordId);
			}
		}

		//return records
		return $records;
	}

	/**
	* Sends XML data to Plesk Automation and returns result
	* @param string $xml	XML data to send to API
	* @return string	XML data, which are returned from API
	*/
	private function getData($xml)
	{
		//curl request
		$curl = curl_init();
		$curlOptions = array(
			CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
			CURLOPT_USERPWD         => "{$this->pleskApiUser}:{$this->pleskApiPassword}",
			CURLOPT_POSTFIELDS	=> "$xml",
			CURLOPT_CUSTOMREQUEST 	=> "POST",
			CURLOPT_HTTPHEADER	=> array('Content-Type: application/xml'),
			CURLOPT_URL             => $this->pleskApiUrl,
			CURLOPT_RETURNTRANSFER  => TRUE);
		if($this->pleskApiSslVerify != "true")
		{
			$curlOptions[CURLOPT_SSL_VERIFYPEER] = FALSE;
			$curlOptions[CURLOPT_SSL_VERIFYHOST] = FALSE;
		}
		curl_setopt_array($curl, $curlOptions);
		$result = curl_exec($curl);
		$curlHttpResponse = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		//returns XML data	
		return $result;
	}

	/**
	* Adds an A record to the given domain in Plesk Automation
	* @param string $domain		domain for adding the record
	* @param string $hostname	host part
	* @param string $ip		IP address
	*/
	private function addARecord($domain, $hostname, $ip)
	{
		//xml definition: header
		$xml = "<?xml version=\"1.0\"?>";
		$xml.= "<methodCall>";
		$xml.= "<methodName>pem.createDNSRecord</methodName>";
		$xml.= "<params>";
		$xml.= "<param>";
		$xml.= "<value>";
		$xml.= "<struct>";
		//xml definition params
		$xml.= "<member>";
		$xml.= "<name>domain_name</name>";
		$xml.= "<value><string>$domain</string></value>";
		$xml.= "</member>";
		$xml.= "<member>";
		$xml.= "<name>host</name>";
		$xml.= "<value><string>$hostname</string></value>";
		$xml.= "</member>";
		$xml.= "<member>";
		$xml.= "<name>type</name>";
		$xml.= "<value><string>A</string></value>";
		$xml.= "</member>";
		$xml.= "<member>";
		$xml.= "<name>data</name>";
		$xml.= "<value><string>$ip</string></value>";
		$xml.= "</member>";
		//xml definition: footer
		$xml.= "</struct>";
		$xml.= "</value>";
		$xml.= "</param>";
		$xml.= "</params>";
		$xml.= "</methodCall>";
	
		//send XML data to Plesk automation
		$this->getData($xml);
	}

	/**
	* Deletes the DNS record with the given id
	* @param $id	ID of the DNS record
	*/
	private function deleteRecord($id)
	{
		//xml definition
		$xml = "<?xml version=\"1.0\"?>";
		$xml.= "<methodCall>";
		$xml.= "<methodName>pem.deleteDNSRecord</methodName>";
		$xml.= "<params>";
		$xml.= "<param>";
		$xml.= "<value>";
		$xml.= "<struct>";
		//xml definition params
		$xml.= "<member>";
		$xml.= "<name>record_id</name>";
		$xml.= "<value><int>$id</int></value>";
		$xml.= "</member>";
		//xml definition: footer
		$xml.= "</struct>";
		$xml.= "</value>";
		$xml.= "</param>";
		$xml.= "</params>";
		$xml.= "</methodCall>";
	
		//send XML data to Plesk automation
		$this->getData($xml);
	
	}

	/**
	* Checks, if a hostname has the correct format
	* @return $string	input in the correct format
	*/
	private function formatHostname($input)
	{
		$input = preg_replace("/ /", "", $input);
		$input = preg_replace("/[^A-Za-z0-9\-\.]/", "", $input);
		return $input;
	}

	/**
	* Checks if an IPv4 address has the correct format
	* @return $string	input address, if it has the correct format
	*			0.0.0.0 if not
	*/	
	private function formatIp($input)
	{
		if(filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		{
			return $input;
		}
		return "0.0.0.0";
	}

}
?>
