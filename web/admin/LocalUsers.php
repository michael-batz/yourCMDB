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

/**
* WebUI element: manage local users
* @author Michael Batz <michael@yourcmdb.org>
*/
	//imports
	use yourCMDB\security\AuthenticationProviderLocal;
	use yourCMDB\security\SecurityChangeUserException;
	use yourCMDB\exceptions\CmdbLocalUserAlreadyExistsException;
	use yourCMDB\exceptions\CmdbLocalUserNotFoundException;

	//include base
	include "../include/bootstrap-web.php";

	//authentication and authorisation
	$authorisationAppPart = "admin";
	include "../include/auth.inc.php";
	include "../include/authorisation.inc.php";

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
			catch(CmdbLocalUserAlreadyExistsException $e)
			{
				printErrorMessage(sprintf(gettext("Error creating user  %s. User already exists"), $username));
			}
			break;

		case "deleteUser":
			$username = getHttpGetVar("username", "");
			if($username != $authUser)
			{
				try
				{
					$result = $authProviderLocal->deleteUser($username);
					printInfoMessage(sprintf(gettext("user %s successfully deleted"), $username));
				}
				catch(CmdbLocalUserNotFoundException $e)
				{
					printErrorMessage(sprintf(gettext("Error deleting user %s. User not found."), $username));
				}
			}
			break;

		case "editUser":
			$username = getHttpGetVar("username", "");
			$password = getHttpGetVar("password", "");
			$accessgroup = getHttpGetVar("accessgroup", "");
			$message = "";
			//only change password if field is not empty
			try
			{
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
			}
			catch(CmdbLocalUserNotFoundException $e)
			{
				printErrorMessage(sprintf(gettext("Error editing user %s. User not found."), $username));
			}
			break;
	}

	//get data
	$users = $authProviderLocal->getUsers();

	//output: navigation
	echo "<div>";
	echo "<p>";
	echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#addUser\">";
	echo "<span class=\"glyphicon glyphicon-plus\"></span>".gettext("add new user")."</a>";
	echo "</p>";
	echo "</div>";

	//output: header
	echo "<h1 class=\"text-center\">".gettext("local user management")."</h1>";

	//output: user table
	echo "<table class=\"table cmdb-cleantable\">";
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
		$urlUserDelete = "javascript:cmdbOpenUrlAjax('admin/LocalUsers.php?action=deleteUser&amp;username=$userName', '#adminAuthentication', false, true)";

		echo "<tr>";
		echo "<td><span class=\"glyphicon glyphicon-user\"></td>";
		echo "<td>$userName</td>";
		echo "<td>$userAccessgroup</td>";
		echo "<td>";
		//prevent a user from deleting and changing its own user account
		if($userName != $authUser)
		{
			//edit button
			echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#editUser\" data-form-username=\"$userName\">";
			echo "<span class=\"glyphicon glyphicon-pencil\" title=\"".gettext("edit")."\"></span></a>&nbsp;&nbsp;&nbsp;";
			//delete button
			echo "<a href=\"$urlUserDelete\"><span class=\"glyphicon glyphicon-trash\" title=\"".gettext("delete")."\"></span></a>";
		}
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";

	//output: add user form
	$urlFormAddUser = "cmdbSubmitModal('#addUser', 'admin/LocalUsers.php?' + $( '#adminAuthAddUserForm' ).serialize(), '#adminAuthentication', false, true);";
	echo "<div class=\"modal fade\" id=\"addUser\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"addUserLabel\" aria-hidden=\"true\">";
	echo "<div class=\"modal-dialog\">";
	echo "<div class=\"modal-content\">";
	//form: header
	echo "<div class=\"modal-header\">";
	echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
	echo "<h4 class=\"modal-title\" id=\"addUserLabel\">".gettext("add new user")."</h4>";
	echo "</div>";
	//form: body
	echo "<div class=\"modal-body\">";
	echo "<form id=\"adminAuthAddUserForm\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<table>";
	echo "<input type=\"hidden\" name=\"action\" value=\"addUser\">";
	echo "<tr><td>".gettext("username:")."</td><td><input type=\"text\" name=\"username\"></td></tr>";
	echo "<tr><td>".gettext("password:")."</td><td><input type=\"password\" name=\"password\"></td></tr>";
	echo "<tr><td>".gettext("access group:")."</td><td><input type=\"text\" name=\"accessgroup\"></td></tr>";
	echo "</table>";
	echo "</form>";
	echo "</div>";
	//form: footer
	echo "<div class=\"modal-footer\">";
	echo "<a href=\"#\" onClick=\"$urlFormAddUser\" class=\"btn btn-danger\">".gettext("Go!")."</a>";
	echo "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">".gettext("cancel")."</button>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";

	//output: edit user form
	$urlFormEditUser = "cmdbSubmitModal('#editUser', 'admin/LocalUsers.php?' + $( '#adminAuthEditUserForm' ).serialize(), '#adminAuthentication', false, true);";
	echo "<div class=\"modal fade\" id=\"editUser\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"editUserLabel\" aria-hidden=\"true\">";
	echo "<div class=\"modal-dialog\">";
	echo "<div class=\"modal-content\">";
	//form: header
	echo "<div class=\"modal-header\">";
	echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
	echo "<h4 class=\"modal-title\" id=\"editUserLabel\">".gettext("edit user")."</h4>";
	echo "</div>";
	//form: body
	echo "<div class=\"modal-body\">";
	echo "<form id=\"adminAuthEditUserForm\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<p>".gettext("Leave fields empty, that you do not wish to change.")."</p>";
	echo "<table>";
	echo "<input type=\"hidden\" name=\"action\" value=\"editUser\">";
	echo "<tr><td>".gettext("new password:")."</td><td><input type=\"password\" name=\"password\"></td></tr>";
	echo "<tr><td>".gettext("new access group:")."</td><td><input type=\"text\" name=\"accessgroup\"></td></tr>";
	echo "</table>";
	echo "</form>";
	echo "</div>";
	//form: footer
	echo "<div class=\"modal-footer\">";
	echo "<a href=\"#\" onClick=\"$urlFormEditUser\" class=\"btn btn-danger\">".gettext("Go!")."</a>";
	echo "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">".gettext("cancel")."</button>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";

?>
