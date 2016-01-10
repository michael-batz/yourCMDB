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
namespace yourCMDB\printer;

/**
* Options for a Printer object in yourCMDB
* @author Michael Batz <michael@yourcmdb.org>
*/
class PrinterOptions
{
	//array with printer options
	private $printerOptions;

	/**
	* Creates a new, empty PrinterOptions object
	*/
	public function __construct()
	{
		$this->printerOptions = Array();
	}

	/**
	* Adds a new option
	* @param string $key	key of the option
	* @param string $value	value of the option
	*/
	public function addOption($key, $value)
	{
		$this->printerOptions[$key] = $value;
	}

	/**
	* Returns the value for the given option or the default value, if option does not exist
	* @param string $key		key of the option
	* @param string $defaultValue	default value of the option
	* @return string		value of the given option or the default value
	*/
	public function getOption($key, $defaultValue)
	{
		$output = $defaultValue;
		if(isset($this->printerOptions[$key]))
		{
			$output = $this->printerOptions[$key];
		}
		return $output;
	}
}
?>
