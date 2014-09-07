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
* WebUI element: show menu with object types
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get object types from configuration
	$objectGroups = $config->getObjectTypeConfig()->getObjectTypeGroups();

	//start menu
	echo "<div class=\"menu\">";

	//quick search box
	include "quicksearch.inc.php";

	//object menu
	echo "<div class=\"box\">";
	echo "<h1>";
	echo gettext("Objects");
	echo "</h1>";
	echo "<ul id=\"jsMenu\">";
	//walk through all object type groups
	foreach(array_keys($objectGroups) as $groupname)
	{
		echo "<li><a href=\"#\">$groupname</a><ul>";
		foreach($objectGroups[$groupname] as $objectType)
		{
			echo "<li>";
			echo "<a  href=\"object.php?action=list&amp;type=$objectType\">";
			echo "$objectType (".$datastore->getObjectCounts($objectType).")";
			echo "</a>";
			echo "</li>";
		}
		echo "</ul></li>";
	}
	echo "</ul>";
	echo "</div>";

	//end of menu
	echo "</div>";
