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

	//<!-- headline  -->
	echo "<h1>";
	echo gettext("Import and Export");
	echo "</h1>";

	//<!-- import box  -->
	echo "<form action=\"import.php\" enctype=\"multipart/form-data\" method=\"post\">";
	echo "<table class=\"cols2\">";
	echo "<tr><th colspan=\"2\">".gettext("Import Objects")."</th></tr>";
	echo "<tr>";
	echo "<td>".gettext("Object Type:")."</td>";
	echo "<td><select name=\"type\">";
	foreach(array_keys($objectTypes) as $group)
	{
		echo "<optgroup label=\"$group\">";
		foreach($objectTypes[$group] as $type)
		{
			echo "<option>$type</option>";
		}
		echo "</optgroup>";
	}
	echo "</select></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>".gettext("Import Format:")."</td>";
	echo "<td><select name=\"format\">";
	foreach($importFormats as $importFormat)
	{
		echo "<option>$importFormat</option>";
	}
	echo "</select></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>".gettext("Import File:")."</td>";
	echo "<td>";
	echo "<input type=\"hidden\" name=\"action\" value=\"preview\" />";
	echo "<input type=\"file\" name=\"file\" /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan=\"2\"><input type=\"submit\" value=\"".gettext("Go")."\" /></td>";
	echo "</tr>";
 	echo "</table>";
	echo "</form>";


	//<!-- export box  -->
	echo "<form action=\"export.php\" method=\"get\">";
	echo "<table class=\"cols2\">";
	echo "<tr><th colspan=\"2\">".gettext("Export Objects")."</th></tr>";
	echo "<tr>";
	echo "<td>".gettext("Object Type:")."</td>";
	echo "<td><select name=\"type\">";
	foreach(array_keys($objectTypes) as $group)
	{
		echo "<optgroup label=\"$group\">";
		foreach($objectTypes[$group] as $type)
		{
			echo "<option>$type</option>";
		}
		echo "</optgroup>";
	}
	echo "</select></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>".gettext("Output Format:")."</td>";
	echo "<td><select name=\"format\">";
	foreach($exportFormats as $exportFormat)
	{
		echo "<option>$exportFormat</option>";
	}
	echo "</select></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan=\"2\">";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</form>";

?>
