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
* WebUI element: object actions
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get header
	include "include/bootstrap-web.php";
	include "include/auth.inc.php";
	include "include/htmlheader.inc.php";
	include "include/cmdbheader.inc.php";

	//class loading
	use yourCMDB\exceptions\CmdbObjectNotFoundException;

	//get UI Helper
	include "object/ObjectUiHelper.php";

	//get parameters
	$paramId = getHttpGetVar("id", 0);
	$paramIdB = getHttpGetVar("idb", 0);
	$paramAction = getHttpGetVar("action", "show");
	$paramEvent = getHttpGetVar("event", "");
	$paramType = getHttpGetVar("type", "");
	$paramMessage = "";
	$paramMax = getHttpGetVar("max", $config->getViewConfig()->getContentTableLength());
	$paramPage = getHttpGetVar("page", "1");
	$paramSort = getHttpGetVar("sort", "");
	$paramSortType = getHttpGetVar("sorttype", "ASC");
	$paramStatus = getHttpGetVar("status", "A");
	if($paramStatus != "A")
	{
		$paramStatus = "0";
	}
	if($paramSortType != "ASC")
	{
		$paramSortType = "DESC";
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
				$object= $objectController->getObject($paramId, $authUser);
			}
			catch(CmdbObjectNotFoundException $e)
			{
				//ToDo:show error message and search form
				$paramError = sprintf(gettext("No object with AssetID %s found..."), $paramId);
				//include "include/messagebar.inc.php";
				//search functions
				//include "search/SearchFunctions.php";
				//show search form
				//include "search/SearchForm.php";
				//load search result using AJAX
				//include "search/SearchResultFrame.php";
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
				$paramError = gettext("No data were set when saving an object.");
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
				$paramError = gettext("Error saving new object");
				include "error/Error.php";
				break;
			}
			//show new object
			$paramMessage = gettext("New Object successfully created");
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
					$paramError = gettext("No data were set when saving an object.");
					include "object/ShowObject.php";
					break;
				}	
				$datastore->changeObjectStatus($paramId, $status);
				$datastore->changeObjectFields($paramId, $objectFields);
				$object = $datastore->getObject($paramId);
			}
			catch(NoSuchObjectException $e)
			{
				$paramError = gettext("Error saving object");
				include "error/Error.php";
				break;
			}
			//show changed object
			$paramMessage = gettext("Object successfully changed");
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
				$paramError = gettext("Error deleting object: Object not found");
				include "error/Error.php";
				break;
			}
			//show object list with message
			$paramMessage = gettext("Object deleted");
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
				$paramError = gettext("Object for adding links not found.");
				include "error/Error.php";
				break;
			}

			//tryp to add a link
			try
			{
				$result = $datastore->addObjectLink($paramId, $paramIdB);
				$paramMessage = gettext("Object link successfully added");
			}
			catch(NoSuchObjectException $e)
			{
				$paramError = gettext("Error adding object link: Object B not found");
			}
			catch(ObjectActionNotAllowed $e)
			{
				$paramError = sprintf(gettext("Link object %s with object %s is not allowed."), $paramId, $paramIdB);
				if($paramId != $paramIdB)
				{
					$paramError.= gettext(" The object link already exists.");
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
				$paramMessage = gettext("Object link was successfully deleted");
			}
			catch(NoSuchObjectException $e)
			{
				$paramError = gettext("Error deleting object link: object not found");
				include "error/Error.php";
				break;
			}

			//open object page
			include "object/ShowObject.php";
			break;

		case "sendEvent":
			try
			{
				$object = $datastore->getObject($paramId);
				$controller->getEventProcessor()->generateEvent($paramEvent, $object->getId(), $object->getType());
				$paramMessage = sprintf(gettext("Event %s was successfully sent"), $paramEvent);
			}
			catch(NoSuchObjectException $e)
			{
				$paramError = gettext("Error sending event: object not found");
				include "error/Error.php";
				break;
			}

			//open object page
			include "object/ShowObject.php";
			break;
	}

	//include footer
	include "include/cmdbfooter.inc.php";
	include "include/htmlfooter.inc.php";
?>
