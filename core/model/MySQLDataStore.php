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
* MySQL database access
* @author Michael Batz <michael@yourcmdb.org>
*/
class MySQLDataStore implements DataStoreInterface
{
	//datastore configuration
	private $configDatastore;

	//object type configuration
	private $configObjectTypes;

	//database connection
	private $dbConnection;

	//data type interpreter
	private $interpreter;

	public function __construct()
	{
		$config = new CmdbConfig();
		$this->configDatastore = $config->getDatastoreConfig()->getParameters();
		$this->configObjectTypes = $config->getObjectTypeConfig();
		$this->interpreter = new DataTypeInterpreter();

		//open connection to database server
		try
		{
			$dbConnectionString = "mysql:";
			$dbConnectionString .= "host=".$this->configDatastore['server'].";";
			$dbConnectionString .= "port=".$this->configDatastore['port'].";";
			$dbConnectionString .= "dbname=".$this->configDatastore['db'].";";
			$dbConnectionString .= "charset=utf8";
			$dbConnectionOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
			$this->dbConnection = new PDO($dbConnectionString, $this->configDatastore['user'], $this->configDatastore['password'], $dbConnectionOptions);
		}
		catch(Exception $e)
		{
			echo "Error Connecting to MySQL Server";
			exit();
		}

	}

	private function dbGetData($sql)
	{
		$sqlResult = $this->dbConnection->query($sql);
		if($sqlResult === false)
		{
			return false;
		}
		$output = $sqlResult->fetchAll();
		return $output;
	}

	private function dbSetData($sql)
	{
		$sqlResult = $this->dbConnection->exec($sql);
		if($sqlResult === false)
		{
			return false;
		}
		else
		{
			return true;
		}
	}


	public function isObject($id, $type=null)
	{
		//check, if there is a parameter
		if($id == "")
		{
			return false;
		}

		//escape strings
		$id = intval($id);

		//getting objecttype
		$sql = "SELECT type from CmdbObject WHERE assetid=$id AND active!='D'";
		$result = $this->dbGetData($sql);
		if($result == null)
		{
			return false;
		}
		$objectType = $result[0][0];

		//check if the given object has the correct type
		if($type != null)
		{
			if($objectType != $type)
			{
				return false;
			}
		}

		//return true, of object exists
		return true;
		
	}

        public function getObject($id)
	{
		//check, if there is a parameter
		if($id == "")
		{
			throw new NoSuchObjectException("Empty object ID");
		}

		//escape strings
		$id = intval($id);

		//getting objecttype
		$sql = "SELECT type, active from CmdbObject WHERE assetid=$id AND active!='D'";
		$result = $this->dbGetData($sql);
		if($result == null)
		{
			throw new NoSuchObjectException("Object with id $id not found");
		}
		$objectType = $result[0][0];
		$objectStatus = $result[0][1];

		//getting fields from database
		$sql = "SELECT fieldkey, fieldvalue FROM CmdbObjectField WHERE assetid=$id";
		$result = $this->dbGetData($sql);
		$objectFields = Array();
		$configFields = $this->configObjectTypes->getFields($objectType);
		foreach($result as $row)
		{
			$fieldkey = $row['fieldkey'];
			$fieldvalue = $row['fieldvalue'];
			//only gets the data, if there exists a configuration for the field
			if(isset($configFields[$fieldkey]))
			{
				$objectFields[$fieldkey] = $fieldvalue;
			}
		}


		return new CmdbObject($objectType, $objectFields, $id, $objectStatus);
	}

