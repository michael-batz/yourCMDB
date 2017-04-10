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

use yourCMDB\config\CmdbConfig;
use yourCMDB\exporter\Exporter;
use \Exception;

/**
* REST resource for accessing the ExportAPI
*
* usage:
* /rest.php/exporter/list/
* - GET 	/rest.php/exporter/list/
* /rest.php/exporter/export/<task>
* - PUT		/rest.php/exporter/export/<task>
* - GET		/rest.php/exporter/export/<task>
*
* @author Michael Batz <michael@yourcmdb.org>
*/
class RestResourceExporter extends RestResource
{

	public function getResource()
	{
		//check given resource
		$resource = $this->uri[1];
		switch($resource)
		{
			//resource is list
			case "list":
				$config = CmdbConfig::create();
				$output = $config->getExporterConfig()->getTasks();
				return new RestResponse(200, json_encode($output));
				break;
				
			//resource is export
			case "export":
				try
				{
					//get exporter task
					$task = $this->uri[2];

					//turn on output buffering to get exporters STDOUT
					ob_start();

					//run export task					
					$exporter = new Exporter($task);

					//get output from buffer
					$output = ob_get_contents();

					//end and clear output buffer
					ob_end_clean();

					//return result
					return new RestResponse(200, $output);
				}
				catch(Exception $e)
				{
					return new RestResponse(400);
				}
				break;
				
			//other values: return 405
			default:
				return new RestResponse(405);
				break;
		}
	}

	public function deleteResource()
	{
		return new RestResponse(405);
	}

	public function postResource($data)
	{
		return new RestResponse(405);
	}

	public function putResource($data)
	{
		//check given resource
		$resource = $this->uri[1];
		switch($resource)
		{
			//resource is export
			case "export":
				try
				{
					//get exporter task
					$task = $this->uri[2];

					//turn on output buffering to get exporters STDOUT
					ob_start();

					//run export task					
					$exporter = new Exporter($task);

					//get output from buffer
					$output = ob_get_contents();

					//end and clear output buffer
					ob_end_clean();

					//return result
					return new RestResponse(200, $output);
				}
				catch(Exception $e)
				{
					return new RestResponse(400);
				}
				break;

			//other values: return 405
			default:
				return new RestResponse(405);
				break;
		}

	}
}
?>
