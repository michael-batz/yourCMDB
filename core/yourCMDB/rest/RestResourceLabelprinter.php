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
use yourCMDB\controller\ObjectController;
use yourCMDB\labelprinter\LabelPrinter;
use \Exception;

/**
* REST resource for accessing the LabelPrinterAPI
*
* usage:
* /rest.php/labelprinter/list/
* - GET 	/rest.php/labelprinter/list/print
* /rest.php/labelprinter/print/<labelprinter>/<assetId>
* - PUT		/rest.php/labelprinter/print/<labelprinter>/<assetId>
*
* @author Michael Batz <michael@yourcmdb.org>
*/
class RestResourceLabelprinter extends RestResource
{

	public function getResource()
	{
		//check given resource
		$resource = $this->uri[1];
		switch($resource)
		{
			//resource is list
			case "list":
				$labelprinterType = $this->uri[2];
				switch($labelprinterType)
				{
					case "print":
						$config = CmdbConfig::create();
						$output = $config->getLabelprinterConfig()->getLabelprinterNamesForPrinting();
						return new RestResponse(200, json_encode($output));
					default:
						return new RestResponse(405);
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
			case "print":
				try
				{
					//get parameters
					$labelprinter = $this->uri[2];
					$objectId = $this->uri[3];

					//print label
					$objectController = ObjectController::create();
					$object = $objectController->getObject($objectId, $this->user);
					$labelPrinter = new LabelPrinter($object, $labelprinter);
					$labelPrinter->printLabel();

					//return result
					$output = Array('status' => 'OK');
					return new RestResponse(200, json_encode($output));
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
