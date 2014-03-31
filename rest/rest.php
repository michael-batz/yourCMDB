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


	/**
	* autoloading of classes
	*/
	function __autoload($className)
	{
		$scriptBaseDir = dirname(__FILE__);
                $coreBaseDir = realpath("$scriptBaseDir/../core");
		$paths = array('', 'model', 'config', 'controller', 'libs', 'rest', 'exporter');
		$filename = $className.'.php';
		foreach($paths as $path)
		{
			if(file_exists("$coreBaseDir/$path/$filename"))
			{
				include "$coreBaseDir/$path/$filename";
			}
		}
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
		header("HTTP/1.0 404 Not Found");;
		exit(-1);
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

