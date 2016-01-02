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

/**
* WebUI element: show label of a CmdbObject
* loaded directly
* @author Michael Batz <michael@yourcmdb.org>
*/

	//include base functions and search form
	include "../include/bootstrap-web.php";
	include "../include/auth.inc.php";

	use yourCMDB\labelprinter\LabelPrinter;

	//get parameters
	$paramId = getHttpGetVar("id", 0);

	//try to load object and print label
	try
	{
		$object= $objectController->getObject($paramId, $authUser);
		$labelPrinter = new LabelPrinter($object);

		//ToDo: set correct header options
		header("content-type: application/pdf");
		echo $labelPrinter->getLabel();
	}
	catch(Exception $e)
	{
		//ToDo: error handling
	}
?>
