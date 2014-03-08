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
* Class for access to data exchange configuration
* @author Michael Batz <michael@yourcmdb.org>
*/
class DataExchangeConfig
{

	//import formats
	private $importFormats;

	//export formats
	private $exportFormats;


	/**
	* creates a ViewConfig object from xml file view-configuration.xml
	*/
	public function __construct($xmlfile)
	{
		$xmlobject = simplexml_load_file($xmlfile);

		//read import formats
		$this->importFormats = Array();
		foreach($xmlobject->xpath('//import') as $importFormat)
		{
			//save format name
			$formatName = (string)$importFormat['type'];
			$this->importFormats[$formatName] = Array();

			//read format parameters
			foreach($importFormat[0]->parameter as $parameter)
			{
				$parameterName = (string) $parameter['key'];
				$parameterValue = (string) $parameter['value'];
				$this->importFormats[$formatName][$parameterName] = $parameterValue;
			}
		}

		//read export formats
		$this->exportFormats = Array();
		foreach($xmlobject->xpath('//export') as $exportFormat)
		{
			//save format name
			$formatName = (string)$exportFormat['type'];
			$this->exportFormats[$formatName] = Array();

			//read format parameters
			foreach($exportFormat[0]->parameter as $parameter)
			{
				$parameterName = (string) $parameter['key'];
				$parameterValue = (string) $parameter['value'];
				$this->exportFormats[$formatName][$parameterName] = $parameterValue;
			}

		}

	}

	/**
	* Returns import formats
	*/
	public function getImportFormats()
	{
		return array_keys($this->importFormats);
	}

	/**
	* Returns export formats
	*/
	public function getExportFormats()
	{
		return array_keys($this->exportFormats);
	}

	/**
	* Returns import options
	*/
	public function getImportOptions($format)
	{
		if(isset($this->importFormats[$format]))
		{
			return $this->importFormats[$format];
		}
		else
		{
			return null;
		}
	}

	/**
	* Returns export options
	*/
	public function getExportOptions($format)
	{
		if(isset($this->exportFormats[$format]))
		{
			return $this->exportFormats[$format];
		}
		else
		{
			return null;
		}
	}

}

?>
