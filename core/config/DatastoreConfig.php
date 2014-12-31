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

/**
* class for access to datastore configuration
* @author Michael Batz <michael@yourcmdb.org>
*/

class DatastoreConfig
{

	//datastore class
	private $datastoreClass;

	//datastore parameters
	private $datastoreParameters;

	/**
	* creates a DatastoreConfig object from xml file datastore-configuration.xml
	*/
	public function __construct($xmlfile)
	{
		$xmlobject = simplexml_load_file($xmlfile);

		//read class
		$this->datastoreClass = (string) $xmlobject->datastore[0]['class'];
		
		//get parameters for datastore
		$this->datastoreParameters = Array();
		foreach($xmlobject->datastore->parameter as $parameter)
		{
			$parameterKey = (string) $parameter['key'];
			$parameterValue = (string) $parameter['value'];
			$this->datastoreParameters[$parameterKey] = $parameterValue;
		}
	}

	/**
	* Returns class of datastore
	*/
	public function getClass()
	{
		return $this->datastoreClass;
	}

	/**
	* Returns parameters of datastore as array
	*/
	public function getParameters()
	{
		return $this->datastoreParameters;
	}
	
}

?>