	/**
	* Adds the given CmdbObject to datastore and returns the new assetID for the object
	*/
	public function addObject(CmdbObject $cmdbObject)
        {
		//escape strings
		$escapedObjectType = $this->dbConnection->quote($cmdbObject->getType());
		$escapedObjectStatus = $this->dbConnection->quote($cmdbObject->getStatus());

		//Generate CmdbObject and get ObjectID from database
		$sql = "INSERT INTO CmdbObject(type, active) VALUES($escapedObjectType, $escapedObjectStatus)";
		$sqlResult = $this->dbSetData($sql);
		if($sqlResult == FALSE)
		{
			echo "Error inserting data into database";
			return 0;
		}
		$sql = "SELECT LAST_INSERT_ID()";
		$sqlResult = $this->dbGetData($sql);
		$objectID = $sqlResult[0][0];

		//store object fields - only if they are defined in config
		$objectFields = $cmdbObject->getFieldNames();
		$configFields = $this->configObjectTypes->getFields($cmdbObject->getType());
		foreach($objectFields as $objectField)
		{
			if(isset($configFields[$objectField]))
			{
				$objectFieldValue = $this->interpreter->interpret($cmdbObject->getFieldValue($objectField), $configFields[$objectField]);
				$escapedObjectFieldValue = $this->dbConnection->quote($objectFieldValue);
				$escapedObjectField = $this->dbConnection->quote($objectField);
				$sql = "INSERT INTO CmdbObjectField(assetid, fieldkey, fieldvalue) VALUES('$objectID', $escapedObjectField, $escapedObjectFieldValue)";
				$sqlResult = $this->dbSetData($sql);
				if($sqlResult == FALSE)
				{
					echo "Error inserting data into database";
				}
			}
		}

		//store object log entry
		$sql = "INSERT INTO CmdbObjectLog(assetid, action, date) VALUES('$objectID', 'add', NOW())";
		$sqlResult = $this->dbSetData($sql);
		if($sqlResult == FALSE)
		{
			echo "Error inserting data into database";
		}

		//generate objectAdded event
		$eventProcessor = new EventProcessor();
		$eventProcessor->generateEvent("objectAdded", $objectID, $cmdbObject->getType());

		//return objectID
		return $objectID;
        }

	/**
	* Change the fields of the given object
	* @param $id		ID of the object to change
	* @param $newFields	Array with new fields in form fieldname=>fieldvalue
	* @returns 		true, if successful, false if not
	*/
	public function changeObjectFields($id, $newFields)
        {
		//escape strings
		$id = intval($id);

		//getting objecttype
		$sql = "SELECT type from CmdbObject WHERE assetid=$id AND active!='D'";
		$result = $this->dbGetData($sql);
		if($result == null)
		{
			throw new NoSuchObjectException("Object with id $id not found");
		}
		$objectType = $result[0][0];


		//delete all current fields of the object in database
		$sql = "DELETE FROM CmdbObjectField WHERE assetid=$id";
		$sqlResult = $this->dbSetData($sql);
		if($sqlResult == FALSE)
		{
			error_log("Error deleting data from database");
			return false;
		}
	

		//add new fields for object in database - only if they are defined in configuration
		$configFields = $this->configObjectTypes->getFields($objectType);
		foreach(array_keys($newFields) as $fieldName)
		{
			if(isset($configFields[$fieldName]))
			{
				$fieldValue = $this->interpreter->interpret($newFields[$fieldName], $configFields[$fieldName]);
				$escapedFieldValue = $this->dbConnection->quote($fieldValue);
				$escapedFieldName = $this->dbConnection->quote($fieldName);
               	        	$sql = "INSERT INTO CmdbObjectField(assetid, fieldkey, fieldvalue) VALUES('$id', $escapedFieldName, $escapedFieldValue)";
				$sqlResult = $this->dbSetData($sql);
				if($sqlResult == FALSE)
				{
					error_log("Error inserting data into database");
				}
			}
		}
		
		//add log entry	
		$sql = "INSERT INTO CmdbObjectLog(assetid, action, date) VALUES('$id', 'change', NOW())";
		$sqlResult = $this->dbSetData($sql);
		if($sqlResult == FALSE)
		{
			error_log("Error inserting data into database");
		}

		//generate event
		$eventProcessor = new EventProcessor();
		$eventProcessor->generateEvent("objectChanged", $id, $objectType);

		return true;
        }



