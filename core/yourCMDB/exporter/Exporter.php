<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2016 Michael Batz
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
namespace yourCMDB\exporter;

use yourCMDB\config\CmdbConfig;
use yourCMDB\controller\ObjectController;
use yourCMDB\orm\OrmController;

/**
* Exporter for yourCMDB
* @author Michael Batz <michael@yourcmdb.org>
*/

class Exporter
{
	//exporter task
	private $task;

	//exporter sources
	private $exportSources;

	//exporter destinationa
	private $exportDestinations;

	//exporter variables
	private $exportVariables;


	function __construct($taskname)
	{
		//create configuration object
		$config = CmdbConfig::create();

		//get configuration for exporter task
		$this->task = $taskname;
		$this->exportSources = $config->getExporterConfig()->getSourcesForTask($taskname);
		$this->exportDestinations = $config->getExporterConfig()->getDestinationsForTask($taskname);
		$this->exportVariables = $config->getExporterConfig()->getVariablesForTask($taskname);

		//run export
		$this->runExport();
	}

	/**
	* Exports the yourCMDB data
	*
	*/
	private function runExport()
	{
		//get ObjectController
		$objectController = ObjectController::create();

		//walk through all ExportDestinations
		foreach($this->exportDestinations as $exportDestinationObj)
		{
	
			//set up exportDestination
			$exportDestinationClass = __NAMESPACE__."\\".$exportDestinationObj->getClass();
			$exportDestination = new $exportDestinationClass();
			$exportDestination->setUp($exportDestinationObj, $this->exportVariables);
			
	
			//walk through all ExportSources
			foreach($this->exportSources as $exportSource)
			{
				//get objects to export
				$objects = Array();
				$exportSourceFieldname = $exportSource->getFieldname();
				$exportSourceFieldvalue = $exportSource->getFieldvalue();
				$exportSourceObjectTypes = array($exportSource->getObjectType());
				$exportSourceStatusActive = $exportSource->getStatus();
				if($exportSourceFieldname == null || $exportSourceFieldvalue == null )
				{
					$objects = $objectController->getObjectsByType($exportSourceObjectTypes[0], "", "ASC", $exportSourceStatusActive, 0, 0, "yourCMDB-exporter");
				}
				else
				{
					$objects = $objectController->getObjectsByField($exportSourceFieldname, 
											$exportSourceFieldvalue, 
											$exportSourceObjectTypes, 
											$exportSourceStatusActive,
											0,0, "yourCMDB-exporter");
				}
	
				//export objects
				foreach($objects as $object)
				{
					$exportDestination->addObject($object);
				}
			}
	
			//finish export
			$exportDestination->finishExport();
		}
	}
}
?>
