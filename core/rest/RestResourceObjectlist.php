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
* REST resource for a list of CMDB objects
*
* usage:
* /rest/objectlist/by-fieldvalue/<value>
* - GET 		/rest/objectlist/by-fieldvalue/<value>
* /rest/objectlist/by-objecttype/<type>
* - GET		/rest/objectlist/by-objecttype/<type>
*
* @author Michael Batz <michael@yourcmdb.org>
*/
class RestResourceObjectlist extends RestResource
{

	public function getResource()
	{
		//try to get a list of objects
		try
		{
			$listtype = $this->uri[1];
			$searchvalue = $this->uri[2];
			switch($listtype)
			{
				case "by-fieldvalue":
					$objects = $this->datastore->getObjectsByFieldvalue(array($searchvalue), null, false, 0, 0); 
					break;

				case "by-objecttype":
					$objects = $this->datastore->getObjectsByType($searchvalue, "", "asc", false, 0, 0);
					break;

				default:
					return new RestResponse(400);
					break;
			}

			//generate output
			$output = Array();
			foreach($objects as $object)
			{
				$output[] = $object->getId();
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
