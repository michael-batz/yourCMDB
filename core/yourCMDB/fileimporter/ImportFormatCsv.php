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
namespace yourCMDB\fileimporter;

use yourCMDB\entities\CmdbObject;
use yourCMDB\controller\ObjectController;

/**
* File Importer - ImportFormat: CSV
* @author Michael Batz <michael@yourcmdb.org>
*/
class ImportFormatCsv extends ImportFormat
{
	public static function getFormatName()
	{
		return "CSV";
	}

	public function getPreviewData()
	{
		//check if required options are set
		$optionDelimiter = $this->importOptions->getOptionValue("delimiter", ";");
		$optionEnclosure = $this->importOptions->getOptionValue("enclosure", "");
		$optionType = $this->importOptions->getOptionValue("objectType", "");
		if($optionType == "")
		{
			throw new FileImportOptionsRequiredException(gettext("Missing option objectType for file import"));
		}


		$output = Array();

		//open file		
		$csvFile = fopen($this->importFilename, "r");
		if($csvFile == FALSE)
		{
			throw new FileImportException(gettext("Could not open file for import."));
		}

		//read max 5 lines from CSV file
		$rows = 0;
		while(($line = $this->readCsv($csvFile, 0, $optionDelimiter, $optionEnclosure)) !== FALSE)
		{
			if($rows >= 5)
			{
				break;
			}
			$output[] = $line;
			$rows++;
		}

		//close file
		fclose($csvFile);

		return $output;
	}
	
	public function import($start=0, $length=0)
	{
		//ToDo: partial import

		//check if required options are set
		$optionFirstrow = $this->importOptions->getOptionValue("firstrow", "0");
		$optionCols = $this->importOptions->getOptionValue("cols", "0");
		$optionDelimiter = $this->importOptions->getOptionValue("delimiter", ";");
		$optionEnclosure = $this->importOptions->getOptionValue("enclosure", "");
		$optionType = $this->importOptions->getOptionValue("objectType", "");
		if($optionType == "")
		{
			throw new FileImportOptionsRequiredException(gettext("Missing option objectType for file import"));
		}

		//create object controller
		$objectController = ObjectController::create();

		//get mapping of csv columns to object fiels
		$objectFieldMapping = Array();
		for($i = 0; $i < $optionCols; $i++)
		{
			if($this->importOptions->getOptionValue("column$i", "") != "")
			{
				$objectFieldMapping[$this->importOptions->getOptionValue("column$i", "")] = $i;
			}
		}

		//open file		
		$csvFile = fopen($this->importFilename, "r");
		if($csvFile == FALSE)
		{
			throw new FileImportException(gettext("Could not open file for import."));
		}

		//create objects for each line in csv file
		$i = 0;
		$j = 0;
		while(($line = $this->readCsv($csvFile, 0, $optionDelimiter, $optionEnclosure)) !== FALSE)
		{
			//check start of import
			if($i >= $optionFirstrow)
			{
				//generate object fields
				$objectFields = Array();
				foreach(array_keys($objectFieldMapping) as $objectField)
				{
					$objectFields[$objectField] = $line[$objectFieldMapping[$objectField]];
				}

				//generate object and save to datastore
				$objectController->addObject($optionType, 'A', $objectFields, "yourCMDB Fileimporter");
				$j++;
			}

			//increment counter
			$i++;
		}

		//close file
		fclose($csvFile);

		//delete file from server
		unlink($this->importFilename);

		//return imported objects
		return $j;
	}

	private function readCsv($file, $length, $delimiter, $enclosure)
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

}
?>
