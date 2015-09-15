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

use yourCMDB\fileimporter\Importer;

	//get import and export formats
	$importFormats = Importer::getInputFormats();
	$exportFormats = $config->getDataExchangeConfig()->getExportFormats();

	//get objecttypes
	$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();

	//print messagebar
	include "include/messagebar.inc.php";

	//start: panel
	echo "<div class=\"container\">";
	echo "<div class=\"panel panel-default cmdb-contentpanel\">";

	//panel headline
	echo "<div class=\"panel-heading\">";
	echo "<h3 class=\"panel-title text-center\">";
	echo gettext("Import and Export");
	echo "</h3>";
	echo "</div>";

	//start panel content
	echo "<div class=\"panel-body\">";

	//<!-- import box  -->
	echo "<h2>".gettext("Import Objects")."</h2>";
	echo "<form action=\"import.php\" enctype=\"multipart/form-data\" method=\"post\" class=\"form-horizontal\">";
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("Import Format:")."</label>";
	echo "<div class=\"col-md-4\">";
	echo "<select name=\"format\" class=\"form-control\">";
	foreach(array_keys($importFormats) as $importFormat)
	{
		$importClassName = $importFormats[$importFormat];
		echo "<option value=\"$importClassName\">$importFormat</option>";
	}
	echo "</select>";
	echo "</div>";
	echo "</div>";
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("Import File:")."</label>";
	echo "<div class=\"col-md-4\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"preview\" />";
	echo "<input type=\"file\" name=\"file\" />";
	echo "</div>";
	echo "</div>";
	echo "<div class=\"form-group\">";
	echo "<div class=\"col-md-4 col-md-offset-5\">";
	echo "<input type=\"submit\" class=\"btn btn-danger\" value=\"".gettext("Go")."\" />";
	echo "</div>";
	echo "</div>";
	echo "</form>";


	//<!-- export box  -->
	echo "<h2>".gettext("Export Objects")."</h2>";
	echo "<form action=\"export.php\" method=\"get\" class=\"form-horizontal\">";
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("Object Type:")."</label>";
	echo "<div class=\"col-md-4\">";
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
	echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("Output Format:")."</label>";
	echo "<div class=\"col-md-4\">";
	echo "<select name=\"format\" class=\"form-control\">";
	foreach($exportFormats as $exportFormat)
	{
		echo "<option>$exportFormat</option>";
	}
	echo "</select>";
	echo "</div>";
	echo "</div>";
	echo "<div class=\"form-group\">";
	echo "<div class=\"col-md-4 col-md-offset-5\">";
	echo "<input type=\"submit\" class=\"btn btn-danger\" value=\"".gettext("Go")."\" />";
	echo "</div>";
	echo "</div>";
	echo "</form>";

	//close panel body and panel
	echo "</div>";
	echo "</div>";
	echo "</div>";

?>
