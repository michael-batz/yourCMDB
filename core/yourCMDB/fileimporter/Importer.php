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
namespace yourCMDB\fileimporter;

use yourCMDB\entities\CmdbObject;

/**
* File Importer
* @author Michael Batz <michael@yourcmdb.org>
*/
class Importer
{
	//file for import
	private $importFilename;

	//options for import
	private $importOptions;

	//import format object
	private $importFormat;

	//user that makes the import
	private $authUser;

	/**
	* Creates a new Importer
	* @param string $importFilename		file name
	* @param string $importClassname	name if the import format class
	* @param ImportOptions $importOptions	ImportOptions object
	* @param string $authUser		name of the user, that wants to make the import
	*/
	public function __construct($importFilename, $importClassname, $importOptions, $authUser)
	{
		//save variables
		$this->importFilename = $importFilename;
		$importClassname = 'yourCMDB\fileimporter\\'. $importClassname;
		$this->importClassname = $importClassname;
		$this->importOptions = $importOptions;
		$this->authUser = $authUser;

		//create ImportFormat object
		$this->importFormat = new $importClassname($importFilename, $importOptions, $authUser);
	}

	/**
	* Gets and returns preview data for the import
	* @return Array previewData
	*/
	public function getPreviewData()
	{
		return $this->importFormat->getPreviewData();
	}

	/**
	* Executes the import
	* @return int 	position in import file (i.e. if a partial import was executed)
	*/
	public function import()
	{
		return $this->importFormat->import();
	}

	/**
	* Returns the number of objects to import
	* @return int	number of objects to import
	*/
	public function getObjectsToImportCount()
	{
		return $this->importFormat->getObjectsToImportCount();
	}

	/**
	* Returns all supported InputFormats
	* @return Array		supported InputFormats (format -> classname)
	*/
	public static function getInputFormats()
	{
		//define input formats
		$inputFormats = Array();
		$inputFormats["CSV"] = "ImportFormatCsv";

		//return input formats
		return $inputFormats;
	}
}
?>
