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
namespace yourCMDB\fileimporter;

use yourCMDB\entities\CmdbObject;

/**
* File Importer - Interface for an import format
* @author Michael Batz <michael@yourcmdb.org>
*/
abstract class ImportFormat
{
	//import filename
	protected $importFilename;

	//import options
	protected $importOptions;

	//user that makes the import
	protected $authUser;

	/**
	* Creates a new ImportFormat object
	* @param string $importFilename		file name
	* @param ImportOptions $importOptions	ImportOptions object
	* @param string $authUser		name of the user, that wants to make the import
	*/
	public function __construct($importFilename, $importOptions, $authUser)
	{
		$this->importFilename = $importFilename;
		$this->importOptions = $importOptions;
		$this->authUser = $authUser;
	}

	/**
	* Returns the name of the format
	* @return string	name of the format
	*/ 
	public static abstract function getFormatName();

	/**
	* Gets and returns preview data for the import
	* @return Array previewData
	*/
	public abstract function getPreviewData();

	/**
	* Executes the import
	* @return int 	position in import file (i.e. if a partial import was executed)
	*/
	public abstract function import();

	/**
	* Returns the number of objects to import
	* @return int	number of objects to import
	*/
	public abstract function getObjectsToImportCount();
}
?>
