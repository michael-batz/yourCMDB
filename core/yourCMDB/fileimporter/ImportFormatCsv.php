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

use yourCMDB\config\CmdbConfig;
use yourCMDB\entities\CmdbObject;
use yourCMDB\controller\ObjectController;
use yourCMDB\exceptions\CmdbObjectNotFoundException;

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
	
	public function import()
	{
		//check if required options are set
		$optionStart = $this->importOptions->getOptionValue("start", "0");
		$optionLength = $this->importOptions->getOptionValue("length", "0");
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
		$config = CmdbConfig::create();

		//get mapping of csv columns to object fiels
		$objectFieldConfig = $config->getObjectTypeConfig()->getFields($optionType);
		$objectFieldMapping = Array();
		$foreignKeyMapping = Array();
		$assetIdMapping = -1;
		for($i = 0; $i < $optionCols; $i++)
		{
			$fieldname = $this->importOptions->getOptionValue("column$i", "");
			//assetId mapping
			if($fieldname == "yourCMDB_assetid")
			{
				$assetIdMapping = $i;
			}
			//foreign key mapping
			elseif(preg_match('#^yourCMDB_fk_(.*)/(.*)#', $fieldname, $matches) == 1)
			{
				$foreignKeyField = $matches[1];
				$foreignKeyRefField = $matches[2];
				$foreignKeyMapping[$foreignKeyField][$foreignKeyRefField] = $i;
			}
			//fielf mapping
			elseif($fieldname != "")
			{
				$objectFieldMapping[$fieldname] = $i;
			}
		}

		//open file		
		$csvFile = fopen($this->importFilename, "r");
		if($csvFile == FALSE)
		{
			throw new FileImportException(gettext("Could not open file for import."));
		}

		//create or update objects for each line in csv file
		$i = 0;
		while(($line = $this->readCsv($csvFile, 0, $optionDelimiter, $optionEnclosure)) !== FALSE)
		{
			//
			if($i >= ($optionLength + $optionStart) && $optionLength != 0)
			{
				break;
			}

			//check start of import
			if($i >= $optionStart)
			{
				//generate object fields
				$objectFields = Array();
				foreach(array_keys($objectFieldMapping) as $objectField)
				{
					$objectFields[$objectField] = $line[$objectFieldMapping[$objectField]];
				}

				//resolve foreign keys
				foreach(array_keys($foreignKeyMapping) as $foreignKey)
				{
					foreach(array_keys($foreignKeyMapping[$foreignKey]) as $foreignKeyRefField)
					{
						//set foreign key object type
						$foreignKeyType = Array(preg_replace("/^objectref-/", "", $objectFieldConfig[$foreignKey]));
						$foreignKeyLinePosition = $foreignKeyMapping[$foreignKey][$foreignKeyRefField];
						$foreignKeyRefFieldValue = $line[$foreignKeyLinePosition];

						//get object defined by foreign key
						$foreignKeyObjects = $objectController->getObjectsByField(	$foreignKeyRefField, 
														$foreignKeyRefFieldValue, 
														$foreignKeyType, 
														null, 0, 0, "yourCMDB fileimporter");
						//if object was found, set ID as fieldvalue
						if(isset($foreignKeyObjects[0]))
						{
							$objectFields[$foreignKey] = $foreignKeyObjects[0]->getId();
						}
					}
				}

				//check if assetID is set in CSV file for updating objects
				if($assetIdMapping != -1)
				{
					$assetId = $line[$assetIdMapping];
					try
					{
						$objectController->updateObject($assetId, 'A', $objectFields, "yourCMDB Fileimporter");
					}
					catch(CmdbObjectNotFoundException $e)
					{
						//if object was not found, add new one
						$objectController->addObject($optionType, 'A', $objectFields, "yourCMDB Fileimporter");
					}
				}
				//if not, create a new object
				else
				{
					//generate object and save to datastore
					$objectController->addObject($optionType, 'A', $objectFields, "yourCMDB Fileimporter");
				}
			}

			//increment counter
			$i++;
		}

		//check, if CSV file could be deleted
		$deleteFile = false;
		if(feof($csvFile))
		{
			$deleteFile = true;
		}

		//close file
		fclose($csvFile);

		//delete file from server
		if($deleteFile)
		{
			unlink($this->importFilename);
		}

		//return imported objects
		return $i;
	}

	public function getObjectsToImportCount()
	{
		//open file		
		$csvFile = fopen($this->importFilename, "r");
		if($csvFile == FALSE)
		{
			throw new FileImportException(gettext("Could not open file for import."));
		}

		//count lines
		$lines = 0;
		while(($line = fgets($csvFile)) !== FALSE)
		{
			$lines++;
		}

		//close file
		fclose($csvFile);

		//return result
		return $lines;
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