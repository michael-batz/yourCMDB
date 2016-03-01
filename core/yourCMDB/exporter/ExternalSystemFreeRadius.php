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
* Export API - External System: user accounts for FreeRadius
* Exports user accounts to a FreeRadius database
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExternalSystemFreeRadius implements ExternalSystem
{
	//ExportVariables
	private $variables;

	//database connection URL
	private $databaseUrl;

	//prefix for username
	private $prefixUsername;

	//generic rad reply entries
	private $genericRadReply;

	//database connection
	private $databaseConnection;

	//store for radius account to create
	private $accountsToCreate;

	//store for existing radius accounts
	private $existingAccounts;

	public function setUp(ExportDestination $destination, ExportVariables $variables)
	{
		//get variables
		$this->variables = $variables;

		//check, if parameters are set
		$parameterKeys = $destination->getParameterKeys();
		if(!(	in_array("databaseUrl", $parameterKeys) &&
			in_array("prefixUsername", $parameterKeys) ))
		{
			throw new ExportExternalSystemException("Parameters for ExternalSystem not set correctly");
		}

		//get parameters for export
		$this->databaseUrl = $destination->getParameterValue("databaseUrl");
		$this->prefixUsername = $destination->getParameterValue("prefixUsername");

		//get generic rad reply entries
		$this->genericRadReply =  Array();
		foreach($destination->getParameterKeys() as $parameterKey)
		{
			//check if it is a "radreply_" variable
			if(preg_match('/^radreply_(.*)$/', $parameterKey, $matches) == 1)
			{
				$radreplyValue = $destination->getParameterValue($parameterKey);

				//parse radreply entry
				if(preg_match('/^(.*?)(:=|\+=|==)(.*)$/', $radreplyValue,  $matches) == 1)
				{
					$attribute  = trim($matches[1]);
					$op  = trim($matches[2]);
					$value  = trim($matches[3]);
					$this->genericRadReply[] = Array(	'attribute' 	=> $attribute,
										'op'		=> $op,
										'value'		=> $value);
				}
			}

		}
		
		//create database connection
		$dbalConnectionParams = array( 'url' => $this->databaseUrl );
		$dbalConfig = new \Doctrine\DBAL\Configuration();
		$this->databaseConnection = DriverManager::getConnection($dbalConnectionParams, $dbalConfig);

		//get all existing Radius accounts from database
		$this->existingAccounts = $this->getAllExistingAccounts();
		$this->accountsToCreate = Array();
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object)
	{
		//get data
		$radiusUsername = $this->prefixUsername.$object->getId();
		$radiusPassword = $this->variables->getVariable("password")->getValue($object);
		$radiusReply = Array();
		foreach($this->genericRadReply as $genericRadReplyEntry)
		{
			//replace values
			$attribute = $genericRadReplyEntry['attribute'];
			$op = $genericRadReplyEntry['op'];
			$value = $this->replaceVariables($genericRadReplyEntry['value'], $object);
			$radiusReply[] = Array(	'attribute'     => $attribute,
						'op'            => $op,
						'value'         => $value);
		}

		//check, if a record exist for this account
		if(isset($this->existingAccounts[$radiusUsername]))
		{
			//check, if password has changed
			if($this->existingAccounts[$radiusUsername]['password'] != $radiusPassword)
			{
				//recreate entry
				$this->removeRadiusAccount($radiusUsername);
				$this->addRadiusAccount($radiusUsername, $radiusPassword, $radiusReply);
			}

			//check, if radreply entries have changed
			$radiusReplyCheck = $radiusReply;
			$existingReplyCheck = $this->existingAccounts[$radiusUsername]['reply'];
			foreach($existingReplyCheck as $i => $existingReplyCheckEntry)
			{
				//walk through all reply entries
				foreach($radiusReplyCheck as $j => $radiusReplyCheckEntry)
				{
					//remove entries, if they are equal
					if(count(array_diff_assoc($existingReplyCheckEntry, $radiusReplyCheckEntry)) == 0)
					{
						unset($existingReplyCheck[$i]);
						unset($radiusReplyCheck[$j]);
					}
				}
			}
			if(count($existingReplyCheck) > 0 || count($radiusReplyCheck) >  0)
			{
				//recreate entry
				$this->removeRadiusAccount($radiusUsername);
				$this->addRadiusAccount($radiusUsername, $radiusPassword, $radiusReply);
			}

			//delete entry from existing records array
			unset($this->existingAccounts[$radiusUsername]);
		}
		//if not create a new one
		else
		{
			$this->accountsToCreate[$radiusUsername] = Array();
			$this->accountsToCreate[$radiusUsername]['password'] = $radiusPassword;
			$this->accountsToCreate[$radiusUsername]['reply'] = $radiusReply;
		}
	}

	public function finishExport()
	{
		//add accounts to create to FreeRadius database
		foreach(array_keys($this->accountsToCreate) as $username)
		{
			$password = $this->accountsToCreate[$username]['password'];
			$reply = $this->accountsToCreate[$username]['reply'];
			$this->addRadiusAccount($username, $password, $reply);
		}

		//remove all entries that does not exist in CMDB
		foreach(array_keys($this->existingAccounts) as $existingAccountUsername)
		{
			$this->removeRadiusAccount($existingAccountUsername);
		}
	}

	/**
	* Get all existing accounts from FreeRadius database
	*/
	private function getAllExistingAccounts()
	{
		$existingRadiusEntries = Array();
		
		//get radcheck entries
		$radcheckEntries = $this->databaseConnection->fetchAll('SELECT * FROM  radcheck');

		//get radreply entries
		$radreplyEntries = $this->databaseConnection->fetchAll('SELECT * FROM  radreply');

		//save existing radius accounts in store
		foreach($radcheckEntries as $radcheckEntry)
		{
			$username = $radcheckEntry['username'];
			$password = $radcheckEntry['value'];

			$existingRadiusEntries[$username] = Array();
			$existingRadiusEntries[$username]['password'] = $password;
			$existingRadiusEntries[$username]['reply'] = Array();
		}

		//save radreply entries in store
		foreach($radreplyEntries as $radreplyEntry)
		{
			$username = $radreplyEntry['username'];
			$attribute = $radreplyEntry['attribute'];
			$op = $radreplyEntry['op'];
			$value = $radreplyEntry['value'];

			$existingRadiusEntries[$username]['reply'][] = Array
			(
				'attribute'	=> $attribute,
				'op'		=> $op,
				'value'		=> $value
			);
		}

		//return data as array
		return $existingRadiusEntries;
	}

	/**
	* Creates a new account in FreeRadius database
	* @param string $username	username for FreeRadius account
	* @param string $password	password for FreeRadius account
	* @param Array $reply		radreply entries (format: array->array(attribute, op, value))
	*/
	private function addRadiusAccount($username, $password, $reply)
	{
		//insert data in database table radcheck
		$radcheckData = Array();
		$radcheckData['UserName'] = $username;
		$radcheckData['Attribute'] = "Cleartext-Password";
		$radcheckData['op'] = ":=";
		$radcheckData['Value'] = $password;
		$this->databaseConnection->insert('radcheck', $radcheckData);

		//insert data in database table radreply
		foreach($reply as $replyEntry)
		{
			$radreplyData = Array();
			$radreplyData['UserName'] = $username;
			$radreplyData['Attribute'] = $replyEntry['attribute'];
			$radreplyData['op'] = $replyEntry['op'];
			$radreplyData['Value'] = $replyEntry['value'];
			$this->databaseConnection->insert('radreply', $radreplyData);
		}
	}

	/**
	* Removes the account with the given username in FreeRadius database
	* @param string $username	username for FreeRadius account
	*/
	private function removeRadiusAccount($username)
	{
		//remove radreply entries
		$this->databaseConnection->delete('radreply', Array('username' => $username));

		//remove radcheck entries
		$this->databaseConnection->delete('radcheck', Array('username' => $username));
	}

	private function replaceVariables($input, $cmdbObject)
	{
		$output  = preg_replace_callback("/%(.+?)%/", 
						function($pregResult) use($cmdbObject)
						{
							$value =  $this->variables->getVariable($pregResult[1])->getValue($cmdbObject);
							return $value;
						},
						$input);
		return $output;
	}

}
?>