	/**
	* Change the status of the given object
	* @param $id		ID of the object to change
	* @param $newStatus	new Status of the object ('A', 'N')
	* @returns 		true, if successful, false if not
	*/
	public function changeObjectStatus($id, $newStatus)
	{
		//escape strings
		$id = intval($id);

		//getting objecttype
		$sql = "SELECT type from CmdbObject WHERE assetid=$id AND active!='D'";
		$result = $this->dbGetData($sql);
		if($result == null)
		{
			throw new NoSuchObjectException("Object with id $id not found");
		}
		$objectType = $result[0][0];


		//change status of the object
		if($newStatus != 'A' && $newStatus != 'N')
		{
			$newStatus = 'N';
		}
		$sql = "UPDATE CmdbObject SET active = '$newStatus' WHERE assetid = '$id'";
		$sqlResult = $this->dbSetData($sql);
	}


	public function deleteObject($id)
	{
		//escape strings
		$id = intval($id);

		//getting objecttype and references
		$sql = "SELECT type from CmdbObject WHERE assetid=$id AND active!='D'";
		$result = $this->dbGetData($sql);
		if($result == null)
		{
			throw new NoSuchObjectException("Object with id $id not found");
		}
		$objectType = $result[0][0];
		$references = $this->getObjectReferences($id);

		//delete object fields
		$sql = "DELETE FROM CmdbObjectField WHERE assetid=$id";
		$sqlResult = $this->dbSetData($sql);

		//delete object links
		$sql = "DELETE FROM CmdbObjectLink WHERE assetidA=$id OR assetidB=$id";
		$sqlResult = $this->dbSetData($sql);

		//set object to deleted
		$sql = "UPDATE CmdbObject SET active='D' WHERE assetid=$id";
		$sqlResult = $this->dbSetData($sql);

		//add object log
		$sql = "INSERT INTO CmdbObjectLog(assetid, action, date) VALUES('$id', 'delete', NOW())";
		$sqlResult = $this->dbSetData($sql);

		//delete references to object
		foreach($references as $reference)
		{
			$referenceObject = $this->getObject($reference);
			$this->changeObjectFields($reference, $referenceObject->getFields());
		}

		//generate event
		$eventProcessor = new EventProcessor();
		$eventProcessor->generateEvent("objectDeleted", $id, $objectType);
	}
	
	public function getObjectsByType($type, $sortfield="", $sorttype = "asc", $activeOnly=true, $max=0, $start=0)
	{
		//escape strings
		$type = $this->dbConnection->quote($type);
		$max = intval($max);
		$start = intval($start);

		//get all IDs
		$sql = "SELECT distinct CmdbObject.assetid FROM CmdbObject ";
		$sql.= "LEFT JOIN CmdbObjectField ON CmdbObject.assetid = CmdbObjectField.assetid ";
		$sql.= "WHERE CmdbObject.type=$type ";
		if($activeOnly)
		{
			$sql.= "AND CmdbObject.active='A' ";
		}
		else
		{
			$sql.= "AND CmdbObject.active!='D' ";
		}
		if($sortfield != "")
		{
			$sortfield = $this->dbConnection->quote($sortfield);
			$sql.= "AND CmdbObjectField.fieldkey = $sortfield ";
			$sql.= "ORDER BY CmdbObjectField.fieldvalue $sorttype ";
		}
		else
		{
			$sql.= "ORDER BY CmdbObject.assetid $sorttype ";
		}
		if($max != 0)
		{
			$sql.= "limit $start, $max";
		}
		$result = $this->dbGetData($sql);

		//create array with CmdbObjects
		$output = Array();
		foreach($result as $objectID)
		{
			$output[] = $this->getObject($objectID['assetid']);
		}
		return $output;
	}

