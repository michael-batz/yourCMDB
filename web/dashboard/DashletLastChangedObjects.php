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
* yourCMDB WebUI Dashboard: dashlet that show the last changed objects
* @author: Michael Batz <michael@yourcmdb.org>
*/

	$objects = $objectController->getLastChangedObjects(null, 10, 0, $authUser);
	
	echo "<h1 class=\"text-center\">".gettext("last changed objects")."</h1>";
	echo "<table class=\"table cmdb-cleantable\">";
	echo "<tr>";
	echo "<th>".gettext("AssetID")."</th>";
	echo "<th>".gettext("type")."</th>";
	echo "<th>".gettext("date changed")."</th>";
	echo "<th>".gettext("user")."</th>";
	echo "<th>".gettext("action")."</th>";
	echo "</tr>";

	//walk through all objects
	foreach($objects as $object)
	{
		//get data
		$objectId = $object->getId();
		$objectType = $object->getType();
		$objectChangedTime = "---";
		$objectChangedUser = "---";
		$objectLogEntryChanged = $objectLogController->getChangedLogEntry($object, $authUser);
		if($objectLogEntryChanged != null)
		{
			$objectChangedTime = $objectLogEntryChanged->getTimestamp()->format("d.m.Y H:i");
			$objectChangedUser = $objectLogEntryChanged->getUser();
		}
		$statusIcon = "<span class=\"label label-success\" title=\"".gettext("active object")."\">A</span>";
		if($object->getStatus() != 'A')
		{
			$statusIcon = "<span class=\"label label-danger\" title=\"".gettext("inactive object")."\">N</span>";
		}
		$urlShowObject = "object.php?action=show&amp;id=$objectId";

		//output
		echo "<tr>";
		echo "<td class=\"cmdb-nowrap\">$statusIcon $objectId</td>";
		echo "<td>$objectType</td>";
		echo "<td>$objectChangedTime</td>";
		echo "<td>$objectChangedUser</td>";
		echo "<td><a href=\"$urlShowObject\"><span class=\"glyphicon glyphicon-eye-open\"></span></a></td>";
		echo "</tr>";

	}

	echo "</table>";

?>
