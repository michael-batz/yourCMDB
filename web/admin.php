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

	//base
	include "include/base.inc.php";

	//authentication and authorisation
	$authorisationAppPart = "admin";
	include "include/auth.inc.php";
	include "include/authorisation.inc.php";

	//header
	include "include/htmlheader.inc.php";
	include "include/yourcmdbheader.inc.php";

	//<!-- title -->
	echo "<h1>".gettext("Admin")."</h1>";

	//<!-- start admin tabs -->
	echo "<div id=\"jsAccordion\">";

	//tab: about
	echo "<h3>".gettext("About")."</h3>";
	echo "<div>";
	include "admin/About.php";
	echo "</div>";

	//tab: user manager
	echo "<h3>".gettext("Authentication")."</h3>";
	echo "<div id=\"adminTabAuthentication\">";
	echo "<script language=\"JavaScript\">";
	echo "openUrlAjax('admin/LocalUsers.php?', '#adminTabAuthentication', false, true);";
	echo "</script>";
	echo "</div>";

	//tab: authorisation
	echo "<h3>".gettext("Authorisation")."</h3>";
	echo "<div id=\"adminTabAuthorisation\">";
	echo "<script language=\"JavaScript\">";
	echo "openUrlAjax('admin/Authorisation.php?', '#adminTabAuthorisation', false, true);";
	echo "</script>";
	echo "</div>";

	//<!-- end admin tabs -->
	echo "</div>";


	//include footer
	include "include/yourcmdbfooter.inc.php";
	include "include/htmlfooter.inc.php";
?>
