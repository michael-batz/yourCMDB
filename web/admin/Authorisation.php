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
* WebUI element: manage access rights
* @author Michael Batz <michael@yourcmdb.org>
*/

	//include base
	include "../include/base.inc.php";

	//authentication and authorisation
	$authorisationAppPart = "admin";
	include "../include/auth.inc.php";
	include "../include/authorisation.inc.php";

	//central objects
	$authorisationProviderLocal = new AuthorisationProviderLocal();

	//execute actions if required
	$action = getHttpGetVar("action", "");
	switch($action)
	{
		case "editGroupForm":
			$accessgroup = getHttpGetVar("name", "");
			$accessRights = $authorisationProviderLocal->getAccessRights($accessgroup);
			$htmlselectoptions = "<option value=\"0\">".gettext("no access")."</option>";
			$htmlselectoptions.= "<option value=\"1\">".gettext("read only")."</option>";
			$htmlselectoptions.= "<option value=\"2\">".gettext("read-write")."</option>";
			echo "<input type=\"hidden\" name=\"action\" value=\"editGroup\" />";
			echo "<input type=\"hidden\" name=\"name\" value=\"$accessgroup\" />";
			echo "<p>".sprintf(gettext("Rights for group %s"), $accessgroup)."</p>";
			echo "<table id=\"adminAuthorisationEditGroupFormTable\">";
			foreach($accessRights as $accessRight)
			{
				$accessRightName = $accessRight[0];
				$accessRightValue = $accessRight[1];
				echo "<tr id=\"adminAuthorisationEditGroupField$accessRightName\">";
				echo "<td>$accessRightName</td>";
				echo "<td><select name=\"access/$accessRightName\">";
				switch($accessRightValue)
				{
					case 0:
						echo "<option value=\"0\" selected=\"true\">".gettext("no access")."</option>";
						break;
					case 1:
						echo "<option value=\"1\" selected=\"true\">".gettext("read only")."</option>";
						break;
					case 2:
						echo "<option value=\"2\" selected=\"true\">".gettext("read-write")."</option>";
						break;

				}
				echo "$htmlselectoptions</select></td>";
				echo "<td><a href=\"javascript:removeElement('#adminAuthorisationEditGroupField$accessRightName')\">";
				echo "<img src=\"img/icon_delete.png\" title=\"".gettext("delete")."\" alt=\"".gettext("delete")."\" /></a></td>";
				echo "</tr>";
			}
			echo "</table>";
			//link for adding new access entries
			echo "<a href=\"javascript:adminAuthorisationEditGroupAddEntry('#adminAuthorisationEditGroupFormTable')\">";
			echo "add new</a>";
			exit();
			break;

		case "editGroup":
			$accessgroup = getHttpGetVar("name", "");
			$newAccessRights = Array();
			foreach(array_keys($_GET) as $inputVarName)
			{
				if(preg_match("#access/(.*)#", $inputVarName, $matches) === 1)
				{
					$applicationPart = $matches[1];
					$accessRight = $_GET[$inputVarName];
					$newAccessRights[] = Array($applicationPart, $accessRight);
				}
				if(preg_match("#newAccess_(.*)#", $inputVarName, $matches) === 1)
				{
					$id = $matches[1];
					$applicationPart = $_GET[$inputVarName];
					$accessRight = $_GET["newAccessSelect_$id"];
					$newAccessRights[] = Array($applicationPart, $accessRight);
				}

			}
			$result = $authorisationProviderLocal->setAccessRights($accessgroup, $newAccessRights);
			if($result)
			{
				printInfoMessage(sprintf(gettext("access group %s successfully updated"), $accessgroup));
			}
			else
			{
				printErrorMessage(sprintf(gettext("Error updating access rights for access group %s"), $accessgroup));
			}
			break;
		case "deleteGroup":
			$accessgroup = getHttpGetVar("name", "");
			$result = $authorisationProviderLocal->deleteAccessRights($accessgroup);
			if($result)
			{
				printInfoMessage(sprintf(gettext("access group %s successfully deleted"), $accessgroup));
			}
			else
			{
				printErrorMessage(sprintf(gettext("Error deleting access group %s"), $accessgroup));
			}
			break;

			break;
	}

	//get data
	$accessgroups = $authorisationProviderLocal->getAccessgroups();

	//output: header
	echo "<h1>access rights management</h1>";

	//output: user table
	echo "<table class=\"list\">";
	echo "<tr>";
	echo "<th>".gettext("access group")."</th>";
	echo "<th>&nbsp;</th>";
	echo "</tr>";
	foreach($accessgroups as $accessgroup)
	{
		$urlAccessDelete = "javascript:openUrlAjax('admin/Authorisation.php?action=deleteGroup&amp;name=$accessgroup', '#adminTabAuthorisation', false, true)";
		$urlAccessEdit = "javascript:adminAuthorisationEditGroup('$accessgroup', '".gettext("Go!")."', '".gettext("Cancel")."')";

		echo "<tr>";
		echo "<td>$accessgroup</td>";
		echo "<td>";
		echo "<a href=\"$urlAccessEdit\"><img src=\"img/icon_edit.png\" title=\"".gettext("edit")."\" alt=\"".gettext("edit")."\" /></a>&nbsp;&nbsp;&nbsp;";
		echo "<a href=\"$urlAccessDelete\"><img src=\"img/icon_delete.png\" title=\"".gettext("delete")."\" alt=\"".gettext("delete")."\" /></a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";

	//output: edit accessgroup
	echo "<div class=\"blind\" id=\"adminAuthorisationEditGroup\" title=\"".gettext("edit access group")."\">";
	echo "<form id=\"adminAuthorisationEditGroupForm\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "</form>";
	echo "</div>";

?>
