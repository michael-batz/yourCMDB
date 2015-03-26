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
* controller for accessing objects
* singleton: use ObjectController::create() for creating a new instance
* @author Michael Batz <michael@yourcmdb.org>
*
*/
class ObjectController
{
	//object controller (for singleton pattern)
	private static $objectController;

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
	* creates a new object controller
	* @param EnitityManager	$entityManager	doctrine entityManager
	* @return ObjectController	ObjectController instance
	*/
	public static function create($entityManager)
	{
		//check, if an ObjectController instance exists with the correct entityManager
		if(ObjectController::$objectController == null || ObjectController::$objectController->entityManager != $entityManager)
		{
			ObjectController::$objectController = new ObjectController($entityManager);
		}

		return ObjectController::$objectController;
	}

	/**
	* Adds a new CmdbObject
	* @param string	$type		object type
	* @param string	$status		status of the object ("A" = active, "N" = not active)
	* @param mixed[] $fields	array of fields: fieldkey => fieldvalue
	* @param string	$user		name of user that wishes the change
	* @return CmdbObject	the created CmdbObject
	*/
	public function addObject($type, $status, $fields, $user)
	{
		//reset status value if invalid
		if($status != "A" && $status != "N")
		{
			$status = "A";
		}

		//create object
		$object = new CmdbObject($type, $status);
		$this->entityManager->persist($object);
		$this->entityManager->flush();

		//create fields and add to object
		foreach(array_keys($fields) as $key)
		{
			$value = $fields[$key];
			$objectField = new CmdbObjectField($object, $key, $value);
			$object->getFields()->add($objectField);
			$this->entityManager->persist($objectField);
		}
		$this->entityManager->flush();

		//create log entry
		$logEntry = new CmdbObjectLogEntry($object, "create", null, $user);
		$this->entityManager->persist($logEntry);
		$this->entityManager->flush();

		//return created object
		return $object;
	}

	/**
	* Get a CmdbObject by ID
	* @param int $id	ID of the object
	* @param string $user	name of the user that wants to get the object
	* @return CmdbObject	the CmdbObject
	* @throws CmdbObjectNotFoundException
	*/
	public function getObject($id, $user)
	{
		$object = $this->entityManager->find("CmdbObject", $id);
		if($object == null)
		{
			throw new CmdbObjectNotFoundException("Object with ID $id was not found.");
		}
		return $object;
	}

	/**
	* Updates the given CmdbObject
	* @param int $id		ID of the object that should be updated
	* @param string $status		new status of the CmdbObject
	* @param mixed[] $fields	array of updated fields: fieldkey => fieldvalue
	* @param string	$user		name of user that wishes the change
	* @return CmdbObject		the updated CmdbObject
	* @throws CmdbObjectNotFoundException
	*/
	public function updateObject($id, $status, $fields, $user)
	{
		//reset status value if invalid
		if($status != "A" && $status != "N")
		{
			$status = "A";
		}

		//get object
		$object = $this->getObject($id, $user);

		//update status if changed
		$oldStatus = $object->getStatus();
		if($oldStatus != $status)
		{
			$object->setStatus($status);
			$logEntry = new CmdbObjectLogEntry($object, "change status", "$oldStatus -> $status", $user);
			$this->entityManager->persist($logEntry);
			$this->entityManager->flush();
		}

		//update fields if changed
		$logString = "";
		foreach(array_keys($fields) as $key)
		{
			$value = $fields[$key];
			$objectField = $object->getFields()->get($key);
			//if field not exists, create it
			if($objectField == null)
			{
				$objectField = new CmdbObjectField($object, $key, $value);
				$object->getFields()->add($objectField);
				$this->entityManager->persist($objectField);
				$logString.= "$key: null -> $value; ";
			}
			//if field exists, but has a different value
			elseif($objectField->getFieldvalue() != $value)
			{
				$oldValue = $objectField->getFieldvalue();
				$objectField->setFieldvalue($value);
				$logString.= "$key: $oldValue -> $value; ";
			}
		}
		if($logString != "")
		{
			$logEntry = new CmdbObjectLogEntry($object, "change fields", $logString, $user);
			$this->entityManager->persist($logEntry);
			$this->entityManager->flush();
		}

		//return the object
		return $object;
	}

	/**
	* Deletes the CmdbObject with the given ID
	* @param int $id	ID of the CmdbObject
	* @param string	$user		name of user that wishes the change
	* @throws CmdbObjectNotFoundException
	*/
	public function deleteObject($id, $user)
	{
		$object = $this->getObject($id, $user);

		//remove log entries
		$logEntries = $this->entityManager->getRepository("CmdbObjectLogEntry")->findBy(array("object" => $id));
		foreach($logEntries as $logEntry)
		{
			$this->entityManager->remove($logEntry);
		}

		//ToDo: remove links

		//remove the object
		$this->entityManager->remove($object);

		//flush
		$this->entityManager->flush();
	}
}
?>
