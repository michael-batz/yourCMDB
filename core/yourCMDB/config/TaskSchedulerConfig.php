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
namespace yourCMDB\config;

use yourCMDB\taskscheduler\Task;

/**
* Class for access to task scheduler configuration
* @author Michael Batz <michael@yourcmdb.org>
*/
class TaskSchedulerConfig
{

	//event trigger
	private $eventTrigger;


	/**
	* creates a TaskSchedulerConfig object from xml file taskscheduler-configuration.xml
	*/
	public function __construct($xmlfile)
	{
		$xmlobject = simplexml_load_file($xmlfile);

		//initialize arrays
		$this->eventTrigger = Array();

		foreach($xmlobject->xpath('//task') as $task)
		{
			$taskTrigger = (string)$task['trigger'];
			$taskEvent = (string)$task['event'];
			$taskObjectType = (string)$task['objecttype'];
			$taskAction = (string)$task['action'];
			$taskActionParm = (string)$task['actionParameter'];

			//get eventTrigger
			if($taskTrigger == "event")
			{
				$taskTaskObject = new Task($taskAction, $taskActionParm);
				$this->eventTrigger[$taskEvent][$taskObjectType][] = $taskTaskObject;
			}
		}
	}


	/**
	* Returns the tasks for the given event and objecttype
	* or null if no task was found
	* @return task[]
	*/
	public function getTasksForEvent($event, $objectType)
	{
		if(isset($this->eventTrigger[$event][$objectType]))
		{
			return $this->eventTrigger[$event][$objectType];
		}
		return array();
	}
}

?>
