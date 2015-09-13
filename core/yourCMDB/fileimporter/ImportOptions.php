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
* Options for file import
* consists of multiple key value pairs
* @author Michael Batz <michael@yourcmdb.org>
*/
class ImportOptions
{

	//Array with import options
	private $importOptions;

	/**
	* Creates a new ImportOptions object
	*/
	public function __construct()
	{
		$this->importOptions = Array();
	}

	/**
	* Adds a new import option. If the key already exists, the exitsing value will be overwritten
	* @param string $key		key of the import option
	* @param string $value		value of the import option
	*/
	public function addOption($key, $value)
	{
		$this->importOptions[$key] = $value;
	}

	/**
	* Returns the value for an import option identified by $key
	* @param string $key		key of the import option
	* @return string		value of the import option if set
	*				empty string, if the key does not exist
	*/
	public function getOptionValue($key)
	{
		$output = "";
		if(isset($this->importOptions[$key]))
		{
			$output = $this->importOptions[$key];
		}

		return $output;
	}
}
?>
