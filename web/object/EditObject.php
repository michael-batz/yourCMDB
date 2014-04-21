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
	$textTitle = "Edit Object $paramType:$paramId";
	$formAction = "object.php?action=save&amp;type=$paramType&amp;id=$paramId";
	if($paramAction == "add")
	{
		$textTitle = "Add $paramType Object";
		$formAction = "object.php?action=saveNew&amp;type=$paramType";
	}
	$checkboxString = "<input type=\"checkbox\" name=\"yourCMDB_active\" value=\"A\" checked=\"checked\" />";
	if($sourceObject != null && $sourceObject->getStatus() != 'A')
	{
		$checkboxString = "<input type=\"checkbox\" name=\"yourCMDB_active\" value=\"A\" />";
	}
	
?>

	<!-- title -->
	<div class="objectbox">
	<h1><?php echo $textTitle; ?></h1>
	<form method="post" action="<?php echo $formAction; ?>" accept-charset="UTF-8">


	<!-- set object active/inactive -->
	<table class="cols2">
	<tr>
		<th colspan="2">Object Status</th>
	</tr>
	<tr>
		<td>active</td>
		<td><?php echo $checkboxString; ?></td>
	</tr>
	</table>

	<!-- object fields -->
	<?php
	foreach($config->getObjectTypeConfig()->getFieldGroups($paramType) as $groupname)
	{ ?>
		<table class="cols2">
		<tr>
			<th colspan="2"><?php echo $groupname;?></th>
		</tr>
		<?php
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
		?>
			<tr>
				<td><?php echo $fieldLabel;?>:</td>
				<td><?php showFieldForDataType($paramType, $fieldName, $fieldValue, $fieldType); ?></td>
			</tr>
		<?php
		} ?>
                </table>
	<?php
	} ?>

	
		<p>
			<input type="submit" value="Go" />
		</p>
	</form>
	</div>
