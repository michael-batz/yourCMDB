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

	public function __construct()
	{
		$config = new CmdbConfig();
		$this->configDatastore = $config->getDatastoreConfig()->getParameters();
		$this->configObjectTypes = $config->getObjectTypeConfig();

		//open connection to database server
		$this->dbConnection = mysql_connect($this->configDatastore['server'].":".$this->configDatastore['port'], $this->configDatastore['user'], $this->configDatastore['password']);
		if($this->dbConnection == FALSE)
		{
			echo "Error Connecting to MySQL Server";
			exit();
		}
		mysql_set_charset('utf8', $this->dbConnection);
		mysql_select_db($this->configDatastore['db'], $this->dbConnection);

	}

	private function dbGetData($sql)
	{
		$sqlResult = mysql_query($sql, $this->dbConnection);
		$output = Array();
		while($row = mysql_fetch_array($sqlResult))
		{
			$output[] = $row;
		}

		mysql_free_result($sqlResult);
		return $output;
	}

	private function dbSetData($sql)
	{
		$sqlResult = mysql_query($sql, $this->dbConnection);
		return $sqlResult;
	}



        public function getObject($id)
	{
		//escape strings
		$id = mysql_real_escape_string($id, $this->dbConnection);

		//getting objecttype
		$sql = "SELECT type, active from CmdbObject WHERE assetid=$id AND active!='D'";
		$result = $this->dbGetData($sql);
		if($result == null)
		{
			throw new NoSuchObjectException("Object with id $id not found");
		}
		$objectType = $result[0][0];
		$objectStatus = $result[0][1];

		//getting fields
		$sql = "SELECT fieldkey, fieldvalue FROM CmdbObjectField WHERE assetid=$id";
		$result = $this->dbGetData($sql);
		$objectFields = Array();
		foreach($result as $row)
		{
			$fieldkey = $row['fieldkey'];
			$fieldvalue = $row['fieldvalue'];
			$objectFields[$fieldkey] = $fieldvalue;
		}


		return new CmdbObject($objectType, $objectFields, $id, $objectStatus);
	}

	/**
	* Adds the given CmdbObject to datastore and returns the new assetID for the object
	*/
	public function addObject(CmdbObject $cmdbObject)
        {
		//escape strings
		$escapedObjectType = mysql_real_escape_string($cmdbObject->getType(), $this->dbConnection);
		$escapedObjectStatus = mysql_real_escape_string($cmdbObject->getStatus(), $this->dbConnection);

		//Generate CmdbObject and get ObjectID from database
		$sql = "INSERT INTO CmdbObject(type, active) VALUES('$escapedObjectType', '$escapedObjectStatus')";
		$sqlResult = $this->dbSetData($sql);
		if($sqlResult == FALSE)
		{
			echo "Error inserting data into database";
			return 0;
		}
		$sql = "SELECT LAST_INSERT_ID()";
		$sqlResult = $this->dbGetData($sql);
		$objectID = $sqlResult[0][0];

		//store object fields
		$objectFields = $cmdbObject->getFieldNames();
		foreach($objectFields as $objectField)
		{
			$escapedObjectFieldType = mysql_real_escape_string($this->configObjectTypes->getFieldType($cmdbObject->getType(), $objectField), $this->dbConnection);
			$escapedObjectFieldValue = mysql_real_escape_string($cmdbObject->getFieldValue($objectField),$this->dbConnection);
			$escapedObjectField = mysql_real_escape_string($objectField, $this->dbConnection);
			$sql = "INSERT INTO CmdbObjectField(assetid, fieldkey, fieldtype, fieldvalue) VALUES('$objectID', '$escapedObjectField', '$escapedObjectFieldType', '$escapedObjectFieldValue')";
			$sqlResult = $this->dbSetData($sql);
			if($sqlResult == FALSE)
			{
				echo "Error inserting data into database";
			}
		}

		//store object log entry
		$sql = "INSERT INTO CmdbObjectLog(assetid, action, date) VALUES('$objectID', 'add', NOW())";
		$sqlResult = $this->dbSetData($sql);
		if($sqlResult == FALSE)
		{
			echo "Error inserting data into database";
		}

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
		$id = mysql_real_escape_string($id, $this->dbConnection);

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
	

		//add new fields for object in database
		foreach(array_keys($newFields) as $fieldName)
		{
			$escapedFieldValue = mysql_real_escape_string($newFields[$fieldName], $this->dbConnection);
			$escapedFieldType = mysql_real_escape_string($this->configObjectTypes->getFieldType($objectType, $fieldName), $this->dbConnection);
			$escapedFieldName = mysql_real_escape_string($fieldName, $this->dbConnection);
                        $sql = "INSERT INTO CmdbObjectField(assetid, fieldkey, fieldtype, fieldvalue) VALUES('$id', '$escapedFieldName', '$escapedFieldType', '$escapedFieldValue')";
			$sqlResult = $this->dbSetData($sql);
			if($sqlResult == FALSE)
			{
				error_log("Error inserting data into database");
			}
		}
		
		//add log entry	
		$sql = "INSERT INTO CmdbObjectLog(assetid, action, date) VALUES('$id', 'change', NOW())";
		$sqlResult = $this->dbSetData($sql);
		if($sqlResult == FALSE)
		{
			error_log("Error inserting data into database");
		}

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
		$id = mysql_real_escape_string($id, $this->dbConnection);
		$newStatus = mysql_real_escape_string($newStatus, $this->dbConnection);

		//check if object exists in database
                $sql = "SELECT assetid from CmdbObject WHERE assetid=$id AND active!='D'";
                $result = $this->dbGetData($sql);
                if($result == null)
                {
                        throw new NoSuchObjectException("Object with id $id not found");
                }

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
		$id = mysql_real_escape_string($id, $this->dbConnection);

		//check if object exists in database
                $sql = "SELECT assetid from CmdbObject WHERE assetid=$id AND active!='D'";
                $result = $this->dbGetData($sql);
                if($result == null)
                {
                        throw new NoSuchObjectException("Object with id $id not found");
                }

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
	}
	
	public function getObjectsByType($type, $sortfield="", $sorttype = "asc", $activeOnly=true, $max=0, $start=0)
	{
		//escape strings
		$type = mysql_real_escape_string($type, $this->dbConnection);
		$sortfield = mysql_real_escape_string($sortfield, $this->dbConnection);
		$sorttype = mysql_real_escape_string($sorttype, $this->dbConnection);
		$activeOnly = mysql_real_escape_string($activeOnly, $this->dbConnection);
		$max = mysql_real_escape_string($max, $this->dbConnection);
		$start = mysql_real_escape_string($start, $this->dbConnection);

		//get all IDs
		$sql = "SELECT distinct CmdbObject.assetid FROM CmdbObject ";
		$sql.= "LEFT JOIN CmdbObjectField ON CmdbObject.assetid = CmdbObjectField.assetid ";
		$sql.= "WHERE CmdbObject.type='$type' ";
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
			$sql.= "AND CmdbObjectField.fieldkey = '$sortfield' ";
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
	* Search for objects. Get all objects with a specific field value
	* @param $searchstring	Searchstring
	* @param $types		Array with object types or null. Only show objects of a specific type
	* @returns 		Array with objects
	*/
	public function getObjectsByFieldvalue($searchstring, $types=null, $activeOnly=true, $max=0, $start=0)
	{
		//escape strings
		$searchstring = mysql_real_escape_string($searchstring, $this->dbConnection);
		$activeOnly = mysql_real_escape_string($activeOnly, $this->dbConnection);
		$max = mysql_real_escape_string($max, $this->dbConnection);
		$start = mysql_real_escape_string($start, $this->dbConnection);
		

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
				$sql.= "'".mysql_real_escape_string($types[$i], $this->dbConnection)."'";
				if($i < (count($types) - 1))
				{
					$sql.= ", ";
				}
			}
			$sql.= ") ";
		}
		$sql.= "AND fieldvalue like '%$searchstring%' ";
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
	* Get all links to objects of a specific object
	*
	*/
	public function getObjectLinks($id)
	{
		//escape strings
		$id = mysql_real_escape_string($id, $this->dbConnection);

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
		$id = mysql_real_escape_string($id, $this->dbConnection);

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
		$idA = mysql_real_escape_string($idA, $this->dbConnection);
		$idB = mysql_real_escape_string($idB, $this->dbConnection);

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
		$idA = mysql_real_escape_string($idA, $this->dbConnection);
		$idB = mysql_real_escape_string($idB, $this->dbConnection);

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
		$type = mysql_real_escape_string($type, $this->dbConnection);

		$sql = "SELECT count(*) FROM CmdbObject WHERE type='$type' AND active='A'";
		$result = $this->dbGetData($sql);
		$objectCount = $result[0][0];

		return $objectCount;
	}

	/**
	* Get all fieldvalues of a specific field of an object type
	* @param $objecttype	Type of object
	* @param $fieldname	Name of the field to get the values
	*/
	public function getAllValuesOfObjectField($objecttype, $fieldname)
	{
		//escape strings
		$objecttype = mysql_real_escape_string($objecttype, $this->dbConnection);
		$fieldname = mysql_real_escape_string($fieldname, $this->dbConnection);

		//create sql query
		$sql = "SELECT distinct fieldvalue FROM CmdbObjectField ";
		$sql.= "LEFT JOIN CmdbObject ON CmdbObjectField.assetid = CmdbObject.assetid ";
		$sql.= "WHERE CmdbObject.type = '$objecttype' ";
		$sql.= "AND CmdbObjectField.fieldkey = '$fieldname' ";
		$sql.= "AND CmdbObjectField.fieldvalue !='' ";
		$sql.= "ORDER BY fieldvalue ASC";
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
		$objectId = mysql_real_escape_string($objectId, $this->dbConnection);

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
		$n = mysql_real_escape_string($n, $this->dbConnection);

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
		$n = mysql_real_escape_string($n, $this->dbConnection);

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
}
?>
