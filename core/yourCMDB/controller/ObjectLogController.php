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
namespace yourCMDB\controller;

use yourCMDB\orm\OrmController;

use yourCMDB\entities\CmdbObject;
use yourCMDB\entities\CmdbObjectLogEntry;

/**
* controller for accessing object log entries
* singleton: use ObjectLogController::create() for creating a new instance
* @author Michael Batz <michael@yourcmdb.org>
*
*/
class ObjectLogController
{
	//object log controller (for singleton pattern)
	private static $objectLogController;

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
	* creates a new object log controller
	* @return ObjectLogController	ObjectLogController instance
	*/
	public static function create()
	{
		//check, if an ObjectLogController instance already exists
		if(ObjectLogController::$objectLogController == null)
		{
			ObjectLogController::$objectLogController = new ObjectLogController();
		}

		return ObjectLogController::$objectLogController;
	}

	/**
	* Returns CmdbLogEntries for a given object
	* @param CmdbObject $object		CmdbObject for getting the log entries
	* @param $start
	* @param int $max		max count of results
	* @param int $start		offset for the first result
	* @param string $user		name of the user that wants to get the objects
	* @return CmdbObjectLogEntry[]	array with CmdbObjectLogEntry  objects
	*/
	public function getLogEntries($object, $max=0, $start=0, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("l");
		$queryBuilder->from("yourCMDB:CmdbObjectLogEntry", "l");
		$queryBuilder->andWhere("IDENTITY(l.object) = ?1");
		$queryBuilder->setParameter(1, $object->getId());
		$queryBuilder->orderBy("l.timestamp", "DESC");

		//limit results
		$queryBuilder->setFirstResult($start);
		if($max != 0)
		{
			$queryBuilder->setMaxResults($max);
		}

		//get results
		$query = $queryBuilder->getQuery();
		$logEntries = $query->getResult();

		//return
		return $logEntries;
	}

	/**
	* Returns the CmdbLogEntry for the creation of the given object
	* @param CmdbObject $object	CmdbObject for getting the log entries
	* @param string $user		name of the user that wants to get the objects
	* @return CmdbObjectLogEntry	CmdbObjectLogEntry object or null if nothing was found
	*/
	public function getCreatedLogEntry($object, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("l");
		$queryBuilder->from("yourCMDB:CmdbObjectLogEntry", "l");
		$queryBuilder->andWhere("IDENTITY(l.object) = ?1");
		$queryBuilder->setParameter(1, $object->getId());
		$queryBuilder->andWhere("l.action = ?2");
		$queryBuilder->setParameter(2, "create");

		//get results
		$query = $queryBuilder->getQuery();
		$logEntries = $query->getResult();

		//generate output
		$logEntry = null;
		if($logEntries != null)
		{
			$logEntry = $logEntries[0];
		}

		//return
		return $logEntry;
	}

	/**
	* Returns the CmdbLogEntry for the last change of the given object
	* @param CmdbObject $object	CmdbObject for getting the log entries
	* @param string $user		name of the user that wants to get the objects
	* @return CmdbObjectLogEntry	CmdbObjectLogEntry object or null if nothing was found
	*/
	public function getChangedLogEntry($object, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("l");
		$queryBuilder->from("yourCMDB:CmdbObjectLogEntry", "l");
		$queryBuilder->andWhere("IDENTITY(l.object) = ?1");
		$queryBuilder->setParameter(1, $object->getId());
		$queryBuilder->andWhere("l.action != ?2");
		$queryBuilder->setParameter(2, "create");
		$queryBuilder->orderBy("l.timestamp", "DESC");

		//get results
		$query = $queryBuilder->getQuery();
		$logEntries = $query->getResult();

		//generate output
		$logEntry = null;
		if($logEntries != null)
		{
			$logEntry = $logEntries[0];
		}

		//return
		return $logEntry;
	}

	/**
	* Deletes all CmdbLogEntries for a given object
	* @param CmdbObject $object		CmdbObject
	* @param string $user			name of the user
	*/
	public function deleteLogEntries($object, $user)
	{
		//get all log entries
		$logEntries = $this->getLogEntries($object, 0, 0, $user);

		//delete the log entries
		foreach($logEntries as $logEntry)
		{
			$this->entityManager->remove($logEntry);
		}
		$this->entityManager->flush();
	}

	/**
	* Adds a new log entry for the given object
	* @param CmdbObject $object		CmdbObject
	* @param string $action			action string of the log entry
	* @param string	$description		description string of the log entry
	* @param string $user			username string of the log entry
	*/
	public function addLogEntry($object, $action, $description, $user)
	{
		$logEntry = new CmdbObjectLogEntry($object, $action, $description, $user);
		$this->entityManager->persist($logEntry);
		$this->entityManager->flush();
	}
}
?>
