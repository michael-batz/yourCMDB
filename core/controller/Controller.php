<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2014 Michael Batz
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

/**
* Controller for yourCMDB
* @author Michael Batz <michael@yourcmdb.org>
*/

class Controller
{

	//datastore
	private $datastore;

	//configuration
	private $config;

	//eventProcessor
	private $eventProcessor;
	
	//yourcmdb version
	private $version;


	function __construct()
	{
		//create configuration object
		$this->config = new CmdbConfig();

		//create datastore object
		$datastoreClass = $this->config->getDatastoreConfig()->getClass();
		$this->datastore = new $datastoreClass;

		//creates event processor object
		$this->eventProcessor = new EventProcessor();

		//set version
		$this->version = "0.7.1";
		
	}

	/**
	* Returns cmdb configuration
	*
	*/
	public function getCmdbConfig()
	{
		return $this->config;
	}

	/**
	* Returns cmdb datastore
	*
	*/
	public function getDatastore()
	{
		return $this->datastore;
	}

	/**
	* Returns cmdb event processor
	*
	*/
	public function getEventProcessor()
	{
		return $this->eventProcessor;
	}

	/**
	* Returns version
	*
	*/
	public function getVersion()
	{
		return $this->version;
	}


}
?>
