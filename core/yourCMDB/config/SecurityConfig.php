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
namespace yourCMDB\config;

use yourCMDB\security\SecurityConfigurationException;

/**
* Class for access to security configuration
* @author Michael Batz <michael@yourcmdb.org>
*/
class SecurityConfig
{

	//authentication methods array(part -> instance of AuthenticationProvider)
	private $authmethods;

	/**
	* creates a SecurityConfig object from xml file security-configuration.xml
	*/
	public function __construct($xmlfile)
	{
		$xmlobject = simplexml_load_file($xmlfile);

		//initialize variables
		$this->authmethods = array();
		$authproviders = array();

		//read authproviders
		foreach($xmlobject->xpath('//authprovider') as $authprovider)
		{
			$authproviderName = (string) $authprovider['name'];
			$authproviderClass = (string) $authprovider['class'];
			$authproviderClass = "\yourCMDB\security\\".$authproviderClass;
			//read parameters of authprovider
			$authproviderParams = array();
			foreach($authprovider[0]->parameter as $param)
			{
				$paramKey = (string) $param['key'];
				$paramValue = (string) $param['value'];
				$authproviderParams[$paramKey] = $paramValue;
			}
			//create instance
			if(class_exists($authproviderClass))
			{
				$authproviders[$authproviderName] = new $authproviderClass($authproviderParams);
			}
		}

		//read authmethods
		foreach($xmlobject->xpath('//authmethod') as $authmethod)
		{
			$authmethodPart = (string) $authmethod['part'];
			$authmethodProvider = (string) $authmethod['authprovider'];

			if(isset($authproviders[$authmethodProvider]))
			{
				$this->authmethods[$authmethodPart] = $authproviders[$authmethodProvider];
			}
		}

	}

	/**
	* Return an instance of the configured AuthenticationProvider for software part $part
	*/
	public function getAuthProvider($part)
	{
		if(!isset($this->authmethods[$part]))
		{
			throw new SecurityConfigurationException("Authmethod for part $part is not configured");
		}
		return $this->authmethods[$part];
	}
}

?>
