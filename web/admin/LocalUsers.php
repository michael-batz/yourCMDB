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
	$authProviderLocal = new AuthenticationProviderLocal(null);

	//execute actions if required
	$action = getHttpGetVar("action", "");
	switch($action)
	{
		case "addUser":
			$username = getHttpGetVar("username", "");
			$password = getHttpGetVar("password", "");
			$accessgroup = getHttpGetVar("accessgroup", "");
			try{
				$result = $authProviderLocal->addUser($username, $password, $accessgroup);
				if($result)
				{
					printInfoMessage(sprintf(gettext("user %s successfully created"), $username));
				}
				else
				{
					printErrorMessage(sprintf(gettext("Error creating user  %s"), $username));
				}
			}
			catch(SecurityChangeUserException $e)
			{
					printErrorMessage(sprintf(gettext("Error creating user  %s. Empty username or password"), $username));
			}
			break;

		case "deleteUser":
			$username = getHttpGetVar("username", "");
			if($username != $authUser)
			{
				$result = $authProviderLocal->deleteUser($username);
				if($result)
				{
					printInfoMessage(sprintf(gettext("user %s successfully deleted"), $username));
				}
				else
				{
					printErrorMessage(sprintf(gettext("Error deleting user  %s"), $username));
				}
			}
			break;

		case "editUser":
			$username = getHttpGetVar("username", "");
			$password = getHttpGetVar("password", "");
			$accessgroup = getHttpGetVar("accessgroup", "");
			$message = "";
			//only change password if field is not empty
			if($password != "")
			{
				$result = $authProviderLocal->resetPassword($username, $password);
				if($result)
				{
					$message .= gettext("Password successfully changed. ");
				}
			}
			//only change accessgroup if field is not empty
			if($accessgroup != "")
			{
				$result = $authProviderLocal->setAccessGroup($username, $accessgroup);
				if($result)
				{
					$message .= gettext("Accessgroup successfully changed. ");
				}
			}
			if($message != "")
			{
				printInfoMessage(sprintf(gettext("User %s successfully changed. "), $username) .$message);
			}
			break;
	}

	//get data
	$users = $authProviderLocal->getUsers();

	//output: navigation
	echo "<div class=\"submenu\">";
	echo "<p>";
	echo "<a href=\"javascript:adminAuthAddUser('".gettext("Go!")."', '".gettext("Cancel")."')\"><img src=\"img/icon_add.png\" alt=\"".gettext("add new user")."\"/>".gettext("add new user")."</a>";
	echo "</p>";
	echo "</div>";

	//output: header
	echo "<h1>local user management</h1>";

	//output: user table
	echo "<table class=\"list\">";
	echo "<tr>";
	echo "<th>&nbsp;</th>";
	echo "<th>".gettext("username")."</th>";
	echo "<th>".gettext("access group")."</th>";
	echo "<th>&nbsp;</th>";
	echo "</tr>";
	foreach($users as $user)
	{
		$userName = $user->getUsername();
		$userAccessgroup = $user->getAccessgroup();
		$urlUserDelete = "javascript:openUrlAjax('admin/LocalUsers.php?action=deleteUser&amp;username=$userName', '#adminTabAuthentication', false, true)";
		$urlUserEdit = "javascript:adminAuthEditUser('$userName', '".gettext("Go!")."', '".gettext("Cancel")."')";

		echo "<tr>";
		echo "<td><img src=\"img/icon_user.png\" alt=\"".gettext("user")."\" /></td>";
		echo "<td>$userName</td>";
		echo "<td>$userAccessgroup</td>";
		echo "<td>";
		//prevent a user from deleting and changing its own user account
		if($userName != $authUser)
		{
			echo "<a href=\"$urlUserEdit\"><img src=\"img/icon_edit.png\" title=\"".gettext("edit")."\" alt=\"".gettext("edit")."\" /></a>&nbsp;&nbsp;&nbsp;";
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

	//output: edit user form
	echo "<div class=\"blind\" id=\"adminAuthEditUser\" title=\"".gettext("edit user")."\">";
	echo "<form id=\"adminAuthEditUserForm\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<p>".gettext("Leave fields empty, that you do not wish to change.")."</p>";
	echo "<table>";
	echo "<input type=\"hidden\" name=\"action\" value=\"editUser\">";
	echo "<tr><td>".gettext("new password:")."</td><td><input type=\"password\" name=\"password\"></td></tr>";
	echo "<tr><td>".gettext("new access group:")."</td><td><input type=\"text\" name=\"accessgroup\"></td></tr>";
	echo "</table>";
	echo "</form>";
	echo "</div>";
?>
