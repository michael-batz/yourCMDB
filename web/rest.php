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
* REST request dispatcher
* @author: Michael Batz <michael@yourcmdb.org>
*/

	//get WebUI base
	include "include/base.inc.php";

	//check authentication (HTTP Basic AUTH)
	$authProvider = $controller->getAuthProvider("rest");
	$authUser = "";
	$authAccessgroup = "";
	$authAuthenticated = false;
	if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
	{
		$authUser = $_SERVER['PHP_AUTH_USER'];
		$authPassword = $_SERVER['PHP_AUTH_PW'];

		if($authProvider->authenticate($authUser,$authPassword))
		{
			$authAccessgroup = $authProvider->getAccessGroup($authUser);
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

	//execute operation on RestResource
	switch($requestMethod)
	{
		case "GET":
			$restResponse = $restResource->getResource();
			break;

		case "DELETE":
			$restResponse = $restResource->deleteResource();
			break;

		case "POST":
			$restResponse = $restResource->postResource($requestData);
			break;

		case "PUT":
			$restResponse = $restResource->putResource($requestData);
			break;

		default:
			$restResponse = $restResource->getResource();
			break;
	}
	$restResponse->sendResponse();
	
?>

