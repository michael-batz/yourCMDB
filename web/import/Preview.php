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
* WebUI element: preview of import
* @author Michael Batz <michael@yourcmdb.org>
*/

	//save uploaded file in temp directory
	$paramFilename = "../tmp/".time().".import";
	move_uploaded_file($_FILES['file']['tmp_name'], $paramFilename);

	switch($paramFormat)
	{
		case "csv":
			importCsvPreview($paramType, $paramFilename);
			break;

	}



	//define functions
	function readCsv($file, $length, $delimiter, $enclosure)
	{
		if($enclosure != "")
		{
			return fgetcsv($file, $length, $delimiter, $enclosure);
		}
		else
		{
			return fgetcsv($file, $length, $delimiter);
		}
	}	

	function importCsvPreview($paramType, $paramFilename)
	{
		//access to global configuration variables
		global $config;

		//get CSV parameters
		$importOptions = $config->getDataExchangeConfig()->getImportOptions("csv");
		$delimiter = ';';
		$enclosure = "";
		if(isset($importOptions['delimiter']))
		{
			$delimiter = $importOptions['delimiter'];
		}
		if(isset($importOptions['enclosure']))
		{
			$enclosure = $importOptions['enclosure'];
		}

		//get first lines of csv file
		$data = Array();
		$file = fopen($paramFilename, "r");
		if($file != FALSE)
		{
		
			$rows = 0;
			while(($line = readCsv($file, 0, $delimiter, $enclosure)) !== FALSE)
			{
				if($rows >= 5)
				{
					break;
				}
				$data[] = $line;
				$rows++;
			}
			//get count of cols
			//use length of first line in csv file
			$cols = 0;
			if(isset($data[0]))
			{
				$cols = count($data[0]);
			}

			//close csv file
			fclose($file);

        		//generate output
			echo "<h1>";
			echo gettext("CSV Import - Preview (first lines of csv file)");
			echo "</h1>";
			echo "<table>";
			echo "<form action=\"import.php\" method=\"post\">";
			echo "<tr>";
			//output field mapping
			for($i = 0; $i < $cols; $i++)
			{
				echo "<td><select name=\"column$i\">";
				echo "<option></option>";
				foreach(array_keys($config->getObjectTypeConfig()->getFields($paramType)) as $objectFieldName)
				{
					echo "<option>$objectFieldName</option>";
				}
				echo "</select></td>";
			}
			echo "</tr>";
			//output csv preview data
			foreach($data as $line)
			{
				echo "<tr>";
				foreach($line as $field)
				{
					echo "<td>$field</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
			//output form
			echo "<p>";
			echo "<input type=\"hidden\" name=\"action\" value=\"import\" />";
			echo "<input type=\"hidden\" name=\"filename\" value=\"$paramFilename\" />";
			echo "<input type=\"hidden\" name=\"format\" value=\"csv\" />";
			echo "<input type=\"hidden\" name=\"type\" value=\"$paramType\" />";
			echo "<input type=\"hidden\" name=\"cols\" value=\"$cols\" />";

			echo gettext("Start in line ");
			echo "<select name=\"firstrow\">";
			for($i = 0; $i < $cols && $i < 5; $i++)
			{
				echo "<option>$i</option>";
			}
			echo "<input type=\"submit\" value=\"".gettext("Go")."\" />";
			echo "</p>";
			echo "</form>";
		}
		else
		{
			//print error
			$paramError = gettext("Could not read from uploaded file. Please check permissions.");
			include "import/Form.php";
		}

	}



?>

