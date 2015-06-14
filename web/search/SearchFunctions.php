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
* WebUI element: search functions
* @author Michael Batz <michael@yourcmdb.org>
*/

	//get parameter
	$paramString = $_SERVER['QUERY_STRING'];
	$paramType = getHttpGetVar("type", "");
	$paramTypeGroup = getHttpGetVar("typegroup", "");
	$paramMax = getHttpGetVar("max", $config->getViewConfig()->getContentTableLength());
        $paramPage = getHttpGetVar("page", "1");
        $paramActiveOnly = getHttpGetVar("activeonly", "1");
	$paramStatus = null;
	if($paramActiveOnly == "1")
	{
		$paramStatus = "A";
	}
        $paramSearchString = getHttpGetVar("searchstring", "");

	//get data
	$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();

	//parse searchstring. create array with single words
	$searchstrings = Array();
	if($paramSearchString != "")
	{
		$searchstrings = array_filter(explode(" ", $paramSearchString));
	}
?>
