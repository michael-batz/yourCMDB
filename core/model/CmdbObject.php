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
* A CMDB object.
* @author Michael Batz <michael@yourcmdb.org>
*/
class CmdbObject
{
	//type of the CMDB object
	private $objectType;
	
	//data fields of the CMDB object
	private $objectFields;

	//Object ID
	private $objectId;

	//Object status
	private $status;
	
	/**
	* Creates a new CMDB object
	*
	*/
	public function __construct($objectType, $objectFields, $objectId=0, $status='A')
	{
		$this->objectType = $objectType;
		$this->objectFields = $objectFields;
		$this->objectId = $objectId;
		if($status != 'A' && $status != 'N' && $status != 'D')
		{
			$status = 'A';
		}
		$this->status = $status;
	}

	/**
	* Get all fields of the CMDB object
	*/
	public function getFieldNames()
	{
		return array_keys($this->objectFields);
	}

	/**
	* Get value of a field of the CMDB object
	* or empty string, if field was not found
	*/
	public function getFieldValue($fieldname)
	{
		$output = "";
		if(isset($this->objectFields[$fieldname]))
		{
			$output = $this->objectFields[$fieldname];
		}
		return $output;
	}

	/**
	* Get type of the object
	*/
	public function getType()
	{
		return $this->objectType;
	}

	/**
	* Get Object ID
	*/
	public function getId()
	{
		return $this->objectId;
	}

	/**
	* Get status of the object
	* A status could be A (=active), N (=not active) or D (=deleted)
	*/
	public function getStatus()
	{
		return $this->status;
	}
}
?>
