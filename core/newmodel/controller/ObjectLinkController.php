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
	* @return CmdbObjectLink[]	CmdbObjectLink or null, if nothing was found
	*/
	public function getObjectLink($objectA, $objectB)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("l");
		$queryBuilder->from("CmdbObjectLink", "l");
		$queryBuilder->andWhere("(IDENTITY(l.objectA) = ?1 AND IDENTITY(l.objectB) = ?2) OR (IDENTITY(l.objectA) = ?2 AND IDENTITY(l.objectB) = ?1)");
		$queryBuilder->setParameter(1, $objectA->getId());
		$queryBuilder->setParameter(2, $objectB->getId());

		//get results
		$query = $queryBuilder->getQuery();
		$objectLinks = $query->getResult();

		//return result
		return $objectLinks;


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
		$queryBuilder->from("CmdbObjectLink", "l");
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
		if($this->getObjectLink($objectA, $objectB) != null)
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

		//return link
		return $link;
	}
}
?>
