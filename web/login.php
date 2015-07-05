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
* yourCMDB WebUI: login page
* @author: Michael Batz <michael@yourcmdb.org>
*/

	//get header
	include "include/bootstrap-web.php";
	include "include/htmlheader.inc.php";

	$loginError = getHttpGetVar("error", "false");

	//container
	echo "<div id=\"cmdb-logincontainer\">";
	echo "<div class=\"container\">";
	echo "<div class=\"row\">";
	echo "<div class=\"col-md-6 col-md-offset-3\">";

	//login panel
	echo "<div class=\"panel\" id=\"cmdb-loginpanel\">";
	//login panel headline
	echo "<div class=\"panel-heading\">".gettext("Welcome to yourCMDB!")."</div>";
	//login panel body
	echo "<div class=\"panel-body\">";
	echo "<form method=\"post\" action=\"index.php\" class=\"form-horizontal\">";
	//login form
	if($loginError != "false")
	{
		printErrorMessage(gettext("Sorry, wrong username or password. Please try again..."));
	}
	echo "<img src=\"img/logo.png\" alt=\"".gettext("yourCMDB logo")."\" class=\"center-block\" />";

	//login form field: username
	echo "<div class=\"form-group\">";
	echo "<div class=\"input-group col-xs-10 col-xs-offset-1\">";
	echo "<div class=\"input-group-addon\"><span class=\"glyphicon glyphicon-user\" aria-hidden=\"true\"></span></div>";
	echo "<input type=\"text\" class=\"form-control\" placeholder=\"".gettext("user")."\" id=\"cmdbLoginUsername\" name=\"authUser\"/>";
	echo "</div>";
	echo "</div>";

	//login form field: password
	echo "<div class=\"form-group\">";
	echo "<div class=\"input-group col-xs-10 col-xs-offset-1\">";
	echo "<div class=\"input-group-addon\"><span class=\"glyphicon glyphicon-option-horizontal\" aria-hidden=\"true\"></span></div>";
	echo "<input type=\"password\" class=\"form-control\" placeholder=\"".gettext("password")."\" id=\"loginPassword\" name=\"authPassword\"/>";
	echo "</div>";
	echo "</div>";

	//login form: login button
	echo "<div class=\"form-group\">";
	echo "<button type=\"submit\" class=\"btn btn-default col-xs-offset-1\">".gettext("Go!")."</button>";
	echo "</div>";

	echo "</form>";
	//end login panel
	echo "</div>";
	echo "</div>";

	//end container
	echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";

	//include footer
	include "include/htmlfooter.inc.php";
?>
