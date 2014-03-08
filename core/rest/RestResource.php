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
* a REST resource
* @author Michael Batz <michael@yourcmdb.org>
*/
class RestResource
{

	//uri of REST resource
	protected $uri;

	protected $datastore;

	protected $config;

	//creates a new REST resource
	public function __construct($uri)
	{
		$this->uri = $uri;
		$controller = new Controller();
		$this->datastore = $controller->getDatastore();
		$this->config = $controller->getCmdbConfig();
	}

	public function getResource()
	{
		return new RestResponse(400);
	}

	public function deleteResource()
	{
		return new RestResponse(400);
	}

	public function postResource($data)
	{
		return new RestResponse(400);
	}

	public function putResource($data)
	{
		return new RestResponse(400);
	}
}
?>
