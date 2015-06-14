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
	include "../include/bootstrap-web.php";
	include "../include/auth.inc.php";

	//central objects: $authProvider
	$functionalityPasswordChange = false;
	if(method_exists($authProvider, "resetPassword"))
	{
		$functionalityPasswordChange = true;
	}

	//execute actions if required
	$action = getHttpGetVar("action", "");
	switch($action)
	{
		case "changePassword":
			$oldPassword = getHttpGetVar("oldPassword", "");
			$newPassword = getHttpGetVar("newPassword", "");
			$newPassword2 = getHttpGetVar("newPassword2", "");
			//check, if AuthenticationProvider allows password changeing
			if(!$functionalityPasswordChange)
			{
				printErrorMessage(gettext("Password changeing is not allowed."));
				break;
			}
			//check if oldPassword is correct
			$result = $authProvider->authenticate($authUser, $oldPassword);
			if(!$result)
			{
				printErrorMessage(gettext("Password not changed. Your old password was not correct."));
				break;
			}
			//check new passwords and set the new passwordonly change password if field is not empty
			if($newPassword != "" && $newPassword == $newPassword2)
			{
				$result = $authProvider->resetPassword($authUser, $newPassword);
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
	$userName = $authUser;
	$userAccessgroup = $authAccessgroup;
	$urlChangePassword = "cmdbSubmitModal('#changeSettings', 'settings/UserDetails.php?' + $( '#settingsUserDetailsChangePasswordForm' ).serialize(), '#settingsTabUserDetails', false, true);";
	//$urlChangePassword = "javascript:cmdbHideModal('#changeSettings');";

	//output: header
	echo "<h1 class=\"text-center\">".sprintf(gettext("User: %s"), $userName)."</h1>";

	//output: user table
	echo "<table class=\"table\">";
	echo "<tr><td>".gettext("username")."</td><td>$userName</td></tr>";
	echo "<tr><td>".gettext("access group")."</td><td>$userAccessgroup</td></tr>";
	if($functionalityPasswordChange)
	{
		echo "<tr><td>".gettext("password")."</td>";
		echo "<td><a href=\"#\" data-toggle=\"modal\" data-target=\"#changeSettings\"><span class=\"glyphicon glyphicon-pencil\" title=\"".gettext("change password")."\"></span></a></td></tr>";
	}
	echo "</table>";

	//output: edit user form
	echo "<div class=\"modal fade\" id=\"changeSettings\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"changeSettingsLabel\" aria-hidden=\"true\">";
	echo "<div class=\"modal-dialog\">";
	echo "<div class=\"modal-content\">";
	//confirmation: header
	echo "<div class=\"modal-header\">";
	echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
	echo "<h4 class=\"modal-title\" id=\"changeSettingsLabel\">".gettext("Change password")."</h4>";
	echo "</div>";
	//confirmation: body
	echo "<div class=\"modal-body\">";
	echo "<form id=\"settingsUserDetailsChangePasswordForm\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "<table>";
	echo "<input type=\"hidden\" name=\"action\" value=\"changePassword\">";
	echo "<tr><td>".gettext("old password:")."</td><td><input type=\"password\" name=\"oldPassword\"></td></tr>";
	echo "<tr><td>".gettext("new password:")."</td><td><input type=\"password\" name=\"newPassword\"></td></tr>";
	echo "<tr><td>".gettext("retype new password:")."</td><td><input type=\"password\" name=\"newPassword2\"></td></tr>";
	echo "</table>";
	echo "</form>";
	echo "</div>";
	//confirmation: footer
	echo "<div class=\"modal-footer\">";
	echo "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">".gettext("cancel")."</button>";
	echo "<a href=\"#\" onClick=\"$urlChangePassword\" class=\"btn btn-danger\">".gettext("Go!")."</a>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";

?>
