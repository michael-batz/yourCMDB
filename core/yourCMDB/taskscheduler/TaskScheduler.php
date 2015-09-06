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
namespace yourCMDB\taskscheduler;

use yourCMDB\config\CmdbConfig;
use yourCMDB\controller\JobController;
use yourCMDB\entities\CmdbJob;
use \DateTime;

/**
* TaskScheduler
* @author Michael Batz <michael@yourcmdb.org>
*/
class TaskScheduler
{
	//task scheduler config
	private $configTaskScheduler;

	function __construct()
	{
		//get configuration
		$config = CmdbConfig::create();
		$this->configTaskScheduler = $config->getTaskSchedulerConfig();
	}

	/**
	* Executes the given job
	*/
	private function executeJob(\yourCMDB\entities\CmdbJob $job)
	{
		//check, if job action exists
		$actionClassName = __NAMESPACE__ ."\\". $job->getAction();
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
	* Replaces variables in the given actionParameter with values from $event
	*/
	private function replaceActionParameter($actionParameter, CmdbEvent $event)
	{
		//replacement for ${objectId}
		if($event->getObjectId() != null)
		{
			$actionParameter = str_replace('${objectId}', $event->getObjectId(), $actionParameter);
		}

		//replacement for ${objectType}
		if($event->getObjectType() != null)
		{
			$actionParameter = str_replace('${objectType}', $event->getObjectType(), $actionParameter);
		}

		//return output
		return $actionParameter;
	}

	/**
	* Handle the given CmdbEvent and generate job, if configured
	*/
	public function eventHandler(CmdbEvent $event)
	{
		//check, if there are tasks for the given event
		$tasks = $this->configTaskScheduler->getTasksForEvent($event->getEventType(), $event->getObjectType());

		//get JobController
		$jobController = JobController::create();

		//create jobs
		foreach($tasks as $task)
		{
			$jobAction = $task->getAction();
			$jobActionParm = $this->replaceActionParameter($task->getActionParameter(), $event);
			$job = new CmdbJob($jobAction, $jobActionParm, new DateTime());
			$jobController->addJob($job);
		}
	}

	/**
	* Get all current jobs for execution and executes the job
	*/
	public function executeJobs()
	{
		//get jobs to execute
		$jobController = JobController::create();
		$jobs = $jobController->getAndRemoveJobs();

		foreach($jobs as $job)
		{
			$this->executeJob($job);
		}
	}
}
?>
