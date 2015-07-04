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
* yourCMDB WebUI: home page
* @author: Michael Batz <michael@yourcmdb.org>
*/

	//include base
	include "include/bootstrap-web.php";
	include "include/auth.inc.php";

	//include header
	include "include/htmlheader.inc.php";
	include "include/cmdbheader.inc.php";

	//title
	echo "<h1 class=\"text-center\">".gettext("Welcome to yourCMDB!")."</h1>";

	//1st row
	echo "<div class=\"row\">";
	//dashlet: newest objects
	echo "<div class=\"col-md-8 col-md-offset-2 cmdb-dashlet\">";
	include "dashboard/DashletNewestObjects.php";
	echo "</div>";
	//1st row end
	echo "</div>";

	//2nd row
	echo "<div class=\"row\">";
	//dashlet: last changed objects
	echo "<div class=\"col-md-8 col-md-offset-2 cmdb-dashlet\">";
	include "dashboard/DashletLastChangedObjects.php";
	echo "</div>";
	//2nd row end
	echo "</div>";

	//include footer
	include "include/cmdbfooter.inc.php";
	include "include/htmlfooter.inc.php";
?>
