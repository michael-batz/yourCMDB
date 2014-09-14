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
* WebUI element: show object list
* @author Michael Batz <michael@yourcmdb.org>
*/

	//get data
	$objects = $datastore->getObjectsByType($paramType, $paramSort, $paramSortType, $paramActiveOnly);
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
	$urlSortType = "desc";
	if($paramSortType == "desc")
	{
		$urlSortType = "asc";
	}

	//generate output strings
	$urlShowActiveBase = "object.php?action=list&amp;type=$paramType&amp;activeonly=";
	$urlAdd = "object.php?action=add&amp;type=$paramType";
	$urlCsvExport = "export.php?format=csv&amp;type=$paramType";
	$listnavUrlBase= "object.php?action=list&amp;type=$paramType&amp;max=$paramMax&amp;activeonly=$paramActiveOnly&amp;page=";
	$urlSortBase= "object.php?action=list&amp;type=$paramType&amp;max=$paramMax&amp;activeonly=$paramActiveOnly&amp;sorttype=$urlSortType&amp;sort=";

	//generate link for show active/inactive objects
	if($paramActiveOnly)
	{
		$textShowActive = gettext("Show also inactive objects");
		$urlShowActive = $urlShowActiveBase."0";		
	}
	else
	{
		$textShowActive = gettext("Show only active objects");
		$urlShowActive = $urlShowActiveBase."1";		
	}



	//<!-- confirmation for deleting objects  -->
	echo "<div class=\"blind\" id=\"jsConfirm\" title=\"".gettext("Are you sure?")."\">";
	echo "<p>";
	echo gettext("Do you really want to delete this object?");
	echo "</p>";
	echo "</div>";

	//<!-- submenu -->
	echo "<div class=\"submenu\">";
	echo "<a href=\"$urlShowActive\">$textShowActive</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a href=\"$urlAdd\">add object</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a href=\"$urlCsvExport\">CSV export</a>";
	echo "</div>";

      	//<!-- print messages if available -->
	if(isset($paramMessage) && $paramMessage != "")
	{ 
		printInfoMessage($paramMessage);   
	}
	if(isset($paramError) && $paramError != "")
	{       
		printErrorMessage($paramError);
	}

	//<!-- headline -->
	echo "<h1>$paramType ($objectCount)</h1>";

	//<!-- list objects -->
	echo "<table class=\"list\">";

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
		$statusIcon = "<img src=\"img/icon_active.png\" alt=\"".gettext("active")."\" title=\"".gettext("active object")."\" />";
		if($objects[$i]->getStatus() != 'A')
		{
			$statusIcon = "<img src=\"img/icon_inactive.png\" alt=\"".gettext("inactive")."\" title=\"".gettext("inactive object")."\" />";
		}
		echo "<tr>";
		echo "<td>$statusIcon ".$objects[$i]->getId()."</td>";
		foreach(array_keys($summaryFields) as $fieldname)
		{ 
			$urlObjectShow = "object.php?action=show&amp;id=". $objects[$i]->getId();
			$urlObjectEdit = "object.php?action=edit&amp;id=". $objects[$i]->getId()."&amp;type=".$objects[$i]->getType();
			$urlObjectDelete = "javascript:showConfirmation('object.php?action=delete&amp;id=". $objects[$i]->getId()."', '".gettext("Yes")."', '".gettext("Cancel")."')";
			echo "<td>".$objects[$i]->getFieldValue($fieldname)."</td>";
		}
		echo "<td class=\"right\">";
		echo "<a href=\"$urlObjectShow\"><img src=\"img/icon_show.png\" title=\"".gettext("show")."\" alt=\"".gettext("show")."\" /></a>&nbsp;&nbsp;&nbsp;";
		echo "<a href=\"$urlObjectEdit\"><img src=\"img/icon_edit.png\" title=\"".gettext("edit")."\" alt=\"".gettext("edit")."\" /></a>&nbsp;&nbsp;&nbsp;";
		echo "<a href=\"$urlObjectDelete\"><img src=\"img/icon_delete.png\" title=\"".gettext("delete")."\" alt=\"".gettext("delete")."\" /></a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";

	//<!-- list navigation  -->
	echo "<p class=\"listnav\">";
	//print prev button
	if($listPage != 1)
	{
		$listnavUrl = $listnavUrlBase .($listPage - 1);
		echo "<a href=\"$listnavUrl\">&lt; ";
		echo gettext("previous");
		echo "</a>";
	}
	else
	{
		echo "<a href=\"#\" class=\"disabled\">&lt; ";
		echo gettext("previous");
		echo "</a>";
	}
	//print page numbers
	for($i = 1; $i <= $listPages; $i++)
	{
		$listnavUrl = $listnavUrlBase .$i;
		if($i == $listPage)
		{
			echo "<a href=\"$listnavUrl\" class=\"active\">$i</a>";
		}
		else
		{
			echo "<a href=\"$listnavUrl\">$i</a>";
		}

		//jump to current page
		if($i == 3 && $listPage > 5)
		{
			$i = $listPage - 2;
			echo "...";
		}
		//jump to last page
		if($i > 3 && $i > $listPage && $i < ($listPages - 2))
		{
			$i = $listPages - 2;
			echo "...";
		}
	}
	//print next button
	if($listPage != $listPages)
	{
		$listnavUrl = $listnavUrlBase .($listPage + 1);
		echo "<a href=\"$listnavUrl\">";
		echo gettext("next");
		echo " &gt;</a>";
	}
	else
	{
		echo "<a href=\"#\" class=\"disabled\">";
		echo gettext("next"); 
		echo " &gt;</a>";
	}
	echo "</p>";
?>
