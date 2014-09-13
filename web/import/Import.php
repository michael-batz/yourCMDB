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
* WebUI element: import from file after preview
* @author Michael Batz <michael@yourcmdb.org>
*/

	//switch format
	switch($paramFormat)
	{
		case "csv":
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

			//get mapping of csv columns to object fiels
			$objectFieldMapping = Array();
			for($i = 0; $i < $paramCols; $i++)
			{
				if(getHttpPostVar("column$i", "") != "")
				{
					$objectFieldMapping[getHttpPostVar("column$i", "")] = $i;
				}
			}


			//read from csv file
			$file = fopen($paramFilename, "r");
			if($file != FALSE)
			{
				//create objects for each line in csv file
				$i = 0;
				$j = 0;
				while(($line = readCsv($file, 0, $delimiter, $enclosure)) !== FALSE)
				{
					//check start of import
					if($i >= $paramFirstRow)
					{
						//generate object fields
						$objectFields = Array();
						foreach(array_keys($objectFieldMapping) as $objectField)
						{
							$objectFields[$objectField] = $line[$objectFieldMapping[$objectField]];
						}

						//generate object and save to datastore
						$cmdbObject = new CmdbObject($paramType, $objectFields);
						$assetId = $datastore->addObject($cmdbObject);
						$j++;
					}

					//increment counter
					$i++;
				}

				//delete csv file from server
				fclose($file);
				unlink($paramFilename);

				//generate output
				$paramMessage = sprintf(gettext("Import of %s objects was successful"),$j);

                	}
			else
			{
				$paramError = gettext("Could not read from CSV file.");
			}
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

?>

