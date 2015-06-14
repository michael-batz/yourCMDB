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
* WebUI element: import actions
* @author Michael Batz <michael@yourcmdb.org>
*/

	//get import and export formats
	$importFormats = $config->getDataExchangeConfig()->getImportFormats();
	$exportFormats = $config->getDataExchangeConfig()->getExportFormats();

	//get objecttypes
	$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();

	//print messagebar
	include "include/messagebar.inc.php";

	//container
	echo "<div class=\"container\" id=\"cmdb-objecttable\">";

	//headline
	echo "<div class=\"row\" id=\"cmdb-objecttable-head\">";
	echo "<h1 class=\"text-center\">";
	echo gettext("Import and Export");
	echo "</h1>";
	echo "</div>";

	//<!-- import box  -->
	echo "<div class=\"row\">";
	echo "<form action=\"import.php\" enctype=\"multipart/form-data\" method=\"post\" class=\"form-horizontal\">";
	echo "<h2>".gettext("Import Objects")."</h2>";
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 control-label\">".gettext("Object Type:")."</label>";
	echo "<div class=\"col-md-3\">";
	echo "<select name=\"type\" class=\"form-control\">";
	foreach(array_keys($objectTypes) as $group)
	{
		echo "<optgroup label=\"$group\">";
		foreach($objectTypes[$group] as $type)
		{
			echo "<option>$type</option>";
		}
		echo "</optgroup>";
	}
	echo "</select>";
	echo "</div>";
	echo "</div>";
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 control-label\">".gettext("Import Format:")."</label>";
	echo "<div class=\"col-md-3\">";
	echo "<select name=\"format\" class=\"form-control\">";
	foreach($importFormats as $importFormat)
	{
		echo "<option>$importFormat</option>";
	}
	echo "</select>";
	echo "</div>";
	echo "</div>";
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 control-label\">".gettext("Import File:")."</label>";
	echo "<div class=\"col-md-3\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"preview\" />";
	echo "<input type=\"file\" name=\"file\" />";
	echo "</div>";
	echo "</div>";
	echo "<div class=\"form-group\">";
	echo "<div class=\"col-md-3 col-md-offset-2\">";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</div>";
	echo "</div>";
	echo "</form>";


	//<!-- export box  -->
	echo "<form action=\"export.php\" method=\"get\" class=\"form-horizontal\">";
	echo "<h2>".gettext("Export Objects")."</h2>";
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 control-label\">".gettext("Object Type:")."</label>";
	echo "<div class=\"col-md-3\">";
	echo "<select name=\"type\" class=\"form-control\">";
	foreach(array_keys($objectTypes) as $group)
	{
		echo "<optgroup label=\"$group\">";
		foreach($objectTypes[$group] as $type)
		{
			echo "<option>$type</option>";
		}
		echo "</optgroup>";
	}
	echo "</select>";
	echo "</div>";
	echo "</div>";
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 control-label\">".gettext("Output Format:")."</label>";
	echo "<div class=\"col-md-3\">";
	echo "<select name=\"format\" class=\"form-control\">";
	foreach($exportFormats as $exportFormat)
	{
		echo "<option>$exportFormat</option>";
	}
	echo "</select>";
	echo "</div>";
	echo "</div>";
	echo "<div class=\"form-group\">";
	echo "<div class=\"col-md-3 col-md-offset-2\">";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</div>";
	echo "</div>";
	echo "</form>";

	echo "</div>";
	echo "</div>";

?>
