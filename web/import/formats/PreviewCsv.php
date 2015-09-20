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

	//required parameters: $previewData, $importOptions, $paramFilename

	//output: headline
	echo "<h1 class=\"text-center\">";
	echo gettext("CSV Import - Preview");
	echo "</h1>";
	echo "<form action=\"import.php\" method=\"post\" class=\"form-horizontal\">";

	//check, if previewData could be fetched
	if($previewData != null)
	{
		//load options
		$optionType = $importOptions->getOptionValue("objectType", "");
		$optionDelimiter = $importOptions->getOptionValue("delimiter", ";");
		$optionEnclosure = $importOptions->getOptionValue("enclosure", "");

		//get
		$previewDataMaxCols = 0;
		foreach($previewData as $line)
		{
			if(count($line) > $previewDataMaxCols)
			{
				$previewDataMaxCols = count($line);
			}
		}
	
		//preview data and field mapping
		echo "<table class=\"table\">";
		for($i = 0; $i < $previewDataMaxCols; $i++)
		{
			echo "<tr>";

			//get value in line0
			$valueLine0 = "";
			if(isset($previewData[0][$i]))
			{
				$valueLine0 = $previewData[0][$i];
			}

			//show dropdown for each col
			echo "<td><select name=\"column$i\" class=\"form-control\">";
			echo "<option></option>";
			echo "<option>yourCMDB_assetid</option>";
			foreach(array_keys($config->getObjectTypeConfig()->getFields($optionType)) as $objectFieldName)
			{
				if($valueLine0 == $objectFieldName)
				{
					echo "<option selected=\"selected\">$objectFieldName</option>";
				}
				else
				{
					echo "<option>$objectFieldName</option>";
				}
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
		echo "<input type=\"hidden\" name=\"objectType\" value=\"$optionType\" />";
		echo "<input type=\"hidden\" name=\"delimiter\" value=\"$optionDelimiter\" />";
		echo "<input type=\"hidden\" name=\"enclosure\" value=\"$optionEnclosure\" />";
		echo "<input type=\"hidden\" name=\"cols\" value=\"$previewDataMaxCols\" />";
	
		echo "<div class=\"form-group\">";
		echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("Start in line:")."</label>";
		echo "<div class=\"col-md-4\">";
		echo "<select name=\"start\" class=\"form-control\">";
		for($i = 0; $i < count($previewData) && $i < 5; $i++)
		{
			echo "<option>$i</option>";
		}
		echo "</select>";
		echo "</div>";
		echo "</div>";
	}
	//if previewData could not be fetched
	else
	{
		//show options
		$objectTypes = $config->getObjectTypeConfig()->getObjectTypeGroups();

		//option: object type	
		echo "<div class=\"form-group\">";
		echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("Object Type:")."</label>";
		echo "<div class=\"col-md-4\">";
		echo "<select name=\"objectType\" class=\"form-control\">";
		foreach(array_keys($objectTypes) as $group)
		{
			echo "<optgroup label=\"$group\">";
			foreach($objectTypes[$group] as $type)
			{
				echo "<option>$type</option>";
			}
			echo "</optgroup>";
		}
		echo "</select>";
		echo "</div>";
		echo "</div>";

		//option: csv delimiter
		echo "<div class=\"form-group\">";
		echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("CSV delimiter")."</label>";
		echo "<div class=\"col-md-4\">";
		echo "<input type=\"text\" name=\"delimiter\" value=\";\" />";
		echo "</div>";
		echo "</div>";

		//option: csv enclosure
		echo "<div class=\"form-group\">";
		echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("CSV enclosure")."</label>";
		echo "<div class=\"col-md-4\">";
		echo "<input type=\"text\" name=\"enclosure\" value=\"\" />";
		echo "</div>";
		echo "</div>";

		//hidden form data
		echo "<input type=\"hidden\" name=\"action\" value=\"preview\" />";
		
	}

	//hidden form data
	echo "<input type=\"hidden\" name=\"filename\" value=\"$paramFilename\" />";
	echo "<input type=\"hidden\" name=\"format\" value=\"ImportFormatCsv\" />";
	//ToDo: make length configurable
	echo "<input type=\"hidden\" name=\"length\" value=\"10\" />";

	//form submit
	echo "<div class=\"form-group\">";
	echo "<div class=\"col-md-4 col-md-offset-5\">";
	echo "<input type=\"submit\" class=\"btn btn-danger\" value=\"".gettext("Go")."\" />";
	echo "</div>";
	echo "</div>";

	echo "</form>";

?>

