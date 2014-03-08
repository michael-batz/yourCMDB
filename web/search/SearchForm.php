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
* WebUI element: search form
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get data
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
	<h1>Search</h1>

	<!-- search for objects with a specific field value and type -->
	<form action="search.php" method="get" accept-charset="UTF-8">
		<table class="cols2">
			<tr><th colspan="2">Search in all objects</th></tr>
			<tr>
				<td>Searchstring</td>
				<td><input type="text" name="searchstring" /></td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Go" />
				</td>
			</tr>
		</table>
	</form>



	<!-- search for objects with a specific field value and type -->
	<form action="search.php" method="get" accept-charset="UTF-8">
		<table class="cols2">
			<tr><th colspan="2">Search for objects of a specific type</th></tr>
			<tr>
				<td>Type:</td>
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
				<td>Searchstring:</td>
				<td><input type="text" name="searchstring" /></td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Go" />
				</td>
			</tr>
		</table>
	</form>


	<!-- search for objects with a specific field value and object type group -->
	<form action="search.php" method="get" accept-charset="UTF-8">
		<table class="cols2">
			<tr><th colspan="2">Search for objects of a specific group</th></tr>
			<tr>
				<td>Type:</td>
				<td><select name="typegroup">
					<?php
					foreach(array_keys($objectTypes) as $group)
					{
						echo "<option>$group</option>";
					}?>
				</select></td>
			</tr>
			<tr>
				<td>Searchstring:</td>
				<td><input type="text" name="searchstring" /></td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Go" />
				</td>
			</tr>
		</table>
	</form>
