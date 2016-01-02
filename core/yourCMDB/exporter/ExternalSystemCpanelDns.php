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
namespace yourCMDB\exporter;

use yourCMDB\entities\CmdbObject;

/**
* Export API - External System: DNS for cPanel
* Exports DNS A Records to cPanel
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExternalSystemCpanelDns implements ExternalSystem
{
	//ExportVariables
	private $variables;

	//Base-URL of cPanel API
	private $cpanelApiUrl;

	//User of cPanel API
	private $cpanelApiUser;

	//Password of cPanel API
	private $cpanelApiPassword;

	//SSL verify during connection to cPanel API
	private $cpanelApiSslVerify;

	//Domain name for export
	private $domainName;

	//store for existing records in cPanel
	private $existingRecords;

	//store for created records in cPanel
	private $createdRecords;

	public function setUp(ExportDestination $destination, ExportVariables $variables)
	{
		//get variables
		$this->variables = $variables;

		//check, if parameters are set
		$parameterKeys = $destination->getParameterKeys();
		if(!(	in_array("cpanelApiUrl", $parameterKeys) && 
			in_array("cpanelApiUser", $parameterKeys) && 
			in_array("cpanelApiPassword", $parameterKeys) &&
			in_array("domainName", $parameterKeys)))
		{
			throw new ExportExternalSystemException("Parameters for ExternalSystem not set correctly");
		}

		//get parameters for cPanel access
		$this->cpanelApiUser = $destination->getParameterValue("cpanelApiUser");
		$this->cpanelApiPassword = $destination->getParameterValue("cpanelApiPassword");
		$this->domainName = $destination->getParameterValue("domainName");
		$this->cpanelApiUrl = $destination->getParameterValue("cpanelApiUrl");
		$this->cpanelApiUrl .= "/cpanel?cpanel_jsonapi_user=".$this->cpanelApiUser;
		$this->cpanelApiUrl .= "&cpanel_jsonapi_apiversion=2&";

		//SSL verify option
		$this->cpanelApiSslVerify = "true";
		if(in_array("cpanelApiSslVerify", $parameterKeys))
		{
			$this->cpanelApiSslVerify = $destination->getParameterValue("cpanelApiSslVerify");
		}

		//get all existing DNS records from cPanel
		$this->existingRecords = $this->getARecords($this->domainName);
		$this->createdRecords = Array();
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object)
	{
		//get variables from object
		$hostname = $this->formatHostname($this->variables->getVariable("hostname")->getValue($object));
		$ip = $this->formatIp($this->variables->getVariable("ip")->getValue($object));

		//ignore CmdbObject, if IP and/or hostname is invalid
		if($ip ==  "" ||  $hostname == "")
		{
			return;
		}

		//check if a DNS record exist for object
		if(isset($this->existingRecords[$hostname]))
		{
			//check if entry has changed
			if($this->existingRecords[$hostname]["data"] != $ip)
			{
				if(!isset($this->createdRecords[$hostname]))
				{
					//recreate entry
					$this->deleteARecord($this->domainName, $hostname);
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
		//delete all DNS A records that does not exist in CMDB
		foreach(array_keys($this->existingRecords) as $hostname)
		{
			$this->deleteARecord($this->domainName, $hostname);
		}
	}

	/**
	* Sends an HTTP request to cPanel and returns result
	* @param string $url	part of the URL request to use
	* @return string	JSON data, which are returned from API
	*/
	private function getData($url)
	{
		//curl request
		$url =  $this->cpanelApiUrl.$url;
		$curl = curl_init();
		$curlOptions = array
		(
			CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
			CURLOPT_USERPWD         => "{$this->cpanelApiUser}:{$this->cpanelApiPassword}",
			CURLOPT_URL             => $url,
			CURLOPT_RETURNTRANSFER  => TRUE
		);
		if($this->cpanelApiSslVerify != "true")
		{
			$curlOptions[CURLOPT_SSL_VERIFYPEER] = FALSE;
			$curlOptions[CURLOPT_SSL_VERIFYHOST] = FALSE;
		}
		curl_setopt_array($curl, $curlOptions);
		$result = curl_exec($curl);
		if($result === FALSE)
		{
			throw new ExportExternalSystemException("Error correcting to cPanel API");
		}
		$curlHttpResponse = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if($curlHttpResponse == "404")
		{
			throw new ExportExternalSystemException("Error correcting to cPanel API (HTTP 404): Please check cpanelApiUrl");
		}
		curl_close($curl);

		//get JSON data
		$jsonResult = json_decode($result);

		//error handling
		if(isset($jsonResult->cpanelresult->data->reason) && $jsonResult->cpanelresult->data->reason == "Access denied")
		{
			throw new ExportExternalSystemException("Error connecting to cPanel API: access denied");
		}
		return $jsonResult;
	}

	/**
	* Gets all DNS A records for the given domain
	* @param string $domain		DNS zone to get the A records
	* @return Array			A records with hostname -> IP
	*/
	private function getARecords($domain)
	{
		//get A records
		$urlParameters = "cpanel_jsonapi_module=ZoneEdit&cpanel_jsonapi_func=fetchzone&domain=$domain&type=A";
		$jsonResult = $this->getData($urlParameters);
		if(isset($jsonResult->cpanelresult->data[0]->status) && $jsonResult->cpanelresult->data[0]->status == 0)
		{
			throw new ExportExternalSystemException("Error reading DNS zone " .$this->domainName);
		}
		$records = $jsonResult->cpanelresult->data[0]->record;

		//create output array
		$output = Array();
		foreach($records as $record)
		{
			$recordHostname = (string) $record->name;
			$recordData = $record->address;
			$recordLine = $record->line;

			//format hostname
			if(preg_match("/(.*?).$domain\./", $recordHostname, $matches) === 1)
			{
				$recordHostname = $matches[1];
				$output[$recordHostname] = Array("data" => $recordData, "line" => $recordLine);
			}
		}
		return $output;
	}

	/**
	* Adds an A record to the given domain in cPanel
	* @param string $domain		domain for adding the record
	* @param string $hostname	host part
	* @param string $ip		IP address
	*/
	private function addARecord($domain, $hostname, $ip)
	{
		$urlParameters = "cpanel_jsonapi_module=ZoneEdit&cpanel_jsonapi_func=add_zone_record";
		$urlParameters.= "&domain=$domain&name=$hostname&type=A&address=$ip";
		$jsonResult = $this->getData($urlParameters);
	}

	/**
	* Adds an A record to the given domain in cPanel
	* @param string $domain		domain for adding the record
	* @param string $hostname	host part
	*/
	private function deleteARecord($domain, $hostname)
	{
		//get all A records in zone
		$records = $this->getARecords($domain);
		foreach(array_keys($records) as $recordName)
		{
			//check if record has the correct name
			if($recordName == $hostname)
			{
				//delete entry
				$urlParameters = "cpanel_jsonapi_module=ZoneEdit&cpanel_jsonapi_func=remove_zone_record";
				$urlParameters.= "&domain=$domain&line=".$records[$recordName]['line'];
				$jsonResult = $this->getData($urlParameters);
			}
		}
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
	*			empty string, if not
	*/	
	private function formatIp($input)
	{
		if(filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		{
			return $input;
		}
		return "";
	}

}
?>
