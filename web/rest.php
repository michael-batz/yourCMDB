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
* REST request dispatcher
* @author: Michael Batz <michael@yourcmdb.org>
*/

	//get WebUI base
	include "include/base.inc.php";

	//check authentication (HTTP Basic AUTH)
	$authProvider = $controller->getAuthProvider("rest");
	$authorisationProvider = $controller->getAuthorisationProvider("rest");
	$authUser = "";
	$authAccessgroup = "";
	$authAuthenticated = false;
	$accessRest = 0;
	if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
	{
		$authUser = $_SERVER['PHP_AUTH_USER'];
		$authPassword = $_SERVER['PHP_AUTH_PW'];

		if($authProvider->authenticate($authUser,$authPassword))
		{
			$authAccessgroup = $authProvider->getAccessGroup($authUser);
			$accessRest = $authorisationProvider->authorise($authAccessgroup, "rest");
			$authAuthenticated = true;
		}
	}
	if(!$authAuthenticated)
	{
		$response = new RestResponse(401);
		$response->sendResponse();
		exit();
	}


	//get request details
	//parse PATH_INFO string to array and ignore leading slash
	$requestPath = explode("/", ltrim($_SERVER['PATH_INFO'], "/"));
	$requestMethod = $_SERVER['REQUEST_METHOD'];
	$requestDataStream = fopen("php://input", "r");
	$requestData = stream_get_contents($requestDataStream);
	fclose($requestDataStream);

	//get restResource
	$restResource = null;
	switch($requestPath[0])
	{
		case "objects":
			$restResource = new RestResourceObject($requestPath);
			break;

		case "objectlogs":
			$restResource = new RestResourceObjectLog($requestPath);
			break;

		case "objectlinks":
			$restResource = new RestResourceObjectLinks($requestPath);
			break;

		case "objectlist":
			$restResource = new RestResourceObjectlist($requestPath);
			break;

		case "objecttypes":
			$restResource = new RestResourceObjectTypes($requestPath);
			break;
	}

	if($restResource == null)
	{
		//error and exit
		$response = new RestResponse(404);
		$response->sendResponse();
		exit();
	}

	//execute operation on RestResource if access rights are OK
	$restResponse = new RestResponse(403);
	switch($requestMethod)
	{
		case "GET":
			if($accessRest > 0)
			{
				$restResponse = $restResource->getResource();
			}
			break;

		case "DELETE":
			if($accessRest == 2)
			{
				$restResponse = $restResource->deleteResource();
			}
			break;

		case "POST":
			if($accessRest == 2)
			{
				$restResponse = $restResource->postResource($requestData);
			}
			break;

		case "PUT":
			if($accessRest == 2)
			{
				$restResponse = $restResource->putResource($requestData);
			}
			break;
	}
	$restResponse->sendResponse();
	
?>

