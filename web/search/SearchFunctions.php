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

	//create SearchFilter from given URL parameters
	$paramFilter = getHttpGetVar("filter", Array());
	foreach($paramFilter as $filterEntry)
	{
		$searchFilter->addFilter($filterEntry);
	}

	//get objects
	$objects = $searchFilter->getObjects($authUser);

	//setup pagination
	$paramMax = getHttpGetVar("max", $config->getViewConfig()->getContentTableLength());
        $paramPage = getHttpGetVar("page", "1");

	//filter values
	//filter value text
	$filterValuesText = $searchFilter->getFilterValues("text");
	$filterValueText = "";
	if(isset($filterValuesText[0]))
	{
		$filterValueText = $filterValuesText[0];
	}
	$filterValueTextArray = array_filter(explode(" ", $filterValueText));
	//filter value status
	$filterValuesStatus = $searchFilter->getFilterValues("status");
	$filterValueStatus = "";
	if(isset($filterValuesStatus[0]))
	{
		$filterValueStatus = $filterValuesStatus[0];
	}
	//filter value object types
	$filterValuesPosObjTypes = $searchFilter->getFilterValues("type");
	$filterValuesNegObjTypes = $searchFilter->getFilterValues("notType");

	//define urls for UI
	$urlBase = "search/SearchResult.php?max=".urlencode($paramMax);
	$urlBaseFiltered = $urlBase . $searchFilter->getUrlQueryString();
	$listnavUrlBase = $urlBaseFiltered ."&amp;page=";

	//interprete searchstring if set
	/*$searchCondSearchstring = getHttpGetVar("searchstring", "");
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


	//search condition: active/inactive objects
        $paramActiveOnly = getHttpGetVar("activeonly", "1");
	$searchCondStatus = null;
	if($paramActiveOnly == "1")
	{
		$searchCondStatus = "A";
	}*/

?>
