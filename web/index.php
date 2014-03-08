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

	//get header
	include "include/header.inc.php";

	//max rows per box
	$rowCount = 5;

	//get data
	$newestObjects = $datastore->getNNewestObjects($rowCount);
	$lastChangedObjects = $datastore->getNLastChangedObjects($rowCount);

	//urls
	$urlShowObject = "object.php?action=show&amp;id=";
	$urlEditObject = "object.php?action=edit&amp;id=";

?>

	<!-- title -->
	<h1>Welcome to yourCMDB!</h1>


	<!-- newest objects -->
	<table class="list">
		<tr>
			<th class="center" colspan="4"><?php echo $rowCount; ?> Newest Objects</th>
		</tr>
		<?php
		//get objecttypes and objectcount
		foreach($newestObjects as $objectEntry)
		{
			$object = $objectEntry[0];
			$objectDate = $objectEntry[1];
			$objectId = $object->getId();
			$objectType = $object->getType();

			$urlShowObjectId = "$urlShowObject$objectId";
			$urlEditObjectId = "$urlEditObject$objectId&amp;type=$objectType";

			//get object status icon
			$statusIcon = "<img src=\"img/icon_active.png\" alt=\"active\" title=\"active object\" />";
			if($object->getStatus() != 'A')
			{
				$statusIcon = "<img src=\"img/icon_inactive.png\" alt=\"inactive\" title=\"inactive object\" />";
			}


			?>
			<tr>
				<td><?php echo "$statusIcon $objectId";?></td>
				<td><?php echo $objectType;?></td>
				<td><?php echo $objectDate;?></td>
				<td class="right">
					<a href="<?php echo $urlShowObjectId; ?>"><img src="img/icon_show.png" title="show" alt="show" /></a>&nbsp;&nbsp;&nbsp;
					<a href="<?php echo $urlEditObjectId; ?>"><img src="img/icon_edit.png" title="edit" alt="edit" /></a>
				</td>

			</tr>
		<?php
		}?>
	</table>


	<!-- last changed objects -->
	<table class="list">
		<tr>
			<th class="center" colspan="4"><?php echo $rowCount; ?> Last Changed Objects</th>
		</tr>
		<?php
		//get objecttypes and objectcount
		foreach($lastChangedObjects as $objectEntry)
		{
			$object = $objectEntry[0];
			$objectDate = $objectEntry[1];
			$objectId = $object->getId();
			$objectType = $object->getType();

			$urlShowObjectId = "$urlShowObject$objectId";
			$urlEditObjectId = "$urlEditObject$objectId&amp;type=$objectType";

			//get object status icon
			$statusIcon = "<img src=\"img/icon_active.png\" alt=\"active\" title=\"active object\" />";
			if($object->getStatus() != 'A')
			{
				$statusIcon = "<img src=\"img/icon_inactive.png\" alt=\"inactive\" title=\"inactive object\" />";
			}


			?>
			<tr>
				<td><?php echo "$statusIcon $objectId";?></td>
				<td><?php echo $objectType;?></td>
				<td><?php echo $objectDate;?></td>
				<td class="right">
					<a href="<?php echo $urlShowObjectId; ?>"><img src="img/icon_show.png" title="show" alt="show" /></a>&nbsp;&nbsp;&nbsp;
					<a href="<?php echo $urlEditObjectId; ?>"><img src="img/icon_edit.png" title="edit" alt="edit" /></a>
				</td>

			</tr>
		<?php
		}?>
	</table>

<?php 
//include footer
include "include/footer.inc.php";
?>
