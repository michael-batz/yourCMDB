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

/**
* a REST response
* @author Michael Batz <michael@yourcmdb.org>
*/
class RestResponse
{

	//HTTP response code
	private $httpResponseCode;

	//data for HTTP
	private $data;

	//creates a new REST response and send it to the client
	public function __construct($httpResponseCode, $data=null)
	{
		$this->httpResponseCode = $httpResponseCode;
		$this->data = $data;
	}

	public function sendResponse()
	{
		switch($this->httpResponseCode)
		{
			case 200:
				$this->sendHttp200();
				break;

			case 201:
				$this->sendHttp201();
				break;

			case 204:
				$this->sendHttp204();
				break;
		
			case 400:
				$this->sendHttp400();
				break;

			case 401:
				$this->sendHttp401();
				break;

			case 403:
				$this->sendHttp403();
				break;

			case 404:
				$this->sendHttp404();
				break;

			case 405:
				$this->sendHttp405();
				break;

			default:
				$this->sendHttp500();
				break;
		}
	}

	private function sendHttp200()
	{
		header("HTTP/1.1 200 OK");
		header("Content-Type: application/json");
		echo $this->data;
	}

	private function sendHttp201()
	{
		header("HTTP/1.1 201 Created");
		if($this->data != null)
		{
			header("Content-Type: application/json");
			header("Location: $this->data");
		}
	}

	private function sendHttp204()
	{
		header("HTTP/1.1 204 No Content");
	}

	private function sendHttp400()
	{
		header("HTTP/1.1 400 Bad Request");
	}

	private function sendHttp401()
	{
		header("HTTP/1.1 401 Unauthorized");
		header("WWW-Authenticate: Basic realm=\"yourCMDB REST API\"");
	}

	private function sendHttp403()
	{
		header("HTTP/1.1 403 Forbidden");
	}

	private function sendHttp404()
	{
		header("HTTP/1.1 404 Not Found");
	}

	private function sendHttp405()
	{
		header("HTTP/1.1 405 Method not Allowed");
		if($this->data != null)
		{
			header("Allow: $this->data");
		}
		else
		{
			header("Allow: GET");
		}
	}

	private function sendHttp500()
	{
		header("HTTP/1.1 500 Internal Server Error");
	}

}
?>
