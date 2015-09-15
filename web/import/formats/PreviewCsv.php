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
* WebUI element: preview of import for format CSV
* @author Michael Batz <michael@yourcmdb.org>
*/

	//parameters: $previewData, $importOptions

	//get
	$previewDataMaxCols = 0;
	foreach($previewData as $line)
	{
		if(count($line) > $previewDataMaxCols)
		{
			$previewDataMaxCols = count($line);
		}
	}
	
	//ToDo: check, if all required import options for preview was set

	//output: headline
	echo "<h1 class=\"text-center\">";
	echo gettext("CSV Import - Preview (first lines of csv file)");
	echo "</h1>";

	echo "<table class=\"table\">";
	echo "<form action=\"import.php\" method=\"post\">";

	//preview data and field mapping
	for($i = 0; $i < $previewDataMaxCols; $i++)
	{
		echo "<tr>";

		//show dropdown for each col
		echo "<td><select name=\"column$i\" class=\"form-control\">";
		echo "<option></option>";
		foreach(array_keys($config->getObjectTypeConfig()->getFields($paramType)) as $objectFieldName)
		{
			echo "<option>$objectFieldName</option>";
		}
		echo "</select></td>";

		//show preview data for each col
		foreach($previewData as $line)
		{
			echo "<td>";
			if(isset($line[$i]))
			{
				echo $line[$i];
			}
			echo "</td>";
		}
		echo "</tr>";
	}


	echo "</table>";
	echo "<p>";
	echo "<input type=\"hidden\" name=\"action\" value=\"import\" />";
	//echo "<input type=\"hidden\" name=\"filename\" value=\"$paramFilename\" />";
	echo "<input type=\"hidden\" name=\"format\" value=\"csv\" />";
	//echo "<input type=\"hidden\" name=\"type\" value=\"$paramType\" />";
	//echo "<input type=\"hidden\" name=\"cols\" value=\"$cols\" />";


	echo gettext("Start in line ");
	echo "<select name=\"firstrow\">";
	for($i = 0; $i < count($previewData) && $i < 5; $i++)
	{
		echo "<option>$i</option>";
	}
	echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
	echo "</p>";
	echo "</form>";



?>

