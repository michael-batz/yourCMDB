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

/**
* class for access to datastore configuration
* @author Michael Batz <michael@yourcmdb.org>
*/

class DatastoreConfig
{
	//datastore driver
	private $driver;

	//datastore server
	private $server;

	//datastore port
	private $port;

	//datastore database
	private $db;

	//datastore username
	private $user;

	//datastore password
	private $password;

	/**
	* creates a DatastoreConfig object from xml file datastore-configuration.xml
	*/
	public function __construct($xmlfile)
	{
		//init parameters with default values
		$this->driver = "pdo_mysql";
		$this->server = "localhost";
		$this->port = "3306";
		$this->db = "cmdb";
		$this->user = "cmdb";
		$this->password = "cmdb";

		//load XML configuration file
		$xmlobject = simplexml_load_file($xmlfile);

		//get parameters for datastore
		foreach($xmlobject->datastore->parameter as $parameter)
		{
			$parameterKey = (string) $parameter['key'];
			$parameterValue = (string) $parameter['value'];
			switch($parameterKey)
			{
				case "driver":
					$this->driver = $parameterValue;
					break;

				case "server":
					$this->server = $parameterValue;
					break;

				case "port":
					$this->port = $parameterValue;
					break;

				case "db":
					$this->db = $parameterValue;
					break;

				case "user":
					$this->user = $parameterValue;
					break;

				case "password":
					$this->password = $parameterValue;
			}
		}
	}

	/**
	* Returns datastore driver
	*/
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	* Returns datastore server
	*/
	public function getServer()
	{
		return $this->server;
	}

	/**
	* Returns datastore port
	*/
	public function getPort()
	{
		return $this->port;
	}

	/**
	* Returns datastore database name
	*/
	public function getDatabaseName()
	{
		return $this->db;
	}

	/**
	* Returns datastore user
	*/
	public function getUser()
	{
		return $this->user;
	}

	/**
	* Returns datastore password
	*/
	public function getPassword()
	{
		return $this->password;
	}

}

?>
