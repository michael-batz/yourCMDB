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

	//generic extensions entry
	private $genericExtentsions;

	//in Asterisk RealTime Database existing accounts
	private $existingAccounts;

	//accounts for creation
	private $accountsToCreate;

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
			in_array("prefixUsername", $parameterKeys) ))
		{
			throw new ExportExternalSystemException("Parameters for ExternalSystem not set correctly");
		}

		//get parameters for export
		$this->databaseUrl = $destination->getParameterValue("databaseUrl");
		$this->databaseTableSip = $destination->getParameterValue("databaseTableSip");
		$this->databaseTableExtensions = $destination->getParameterValue("databaseTableExtensions");
		$this->prefixUsername = $destination->getParameterValue("prefixUsername");

		//get generic extensions
		$this->genericExtensions = Array();

		//check if it is a "extension_" variable
		foreach($destination->getParameterKeys() as $parameterKey)
		{
			if(preg_match('/^extension_(.*)$/', $parameterKey, $matches) == 1)
			{
				$extensionValue = $destination->getParameterValue($parameterKey);
	
				//parse extension entry
				$extensionEntry = str_getcsv($extensionValue);
				$extensionContext = $extensionEntry[0];
				$extensionExten = $extensionEntry[1];
				$extensionPrio = $extensionEntry[2];
				$extensionApp = $extensionEntry[3];
				$extensionAppData = $extensionEntry[4];
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
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object)
	{
		//get data
		$sipUsername = $this->prefixUsername.$object->getId();
		$sipPassword = $this->variables->getVariable("password")->getValue($object);
		$sipContext =  "outgoing";
		$sipHost = "dynamic";
		$extensions = Array();

		//ToDo: replace variables
		$specialVars = Array();
		$specialVars['yourCMDB_sip_username'] = $sipUsername;
		foreach($this->genericExtensions as $genericExtension)
		{
			$extenContext = $this->replaceVariables($genericExtension['context'], $object, $specialVars);
			$extenExten = $this->replaceVariables($genericExtension['exten'], $object, $specialVars);
			$extenPriority = $this->replaceVariables($genericExtension['priority'], $object, $specialVars);
			$extenApp = $this->replaceVariables($genericExtension['app'], $object, $specialVars);
			$extenAppData = $this->replaceVariables($genericExtension['appdata'], $object, $specialVars);
			$extensions[] = Array
			(
				'sipname'	=> $sipUsername,
				'context'	=> $extenContext,
				'exten'		=> $extenExten,
				'priority'	=> $extenPriority,
				'app'		=> $extenApp,
				'appdata'	=> $extenAppData
			);
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

	private function replaceVariables($input, $cmdbObject, $specialVars)
	{
		$output  = preg_replace_callback("/%(.+?)%/", 
						function($pregResult) use($cmdbObject, $specialVars)
						{
							$varName = $pregResult[1];
							$value = $pregResult[0];
							if($this->variables->getVariable($varName) != null)
							{
								$value =  $this->variables->getVariable($varName)->getValue($cmdbObject);
							}
							elseif(isset($specialVars[$varName]))
							{
								$value = $specialVars[$varName];
							}
							return $value;
						},
						$input);
		return $output;
	}

}
?>
