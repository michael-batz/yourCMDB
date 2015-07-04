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
* WebUI element: show object list
* @author Michael Batz <michael@yourcmdb.org>
*/

	//get data
	$paramStatusVar = $paramStatus;
	if($paramStatus == "0")
	{
		$paramStatusVar = null;
	}
	$objects = $objectController->getObjectsByType(Array($paramType), $paramSort, $paramSortType, $paramStatusVar, 0, 0, $authUser);
	$summaryFields = $config->getObjectTypeConfig()->getSummaryFields($paramType);

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

	//sort options
	$urlSortType = "DESC";
	if($paramSortType == "DESC")
	{
		$urlSortType = "ASC";
	}

	//generate output strings
	$urlShowActiveBase = "object.php?action=list&amp;type=$paramType&amp;status=";
	$urlAdd = "object.php?action=add&amp;type=$paramType";
	$urlCsvExport = "export.php?format=csv&amp;type=$paramType";
	$listnavUrlBase= "object.php?action=list&amp;type=$paramType&amp;max=$paramMax&amp;status=$paramStatus&amp;sorttype=$paramSortType&amp;sort=$paramSort&amp;page=";
	$urlSortBase= "object.php?action=list&amp;type=$paramType&amp;max=$paramMax&amp;status=$paramStatus&amp;sorttype=$urlSortType&amp;sort=";

	//generate link for show active/inactive objects
	if($paramStatus == "A")
	{
		$textShowActive = gettext("Show also inactive objects");
		$urlShowActive = $urlShowActiveBase."0";		
	}
	else
	{
		$textShowActive = gettext("Show only active objects");
		$urlShowActive = $urlShowActiveBase."A";		
	}



	//<!-- confirmation for deleting this object  -->
	echo "<div class=\"modal fade\" id=\"confirmDeleteList\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"confirmDeleteListLabel\" aria-hidden=\"true\">";
	echo "<div class=\"modal-dialog\">";
	echo "<div class=\"modal-content\">";
	echo "<form action=\"object.php\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"delete\">";
	//confirmation: header
	echo "<div class=\"modal-header\">";
	echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
	echo "<h4 class=\"modal-title\" id=\"confirmDeleteListLabel\">".gettext("Are you sure...?")."</h4>";
	echo "</div>";
	//confirmation: body
	echo "<div class=\"modal-body\">";
        echo "<p>";
	echo gettext("Do you really want to delete this object?");
	echo "</p>";
	echo "</div>";
	//confirmation: footer
	echo "<div class=\"modal-footer\">";
	echo "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">".gettext("cancel")."</button>";
	echo "<button type=\"submit\" class=\"btn btn-danger\">".gettext("delete")."</button>";
	echo "</div>";
	echo "</form>";
	echo "</div>";
	echo "</div>";
	echo "</div>";

	//<!-- submenu -->
	echo "<div>";
	echo "<a href=\"$urlShowActive\"><span class=\"glyphicon glyphicon-tags\" title=\"".gettext("switch")."\"></span>$textShowActive</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a href=\"$urlAdd\"><span class=\"glyphicon glyphicon-plus\" title=\"".gettext("add")."\"></span>".gettext("add new object")."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a href=\"$urlCsvExport\"><span class=\"glyphicon glyphicon-share-alt\" title=\"".gettext("export")."\"></span>CSV export</a>";
	echo "</div>";

	//print messagebar
	include "include/messagebar.inc.php";

	//<!-- headline -->
	echo "<h1 class=\"text-center\">$paramType ($objectCount)</h1>";

	//<!-- list objects -->
	echo "<table class=\"table table-hover\">";

	//<!-- table header -->
	echo "<tr>";
	echo "<th><a href=\"$urlSortBase\">";
	echo gettext("AssetID");
	echo "</a></th>";
	foreach(array_keys($summaryFields) as $fieldname)
	{
		$urlSort = $urlSortBase .$fieldname;
		echo "<th><a href=\"$urlSort\">".$config->getObjectTypeConfig()->getFieldLabel($paramType, $fieldname)."</a></th>";
	}
	echo "<th colspan=\"3\">&nbsp;</th>";
	echo "</tr>";

	//<!-- object summary -->
	for($i = $listStart; $i <= $listEnd; $i++)
	{ 
		//get object status icon
		$statusIcon = "<span class=\"label label-success\" title=\"".gettext("active object")."\">A</span>";
		if($objects[$i]->getStatus() != 'A')
		{
			$statusIcon = "<span class=\"label label-danger\" title=\"".gettext("inactive object")."\">N</span>";
		}
		echo "<tr>";
		echo "<td>$statusIcon ".$objects[$i]->getId()."</td>";
		foreach(array_keys($summaryFields) as $fieldname)
		{ 
			$urlObjectShow = "object.php?action=show&amp;id=". $objects[$i]->getId();
			$urlObjectEdit = "object.php?action=edit&amp;id=". $objects[$i]->getId()."&amp;type=".$objects[$i]->getType();
			$fieldValue = $objects[$i]->getFieldValue($fieldname);
			$fieldType = $summaryFields[$fieldname];
			echo "<td>";
			showFieldForDataType($paramType, "$fieldname-$i", $fieldValue, $fieldType, false);
			echo "</td>";
		}
		echo "<td>";
		echo "<a href=\"$urlObjectShow\"><span class=\"glyphicon glyphicon-eye-open\" title=\"".gettext("show")."\"></span></a>&nbsp;&nbsp;&nbsp;";
		echo "<a href=\"$urlObjectEdit\"><span class=\"glyphicon glyphicon-pencil\" title=\"".gettext("edit")."\"></span></a>&nbsp;&nbsp;&nbsp;";
		echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#confirmDeleteList\" data-form-id=\"".$objects[$i]->getId()."\">";
		echo "<span class=\"glyphicon glyphicon-trash\" title=\"".gettext("delete")."\"></span></a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";

	//<!-- list navigation  -->
	echo "<nav>";
	echo "<ul class=\"pagination cmdb-pagination\">";
	//print prev button
	if($listPage != 1)
	{
		$listnavUrl = $listnavUrlBase .($listPage - 1);
		echo "<li><a href=\"$listnavUrl\">&lt; ";
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
			echo "<li class=\"active\"><a href=\"$listnavUrl\">$i</a></li>";
		}
		else
		{
			echo "<li><a href=\"$listnavUrl\">$i</a></li>";
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
		echo "<li><a href=\"$listnavUrl\">";
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
?>
