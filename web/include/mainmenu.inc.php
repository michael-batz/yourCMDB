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

	//get AuthorisationProvider
	$authorisationProvider = $controller->getAuthorisationProvider("web");

	//get additional menu items from configuration
	$menuitems = $config->getViewConfig()->getMenuItems();

	echo "<div class=\"mainmenu\">";
	echo "<ul>";
	echo "<li><a href=\"index.php\">".gettext("Home")."</a></li>";
	echo "<li><a href=\"search.php\">".gettext("Search")."</a></li>";
	echo "<li><a href=\"object.php?action=new\">".gettext("New Object")."</a></li>";
	echo "<li><a href=\"import.php\">".gettext("Import-Export")."</a></li>";
	if(isset($authAccessgroup) && $authorisationProvider->authorise($authAccessgroup, "admin") != 0)
	{
		echo "<li><a href=\"admin.php\">".gettext("Admin")."</a></li>";
	}

	//add additional menu items from configuration
	foreach(array_keys($menuitems) as $itemName)
	{
		echo "<li>";
		echo "<a  href=\"{$menuitems[$itemName]}\">";
		echo gettext($itemName);
		echo "</a>";
		echo "</li>";
	}

	echo "</ul>";
	echo "</div>";

?>
