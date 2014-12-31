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
* REST resource for a CMDB object
*
* usage:
* /rest.php/objects/<assetId>
* - GET 	/rest.php/objects/<assetId>
* - DELETE	/rest.php/objects/<assetId>
* - PUT		/rest.php/objects/<assetId>
* - POST	/rest.php/objects
*
* @author Michael Batz <michael@yourcmdb.org>
*/
class RestResourceObject extends RestResource
{

	public function getResource()
	{
		//try to get object and generate output
		try
		{
			$objectId = $this->uri[1];
			$object = $this->datastore->getObject($objectId);

			//generate json output
			$output = Array();
			$output['objectType'] = $object->getType();
			$output['objectId'] = $object->getId();
			$output['status'] = $object->getStatus();
			$output['objectFields'] = Array();
			foreach($this->config->getObjectTypeConfig()->getFieldGroups($object->getType()) as $groupname)
			{
				$output['objectFields']["$groupname"] = Array();
				foreach(array_keys($this->config->getObjectTypeConfig()->getFieldGroupFields($object->getType(), $groupname)) as $field)
				{
					$output['objectFields']["$groupname"][] = Array(
											"name" => $field,
											"label"=> $this->config->getObjectTypeConfig()->getFieldLabel($object->getType(), $field),
											"type" => $this->config->getObjectTypeConfig()->getFieldType($object->getType(), $field),
											"value"=> $object->getFieldValue($field)
											);
				}
			}

		}
		catch(Exception $e)
		{
			return new RestResponse(404);
		}
		return new RestResponse(200, json_encode($output));
	}

	public function deleteResource()
	{
		try
		{
			$objectId = $this->uri[1];
			$this->datastore->deleteObject($objectId);
		}
		catch(Exception $e)
		{
			return new RestResponse(404);
		}
		return new RestResponse(204);
	}

	public function postResource($data)
	{
		try
		{
			//decode json data
			$decodedData = json_decode($data);
			if( (!isset($decodedData->objectType)) || (!isset($decodedData->objectFields)) || (!isset($decodedData->status)))
			{
				return new RestResponse(400);
			}

			$objectType = $decodedData->objectType;
			$objectStatus = $decodedData->status;
			$objectFields = Array();
			foreach($decodedData->objectFields as $group)
			{
				foreach($group as $field)
				{
					$fieldname = $field->name;
					$fieldvalue = $field->value;
					$objectFields[$fieldname] = $fieldvalue;
				}
			}
			
			//generate object
			$object = new CmdbObject($objectType, $objectFields, 0, $objectStatus);
			$objectId = $this->datastore->addObject($object);
			$url = "rest.php/objects/$objectId";
		}
		catch(Exception $e)
		{
			return new RestResponse(400);
		}
		return new RestResponse(201, $url);
	}

	public function putResource($data)
	{
		try
		{
			//decode json data
			$decodedData = json_decode($data);
			if( (!isset($decodedData->objectType)) || (!isset($decodedData->objectFields)) || (!isset($decodedData->status)))
			{
				return new RestResponse(400);
			}

			$objectType = $decodedData->objectType;
			$objectId = $decodedData->objectId;
			$objectStatus = $decodedData->status;
			$objectFields = Array();
			foreach($decodedData->objectFields as $group)
			{
				foreach($group as $field)
				{
					$fieldname = $field->name;
					$fieldvalue = $field->value;
					$objectFields[$fieldname] = $fieldvalue;
				}
			}

			//change object fields and status
			$this->datastore->changeObjectFields($objectId, $objectFields);
			$this->datastore->changeObjectStatus($objectId, $objectStatus);
			$url = "rest.php/objects/$objectId";
		}
		catch(Exception $e)
		{
			return new RestResponse(404);
		}
		return new RestResponse(201, $url);
	}
}
?>
