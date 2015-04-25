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
use yourCMDB\entities\CmdbAccessGroup;
use yourCMDB\entities\CmdbAccessRule;
use yourCMDB\exceptions\CmdbAccessGroupNotFoundException;
use yourCMDB\exceptions\CmdbAccessGroupAlreadyExistsException;
use yourCMDB\exceptions\CmdbAccessRuleAlreadyExistsException;
use yourCMDB\exceptions\CmdbAccessRuleNotFoundException;
use \Exception;

/**
* controller for access groups
* singleton: use AccessGroupController::create() for creating a new instance
* @author Michael Batz <michael@yourcmdb.org>
*
*/
class AccessGroupController
{
	//access group controller (for singleton pattern)
	private static $accessGroupController;

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
	* creates a new access group controller
	* @return AccessGroupController	AccessGroupController instance
	*/
	public static function create()
	{
		//check, if an AccessGroupController instance already exists
		if(AccessGroupController::$accessGroupController == null)
		{
			AccessGroupController::$accessGroupController = new AccessGroupController();
		}

		return AccessGroupController::$accessGroupController;
	}

	/**
	* Returns all available AccessGroups
	* @return CmdbAccessGroup[]	Array with CmdbAccessGroup objects
	*/
	public function getAccessGroups()
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("g");
		$queryBuilder->from("yourCMDB:CmdbAccessGroup", "g");

		//get results
		$query = $queryBuilder->getQuery();
		$groups = $query->getResult();

		//return
		return $groups;
	}

	/**
	* Returns the AccessGroups with the given name
	* @param string $name		name of the access group
	* @throws CmdbAccessGroupNotFoundException
	* @return CmdbAccessGroup	the CmdbAccessGroup object
	*/
	public function getAccessGroup($name)
	{
		$group = $this->entityManager->find("yourCMDB:CmdbAccessGroup", $name);
		if($group == null)
		{
			throw new CmdbAccessGroupNotFoundException("no access group with name $name found");
		}

		return $group;
	}

	/**
	* Adds a new CmdbAccessGroup with the given name
	* @param string $name		name of the access group
	* @throws CmdbAccessGroupAlreadyExistsException
	* @return CmdbAccessGroup	the created CmdbAccessGroup
	*/
	public function addAccessGroup($name)
	{
		//check, if the group already exists
		if($this->entityManager->find("yourCMDB:CmdbAccessGroup", $name) != null)
		{
			throw new CmdbAccessGroupAlreadyExistsException("An access group with that name already exists.");
		}

		//create the group
		$group = new CmdbAccessGroup($name);
		$this->entityManager->persist($group);
		$this->entityManager->flush();

		//return the group object
		return $group;
	}

	/**
	* Deletes a CmdbAccessGroup from datastore
	* @param string $name		name of the access group
	* @throws CmdbAccessGroupNotFoundException
	*/
	public function deleteAccessGroup($name)
	{
		//delete the group
		$group = $this->getAccessGroup($name);
		$this->entityManager->remove($group);
		$this->entityManager->flush();
	}

	/**
	* Adds an access rule to an access group
	* @param string $accessGroupName	name of the access group
	* @param string $applicationPart	application part of the rule
	* @param integer $access		access rights
	* @throws CmdbAccessGroupNotFoundException
	* @throws CmdbAccessRuleAlreadyExistsException
	*/
	public function addAccessRule($accessGroupName, $applicationPart, $access)
	{
		//get access group
		$group = $this->getAccessGroup($accessGroupName);

		//create access rule
		try
		{
			$rule = new CmdbAccessRule($group, $applicationPart, $access);
			$group->getAccessRules()->add($rule);
			$this->entityManager->flush();
			
		}
		catch(Exception $e)
		{
			throw new CmdbAccessRuleAlreadyExistsException("access rule already exists");
		}
	}

	/**
	* Deletes an access rule
	* @param string $accessGroupName	name of the access group
	* @param string $applicationPart	application part of the rule
	* @throws CmdbAccessRuleNotFoundException
	*/
	public function deleteAccessRule($accessGroupName, $applicationPart)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("r");
		$queryBuilder->from("yourCMDB:CmdbAccessRule", "r");
		$queryBuilder->from("yourCMDB:CmdbAccessGroup", "g");
		$queryBuilder->andWhere("r.accessgroup = g.name");
		$queryBuilder->andWhere("IDENTITY(r.accessgroup) = ?1");
		$queryBuilder->andWhere("r.applicationPart = ?2");
		$queryBuilder->setParameter(1, $accessGroupName);
		$queryBuilder->setParameter(2, $applicationPart);

		//get results
		$query = $queryBuilder->getQuery();
		$rules = $query->getResult();
		if(count($rules) <= 0)
		{
			throw new CmdbAccessRuleNotFoundException("access rule not found");
		}
		$rule = $rules[0];

		//delete rule
		$this->entityManager->remove($rule);
		$this->entityManager->flush();
	}
}
?>
