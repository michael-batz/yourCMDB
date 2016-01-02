<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2016 Michael Batz
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
	include "include/bootstrap-web.php";
	include "include/auth.inc.php";
	include "include/htmlheader.inc.php";
	include "include/cmdbheader.inc.php";

	//<!-- title -->
	echo "<h1 class=\"text-center\">".gettext("User settings")."</h1>";

	//<!-- start tabs -->
	echo "<div class=\"panel-group\" id=\"accordion\" role=\"tablist\" aria-multiselectable=\"true\">";

	//tab: change password
	echo "<div class=\"panel panel-default\">";
	echo "<div class=\"panel-heading\" role=\"tab\" id=\"tab-1-head\">";
	echo "<h3 class=\"panel-title\"><a href=\"#tab-1-body\" data-toggle=\"collapse\" data-parent=\"#accordion\">".gettext("user details")."</a></h3>";
	echo "</div>";
	echo "<div id=\"tab-1-body\" class=\"panel-collapse collapse in\" role=\"tabpanel\" aria-labledby=\"tab-1-head\">";
	echo "<div class=\"panel-body\" id=\"settingsTabUserDetails\">";
	echo "<script language=\"JavaScript\">";
	echo "cmdbOpenUrlAjax('settings/UserDetails.php?', '#settingsTabUserDetails', false, true);";
	echo "</script>";
	echo "</div>";
	echo "</div>";
	echo "</div>";

	//<!-- end tabs -->
	echo "</div>";


	//include footer
	include "include/cmdbfooter.inc.php";
	include "include/htmlfooter.inc.php";
?>
