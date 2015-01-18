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
* WebUI element: show/edit user details
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
		case "changePassword":
			$oldPassword = getHttpGetVar("oldPassword", "");
			$newPassword = getHttpGetVar("newPassword", "");
			$newPassword2 = getHttpGetVar("newPassword2", "");
			//check if oldPassword is correct
			$result = $authProviderLocal->authenticate($authUser, $oldPassword);
			if(!$result)
			{
				printErrorMessage(gettext("Password not changed. Your old password was not correct."));
				break;
			}
			//check new passwords and set the new passwordonly change password if field is not empty
			if($newPassword != "" && $newPassword == $newPassword2)
			{
				$result = $authProviderLocal->resetPassword($authUser, $newPassword);
				if($result)
				{
					printInfoMessage(gettext("Password successfully changed."));
				}
			}
			else
			{
				printErrorMessage(gettext("Password not changed. Your new passwords did not match. Please try again..."));
			}
			break;
	}

	//get user data
	$user = $authProviderLocal->getUser($authUser);
	$userName = $user->getUsername();
	$userAccessgroup = $user->getAccessgroup();
	$urlChangePassword = "javascript:settingsUserDetailsChangePassword('".gettext("Go!")."', '".gettext("Cancel")."')";

	//output: header
	echo "<h1>".sprintf(gettext("User: %s"), $userName)."</h1>";

	//output: user table
	echo "<table class=\"list\">";
	echo "<tr>";
	echo "<th colspan=\"2\">&nbsp;</th>";
	echo "</tr>";
	echo "<tr><td>".gettext("username")."</td><td>$userName</td></tr>";
	echo "<tr><td>".gettext("access group")."</td><td>$userAccessgroup</td></tr>";
	echo "<tr><td>".gettext("password")."</td>";
	echo "<td><a href=\"$urlChangePassword\"><img src=\"img/icon_edit.png\" title=\"".gettext("change password")."\" alt=\"".gettext("change password")."\" /></a></td></tr>";
	echo "</table>";

	//output: edit user form
	echo "<div class=\"blind\" id=\"settingsUserDetailsChangePassword\" title=\"".gettext("change password")."\">";
	echo "<form id=\"settingsUserDetailsChangePasswordForm\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<table>";
	echo "<input type=\"hidden\" name=\"action\" value=\"changePassword\">";
	echo "<tr><td>".gettext("old password:")."</td><td><input type=\"password\" name=\"oldPassword\"></td></tr>";
	echo "<tr><td>".gettext("new password:")."</td><td><input type=\"password\" name=\"newPassword\"></td></tr>";
	echo "<tr><td>".gettext("retype new password:")."</td><td><input type=\"password\" name=\"newPassword2\"></td></tr>";
	echo "</table>";
	echo "</form>";
	echo "</div>";
?>
