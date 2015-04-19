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

use yourCMDB\entities\CmdbLocalUser;

use yourCMDB\exceptions\CmdbLocalUserAlreadyExistsException;
use yourCMDB\exceptions\CmdbLocalUserNotFoundException;

/**
* controller for accessing local users
* singleton: use LocalUserController::create() for creating a new instance
* @author Michael Batz <michael@yourcmdb.org>
*
*/
class LocalUserController
{
	//local user controller (for singleton pattern)
	private static $localUserController;

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
	* creates a new local user controller
	* @param EnitityManager	$entityManager	doctrine entityManager
	* @return LocalUserController	LocalUserController instance
	*/
	public static function create($entityManager)
	{
		//check, if a LocalUserController instance exists with the correct entityManager
		if(LocalUserController::$localUserController == null || LocalUserController::$localUserController->entityManager != $entityManager)
		{
			LocalUserController::$localUserController = new LocalUserController($entityManager);
		}

		return LocalUserController::$localUserController;
	}

	/**
	* Adds a new local user
	* @param CmdbLocalUser $userObject	the user object
	* @throws CmdbLocalUserAlreadyExistsException
	*/
	public function addUser($userObject)
	{
		if($this->entityManager->find("yourCMDB:CmdbLocalUser", $userObject->getUsername()) != null)
		{
			throw new CmdbLocalUserAlreadyExistsException("A local user with that username already exists.");
		}
		$this->entityManager->persist($userObject);
		$this->entityManager->flush();
	}

	/**
	* Changes a local user
	* @param CmdbLocalUser $userObject	the user object
	*/
	public function changeUser($userObject)
	{
		$this->entityManager->flush();
	}

	/**
	* Deletes the user with the given username
	* @param string $username	name of the user
	* @throws CmdbLocalUserNotFoundException
	*/
	public function deleteUser($username)
	{
		$user = $this->getUser($username);
		$this->entityManager->remove($user);
		$this->entityManager->flush();
	}

	/**
	* Returns the user with the given username
	* @param string $username	name of the user
	* @throws CmdbLocalUserNotFoundException
	* @return CmdbLocalUser		the CmdbLocalUser object
	*/
	public function getUser($username)
	{
		$user = $this->entityManager->find("yourCMDB:CmdbLocalUser", $username);
		if($user == null)
		{
			throw new CmdbLocalUserNotFoundException("no user with $username found");
		}

		return $user;
	}

	/**
	* Returns all local users
	* @return CmdbLocalUser[]	array with CmdbLocalUser objects
	*/
	public function getUsers()
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("u");
		$queryBuilder->from("yourCMDB:CmdbLocalUser", "u");

		//get results
		$query = $queryBuilder->getQuery();
		$users = $query->getResult();

		//return
		return $users;
	}
}
?>