	/**
	* Search for objects. Get all objects with a specific value for a specific field
	* @param $fieldname	Name of the field
	* @param $fieldvalue	Value of the field
	* @param $types		Array with object types or null. Only show objects of a specific type
	* @returns 		Array with objects
	*/
	public function getObjectsByField($fieldname, $fieldvalue, $types=null, $activeOnly=true, $max=0, $start=0)
	{
		//escape strings
		$fieldname = $this->dbConnection->quote($fieldname);
		$fieldvalue = $this->dbConnection->quote($fieldvalue);
		$max = intval($max);
		$start = intval($start);
		

		//get all IDs
                $sql = "SELECT distinct CmdbObject.assetid FROM CmdbObject, CmdbObjectField ";
		$sql.= "WHERE CmdbObject.assetid = CmdbObjectField.assetid ";
		if($activeOnly)
		{
			$sql.= "AND CmdbObject.active='A' ";
		}
		else
		{
			$sql.= "AND CmdbObject.active!='D' ";
		}


		if($types != null && count($types) != 0)
		{
			$sql.= "AND CmdbObject.type IN (";
			for($i = 0; $i < count($types); $i++)
			{
				$sql.= $this->dbConnection->quote($types[$i]);
				if($i < (count($types) - 1))
				{
					$sql.= ", ";
				}
			}
			$sql.= ") ";
		}
		$sql.= "AND fieldkey = $fieldname ";
		$sql.= "AND fieldvalue = $fieldvalue ";
		if($max != 0)
		{
			$sql.= "limit $start, $max";
		}
                $result = $this->dbGetData($sql);

		//create array with CmdbObjects
		$output = Array();
		foreach($result as $objectID)
		{
			$output[] = $this->getObject($objectID['assetid']);
		}
		return $output;
	}

	/**
	* Search for objects. Get all objects with a specific field value
	* @param $searchstrings	Array of searchstrings
	* @param $types		Array with object types or null. Only show objects of a specific type
	* @returns 		Array with objects
	*/
	public function getObjectsByFieldvalue($searchstrings, $types=null, $activeOnly=true, $max=0, $start=0)
	{
		//escape strings
		$max = intval($max);
		$start = intval($start);
		

		//get all IDs
		$sql = "SELECT o.assetid FROM CmdbObject o ";
		$sql.= "WHERE ";
		//sql searchstrings
		foreach($searchstrings as $searchstring)
		{
			$searchstring = $this->dbConnection->quote("%$searchstring%");
			$sql.= "o.assetid IN (SELECT assetid FROM CmdbObjectField WHERE fieldvalue like $searchstring) AND ";
		}
		//sql activeonly
		if($activeOnly)
		{
			$sql.= "o.active='A' ";
		}
		else
		{
			$sql.= "o.active!='D' ";
		}
		//sql object types
		if($types != null && count($types) != 0)
		{
			$sql.= "AND o.type IN (";
			for($i = 0; $i < count($types); $i++)
			{
				$sql.= $this->dbConnection->quote($types[$i]);
				if($i < (count($types) - 1))
				{
					$sql.= ", ";
				}
			}
			$sql.= ") ";
		}
		//sql limit
		if($max != 0)
		{
			$sql.= "LIMIT $start, $max";
		}
                $result = $this->dbGetData($sql);

		//create array with CmdbObjects
		$output = Array();
		foreach($result as $objectID)
		{
			$output[] = $this->getObject($objectID['assetid']);
		}
		return $output;
	}

	/**
	* Get all links to objects of a specific object
	*
	*/
	public function getObjectLinks($id)
	{
		//escape strings
		$id = intval($id);

		$sql = "SELECT distinct assetidB from CmdbObjectLink WHERE assetidA=$id";
		$result = $this->dbGetData($sql);

		//create array with CmdbObjects
		$output = Array();
		foreach($result as $objectID)
		{
			$output[] = $objectID['assetidB'];
		}
		return $output;
	}

	/**
	* Get all objects that link to a specific object
	*
	*/
	public function getLinkedObjects($id)
	{	
		//escape strings
		$id = intval($id);

		$sql = "SELECT distinct assetidA from CmdbObjectLink WHERE assetidB=$id";
		$result = $this->dbGetData($sql);

		//create array with CmdbObjects
		$output = Array();
		foreach($result as $objectID)
		{
			$output[] = $objectID['assetidA'];
		}
		return $output;
	}


