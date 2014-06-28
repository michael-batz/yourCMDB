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
* A CMDB event
* @author Michael Batz <michael@yourcmdb.org>
*/
class CmdbEvent
{
	//type of the CMDB event
	private $eventType;
	
	//id of a linked object
	private $objectId;

	//type of a linked object
	private $objectType;

	/**
	* Creates a new CMDB object
	*
	*/
	public function __construct($eventType, $objectId=null, $objectType=null)
	{
		$this->eventType = $eventType;
		$this->objectId = $objectId;
		$this->objectType = $objectType;
	}

	/**
	* Get event type
	*/
	public function getEventType()
	{
		return $this->eventType;
	}

	/**
	* Get ID of linked object
	*/
	public function getObjectId()
	{
		return $this->objectId;
	}

	/**
	* Get type of linked object
	*/
	public function getObjectType()
	{
		return $this->objectType;
	}

}
?>
