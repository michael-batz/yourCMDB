<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2014 Michael Batz
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
* Shows a form to add or edit an object of a specific type
* @author Michael Batz <michael@yourcmdb.org>
*/
	
	//get source object
	$sourceObject = null;
	if($paramId != 0)
	{
		try
		{
			$sourceObject = $datastore->getObject($paramId);
		}
		catch(NoSuchObjectException $e)
		{
			;
		}
	}

	//create output strings
	$textTitle = sprintf(gettext("Edit Object %s:%s"), $paramType, $paramId);
	$formAction = "object.php?action=save&amp;type=$paramType&amp;id=$paramId";
	if($paramAction == "add")
	{
		$textTitle = sprintf(gettext("Add %s Object"), $paramType);
		$formAction = "object.php?action=saveNew&amp;type=$paramType";
	}
	$checkboxString = "<input type=\"checkbox\" name=\"yourCMDB_active\" value=\"A\" checked=\"checked\" />";
	if($sourceObject != null && $sourceObject->getStatus() != 'A')
	{
		$checkboxString = "<input type=\"checkbox\" name=\"yourCMDB_active\" value=\"A\" />";
	}
	

	//<!-- title -->
	echo "<div class=\"objectbox\">";
	echo "<h1>$textTitle</h1>";
	echo "<form method=\"post\" action=\"$formAction\" accept-charset=\"UTF-8\">";


	//<!-- set object active/inactive -->
	echo "<table class=\"cols2\">";
	echo "<tr>";
	echo "<th colspan=\"2\">";
	echo gettext("Object Status");
	echo "</th>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>active</td>";
	echo "<td>$checkboxString</td>";
	echo "</tr>";
	echo "</table>";

	//<!-- object fields -->
	foreach($config->getObjectTypeConfig()->getFieldGroups($paramType) as $groupname)
	{
		echo "<table class=\"cols2\">";
		echo "<tr>";
		echo "<th colspan=\"2\">$groupname</th>";
		echo "</tr>";
		foreach(array_keys($config->getObjectTypeConfig()->getFieldGroupFields($paramType, $groupname)) as $field)
		{
			$fieldName = $field;
			$fieldLabel = $config->getObjectTypeConfig()->getFieldLabel($paramType, $field);
			$fieldType = $config->getObjectTypeConfig()->getFieldType($paramType, $field);
			$fieldValue = "";
			if($sourceObject != null)
			{
				$fieldValue = $sourceObject->getFieldValue($fieldName);
			}
			echo "<tr>";
			echo "<td>$fieldLabel:</td>";
			echo "<td>";
			echo showFieldForDataType($paramType, $fieldName, $fieldValue, $fieldType);
			echo "</td>";
			echo "</tr>";
		}
                echo "</table>";
	}

	
	echo "<p>";
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</p>";
	echo "</form>";
	echo "</div>";
?>
