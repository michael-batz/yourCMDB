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

use yourCMDB\entities\CmdbObjectLink;
use yourCMDB\entities\CmdbObject;

use yourCMDB\exceptions\CmdbObjectLinkNotAllowedException;
use yourCMDB\exceptions\CmdbObjectLinkNotFoundException;

/**
* controller for accessing objects links
* singleton: use ObjectLinkController::create() for creating a new instance
* @author Michael Batz <michael@yourcmdb.org>
*
*/
class ObjectLinkController
{
	//object link controller (for singleton pattern)
	private static $objectLinkController;

	//Doctrine entityManager
	private $entityManager;

	/**
	* private constructor
	* @param EnitityManager	entityManager	doctrine entityManager
	*/
	private function __construct($entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	* creates a new object link controller
	* @param EnitityManager	$entityManager	doctrine entityManager
	* @return ObjectLinkController	ObjectController instance
	*/
	public static function create($entityManager)
	{
		//check, if an ObjectLinkController instance exists with the correct entityManager
		if(ObjectLinkController::$objectLinkController == null || ObjectLinkController::$objectLinkController->entityManager != $entityManager)
		{
			ObjectLinkController::$objectLinkController = new ObjectLinkController($entityManager);
		}

		return ObjectLinkController::$objectLinkController;
	}

	/**
	* Returns a specific object link between the two objects A and B
	* A link between A and B is identical with a link between B and A
	* @param CmdbObject $objectA	first object
	* @param CmdbObject $objectB	second object
	* @param string $user	name of the user that wants to get the values
	* @return CmdbObjectLink	CmdbObjectLink or null, if nothing was found
	*/
	public function getObjectLink($objectA, $objectB, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("l");
		$queryBuilder->from("yourCMDB:CmdbObjectLink", "l");
		$queryBuilder->andWhere("(IDENTITY(l.objectA) = ?1 AND IDENTITY(l.objectB) = ?2) OR (IDENTITY(l.objectA) = ?2 AND IDENTITY(l.objectB) = ?1)");
		$queryBuilder->setParameter(1, $objectA->getId());
		$queryBuilder->setParameter(2, $objectB->getId());

		//get results
		$query = $queryBuilder->getQuery();
		$objectLinks = $query->getResult();

		//generate output
		$objectLink = null;
		if($objectLinks != null)
		{
			$objectLink = $objectLinks[0];
		}

		//return result
		return $objectLink;
	}

	/**
	* Returns all CmdbObjectLinks for an object
	* @param CmdbObject $object	object
	* @param string $user	name of the user that wants to get the values
	* @returns CmdbObjectLink[]	Array with CmdbObjectLinks
	*/
	public function getObjectLinks($object, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("l");
		$queryBuilder->from("yourCMDB:CmdbObjectLink", "l");
		$queryBuilder->andWhere("(IDENTITY(l.objectA) = ?1 OR IDENTITY(l.objectB) = ?1)");
		$queryBuilder->setParameter(1, $object->getId());

		//get results
		$query = $queryBuilder->getQuery();
		$objectLinks = $query->getResult();

		//return
		return $objectLinks;
	
	}

	/**
	* Creates a new CmdbObjectLink between two objects
	* @param CmdbObject $objectA	first object
	* @param CmdbObject $objectB	second object
	* @param string $user		name of the user
	* @throws CmdbObjectLinkNotAllowedException
	* @returns CmdbObjectLink	the created CmdbObjectLink
	*/
	public function addObjectLink($objectA, $objectB, $user)
	{
		//check, if $objectA == $objectB
		if($objectA == $objectB)
		{
			throw new CmdbObjectLinkNotAllowedException("object A == object B");
		}

		//check, if link already exists
		if($this->getObjectLink($objectA, $objectB, $user) != null)
		{
			throw new CmdbObjectLinkNotAllowedException("Object Link already exists");
		}

		//try to add a new link
		try
		{
			$link = new CmdbObjectLink($objectA, $objectB);
			$this->entityManager->persist($link);
			$this->entityManager->flush();
		}
		catch(Exception $e)
		{
			throw new CmdbObjectLinkNotAllowedException("One of the objects does not exists");
		}

		//create log entry
		$logString = $objectA->getId() . " <-> " . $objectB->getId();
		$objectLogController = ObjectLogController::create($this->entityManager);
		$objectLogController->addLogEntry($objectA, "add link", $logString, $user);
		$objectLogController->addLogEntry($objectB, "add link", $logString, $user);

		//return link
		return $link;
	}

	/**
	* Deletes a CmdbObjectLink between two objects
	* @param CmdbObject $objectA	first object
	* @param CmdbObject $objectB	second object
	* @param string $user		name of the user
	* @throws CmdbObjectLinkNotFoundException
	*/
	public function deleteObjectLink($objectA, $objectB, $user)
	{
		//check, if link exists
		$link = $this->getObjectLink($objectA, $objectB, $user);
		if($link == null)
		{
			throw new CmdbObjectLinkNotFoundException("Object Link not found");
		}

		//remove the link
		$this->entityManager->remove($link);
		$this->entityManager->flush();

		//create log entry
		$logString = $objectA->getId() . " <-> " . $objectB->getId();
		$objectLogController = ObjectLogController::create($this->entityManager);
		$objectLogController->addLogEntry($objectA, "delete link", $logString, $user);
		$objectLogController->addLogEntry($objectB, "delete link", $logString, $user);
	}

	/**
	* Deletes all CmdbObjectLinks for a given CmdbObject
	* @param CmdbObject $object	the CmdbObject
	* @param string $user		name of the user
	* @throws CmdbObjectLinkNotFoundException
	*/
	public function deleteObjectLinks($object, $user)
	{
		//get ObjectLogController
		$objectLogController = ObjectLogController::create($this->entityManager);

		//find the links
		$links = $this->getObjectLinks($object, $user);
		foreach($links as $link)
		{
			//remove the link
			$this->entityManager->remove($link);

			//create log entry
			$objectA = $link->getObjectA();
			$objectB = $link->getObjectB();
			$logString = $objectA->getId() . " <-> " . $objectB->getId();
			$objectLogController->addLogEntry($objectA, "delete link", $logString, $user);
			$objectLogController->addLogEntry($objectB, "delete link", $logString, $user);
		}

		//flush output
		$this->entityManager->flush();
	}

}
?>
