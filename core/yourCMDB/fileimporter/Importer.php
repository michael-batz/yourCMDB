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

	public function __construct($importFilename, $importClassname, $importOptions)
	{
		//save variables
		$this->importFilename = $importFilename;
		$importClassname = 'yourCMDB\fileimporter\\'. $importClassname;
		$this->importClassname = $importClassname;
		$this->importOptions = $importOptions;

		//create ImportFormat object
		$this->importFormat = new $importClassname($importFilename, $importOptions);
	}

	public function getPreviewData()
	{
		return $this->importFormat->getPreviewData();
	}

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
