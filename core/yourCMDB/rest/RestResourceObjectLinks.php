<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2016 Michael Batz
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
namespace yourCMDB\rest;

use yourCMDB\controller\ObjectController;
use yourCMDB\controller\ObjectLinkController;
use \Exception;

/**
* REST resource for CMDB objects links
*
* usage:
* /rest.php/objectlinks/
* - GET 	/rest.php/objectlinks/<assetidA>
* - DELETE	/rest.php/objectlinks/<assetidA>/<assetidB>
* - POST	/rest.php/objectlinks/<assetidA>
*
*
* @author Michael Batz <michael@yourcmdb.org>
*/
class RestResourceObjectLinks extends RestResource
{

	public function getResource()
	{
		$objectController = ObjectController::create();
		$objectLinkController = ObjectLinkController::create();

		//try to get a list of objects
		try
		{
			$objectId = $this->uri[1];
			$object = $objectController->getObject($objectId, $this->user);
			$objectLinks = $objectLinkController->getObjectLinks($object, $this->user);

			//generate output
			$output = Array();
			foreach($objectLinks as $objectLink)
			{
				$output[] = Array(
							'objectIdA' => $objectLink->getObjectA()->getId(),
							'objectIdB' => $objectLink->getObjectB()->getId()
						);
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
		$objectController = ObjectController::create();
		$objectLinkController = ObjectLinkController::create();

		try
		{
			$objectIdA = $this->uri[1];
			$objectIdB = $this->uri[2];
			$objectA = $objectController->getObject($objectIdA, $this->user);
			$objectB = $objectController->getObject($objectIdB, $this->user);
			$objectLinkController->deleteObjectLink($objectA, $objectB, $this->user);

		}
		catch(Exception $e)
		{
			return new RestResponse(404);
		}
		return new RestResponse(204);
	}

	public function postResource($data)
	{
		$objectController = ObjectController::create();
		$objectLinkController = ObjectLinkController::create();

		try
		{
			$decodedData = json_decode($data);
			$objectIdB = $decodedData->objectIdB;
			$objectIdA = $this->uri[1];
			$objectA = $objectController->getObject($objectIdA, $this->user);
			$objectB = $objectController->getObject($objectIdB, $this->user);
			$objectLinkController->addObjectLink($objectA, $objectB, $this->user);
		}
		catch(Exception $e)
		{
			return new RestResponse(400);
		}
		$url = "rest.php/objectlinks/$objectIdA";
		return new RestResponse(201, $url);
	}

	public function putResource($data)
	{
		return new RestResponse(405, "GET, DELETE, POST");
	}
}
?>
