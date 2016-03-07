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
use yourCMDB\helper\VariableSubstitution;
use \Doctrine\DBAL\DriverManager;

/**
* Export API - External System: SIP accounts for Asterisk
* Exports SIP accounts and extensions to an Asterisk RealTime database
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExternalSystemAsterisk implements ExternalSystem
{
	//ExportVariables
	private $variables;

	//database connection URL
	private $databaseUrl;

	//database table for SIP accounts
	private $databaseTableSip;

	//database table for extenions
	private $databaseTableExtensions;

	//prefix for username
	private $prefixUsername;

	//context for SIP accounts
	private $sipContext;

	//default country code
	private $defaultCountryCode;

	//generic extensions entry
	private $genericExtentsions;

	//in Asterisk RealTime Database existing accounts
	private $existingAccounts;

	//accounts for creation
	private $accountsToCreate;

	//store for extensions that should be created for checking that they are unique
	private $checkUniqueExtensions;

	//database connection
	private $databaseConnection;

	public function setUp(ExportDestination $destination, ExportVariables $variables)
	{
		//get variables
		$this->variables = $variables;

		//check, if parameters are set
		$parameterKeys = $destination->getParameterKeys();
		if(!(	in_array("databaseUrl", $parameterKeys) &&
			in_array("databaseTableSip", $parameterKeys) &&
			in_array("databaseTableExtensions", $parameterKeys) &&
			in_array("prefixUsername", $parameterKeys) && 
			in_array("sipContext", $parameterKeys) ))
		{
			throw new ExportExternalSystemException("Parameters for ExternalSystem not set correctly");
		}

		//get parameters for export
		$this->databaseUrl = $destination->getParameterValue("databaseUrl");
		$this->databaseTableSip = $destination->getParameterValue("databaseTableSip");
		$this->databaseTableExtensions = $destination->getParameterValue("databaseTableExtensions");
		$this->prefixUsername = $destination->getParameterValue("prefixUsername");
		$this->sipContext = $destination->getParameterValue("sipContext");
		$this->defaultCountryCode = "+49";
		if(in_array("defaultCountryCode", $parameterKeys))
		{
			$this->defaultCountryCode = $destination->getParameterValue("defaultCountryCode");
		}

		//get generic extensions
		$this->genericExtensions = Array();

		//check if it is a "extension_" variable
		foreach($destination->getParameterKeys() as $parameterKey)
		{
			if(preg_match('/^extension_(.*)$/', $parameterKey, $matches) == 1)
			{
				$extensionValue = $destination->getParameterValue($parameterKey);
	
				//parse extension entry
				$extensionEntry = str_getcsv($extensionValue, ",", "'");
				$extensionContext = trim($extensionEntry[0]);
				$extensionExten = trim($extensionEntry[1]);
				$extensionPrio = trim($extensionEntry[2]);
				$extensionApp = trim($extensionEntry[3]);
				$extensionAppData = trim($extensionEntry[4]);
				$this->genericExtensions[] = Array
				(
					'context'	=> $extensionContext,
					'exten'		=> $extensionExten,
					'priority'	=> $extensionPrio,
					'app'		=> $extensionApp,
					'appdata'	=> $extensionAppData
				);
			}
		}


		//create database connection
		$dbalConnectionParams = array( 'url' => $this->databaseUrl );
		$dbalConfig = new \Doctrine\DBAL\Configuration();
		$this->databaseConnection = DriverManager::getConnection($dbalConnectionParams, $dbalConfig);

		//get all existing Asterisk accounts from database
		$this->existingAccounts = $this->getExistingAccounts();
		$this->accountsToCreate = Array();
		$this->checkUniqueExtensions =  Array();
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object)
	{
		//get data
		$sipUsername = $this->prefixUsername.$object->getId();
		$sipPassword = $this->variables->getVariable("password")->getValue($object);
		$sipContext =  $this->sipContext;
		$sipHost = "dynamic";
		$extensions = Array();

		//replace variables
		$variables = Array();
		$variables['yourCMDB_sip_username'] = $sipUsername;
		foreach($this->variables->getVariableNames() as $exportVariableName)
		{
			$varValue = $this->variables->getVariable($exportVariableName)->getValue($object);
			if(preg_match('/^telephone_(.*)$/', $exportVariableName) == 1)
			{
				$varValue = $this->normalizePhoneNumber($varValue); 
			}

			$variables[$exportVariableName] = $varValue;
		}
		//create extensions
		foreach($this->genericExtensions as $genericExtension)
		{
			$extensionEntry = Array();
			$extensionEntry['sipname'] = $sipUsername;
			$extensionEntry['context'] = VariableSubstitution::substitute($genericExtension['context'], $variables, true);
			$extensionEntry['exten'] = VariableSubstitution::substitute($genericExtension['exten'], $variables, true);
			$extensionEntry['priority'] = VariableSubstitution::substitute($genericExtension['priority'], $variables, true);
			$extensionEntry['app'] = VariableSubstitution::substitute($genericExtension['app'], $variables, true);
			$extensionEntry['appdata'] = VariableSubstitution::substitute($genericExtension['appdata'], $variables, true);
			//only add extensions if the fields were not empty
			if(!($extensionEntry['sipname'] == "" || $extensionEntry['context'] == "" || $extensionEntry['exten'] == "" ||
				$extensionEntry['priority'] == "" || $extensionEntry['app'] == ""))
			{
				//check, if extension entry is unique
				$uniqueExtensionString = $extensionEntry['context']."/".$extensionEntry['exten']."/".$extensionEntry['priority'];
				if(!isset($this->checkUniqueExtensions[$uniqueExtensionString]))
				{
					$extensions[] = $extensionEntry;
					$this->checkUniqueExtensions[$uniqueExtensionString] = "ok";
				}
			}
		}

		//check, if a record exist for this account
		if(isset($this->existingAccounts[$sipUsername]))
		{
			//check, if password, context or host has changed
			if(    	($this->existingAccounts[$sipUsername]['password'] != $sipPassword) ||
				($this->existingAccounts[$sipUsername]['context'] != $sipContext) ||
				($this->existingAccounts[$sipUsername]['host'] != $sipHost))
			{
				//recreate entry
				$this->removeAsteriskAccount($sipUsername);
				$this->addAsteriskAccount($sipUsername, $sipPassword, $sipContext, $sipHost, $extensions);
			}

			//check, if extensions were changed
			$extensionCheck = $extensions;
			$existingExtensionCheck = $this->existingAccounts[$sipUsername]['extensions'];
			foreach($existingExtensionCheck as $i => $existingExtensionCheckEntry)
			{
				//walk through all extension entries
				foreach($extensionCheck as $j => $extensionCheckEntry)
				{
					//remove entries, if they are equal
					if(count(array_diff_assoc($existingExtensionCheckEntry, $extensionCheckEntry)) == 0)
					{
						unset($existingExtensionCheck[$i]);
						unset($extensionCheck[$j]);
					}
				}
			}
			if(count($existingExtensionCheck) > 0 || count($extensionCheck) >  0)
			{
				//recreate entry
				$this->removeAsteriskAccount($sipUsername);
				$this->addAsteriskAccount($sipUsername, $sipPassword, $sipContext, $sipHost, $extensions);
			}

			//delete entry from existing records array
			unset($this->existingAccounts[$sipUsername]);
		}
		else
		//if not, save data to store
		{
			$this->accountsToCreate[$sipUsername] = Array();
			$this->accountsToCreate[$sipUsername]['password'] = $sipPassword;
			$this->accountsToCreate[$sipUsername]['context'] = $sipContext;
			$this->accountsToCreate[$sipUsername]['host'] = $sipHost;
			$this->accountsToCreate[$sipUsername]['extensions'] = $extensions;
		}
	}

	public function finishExport()
	{
		//add accounts to create in Asterisk Realtime database
		foreach(array_keys($this->accountsToCreate) as $username)
		{
			$password = $this->accountsToCreate[$username]['password'];
			$context = $this->accountsToCreate[$username]['context'];
			$host = $this->accountsToCreate[$username]['host'];
			$extensions = $this->accountsToCreate[$username]['extensions'];
			$this->addAsteriskAccount($username, $password, $context, $host, $extensions);
		}

		//remove all accounts that does not exist in CMDB
		foreach(array_keys($this->existingAccounts) as $username)
		{
			$this->removeAsteriskAccount($username);
		}
	}

	private function addAsteriskAccount($username, $password, $context, $host, $extensions)
	{
		//insert data in database table for SIP Accounts
		$sipData =  Array();
		$sipData['name'] = $username;
		$sipData['context'] = $context;
		$sipData['secret'] = $password;
		$sipData['host'] = $host;
		$this->databaseConnection->insert($this->databaseTableSip, $sipData);

		//insert data in database table for extensions
		foreach($extensions as $extension)
		{
			$this->databaseConnection->insert($this->databaseTableExtensions, $extension);
		}
	}

	private function removeAsteriskAccount($username)
	{
		//remove entries from sip account table
		$this->databaseConnection->delete($this->databaseTableSip, Array('name' => $username));

		//remove entries from extensions table
		$this->databaseConnection->delete($this->databaseTableExtensions, Array('sipname' => $username));

	}

	private function getExistingAccounts()
	{
		$existingAccounts = Array();

		//get sip entries
		$sipEntries = $this->databaseConnection->fetchAll('SELECT *  FROM '.$this->databaseTableSip);

		//get extensions entries
		$extensionsEntries = $this->databaseConnection->fetchAll('SELECT *  FROM '.$this->databaseTableExtensions);

		//save existing sip entries in store
		foreach($sipEntries as $sipEntry)
		{
			$username = $sipEntry['name'];
			$password = $sipEntry['secret'];
			$context = $sipEntry['context'];
			$host = $sipEntry['host'];
			$existingAccounts[$username] = Array();
			$existingAccounts[$username]['password'] = $password;
			$existingAccounts[$username]['context'] = $context;
			$existingAccounts[$username]['host'] = $host;
			$existingAccounts[$username]['extensions'] = Array();
		}

		//save extensions in store
		foreach($extensionsEntries as $extensionEntry)
		{
			$username =  $extensionEntry['sipname'];
			$context = $extensionEntry['context'];
			$exten = $extensionEntry['exten'];
			$priority = $extensionEntry['priority'];
			$app = $extensionEntry['app'];
			$appdata = $extensionEntry['appdata'];
			$existingAccounts[$username]['extensions'][] = Array
			(
				'sipname'	=> $username,
				'context'	=> $context,
				'exten'		=> $exten,
				'priority'	=> $priority,
				'app'		=> $app,
				'appdata'	=> $appdata
			);
		}

		return $existingAccounts;
	}

	/**
	* Normalizes a phone number
	* output format: +49123456789 (use of configured default country code)
	* @param string $input		input phone number
	* @return string		normalized phone number
	*				or empty string, if there was an error
	*/
	private function normalizePhoneNumber($input)
	{
		$output = $input;

		//replace starting "+" with "00"
		$output = preg_replace("/^\+/", "00", $output);

		//replace all non numeric characters
		$output = preg_replace("/[^0-9]/", "", $output);

		//replace starting "00" with "+"
		$output = preg_replace("/^00/", "+", $output);

		//replace starting "0" with default country code
		$output = preg_replace("/^0/", $this->defaultCountryCode, $output);

		return $output;
	}
}
?>
