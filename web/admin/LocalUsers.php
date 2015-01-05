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

	//include base functions
	include "../include/base.inc.php";
	include "../include/auth.inc.php";

	//central objects
	$configSecurity = $config->getSecurityConfig();
	$authProviderLocal = new AuthenticationProviderLocal(null);
	$authProviderInfoWeb = get_class($configSecurity->getAuthProvider("web"));
	$authProviderInfoRest = get_class($configSecurity->getAuthProvider("rest"));


	//execute actions if required
	$action = getHttpGetVar("action", "");
	switch($action)
	{
		case "addUser":
			$username = getHttpGetVar("username", "");
			$password = getHttpGetVar("password", "");
			$accessgroup = getHttpGetVar("accessgroup", "");
			$authProviderLocal->addUser($username, $password, $accessgroup);
			break;

		case "deleteUser":
			$username = getHttpGetVar("username", "");
			if($username != $authUser)
			{
				$authProviderLocal->deleteUser($username);
			}
			break;
	}

	//get data
	$users = $authProviderLocal->getUsers();


	//output: authentication provider info
	echo "<table>";
	echo "<tr><th colspan=\"2\">".gettext("authentication methods")."</th></tr>";
	echo "<tr><td>WebUi</td><td>$authProviderInfoWeb</td></tr>";
	echo "<tr><td>REST API</td><td>$authProviderInfoRest</td></tr>";
	echo "</table>";

	//output: header
	echo "<p>user management</p>";

	//navigation
	echo "<p>";
	echo "<a href=\"javascript:adminAuthAddUser('".gettext("Go!")."', '".gettext("Cancel")."')\">".gettext("add user")."</a>";
	echo "</p>";

	//output: user table
	echo "<table>";
	echo "<tr>";
	echo "<th>".gettext("username")."</th>";
	echo "<th>".gettext("access group")."</th>";
	echo "<th>&nbsp;</th>";
	echo "</tr>";
	foreach($users as $user)
	{
		$userName = $user->getUsername();
		$userAccessgroup = $user->getAccessgroup();
		$urlUserDelete = "javascript:openUrlAjax('admin/LocalUsers.php?action=deleteUser&amp;username=$userName', '#adminTabAuthentication', false, true)";

		echo "<tr>";
		echo "<td>$userName</td>";
		echo "<td>$userAccessgroup</td>";
		echo "<td>";
		//prevent a user from deleting its own user account
		if($userName != $authUser)
		{
			echo "<a href=\"$urlUserDelete\"><img src=\"img/icon_delete.png\" title=\"".gettext("delete")."\" alt=\"".gettext("delete")."\" /></a>";
		}
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";

	//output: add user form
	echo "<div class=\"blind\" id=\"adminAuthAddUser\" title=\"".gettext("add new user")."\">";
	echo "<form id=\"adminAuthAddUserForm\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<table>";
	echo "<input type=\"hidden\" name=\"action\" value=\"addUser\">";
	echo "<tr><td>".gettext("username:")."</td><td><input type=\"text\" name=\"username\"></td></tr>";
	echo "<tr><td>".gettext("password:")."</td><td><input type=\"password\" name=\"password\"></td></tr>";
	echo "<tr><td>".gettext("access group:")."</td><td><input type=\"text\" name=\"accessgroup\"></td></tr>";
	echo "</table>";
	echo "</form>";
	echo "</div>";
?>
