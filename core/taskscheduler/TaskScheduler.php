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
* TaskScheduler
* @author Michael Batz <michael@yourcmdb.org>
*/
class TaskScheduler
{
	//datastore object
	private $datastore;

	//task scheduler config
	private $configTaskScheduler;

	function __construct()
	{
		$config = new CmdbConfig();
		$this->configTaskScheduler = $config->getTaskSchedulerConfig();
	}

	public function eventHandler(CmdbEvent $event)
	{
		//check, if there are tasks for the given event
		$tasks = $this->configTaskScheduler->getTasksForEvent($event->getEventType(), $event->getObjectType());

		//create jobs
		foreach($tasks as $task)
		{
			//todo
			error_log(print_r($task, true));
		}
	}
}
?>
