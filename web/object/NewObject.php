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
* WebUI element: form for adding new object
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get data
	$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();

	//<!-- headline -->
	echo "<h1>";
	echo gettext("New Object");
	echo "</h1>";

	echo "<form action=\"object.php\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<table class=\"cols2\">";
	echo "<tr><th colspan=\"2\">";
	echo gettext("New Object");
	echo "</th></tr>";
	echo "<tr>";
	echo "<td>";
	echo gettext("Type:");
	echo "</td>";
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
	echo "<td colspan=\"2\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\" />";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</td></tr>";
	echo "</table>";
	echo "</form>";
?>
