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
* WebUI element: search form
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get data
	$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();

	//print messages if available
	if(isset($paramMessage) && $paramMessage != "")
	{
		printInfoMessage($paramMessage);
	}
	if(isset($paramError) && $paramError != "")
	{
		printErrorMessage($paramError);
	}

	//HTML output
	echo "<h1>";
	echo gettext("Search");
	echo "</h1>";

	//<!-- search for objects with a specific field value and type -->
	echo "<form action=\"search.php\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<table class=\"cols2\">";
	echo "<tr><th colspan=\"2\">".gettext("Search in all objects")."</th></tr>";
	echo "<tr>";
	echo "<td>".gettext("Searchstring")."</td>";
	echo "<td><input type=\"text\" name=\"searchstring\" /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan=\"2\">";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</form>";



	//<!-- search for objects with a specific field value and type -->
	echo "<form action=\"search.php\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<table class=\"cols2\">";
	echo "<tr><th colspan=\"2\">".gettext("Search for objects of a specific type")."</th></tr>";
	echo "<tr>";
	echo "<td>".gettext("Type:")."</td>";
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
	echo "<td>".gettext("Searchstring:")."</td>";
	echo "<td><input type=\"text\" name=\"searchstring\" /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan=\"2\">";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</form>";


	//<!-- search for objects with a specific field value and object type group -->
	echo "<form action=\"search.php\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<table class=\"cols2\">";
	echo "<tr><th colspan=\"2\">".gettext("Search for objects of a specific group")."</th></tr>";
	echo "<tr>";
	echo "<td>".gettext("Type:")."</td>";
	echo "<td><select name=\"typegroup\">";
	foreach(array_keys($objectTypes) as $group)
	{
		echo "<option>$group</option>";
	}
	echo "</select></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>".gettext("Searchstring:")."</td>";
	echo "<td><input type=\"text\" name=\"searchstring\" /></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan=\"2\">";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</form>";
?>
