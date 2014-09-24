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
* WebUI element: search
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get header
	include "include/header.inc.php";


	//get parameter
	$paramType = getHttpGetVar("type", "");
	$paramTypeGroup = getHttpGetVar("typegroup", "");
	$paramMax = getHttpGetVar("max", $config->getViewConfig()->getContentTableLength());
        $paramPage = getHttpGetVar("page", "1");
        $paramActiveOnly = getHttpGetVar("activeonly", "1");
        $paramSearchString = getHttpGetVar("searchstring", Array());

	//get data
	$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();

	//create searchstrings array with searchstrings[0] set and removed empty values
	$searchstrings = Array();
	$searchstrings[0] = "";
	for($i = 0; $i < count($paramSearchString); $i++)
	{
		if($i == 0)
		{
			$searchstrings[$i] = $paramSearchString[$i];
		}
		//remove empty searchstrings
		elseif($paramSearchString[$i] != "")
		{
			$searchstrings[] = $paramSearchString[$i];
		}
	}


	//get all searched objects
	$objects = null;
	if($searchstrings[0] != "")
	{
		if($paramTypeGroup != "")
		{
			$searchTypes = $objectTypes[$paramTypeGroup];
			$objects = $datastore->getObjectsByFieldvalue($searchstrings, $searchTypes, $paramActiveOnly);
		}
		else if($paramType != "")
		{
			$objects = $datastore->getObjectsByFieldvalue($searchstrings, array($paramType), $paramActiveOnly);
		}
		else
		{
			$objects = $datastore->getObjectsByFieldvalue($searchstrings, null, $paramActiveOnly);
		}

		if(count($objects) > 0)
		{
			//show search form
			include "search/SearchForm.php";
			//show search results
			include "search/SearchResult.php";
		}
		else
		{
			//show search form with error message
			$paramMessage = gettext("No objects for this search parameters found. Please try again...");
			//show search form
			include "search/SearchForm.php";
		}
	}
	else
	{
		//show search form
		include "search/SearchForm.php";
	}


	//include footer
	include "include/footer.inc.php";
?>
