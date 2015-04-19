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

use yourCMDB\entities\CmdbObject;
use yourCMDB\entities\CmdbObjectField;

use yourCMDB\exceptions\CmdbObjectNotFoundException;

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
		$objectLogController = ObjectLogController::create($this->entityManager);
		$objectLogController->addLogEntry($object, "create", null, $user);

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
		$object = $this->entityManager->find("yourCMDB:CmdbObject", $id);
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

		//get object and objectLogController
		$object = $this->getObject($id, $user);
		$objectLogController = ObjectLogController::create($this->entityManager);


		//update status if changed
		$oldStatus = $object->getStatus();
		if($oldStatus != $status)
		{
			$object->setStatus($status);
			$objectLogController->addLogEntry($object, "change status", "$oldStatus -> $status", $user);
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
			$objectLogController->addLogEntry($object, "change fields", $logString, $user);
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

		//remove object links
		$objectLinkController = ObjectLinkController::create($this->entityManager);
		$objectLinkController->deleteObjectLinks($object, $user);

		//remove log entries
		$objectLogController = ObjectLogController::create($this->entityManager);
		$objectLogController->deleteLogEntries($object, $user);

		//remove the object
		$this->entityManager->remove($object);

		//flush
		$this->entityManager->flush();
	}

	/**
	* Returns all objects of a given type
	* @param string[] $types	Array with object types
	* @param string $sortfield	object field for sorting the results or null, if no sorting is needed
	* @param string $sorttype	"ASC" or "DESC"
	* @param string $status		status of the object or null, if no limit by status is needed
	* @param int $max		max count of results
	* @param int $start		offset for the first result
	* @param string $user		name of the user that wants to get the objects
	*/
	public function getObjectsByType($types, $sortfield=null, $sorttype="ASC", $status=null, $max=0, $start=0, $user)
	{
		if($sorttype != "DESC")
		{
			$sorttype = "ASC";
		}

		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("o");
		$queryBuilder->from("yourCMDB:CmdbObject", "o");
		$queryBuilder->from("yourCMDB:CmdbObjectField", "f");
		$queryBuilder->andWhere("o.type IN (?1)");
		if($status != null)
		{
			$queryBuilder->andWhere("o.status = ?3");
			$queryBuilder->setParameter(3, $status);
		}
		if($sortfield != null)
		{
			$queryBuilder->andWhere("f.object = o.id");
			$queryBuilder->andWhere("f.fieldkey = ?2");
			$queryBuilder->orderBy("f.fieldvalue", $sorttype);
			$queryBuilder->setParameter(2, $sortfield);
		}
		else
		{
			$queryBuilder->orderBy("o.id", $sorttype);
		}
		$queryBuilder->setParameter(1, $types);

		//limit results
		$queryBuilder->setFirstResult($start);
		if($max != 0)
		{
			$queryBuilder->setMaxResults($max);
		}

		//get results
		$query = $queryBuilder->getQuery();
		$objects = $query->getResult();

		//return
		return $objects;
	}

	/**
	* Returns all objects with a given pair of fieldkey and fieldvalue
	* @param string $fieldkey	name of the field
	* @param string $fieldvalue	value of the field
	* @param string[] $types	Array with object types or null, if no limit by object type is needed
	* @param string $status		status of the object or null, if no limit by status is needed
	* @param int $max		max count of results
	* @param int $start		offset for the first result
	* @param string $user		name of the user that wants to get the objects
	*/
	public function getObjectsByField($fieldkey, $fieldvalue, $types=null, $status=null, $max=0, $start=0, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("o");
		$queryBuilder->from("yourCMDB:CmdbObject", "o");
		$queryBuilder->from("yourCMDB:CmdbObjectField", "f");
		$queryBuilder->andWhere("f.object = o.id");
		$queryBuilder->andWhere("f.fieldkey = ?1");
		$queryBuilder->andWhere("f.fieldvalue = ?2");
		$queryBuilder->setParameter(1, $fieldkey);
		$queryBuilder->setParameter(2, $fieldvalue);
		if($types != null)
		{
			$queryBuilder->andWhere("o.type IN (?3)");
			$queryBuilder->setParameter(3, $types);
		}
		if($status != null)
		{
			$queryBuilder->andWhere("o.status = ?4");
			$queryBuilder->setParameter(4, $status);
		}

		//limit results
		$queryBuilder->setFirstResult($start);
		if($max != 0)
		{
			$queryBuilder->setMaxResults($max);
		}

		//get results
		$query = $queryBuilder->getQuery();
		$objects = $query->getResult();

		//return
		return $objects;
	}

	/**
	* Returns all objects with a given fieldvalue
	* @param string[] searchstrings	Array of searchstrings that must match part of fieldvalues of an objectal
	* @param string[] $types	Array with object types or null, if no limit by object type is needed
	* @param string $status		status of the object or null, if no limit by status is needed
	* @param int $max		max count of results
	* @param int $start		offset for the first result
	* @param string $user		name of the user that wants to get the objects
	*/
	public function getObjectsByFieldvalue($searchstrings, $types=null, $status=null, $max=0, $start=0, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("o");
		$queryBuilder->from("yourCMDB:CmdbObject", "o");
		if($searchstrings != null)
		{
			for($i = 0; $i < count($searchstrings); $i++)
			{
				$searchstring = $searchstrings[$i];
				$queryBuilder->andWhere("o.id IN (SELECT IDENTITY(f$i.object) FROM yourCMDB:CmdbObjectField f$i  WHERE f$i.fieldvalue LIKE ?$i )");
				$queryBuilder->setParameter($i, "%$searchstring%");
			}
		}
		if($types != null)
		{
			$i++;
			$queryBuilder->andWhere("o.type IN (?$i)");
			$queryBuilder->setParameter($i, $types);
		}
		if($status != null)
		{
			$i++;
			$queryBuilder->andWhere("o.status = ?$i");
			$queryBuilder->setParameter($i, $status);
		}

		//limit results
		$queryBuilder->setFirstResult($start);
		if($max != 0)
		{
			$queryBuilder->setMaxResults($max);
		}

		//get results
		$query = $queryBuilder->getQuery();
		$objects = $query->getResult();

		//return
		return $objects;
	}

	/**
	* Gets the last changed objects
	* @param string $foruser		only show objects changed by this user
	* @param int $max			show only max $max entries
	* @param int $start			offset for the first result
	* @param string $user			name of the user that wants to get the information
	* @return CmdbObject[]			Array with CmdbObject
	*/
	public function getLastChangedObjects($foruser=null, $max=10, $start=0, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("o");
		$queryBuilder->from("yourCMDB:CmdbObject", "o");
		$queryBuilder->from("yourCMDB:CmdbObjectLogEntry", "l");
		$queryBuilder->andWhere("l.object = o.id");
		$queryBuilder->andWhere("l.action != 'create'");
		if($foruser != null)
		{
			$queryBuilder->andWhere("l.user = ?1");
			$queryBuilder->setParameter(1, $foruser);
		}
		$queryBuilder->orderBy("l.timestamp", "DESC");

		//limit results
		$queryBuilder->setFirstResult($start);
		if($max != 0)
		{
			$queryBuilder->setMaxResults($max);
		}

		//get results
		$query = $queryBuilder->getQuery();
		$objects = $query->getResult();

		//return
		return $objects;
	}

	/**
	* Gets the last created objects
	* @param string $foruser		only show objects changed by this user
	* @param int $max			show only max $max entries
	* @param int $start			offset for the first result
	* @param string $user			name of the user that wants to get the information
	* @return CmdbObject[]			Array with CmdbObject
	*/
	public function getLastCreatedObjects($foruser=null, $max=10, $start=0, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("o");
		$queryBuilder->from("yourCMDB:CmdbObject", "o");
		$queryBuilder->from("yourCMDB:CmdbObjectLogEntry", "l");
		$queryBuilder->andWhere("l.object = o.id");
		$queryBuilder->andWhere("l.action = 'create'");
		if($foruser != null)
		{
			$queryBuilder->andWhere("l.user = ?1");
			$queryBuilder->setParameter(1, $foruser);
		}
		$queryBuilder->orderBy("l.timestamp", "DESC");

		//limit results
		$queryBuilder->setFirstResult($start);
		if($max != 0)
		{
			$queryBuilder->setMaxResults($max);
		}

		//get results
		$query = $queryBuilder->getQuery();
		$objects = $query->getResult();

		//return
		return $objects;
	}

	/**
	* Returns all already stored fieldvalues filterd by parameters
	* @param string[] $types	Array with object types or null, if no limit by object type is needed
	* @param string $fieldkey	fieldname
	* @param string $searchstring	fieldvalue starts with searchstring, or null if no filter is needed
	* @param int $max		max count of results or 0, if no limit
	* @param string $user		name of the user that wants to get the values
	*/
	public function getAllFieldValues($types=null, $fieldkey=null, $searchstring=null, $max=0, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("f.fieldvalue");
		$queryBuilder->from("yourCMDB:CmdbObjectField", "f");
		if($types != null)
		{
			$queryBuilder->from("yourCMDB:CmdbObject", "o");
			$queryBuilder->andWhere("f.object = o.id");
			$queryBuilder->andWhere("o.type IN (?1)");
			$queryBuilder->setParameter(1, $types);
		}
		if($fieldkey != null)
		{
			$queryBuilder->andWhere("f.fieldkey = ?2");
			$queryBuilder->setParameter(2, $fieldkey);
		}
		if($searchstring != null)
		{
			$queryBuilder->andWhere("f.fieldvalue LIKE ?3");
			$queryBuilder->setParameter(3, "$searchstring%");
		}
		$queryBuilder->distinct();
		$queryBuilder->orderBy("f.fieldvalue", "ASC");

		//limit results
		if($max != 0)
		{
			$queryBuilder->setMaxResults($max);
		}

		//get results
		$query = $queryBuilder->getQuery();
		$fieldvalues = $query->getResult();

		//create output
		$output = Array();
		foreach($fieldvalues as $fieldvalue)
		{
			$output[] = $fieldvalue['fieldvalue'];
		}

		//return output
		return $output;
	}

	/**
	* Returns the number of objects
	* @param string[] $types	Array with object types or null, if no limit by object type is needed
	* @param string $user		name of the user that wants to get the values
	*/
	public function getObjectCounts($types=null, $user)
	{
		//create QueryBuilder
		$queryBuilder = $this->entityManager->createQueryBuilder();

		//create query
		$queryBuilder->select("COUNT(o.id)");
		$queryBuilder->from("yourCMDB:CmdbObject", "o");

		if($types != null)
		{
			$queryBuilder->andWhere("o.type IN (?1)");
			$queryBuilder->setParameter(1, $types);
		}

		//get results
		$query = $queryBuilder->getQuery();
		$count = $query->getResult();
		$output = $count[0][1];

		//return output
		return $output;
	}

	public function getObjectReferences($id)
	{
		//ToDo: get reference fields from config
		;
	}
}
?>
