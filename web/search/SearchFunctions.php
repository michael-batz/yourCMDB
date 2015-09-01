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

	//get data for functions
	$objectTypes = $config->getObjectTypeConfig()->getAllTypes();

	//interprete searchstring if set
	$searchCondSearchstring = getHttpGetVar("searchstring", "");
	if($searchCondSearchstring != "")
	{
		//parse values in searchstring
		$searchCondSearchstringArray = array_filter(explode(" ", $searchCondSearchstring));

		//find objecttypes in searchstring
		$matchesObjectTypes = Array();
		$matchesSearchstrings = Array();
		for($i = 0; $i < count($searchCondSearchstringArray); $i++)
		{
			$countMatches = count($matchesObjectTypes);
			foreach($objectTypes as $objectType)
			{
				if(stripos($objectType, $searchCondSearchstringArray[$i]) !== FALSE)
				{
					$matchesObjectTypes[] = $objectType;
				}
			}
			if(count($matchesObjectTypes) == $countMatches)
			{
				$matchesSearchstrings[] = $searchCondSearchstringArray[$i];
			}
		}
		if(count($matchesSearchstrings > 0))
		{
			$paramTypes = $matchesObjectTypes;
			$searchCondText = "";
			$searchstringOutput = $matchesSearchstrings;
		}
		//default
		else
		{
			$searchstringOutput = $searchCondSearchstringArray;

		}

		//create searchCondText
		foreach($searchstringOutput as  $searchstringOutputElement)
		{
			$searchCondText .= $searchstringOutputElement . " ";
		}
	
	}

	//get search parameters
	//search condition: object types
	if(!isset($paramTypes))
	{
		$paramTypes = getHttpGetVar("type", Array());
	}
	if(!isset($paramNotTypes))
	{
		$paramNotTypes = getHttpGetVar("notType", Array());
	}
	if(count($paramTypes) == 0 && count($paramNotTypes) > 0)
	{
		$searchCondTypes = array_diff($objectTypes, $paramNotTypes);

	}
	elseif(count($paramTypes) == 0 && count($paramNotTypes) == 0)
	{
		$searchCondTypes = null;
	}
	else
	{
		$searchCondTypes = array_diff($paramTypes, $paramNotTypes);
	}

	//search condition: pagination
	$paramMax = getHttpGetVar("max", $config->getViewConfig()->getContentTableLength());
        $paramPage = getHttpGetVar("page", "1");

	//search condition: active/inactive objects
        $paramActiveOnly = getHttpGetVar("activeonly", "1");
	$searchCondStatus = null;
	if($paramActiveOnly == "1")
	{
		$searchCondStatus = "A";
	}


	//search condition: get search text from parameter searchtext
	if(!isset($searchCondText))
	{
		$searchCondText = getHttpGetVar("searchtext", "");
	}
	$searchCondTextArray = Array();
	if($searchCondText != "")
	{
		$searchCondTextArray = array_filter(explode(" ", $searchCondText));
	}

	//define urls for UI
	$urlBase = "search/SearchResult.php?max=".urlencode($paramMax)."&amp;activeonly=".urlencode($paramActiveOnly);
	$urlBase .= "&amp;searchtext=".urlencode($searchCondText);
	$listnavUrlBase = $urlBase;
	foreach($paramTypes as $paramType)
	{
		$listnavUrlBase .= "&amp;type[]=" .urlencode($paramType);
	}
	foreach($paramNotTypes as $paramNotType)
	{
		$listnavUrlBase .= "&amp;notType[]=" .urlencode($paramNotType);
	}
	$listnavUrlBase .= "&amp;page=";

?>
