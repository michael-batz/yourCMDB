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
	//start: panel
	echo "<div class=\"container\">";
	echo "<div class=\"panel panel-default cmdb-contentpanel\">";

	//panel headline
	echo "<div class=\"panel-heading\">";
	echo "<h3 class=\"panel-title text-center\">";
	echo gettext("CSV Import - Preview");
	echo "</h3>";
	echo "</div>";

	//start panel content
	echo "<div class=\"panel-body\">";
	echo "<form action=\"import.php\" method=\"post\" class=\"form-horizontal\">";

	//check, if previewData could be fetched
	if($previewData != null)
	{
		//load options
		$optionType = $importOptions->getOptionValue("objectType", "");
		$optionDelimiter = $importOptions->getOptionValue("delimiter", ";");
		$optionEnclosure = $importOptions->getOptionValue("enclosure", "");
		$optionLength = $importOptions->getOptionValue("length", "10");

		//get preview data
		$previewDataMaxCols = 0;
		foreach($previewData as $line)
		{
			if(count($line) > $previewDataMaxCols)
			{
				$previewDataMaxCols = count($line);
			}
		}
	
		//generate field mapping
		$objectFieldsConfig = $config->getObjectTypeConfig()->getFields($optionType);
		$objectFields = Array();
		$foreignKeys = Array();
		foreach(array_keys($objectFieldsConfig) as $objectFieldConfig)
		{
			//create fieldmapping
			$objectFields[] = $objectFieldConfig;
			
			//check, if type is objectref and add to foreignKeys
			$objectFieldType = $objectFieldsConfig[$objectFieldConfig];
			if(preg_match('/^objectref-(.*)/', $objectFieldType, $matches) == 1)
			{
				$referenceType = $matches[1];
				$referenceFields = array_keys($config->getObjectTypeConfig()->getFields($referenceType));
				$foreignKeys[$objectFieldConfig] = $referenceFields;
			}

		}

		//output preview data
		echo "<h2>".gettext("first lines of csv file")."</h2>";
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
			echo "<td><select name=\"column$i\" class=\"form-control\" id=\"column$i\">";
			echo "<option></option>";
			echo "<option value=\"yourCMDB_assetid\">AssetID</option>";
			//dropdown: object fields
			echo "<optgroup label=\"".gettext("Object Fields")."\">";
			foreach($objectFields as $objectFieldName)
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
			echo "</optgroup>";
			//dropdown: foreign keys
			foreach(array_keys($foreignKeys) as $foreignKey)
			{
				echo "<optgroup label=\"".gettext("foreign key for field: ")."$foreignKey\">";
				foreach($foreignKeys[$foreignKey] as $referenceFieldName)
				{
					echo "<option value=\"yourCMDB_fk_$foreignKey/$referenceFieldName\">$foreignKey/$referenceFieldName</option>";
				}
				echo "</optgroup>";
			}
			
			echo "</select>";
			echo "</td>";
	
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
		echo "<input type=\"hidden\" name=\"action\" value=\"import\" />";
		echo "<input type=\"hidden\" name=\"objectType\" value=\"$optionType\" />";
		echo "<input type=\"hidden\" name=\"delimiter\" value=\"$optionDelimiter\" />";
		echo "<input type=\"hidden\" name=\"enclosure\" value=\"$optionEnclosure\" />";
		echo "<input type=\"hidden\" name=\"length\" value=\"$optionLength\" />";
		echo "<input type=\"hidden\" name=\"cols\" value=\"$previewDataMaxCols\" />";
	
		echo "<h2>".gettext("further options")."</h2>";
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

		//option: import batch size
		echo "<div class=\"form-group\">";
		echo "<label class=\"col-md-2 col-md-offset-3 control-label\">".gettext("Import Batch Size")."</label>";
		echo "<div class=\"col-md-4\">";
		echo "<input type=\"text\" name=\"length\" value=\"10\" />";
		echo "</div>";
		echo "</div>";

		//hidden form data
		echo "<input type=\"hidden\" name=\"action\" value=\"preview\" />";
		
	}

	//hidden form data
	echo "<input type=\"hidden\" name=\"filename\" value=\"$paramFilename\" />";
	echo "<input type=\"hidden\" name=\"format\" value=\"ImportFormatCsv\" />";

	//form submit
	echo "<div class=\"form-group\">";
	echo "<div class=\"col-md-4 col-md-offset-5\">";
	echo "<input type=\"submit\" class=\"btn btn-danger\" value=\"".gettext("Go")."\" />";
	echo "</div>";
	echo "</div>";

	echo "</form>";

	//close panel body and panel
	echo "</div>";
	echo "</div>";
	echo "</div>";

?>

