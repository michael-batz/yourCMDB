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
* WebUI element: form for adding new object
* @author Michael Batz <michael@yourcmdb.org>
*/


	//get data
	$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();
?>
	<!-- headline -->
	<h1>New Object</h1>

	<form action="object.php" method="get" accept-charset="UTF-8">
		<table class="cols2">
			<tr><th colspan="2">New Object</th></tr>
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
				<td colspan="2">
					<input type="hidden" name="action" value="add" />
					<input type="submit" value="Go" />
				</td></tr>
		</table>
	</form>

