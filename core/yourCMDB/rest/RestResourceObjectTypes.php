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
namespace yourCMDB\rest;

use yourCMDB\config\CmdbConfig;
use \Exception;

/**
* REST resource for a list of CMDB object types
*
* usage:
* /rest/objecttypes/groups/
* - GET 		/rest/objecttypes/groups/
* /rest/objecttypes/groups/<groupname>
* - GET		/rest/objecttypes/groups/<groupname>
*
* @author Michael Batz <michael@yourcmdb.org>
*/
class RestResourceObjectTypes extends RestResource
{

	public function getResource()
	{
		$config = new CmdbConfig();

		//try to get a list of objects
		try
		{
			$output = Array();
			$resource = $this->uri[1];
			if(count($this->uri) > 2)
			{
				$param1 = $this->uri[2];
			}
			else
			{
				$param1 = "";
			}
			switch($resource)
			{
				case "groups":
					$groups = $config->getObjectTypeConfig()->getObjectTypeGroups();
					if($param1 == "")
					{
						foreach(array_keys($groups) as $group)
						{
							$output[] = $group;
						}
					}
					else
					{
						if(!isset($groups[$param1]))
						{
							return new RestResponse(404);
						}
						foreach($groups[$param1] as $group)
						{
							$output[] = $group;
						}
					}
					break;

				default:
					return new RestResponse(400);
					break;
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
		return new RestResponse(405);
	}

	public function postResource($data)
	{
		return new RestResponse(405);
	}

	public function putResource($data)
	{
		return new RestResponse(405);
	}
}
?>
