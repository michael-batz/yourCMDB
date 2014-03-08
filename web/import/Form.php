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
* WebUI element: import actions
* @author Michael Batz <michael@yourcmdb.org>
*/

	//get import and export formats
	$importFormats = $config->getDataExchangeConfig()->getImportFormats();
	$exportFormats = $config->getDataExchangeConfig()->getExportFormats();

	//get objecttypes
	$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();

	//print messages if available
	if(isset($paramMessage) && $paramMessage != "")
	{
		printInfoMessage($paramMessage);
	}
	if(isset($paramError) && $paramError != "")
	{
		printErrorMessage($paramError);
	}

?>
	<!-- headline  -->
	<h1>Import and Export</h1>

	<!-- import box  -->
	<form action="import.php" enctype="multipart/form-data" method="post">
		<table class="cols2">
			<tr><th colspan="2">Import Objects</th></tr>
			<tr>
				<td>Object Type:</td>
				<td><select name="type">
					<?php
					foreach(array_keys($objectTypes) as $group)
					{
						echo "<optgroup label=\"$group\">";
						foreach($objectTypes[$group] as $type)
						{
							echo "<option>$type</option>";
						}
						echo "</optgroup>";
					}?>
				</select></td>
			</tr>
			<tr>
				<td>Import Format:</td>
				<td><select name="format">
					<?php
					foreach($importFormats as $importFormat)
					{
						echo "<option>$importFormat</option>";
					}?>
				</select></td>
			</tr>
			<tr>
				<td>Import File:</td>
				<td>
					<input type="hidden" name="action" value="preview" />
					<input type="file" name="file" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Go" /></td>
			</tr>
 		</table>
	</form>


	<!-- export box  -->
	<form action="export.php" method="get">
		<table class="cols2">
			<tr><th colspan="2">Export Objects</th></tr>
			<tr>
				<td>Object Type:</td>
				<td><select name="type">
					<?php
					foreach(array_keys($objectTypes) as $group)
					{
						echo "<optgroup label=\"$group\">";
						foreach($objectTypes[$group] as $type)
						{
							echo "<option>$type</option>";
						}
						echo "</optgroup>";
					}?>
				</select></td>
			</tr>
			<tr>
				<td>Output Format:</td>
				<td><select name="format">
					<?php
					foreach($exportFormats as $exportFormat)
					{
						echo "<option>$exportFormat</option>";
					}?>
				</select></td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Go" />
				</td>
			</tr>
		</table>
	</form>

