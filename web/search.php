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
	$paramSearchString = getHttpGetVar("searchstring", "");
	$paramType = getHttpGetVar("type", "");
	$paramTypeGroup = getHttpGetVar("typegroup", "");
	$paramMax = getHttpGetVar("max", $config->getViewConfig()->getContentTableLength());
        $paramPage = getHttpGetVar("page", "1");
        $paramActiveOnly = getHttpGetVar("activeonly", "1");

	//get data from quicksearch syntax
	if(preg_match('/^search for (.*?) in (.*)$/', $paramSearchString, $matches) == 1 && $paramType == "" && $paramTypeGroup == "")
	{
		//regex match from searchstring
		$paramSearchString = $matches[1];
		$paramSearchType = $matches[2];
		//check search type
		if(preg_match('/^group (.*)$/', $paramSearchType, $matchesSearchType) == 1)
		{
			$paramTypeGroup = $matchesSearchType[1];
		}
	}


	//define action
	$action = "form";
	if($paramSearchString != "")
	{
		$action = "result";
	}

	switch($action)
	{
		case "form":
			//show search form
			include "search/SearchForm.php";
			break;

		case "result":
			//get all searched objects
			$objects = null;
			if($paramTypeGroup != "")
			{
				$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();
				$searchTypes = $objectTypes[$paramTypeGroup];
				$objects = $datastore->getObjectsByFieldvalue($paramSearchString, $searchTypes, $paramActiveOnly);
			}
			else if($paramType != "")
			{
				$objects = $datastore->getObjectsByFieldvalue($paramSearchString, array($paramType), $paramActiveOnly);
			}
			else
			{
				$objects = $datastore->getObjectsByFieldvalue($paramSearchString, null, $paramActiveOnly);
			}

			if(count($objects) > 0)
			{
				//show search results
				include "search/SearchResult.php";
			}
			else
			{
				//show search form with error message
				$paramMessage = sprintf(gettext("No objects with field value %s found. Please try again..."), $paramSearchString);
				include "search/SearchForm.php";
			}
			break;

	}

//include footer
include "include/footer.inc.php";
?>
