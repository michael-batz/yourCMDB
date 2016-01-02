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
namespace yourCMDB\controller;

use yourCMDB\orm\OrmController;
use yourCMDB\entities\CmdbJob;

/**
* controller for accessing jobs
* singleton: use JobController::create() for creating a new instance
* @author Michael Batz <michael@yourcmdb.org>
*
*/
class JobController
{
	//job controller (for singleton pattern)
	private static $jobController;

	//Doctrine entityManager
	private $entityManager;

	/**
	* private constructor
	*/
	private function __construct()
	{
		$ormController = OrmController::create();
		$this->entityManager = $ormController->getEntityManager();
	}

	/**
	* creates a new job controller
	* @return JobController	JobController instance
	*/
	public static function create()
	{
		//check, if a JobController instance already exists
		if(JobController::$jobController == null)
		{
			JobController::$jobController = new JobController();
		}

		return JobController::$jobController;
	}

	/**
	* Adds a new CmdbJob to the database
	* @param CmdbJob $job	the job to add
	*/
	public function addJob($job)
	{
		$this->entityManager->persist($job);
		$this->entityManager->flush();
	}

	/**
	* Gets all old (where timestamp < NOW() or timestamp is null) jobs from the database and removes them
	* @Retrun CmdbJob[]	Array of the removed jobs
	*/
	public function getAndRemoveJobs()
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("j");
		$queryBuilder->from("yourCMDB:CmdbJob", "j");
		$queryBuilder->andWhere("j.timestamp is null OR j.timestamp <= CURRENT_TIMESTAMP()");

		//get results
		$query = $queryBuilder->getQuery();
		$jobs = $query->getResult();

		//delete the giveb jobs
		foreach($jobs as $job)
		{
			$this->entityManager->remove($job);
		}
		$this->entityManager->flush();

		//return jobs
		return $jobs;
	}
}
?>
