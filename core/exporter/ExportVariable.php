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
* Export API - a variable for an export task
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExportVariable
{
	//name of variable
	private $name;

	//default value
	private $defaultValue;

	//fields by object type to get the value
	//Array objectType -> fieldname
	private $fieldValue;

	function __construct($name, $defaultValue, $fieldValue)
	{
		$this->name = $name;
		$this->defaultValue = $defaultValue;
		$this->fieldValue = $fieldValue;
	}

	/**
	* Returns the name of variable
	*/
	public function getName()
	{
		return $this->name;
	}

	/**
	* Returns the value of variable for the given CmdbObject
	* @param CmdbObject $object	the object to get the value
	*/
	public function getValue(CmdbObject $object)
	{
		$value = $this->defaultValue;

		//if there is a configuration for that object type
		//use the content of the specified field as value
		$objectType = $object->getType();
		if(isset($this->fieldValue[$objectType]))
		{
			$fieldname = $this->fieldValue[$objectType];
			$value = $object->getFieldValue($fieldname);
		}

		//return the value
		return $value;
	}

}
?>
