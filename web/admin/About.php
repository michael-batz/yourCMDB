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
* WebUI element: manage local users
* @author Michael Batz <michael@yourcmdb.org>
*/

	//get data
	$configSecurity = $config->getSecurityConfig();
	$authProviderInfoWeb = get_class($configSecurity->getAuthProvider("web"));
	$authProviderInfoRest = get_class($configSecurity->getAuthProvider("rest"));

	$aboutCmdbVersion = $controller->getVersion();
	$aboutOs = php_uname('s');
	$aboutPhp = phpversion();
	$aboutPhpSapi = php_sapi_name();
	$aboutDbVersion = $datastore->getDbServerVersion();


	//output: yourcmdb information
	echo "<div>";
	echo "<img src=\"img/logo.png\" alt=\"yourCMDB\" title=\"yourCMDB\" />";
	echo "<p>";
	echo gettext("yourCMDB is published under GPLv3.");
	echo "<br />";
	echo "&copy; 2013-2015 Michael Batz";
	echo "<br />";
	echo "<a href=\"http://www.yourcmdb.org\">http://www.yourcmdb.org</a>";
	echo "</p>";
	echo "</div>";

	//output: version information
	echo "<table class=\"list\">";
	echo "<tr><th colspan=\"2\" class=\"center\">".gettext("version information")."</th></tr>";
	echo "<tr><td>".gettext("yourCMDB:")."</td><td>$aboutCmdbVersion</td></tr>";
	echo "<tr><td>".gettext("operating system:")."</td><td>$aboutOs</td></tr>";
	echo "<tr><td>".gettext("PHP:")."</td><td>$aboutPhp</td></tr>";
	echo "<tr><td>".gettext("PHP server api:")."</td><td>$aboutPhpSapi</td></tr>";
	echo "<tr><td>".gettext("database server:")."</td><td>$aboutDbVersion</td></tr>";

	//output: authentication provider info
	echo "<tr><th colspan=\"2\" class=\"center\">".gettext("authentication methods")."</th></tr>";
	echo "<tr><td>WebUi</td><td>$authProviderInfoWeb</td></tr>";
	echo "<tr><td>REST API</td><td>$authProviderInfoRest</td></tr>";
	echo "</table>";

?>
