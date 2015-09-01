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
* WebUI element: search results
* loaded using AJAX call
* @author Michael Batz <michael@yourcmdb.org>
*/

	//include base functions and search form
	include "../include/bootstrap-web.php";
	include "../include/auth.inc.php";
	include "SearchFunctions.php";
	include "SearchForm.php";

	//get all searched objects
	$objects = Array();
	if(count($searchCondTextArray) > 0)
	{
		$objects = $objectController->getObjectsByFieldvalue($searchCondTextArray, $searchCondTypes, $searchCondStatus, 0, 0, $authUser);
	}

	//calculate list view
        $objectCount = count($objects);
        $listPage = $paramPage;
        $listPages = floor((($objectCount - 1) / $paramMax) + 1);
        if($listPages < 1)
        {
                $listPages = 1;
        }
        //check, if $listPage makes sense
        if($listPage > $listPages)
        {
                $listPage = $listPages;
        }
        if($listPage < 1)
        {
                $listPage = 1;
        }
        //calculate start and end
        $listStart = ($listPage - 1) * $paramMax;
        $listEnd = $listStart + $paramMax -1;
        if($listEnd >= $objectCount)
        {
                $listEnd = $objectCount - 1;
        }




	//<!-- title -->
	echo "<h2>";
	echo sprintf(gettext("Search Results (%s)"), $objectCount);
	echo "</h2>";
	

	echo "<table class=\"table cmdb-cleantable\">";
	//print found objects
	if(count($objects) > 0)
	{
		//print object summary
		for($i = $listStart; $i <= $listEnd; $i++)
		{
			//get all data
			$objectType = $objects[$i]->getType();
			$objectId = $objects[$i]->getId();
			$objectStatus = $objects[$i]->getStatus();
			$objectFields = $objects[$i]->getFields();
			$objectSummaryFields = $config->getObjectTypeConfig()->getSummaryFields($objectType);
			//get fields that matched to search string
			$objectMatchFields = Array();
			foreach($objectFields->getKeys() as $fieldname)
			{
				foreach($searchCondTextArray as $searchstring)
				{
					if(stristr($objects[$i]->getFieldValue($fieldname), $searchstring) !== FALSE)
					{
						$objectMatchFields[] = $fieldname;
						break;
					}
				}
			}
		
			//get status image
			$statusIcon = "<span class=\"label label-success\" title=\"".gettext("active object")."\">A</span>";
			if($objectStatus != 'A')
			{
				$statusIcon = "<span class=\"label label-danger\" title=\"".gettext("inactive object")."\">N</span>";
			}

			//print headline
			echo "<tr><td>";
			echo "<p><a href=\"object.php?action=show&amp;id=$objectId\">";
			echo "$statusIcon $objectType: $objectId</a><br />";

			//print matches
			echo gettext("Matches: ");
			for($j = 0; $j < count($objectMatchFields); $j++)
			{
				$fieldname = $objectMatchFields[$j];
				$fieldlabel = $config->getObjectTypeConfig()->getFieldLabel($objectType, $fieldname);
				$fieldvalue = $objects[$i]->getFieldValue($fieldname);
				//mark search string in fieldvalues (use case insensitive match)
				foreach($searchCondTextArray as $searchstring)
				{
					if(preg_match("/.*?((?i:$searchstring)).*?/", $fieldvalue, $matchSearchString) == 1)
					{
						$fieldvalue = str_replace($matchSearchString[1], "<mark>$matchSearchString[1]</mark>", $fieldvalue);
					}
				}
				echo "$fieldlabel: $fieldvalue";
				if($j < count($objectMatchFields) - 1)
				{
					echo " | ";
				}
			}
			echo "<br />";

			//print object summary
			echo gettext("Summary: ");
			$fieldnames = array_keys($objectSummaryFields);
			for($j = 0; $j < count($fieldnames); $j++)
			{
				$fieldname = $fieldnames[$j];
				$fieldlabel = $config->getObjectTypeConfig()->getFieldLabel($objectType, $fieldname);
				$fieldvalue = $objects[$i]->getFieldValue($fieldname);
				echo "$fieldlabel: $fieldvalue";
				if($j < count($fieldnames) - 1)
				{
					echo " | ";
				}
			}
			echo "</p></td></tr>";
		}
		echo "</table>";

		//<!-- list navigation  -->
		echo "<nav>";
		echo "<ul class=\"pagination cmdb-pagination\">";
		//print prev button
		if($listPage != 1)
		{
			$listnavUrl = $listnavUrlBase .($listPage - 1);
			echo "<li><a href=\"javascript:cmdbOpenUrlAjax('$listnavUrl', '#searchbarResult', true, true)\">&lt; ";
			echo gettext("previous");
			echo "</a></li>";
		}
		else
		{
			echo "<li class=\"disabled\"><a href=\"#\">&lt; ";
			echo gettext("previous");
			echo "</a></li>";
		}
		//print page numbers
		for($i = 1; $i <= $listPages; $i++)
		{
			$listnavUrl = $listnavUrlBase .$i;
			if($i == $listPage)
			{
				echo "<li class=\"active\"><a href=\"javascript:cmdbOpenUrlAjax('$listnavUrl', '#searchbarResult', true, true)\">$i</a></li>";
			}
			else
			{
				echo "<li><a href=\"javascript:cmdbOpenUrlAjax('$listnavUrl', '#searchbarResult', true, true)\">$i</a></li>";
			}

			//jump to current page
			if($i == 3 && $listPage > 5)
			{
				$i = $listPage - 2;
				echo "<li class=\"disabled\"><a href=\"#\">...</a></li>";
			}
			//jump to last page
			if($i > 3 && $i > $listPage && $i < ($listPages - 2))
			{
				$i = $listPages - 2;
				echo "<li class=\"disabled\"><a href=\"#\">...</a></li>";
			}
		}
		//print next button
		if($listPage != $listPages)
		{
			$listnavUrl = $listnavUrlBase .($listPage + 1);
			echo "<li><a href=\"javascript:cmdbOpenUrlAjax('$listnavUrl', '#searchbarResult', true, true)\">";
			echo gettext("next");
			echo " &gt;</a></li>";
		}
		else
		{
			echo "<li class=\"disabled\"><a href=\"#\">";
			echo gettext("next");
			echo " &gt;</a></li>";
		}
		echo "</ul>";
		echo "</nav>";

	}
	else
	{
		echo "<p>";
		echo gettext("No objects found for searchstring ");
		echo "</p>";
	}


	//search result footer
	echo "</div>";
	echo "</div>";
	echo "</div>";

?>
