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
namespace yourCMDB\labelprinter;

/**
* Options for a Label object in yourCMDB
* @author Michael Batz <michael@yourcmdb.org>
*/
class LabelOptions
{
	//array with label options
	private $labelOptions;

	/**
	* Creates a new, empty LabelOptions object
	*/
	public function __construct()
	{
		$this->labelOptions = Array();
	}

	/**
	* Adds a new option
	* @param string $key	key of the option
	* @param string $value	value of the option
	*/
	public function addOption($key, $value)
	{
		$this->labelOptions[$key] = $value;
	}

	/**
	* Returns the value for the given option or the default value, if option does not exist
	* @param string $key		key of the option
	* @param string $defaultValue	default value of the option
	* @return string	value of the given option or default value
	*/
	public function getOption($key, $defaultValue)
	{
		$output = $defaultValue;
		if(isset($this->labelOptions[$key]))
		{
			$output = $this->labelOptions[$key];
		}
		return $output;
	}
}
?>