	/**
	* Adds an new link between two objects
	* @param $idA		ID of object A
	* @param $idB		ID of object B
	* @returns boolean	true, if link was created, false, if there was an error
	*/
        public function addObjectLink($idA, $idB)
	{
		//escape strings
		$idA = intval($idA);
		$idB = intval($idB);

		//check, if A = B
		if($idA == $idB)
		{
                        throw new ObjectActionNotAllowed("You cannot link an object with itself");
		}

		//check if A exists in database
                $sql = "SELECT assetid from CmdbObject WHERE assetid=$idA AND active!='D'";
                $result = $this->dbGetData($sql);
                if($result == null)
                {
                        throw new NoSuchObjectException("Object with id $idA not found");
                }

		//check if B exists in database
                $sql = "SELECT assetid from CmdbObject WHERE assetid=$idB AND active!='D'";
                $result = $this->dbGetData($sql);
                if($result == null)
                {
                        throw new NoSuchObjectException("Object with id $idB not found");
                }

		//check, if link already exists
		$sql = "SELECT count(*) from CmdbObjectLink WHERE ";
		$sql.= "(assetidA = $idA AND assetidB = $idB) OR";
		$sql.= "(assetidA = $idB AND assetidB = $idA)";
                $result = $this->dbGetData($sql);
		if($result[0][0] != 0)
		{
                        throw new ObjectActionNotAllowed("Object Link already exists.");
		}


		//create link
		$sql = "INSERT INTO CmdbObjectLink(assetidA, assetidB) VALUES('$idA', '$idB')";
		$sqlResult = $this->dbSetData($sql);
                if($sqlResult == FALSE)
                {
                        error_log("Error inserting data into database");
			return false;
                }

		//add object log
		$sql = "INSERT INTO CmdbObjectLog(assetid, action, date) VALUES('$idA', 'change', NOW())";
		$sqlResult = $this->dbSetData($sql);

                return true;
	}

	/**
	* Deletes a link between two objects
	* @param $idA		ID of object A
	* @param $idB		ID of object B
	* @returns boolean	true, if link was deleted, false, if there was an error
	*/
        public function deleteObjectLink($idA, $idB)
	{
		//escape strings
		$idA = intval($idA);
		$idB = intval($idB);

		//delete link
		$sql = "DELETE FROM CmdbObjectLink WHERE (assetidA=$idA AND assetidB=$idB) ";
		$sql.= "OR (assetidA=$idB AND assetidB=$idA)";
		$sqlResult = $this->dbSetData($sql);
                if($sqlResult == FALSE)
                {
                        error_log("Error deleting data from database");
			return false;
                }

		//add object log
		$sql = "INSERT INTO CmdbObjectLog(assetid, action, date) VALUES('$idA', 'change', NOW())";
		$sqlResult = $this->dbSetData($sql);

                return true;
	}


	public function getObjectCounts($type)
	{
		//escape strings
		$type = $this->dbConnection->quote($type);

		$sql = "SELECT count(*) FROM CmdbObject WHERE type=$type AND active='A'";
		$result = $this->dbGetData($sql);
		$objectCount = $result[0][0];

		return $objectCount;
	}

	/**
	* Get all fieldvalues
	* @param $objecttype	show only fieldvalues of a specific object type
	* @param $fieldname	show only fieldvalues of a specific field name
	* @param $searchstring	show only fieldvalues starting with searchstring
	* @param $limit		max count of results
	*/
	public function getAllFieldValues($objecttype=null, $fieldname=null, $searchstring=null, $limit=10)
	{
		//escape strings
		$limit = intval($limit);

		//create sql query
		$sql = "SELECT distinct fieldvalue FROM CmdbObjectField ";
		$sql.= "LEFT JOIN CmdbObject ON CmdbObjectField.assetid = CmdbObject.assetid WHERE ";
		if($objecttype != null)
		{
			$objecttype = $this->dbConnection->quote($objecttype);
			$sql.= "CmdbObject.type = $objecttype AND ";
		}
		if($fieldname != null)
		{
			$fieldname = $this->dbConnection->quote($fieldname);
			$sql.= "CmdbObjectField.fieldkey = $fieldname AND ";
		}
		if($searchstring != null)
		{
			$searchstring = $this->dbConnection->quote("$searchstring%");
			$sql.= "CmdbObjectField.fieldvalue like $searchstring AND ";
		}
		$sql.= "CmdbObjectField.fieldvalue !='' ";
		$sql.= "ORDER BY fieldvalue ASC ";
		$sql.= "LIMIT $limit";
		$result = $this->dbGetData($sql);

		//create array with field values
		$output = Array();
		foreach($result as $row)
		{
			$output[] = $row[0];
		}
		return $output;
	}

