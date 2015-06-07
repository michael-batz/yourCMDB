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
	//import
	use yourCMDB\security\AuthorisationProviderLocal;
	use yourCMDB\exceptions\CmdbAccessGroupNotFoundException;
	use yourCMDB\exceptions\CmdbAccessGroupAlreadyExistsException;
	use yourCMDB\exceptions\CmdbAccessRuleAlreadyExistsException;
	use \Exception;

	//include base
	include "../include/bootstrap-web.php";

	//authentication and authorisation
	$authorisationAppPart = "admin";
	include "../include/auth.inc.php";
	include "../include/authorisation.inc.php";

	//execute actions if required
	$action = getHttpGetVar("action", "");
	switch($action)
	{
		case "editGroupForm":
			$accessgroupName = getHttpGetVar("name", "");
			$htmlselectoptions = "<option value=\"0\">no access</option>";
			$htmlselectoptions.= "<option value=\"1\">read only</option>";
			$htmlselectoptions.= "<option value=\"2\">read-write</option>";

			echo "<input type=\"hidden\" name=\"action\" value=\"editGroup\" />";
			try
			{
				$accessgroup = $accessGroupController->getAccessGroup($accessgroupName);
				$accessRights = $accessgroup->getAccessRules();
				echo "<input type=\"hidden\" name=\"name\" value=\"$accessgroupName\" />";
				echo "<p>".sprintf(gettext("Rights for group %s"), $accessgroupName)."</p>";
				echo "<table id=\"adminAuthorisationEditGroupFormTable\">";
				foreach($accessRights->getKeys() as $accessRightKey)
				{
					$accessRight = $accessRights->get($accessRightKey);
					$applicationPart = $accessRight->getApplicationPart();
					$access = $accessRight->getAccess();
					echo "<tr id=\"adminAuthorisationEditGroupField$applicationPart\">";
					echo "<td>$applicationPart</td>";
					echo "<td><select name=\"access/$applicationPart\">";
					switch($access)
					{
						case 0:
							echo "<option value=\"0\" selected=\"true\">no access</option>";
							break;
						case 1:
							echo "<option value=\"1\" selected=\"true\">read only</option>";
							break;
						case 2:
							echo "<option value=\"2\" selected=\"true\">read-write</option>";
							break;
	
					}
					echo "$htmlselectoptions</select></td>";
					echo "<td><a href=\"javascript:cmdbRemoveElement('#adminAuthorisationEditGroupField$applicationPart')\">";
					echo "<span class=\"glyphicon glyphicon-trash\" title=\"".gettext("delete")."\"></span></a></td>";
					echo "</tr>";
				}
			}
			catch(CmdbAccessGroupNotFoundException $e)
			{
				//add new access group
				echo "<p>";
				echo gettext("Add new access group with name:");
				echo "<input type=\"text\" name=\"name\"  />";
				echo "</p>";
				echo "<table id=\"adminAuthorisationEditGroupFormTable\">";

			}
			echo "</table>";
			//link for adding new access entries
			echo "<a href=\"javascript:cmdbAdminAuthorisationEditGroupAddEntry('#adminAuthorisationEditGroupFormTable')\">";
			echo "<span class=\"glyphicon glyphicon-plus\" title=\"".gettext("add")."\"></span>".gettext("add access right")."</a>";
			exit();
			break;

		case "editGroup":
			//get new access rights from user input
			$newAccessRights = Array();
			foreach(array_keys($_GET) as $inputVarName)
			{
				$applicationPart = "";
				$accessRight = 0;
				//check existing access entries
				if(preg_match("#access/(.*)#", $inputVarName, $matches) === 1)
				{
					$applicationPart = $matches[1];
					$accessRight = $_GET[$inputVarName];
				}
				//check new access entries
				if(preg_match("#newAccess_(.*)#", $inputVarName, $matches) === 1)
				{
					$id = $matches[1];
					$applicationPart = $_GET[$inputVarName];
					$accessRight = $_GET["newAccessSelect_$id"];
				}
				//only set access right, if applicationPart is not empty
				if($applicationPart != "")
				{
					$newAccessRights[] = Array($applicationPart, $accessRight);
				}
	
			}
			//only updating group if accessgroup name was set
			$accessgroupName = getHttpGetVar("name", "");
			if($accessgroupName == "")
			{
				printErrorMessage(gettext("Error: empty group name is not allowed"));
				break;
			}
			//try to get accessGroup object or create one
			try
			{
				$accessgroup = $accessGroupController->getAccessGroup($accessgroupName);
				//delete all existing access rules
				foreach($accessgroup->getAccessRules()->getKeys() as $accessRightKey)
				{
					$accessGroupController->deleteAccessRule($accessgroupName, $accessRightKey);
				}
				//add new access rules
				foreach($newAccessRights as $newAccessRight)
				{
					$accessGroupController->addAccessRule($accessgroupName, $newAccessRight[0], $newAccessRight[1]);
				}
				printInfoMessage(sprintf(gettext("access group %s successfully updated"), $accessgroupName));

			}
			catch(CmdbAccessGroupNotFoundException $e1)
			{
				try
				{
					$accessgroup = $accessGroupController->addAccessGroup($accessgroupName);
					//add new access rules
					foreach($newAccessRights as $newAccessRight)
					{
						$accessGroupController->addAccessRule($accessgroupName, $newAccessRight[0], $newAccessRight[1]);
					}
				}
				catch(CmdbAccessRuleAlreadyExistsException $e3)
				{
					printErrorMessage(sprintf(gettext("Error setting access rights for group %s. Because of dupplicate entries"), $accessgroupName));

				}
				catch(Exception $e2)
				{
					printErrorMessage(sprintf(gettext("Error setting access rights for access group %s"), $accessgroupName));
				}
			}
			catch(CmdbAccessRuleAlreadyExistsException $e3)
			{
				printErrorMessage(sprintf(gettext("Error updating access rights for access group %s. Because of dupplicate entries"), $accessgroupName));
			}
			break;

		case "deleteGroup":
			$accessgroupName = getHttpGetVar("name", "");
			try
			{
				$accessGroupController->deleteAccessGroup($accessgroupName);
				printInfoMessage(sprintf(gettext("access group %s successfully deleted"), $accessgroupName));
			}
			catch(CmdbAccessGroupNotFoundException $e)
			{
				printErrorMessage(sprintf(gettext("Error deleting access group %s"), $accessgroupName));
			}
			break;
	}

	//get data
	$accessgroups = $accessGroupController->getAccessgroups();

	//output: navigation
	echo "<div>";
	echo "<p>";
	echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#editAccessGroup\" data-dynform=\"admin/Authorisation.php?action=editGroupForm&amp;name=\">";
	echo "<span class=\"glyphicon glyphicon-plus\"></span>";
	echo gettext("add new access group")."</a>";
	echo "</p>";
	echo "</div>";

	//output: header
	echo "<h1 class=\"text-center\">".gettext("access rights management")."</h1>";

	//output: user table
	echo "<table class=\"table cmdb-cleantable\">";
	echo "<tr>";
	echo "<th>".gettext("access group")."</th>";
	echo "<th>&nbsp;</th>";
	echo "</tr>";
	foreach($accessgroups as $accessgroup)
	{
		$accessGroupName = $accessgroup->getName();
		$urlAccessDelete = "javascript:cmdbOpenUrlAjax('admin/Authorisation.php?action=deleteGroup&amp;name=$accessGroupName', '#adminAuthorisation', false, true)";
		$urlAccessEdit = "admin/Authorisation.php?action=editGroupForm&amp;name=$accessGroupName";

		echo "<tr>";
		echo "<td>$accessGroupName</td>";
		echo "<td>";
		echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#editAccessGroup\" data-dynform=\"$urlAccessEdit\">";
		echo "<span class=\"glyphicon glyphicon-pencil\" title=\"".gettext("edit")."\"></span></a>&nbsp;&nbsp;&nbsp;";
		echo "<a href=\"$urlAccessDelete\"><span class=\"glyphicon glyphicon-trash\" title=\"".gettext("delete")."\"></span></a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";


	//output: edit accessgroup form
	$urlFormEditGroup = "cmdbSubmitModal('#editAccessGroup', 'admin/Authorisation.php?' + $( '#adminAuthorisationEditGroupForm' ).serialize(), '#adminAuthorisation', false, true);";
	echo "<div class=\"modal fade\" id=\"editAccessGroup\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"editAccessGroupLabel\" aria-hidden=\"true\">";
	echo "<div class=\"modal-dialog\">";
	echo "<div class=\"modal-content\">";
	//form: header
	echo "<div class=\"modal-header\">";
	echo "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
	echo "<h4 class=\"modal-title\" id=\"editAccessGroupLabel\">".gettext("edit access group")."</h4>";
	echo "</div>";
	//form: body
	echo "<div class=\"modal-body\">";
	echo "<form id=\"adminAuthorisationEditGroupForm\" action=\"javascript:void(0);\" method=\"get\" accept-charset=\"UTF-8\">";
	echo "</form>";
	echo "</div>";
	//form: footer
	echo "<div class=\"modal-footer\">";
	echo "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">".gettext("cancel")."</button>";
	echo "<a href=\"#\" onClick=\"$urlFormEditGroup\" class=\"btn btn-danger\">".gettext("Go!")."</a>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
	echo "</div>";


?>
