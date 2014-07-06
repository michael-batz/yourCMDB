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
	//task scheduler config
	private $configTaskScheduler;

	//datastore
	private $datastore;

	function __construct()
	{
		$config = new CmdbConfig();
		$this->configTaskScheduler = $config->getTaskSchedulerConfig();

		//create datastore object
		$datastoreClass = $config->getDatastoreConfig()->getClass();
		$this->datastore = new $datastoreClass;
	}

	/**
	* Executes the given job
	*/
	private function executeJob(CmdbJob $job)
	{
		//check, if job action exists
		$actionClassName = $job->getAction();
		if(class_exists($actionClassName))
		{
			$action = new $actionClassName($job);
			$action->execute();
		}
		else
		{
			error_log("unknown action $actionClassName. Ignoring job");
		}
	}

	/**
	* Handle the given CmdbEvent and generate job, if configured
	*/
	public function eventHandler(CmdbEvent $event)
	{
		//check, if there are tasks for the given event
		$tasks = $this->configTaskScheduler->getTasksForEvent($event->getEventType(), $event->getObjectType());

		//create jobs
		foreach($tasks as $task)
		{
			$jobAction = $task->getAction();
			$jobActionParm = $task->getActionParameter();
			if($event->getObjectId() != null)
			{
				$jobActionParm .= " ".$event->getObjectId();
			}
			$job = new CmdbJob($jobAction, $jobActionParm);
			$this->datastore->addJob($job);
		}
	}

	/**
	* Get all current jobs for execution and executes the job
	*/
	public function executeJobs()
	{
		//get jobs to execute
		$jobs = $this->datastore->getAndRemoveJobs();

		foreach($jobs as $job)
		{
			$this->executeJob($job);
		}
	}
}
?>