	/**
	* Get the object log of a specific object
	* @param $objectId		Id of object
	* @returns CmdbObjectLog 	log of a specific CmdbObject
	*/
	public function getObjectLog($objectId)
	{
		//escape strings
		$objectId = intval($objectId);

		//check if object exists in database
                $sql = "SELECT assetid from CmdbObject WHERE assetid=$objectId AND active!='D'";
                $result = $this->dbGetData($sql);
                if($result == null)
                {
                        throw new NoSuchObjectException("Object with id $objectId not found");
                }

		//create sql query
		$sql = "SELECT date, action FROM CmdbObjectLog ";
		$sql.= "WHERE assetid = $objectId ";
		$sql.= "ORDER BY date DESC";
		$result = $this->dbGetData($sql);

		//create array with log entries
		$output = Array();
		foreach($result as $row)
		{
			$output[] = new CmdbObjectLogEntry($row[0], $row[1]);
		}

		return new CmdbObjectLog($objectId, $output);
	}

	/**
	* Get the Top N newest objects
	* @param $n				return max n objects
	* @returns Array(CmdbObject, Date)
	*/
	public function getNNewestObjects($n)
	{
		//escape strings
		$n = intval($n);

		//create sql query
		$sql = "SELECT CmdbObjectLog.assetid, MAX(CmdbObjectLog.date) FROM CmdbObjectLog ";
		$sql.= "LEFT JOIN CmdbObject ON CmdbObjectLog.assetid = CmdbObject.assetid ";
		$sql.= "WHERE CmdbObject.active != 'D' AND CmdbObjectLog.action = 'Add' ";
		$sql.= "GROUP BY CmdbObjectLog.assetid ORDER BY MAX(CmdbObjectLog.date) DESC LIMIT $n";

		$result = $this->dbGetData($sql);

		//create output
		$output = Array();
		foreach($result as $row)
		{
			$output[] = Array($this->getObject($row[0]), $row[1]);
		}

		return $output;
	}

       	/**
	* Get the Top N last changed objects
	* @param $n				return max n objects
	* @returns Array(CmdbObject, Date)
	*/
        public function getNLastChangedObjects($n)
	{
		//escape strings
		$n = intval($n);

		//create sql query
		$sql = "SELECT CmdbObjectLog.assetid, MAX(CmdbObjectLog.date) FROM CmdbObjectLog ";
		$sql.= "LEFT JOIN CmdbObject ON CmdbObjectLog.assetid = CmdbObject.assetid ";
		$sql.= "WHERE CmdbObject.active != 'D' AND CmdbObjectLog.action = 'Change' ";
		$sql.= "GROUP BY CmdbObjectLog.assetid ORDER BY MAX(CmdbObjectLog.date) DESC LIMIT $n";

		$result = $this->dbGetData($sql);

		//create output
		$output = Array();
		foreach($result as $row)
		{
			$output[] = Array($this->getObject($row[0]), $row[1]);
		}

		return $output;
	}

	/**
	* Add a job to the database
	* @param $job			CmdbJob object
	* @param $timestamp		UNIX timestamp for executing the job - or null,
	*				if job should be executed on the next run of
	*				TaskScheduler
	*/
	public function addJob(CmdbJob $job, int $timestamp = null)
	{
		//escape strings
		$action = $this->dbConnection->quote($job->getAction());
		$actionParameter = $this->dbConnection->quote($job->getActionParameter());

		//create sql statement
		$sql = "INSERT INTO CmdbJob(action, actionParameter, timestamp) VALUES($action, $actionParameter, ";
		if($timestamp != null)
		{
			$sql .= "FROM_UNIXTIME($timestamp)";
		}
		else
		{
			$sql .= "null";
		}
		$sql .= ")";

		//execute query and return result
		$sqlResult = $this->dbSetData($sql);
		if($sqlResult == FALSE)
		{
			error_log("Error creating cmdb job");
		}
		return $sqlResult;
	}

