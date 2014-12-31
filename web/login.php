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

	//get header
	include "include/base.inc.php";
	include "include/htmlheader.inc.php";

	//login container
	echo "<div class=\"logincontainer\">";
	
	//login form
	echo "<div class=\"box\" id=\"loginBox\">";
	echo "<form method=\"post\" action=\"index.php\">";
	echo "<h1>".gettext("Welcome to yourCMDB!")."</h1>";
	echo "<p><img src=\"img/logo.png\" /></p>";
	echo "<table>";

	echo "<tr>";
	echo "<td>".gettext("user:")."</td>";
	echo "<td><input type=\"text\" name=\"authUser\"/></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td>".gettext("password:")."</td>";
	echo "<td><input type=\"password\" name=\"authPassword\"/></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td colspan=\"2\"><input type=\"submit\" value=\"".gettext("Go!")."\"/></td>";
	echo "</tr>";

	echo "</table>";
	echo "</form>";
	echo "</div>";

	//end login container
	echo "</div>";

	//include footer
	include "include/htmlfooter.inc.php";
?>
