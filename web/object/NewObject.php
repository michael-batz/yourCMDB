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

	//start: panel
	echo "<div class=\"container\">";
	echo "<div class=\"panel panel-default cmdb-contentpanel\">";

	//panel headline
	echo "<div class=\"panel-heading\">";
	echo "<h3 class=\"panel-title text-center\">";
	echo gettext("New Object");
	echo "</h3>";
	echo "</div>";

	//panel content
	echo "<div class=\"panel-body\">";
	echo "<form action=\"object.php\" method=\"get\" accept-charset=\"UTF-8\" class=\"form-horizontal\">";
	echo "<h2>".gettext("New Object")."</h2>";
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
	echo "<div class=\"col-md-4 col-md-offset-5\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"add\" />";
	echo "<input type=\"submit\" class=\"btn btn-danger\" value=\"".gettext("Go")."\" />";
	echo "</div>";
	echo "</div>";
	echo "</form>";

	//end container
	echo "</div>";
	echo "</div>";
	echo "</div>";
?>
