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
* EventProcessor - generates events
* @author Michael Batz <michael@yourcmdb.org>
*/

class EventProcessor
{

	function __construct()
	{
		;
	}

	/**
	* Creates and process an event
	* @param $eventType	type of the event
	* @param $objectId	ID of a linked object or null
	* @param $objectType	type of a linked object or null
	*/
	public function generateEvent($eventType, $objectId=null, $objectType=null)
	{
		//create event
		$event = new CmdbEvent($eventType, $objectId, $objectType);

		//process event
		$taskScheduler = new TaskScheduler();
		$taskScheduler->eventHandler($event);
	}
}
?>
