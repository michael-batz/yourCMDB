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
* WebUI element: object actions
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get header
	include "include/header.inc.php";

	//get parameters
	$paramId = getHttpGetVar("id", 0);
	$paramIdB = getHttpGetVar("idb", 0);
	$paramAction = getHttpGetVar("action", "show");
	$paramType = getHttpGetVar("type", "");
	$paramMessage = "";
	$paramMax = getHttpGetVar("max", $config->getViewConfig()->getContentTableLength());
	$paramPage = getHttpGetVar("page", "1");
	$paramSort = getHttpGetVar("sort", "");
	$paramSortType = getHttpGetVar("sorttype", "asc");
	$paramActiveOnly = getHttpGetVar("activeonly", "1");
	if($paramSortType != "asc")
	{
		$paramSortType = "desc";
	}


	switch($paramAction)
	{
		case "list":
			include "object/ListObjects.php";
			break;

		case "show":
			//get object and object links
			try
			{
				$object= $datastore->getObject($paramId);
			}
			catch(NoSuchObjectException $e)
			{
				//show error message and search form
				$paramError = "No object with AssetID $paramId found...";
				include "search/SearchForm.php";
				break;
			}
			//show object page
			include "object/ShowObject.php";
			break;

		case "new":
			include "object/NewObject.php";
			break;

		case "add":
			include "object/EditObject.php";
			break;

		case "edit":
			include "object/EditObject.php";
			break;

		case "saveNew":
			//check, if HTTP POST variables are set
			if(count($_POST) <= 0)
			{
				$paramError = "No data were set when saving an object.";
				include "error/Error.php";
				break;
			}	

			//create data for new object
			$fields = $config->getObjectTypeConfig()->getFields($paramType);
			$status = getHttpPostVar("yourCMDB_active", 'N');
			$objectFields = Array();
			foreach(array_keys($fields) as $field)
			{
                        	$objectFields[$field] = getHttpPostVar($field, "");
			}
			//create new object and return assetId
			try
			{
				$paramId = $datastore->addObject(new CmdbObject($paramType, $objectFields, 0, $status));
				$object = $datastore->getObject($paramId);
			}
			catch(NoSuchObjectException $e)
			{
				$paramError = "Error saving new object";
				include "error/Error.php";
				break;
			}
			//show new object
			$paramMessage = "New Object successfully created";
			include "object/ShowObject.php";
			break;

		case "save":
			//create data for object
			$status = getHttpPostVar("yourCMDB_active", 'N');
			$fields = $config->getObjectTypeConfig()->getFields($paramType);
			$objectFields = Array();
			foreach(array_keys($fields) as $field)
			{
                        	$objectFields[$field] = getHttpPostVar($field, "");
			}
			//change object and return the ShowObject page
			try
			{
				$object = $datastore->getObject($paramId);
				//check, if HTTP POST variables are set
				if(count($_POST) <= 0)
				{
					$paramError = "No data were set when saving an object.";
					include "object/ShowObject.php";
					break;
				}	
				$datastore->changeObjectStatus($paramId, $status);
				$datastore->changeObjectFields($paramId, $objectFields);
				$object = $datastore->getObject($paramId);
			}
			catch(NoSuchObjectException $e)
			{
				$paramError = "Error saving object";
				include "error/Error.php";
				break;
			}
			//show changed object
			$paramMessage = "Object successfully changed";
			include "object/ShowObject.php";
			break;

		case "delete":
			//delete object
			try
			{
				$paramType = $datastore->getObject($paramId)->getType();
				$datastore->deleteObject($paramId);
			}
			catch(NoSuchObjectException $e)
			{
				$paramError = "Error deleting object: Object not found";
				include "error/Error.php";
				break;
			}
			//show object list with message
			$paramMessage = "Object deleted";
			include "object/ListObjects.php";
			break;

		case "addLink":
			//get first object
			try
			{
				$object = $datastore->getObject($paramId);
			}
			catch(NoSuchObjectException $e)
			{
				$paramError = "Object for adding links not found.";
				include "error/Error.php";
				break;
			}

			//tryp to add a link
			try
			{
				$result = $datastore->addObjectLink($paramId, $paramIdB);
				$paramMessage = "Object link successfully added";
			}
			catch(NoSuchObjectException $e)
			{
				$paramError = "Error adding object link: Object B not found";
			}
			catch(ObjectActionNotAllowed $e)
			{
				$paramError = "Link object $paramId with object $paramIdB is not allowed.";
				if($paramId != $paramIdB)
				{
					$paramError.= " The object link already exists.";
				}
			}
			//open object page
			include "object/ShowObject.php";
			break;

		case "deleteLink":
			try
			{
				//delete link
				$object = $datastore->getObject($paramId);
				$result = $datastore->deleteObjectLink($paramId, $paramIdB);
				$paramMessage = "Object link was successfully deleted";
			}
			catch(NoSuchObjectException $e)
			{
				$paramError = "Error deleting object link: object not found";
				include "error/Error.php";
				break;
			}

			//open object page
			include "object/ShowObject.php";
			break;
	}

	//include footer
	include "include/footer.inc.php";
?>