	/**
	* Gets all old (where timestamp is null or in past) jobs and remove them from database
	* @returns	Array of CmdbJob objects to execute
	*/
	public function getAndRemoveJobs()
	{
		//sql query
		$sql = "SELECT jobid, action, actionParameter FROM CmdbJob ";
		$sql.= "WHERE (timestamp is null) or (timestamp < NOW())";
		$result = $this->dbGetData($sql);

		//create output
		$jobIds = Array();
		$output = Array();
		foreach($result as $row)
		{
			$output[] = new CmdbJob($row[1], $row[2]);
			$jobIds[] = $row[0];
		}

		//remove old jobs if there were some
		if(count($jobIds) > 0)
		{
			$sql = "DELETE FROM CmdbJob WHERE jobid in (";
			for($i = 0; $i < count($jobIds); $i++)
			{
				$sql.= "{$jobIds[$i]}";
				if($i != (count($jobIds) - 1))
				{
					$sql.= " ,";
				}
			}
			$sql .= ")";
			$sqlResult = $this->dbSetData($sql);
			if($sqlResult == FALSE)
			{
				error_log("Error deleting cmdb jobs");
			}

		}

		//return output
		return $output;

	}

	/**
	* Get all objects that have a reference to the given object
	* @param $objectId	ID of the given object
	*/
	public function getObjectReferences($objectId)
	{
		$output = Array();

		//check, if object exists
		$object = $this->getObject($objectId);
		$dataType = "objectref-".$object->getType();

		//get reference fields
		$referenceFields = $this->configObjectTypes->getFieldsByType($dataType);

		//check if there are reference fields and get referenced objects
		if(count($referenceFields) > 0)
		{
			//create query
			$sql = "SELECT distinct CmdbObjectField.assetid FROM CmdbObjectField ";
			$sql.= "LEFT JOIN CmdbObject ON CmdbObjectField.assetid = CmdbObject.assetid ";
			$sql.= "WHERE CmdbObjectField.fieldvalue = $objectId ";
			$sql.= "AND (";
			for($i = 0; $i < count($referenceFields); $i++)
			{
				$sql.= "(CmdbObject.type = '{$referenceFields[$i][0]}' ";
				$sql.= "AND CmdbObjectField.fieldkey = '{$referenceFields[$i][1]}') ";
				if($i != count($referenceFields) - 1)
				{
					$sql.= "OR ";
				}

			}
			$sql.= ")";
	                $result = $this->dbGetData($sql);

			//create array with CmdbObjects
			foreach($result as $row)
			{
				$output[] = $row['assetid'];
			}
		}

		//return output
		return $output; 
	}

	public function addUser(CmdbLocalUser $user)
	{
		$username = $user->getUsername();
		$passwordHash = $user->getPasswordHash();
		$accessGroup = $user->getAccessGroup();
		$sql = "INSERT INTO CmdbLocalUser(username, passwordhash, accessgroup) VALUES('$username', '$passwordHash', '$accessGroup')";

		//execute query and return result
		$sqlResult = $this->dbSetData($sql);
		if($sqlResult == FALSE)
		{
			error_log("Error creating cmdb user");
		}
		return $sqlResult;
	}
        
        public function changeUser($username, CmdbLocalUser $newuser)
	{
		;
	}

        public function deleteUser($username)
	{
		;
	}

        public function getUser($username)
	{
		$sql = "SELECT username, passwordhash, accessgroup from CmdbLocalUser WHERE username='$username'";
		$result = $this->dbGetData($sql);
		if(count($result) > 0)
		{
			return new CmdbLocalUser($result[0][0], $result[0][1], $result[0][2]);
		}
		return null;
	}

        public function getUsers()
	{
		$sql = "SELECT username, passwordhash, accessgroup from CmdbLocalUser";
		$result = $this->dbGetData($sql);
		$output = Array();
		foreach($result as $row)
		{
			$output[] = new CmdbLocalUser($row[0], $row[1], $row[2]);
		}
		return $output;
	}

}
?>
