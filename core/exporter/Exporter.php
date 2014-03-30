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
* Exporter for yourCMDB
* @author Michael Batz <michael@yourcmdb.org>
*/

class Exporter
{
	//datastore
	private $datastore;

	//configuration
	private $config;

	//exporter task
	private $task;

	//exporter sources
	private $exportSources;

	//exporter destination
	private $exportDestination;

	//exporter variables
	private $exportVariables;


	function __construct($taskname)
	{

		//create configuration object
		$this->config = new CmdbConfig();

		//create datastore object
		$datastoreClass = $this->config->getDatastoreConfig()->getClass();
		$this->datastore = new $datastoreClass;

		//get configuration for exporter task
		//ToDo: error handling
		$this->task = $taskname;
		$this->exportSources = $this->config->getExporterConfig()->getSourcesForTask($taskname);
		$this->exportDestination = $this->config->getExporterConfig()->getDestinationForTask($taskname);
		$this->exportVariables = $this->config->getExporterConfig()->getVariablesForTask($taskname);


		//run export
		$this->runExport();
	}

	/**
	* Exports the yourCMDB data
	*
	*/
	private function runExport()
	{
		//set up exportDestination
		$exportDestinationClass = $this->exportDestination->getClass();
		$exportDestination = new $exportDestinationClass();
		$exportDestination->setUp($this->exportDestination, $this->exportVariables);
		

		//walk through all ExportSources
		foreach($this->exportSources as $exportSource)
		{
			//get objects to export
			$objects = Array();
			$exportSourceFieldname = $exportSource->getFieldname();
			$exportSourceFieldvalue = $exportSource->getFieldvalue();
			$exportSourceObjectTypes = array($exportSource->getObjectType());
			$exportSourceStatusActive = true;
			if($exportSource->getStatus() == "N")
			{
				$exportSourceStatusActive = false;
			}
			if($exportSourceFieldname == null || $exportSourceFieldvalue == null )
			{
				$objects = $this->datastore->getObjectsByType($exportSourceObjectTypes[0], "", "asc", $exportSourceStatusActive);
			}
			else
			{
				$objects = $this->datastore->getObjectsByField($exportSourceFieldname, $exportSourceFieldvalue, $exportSourceObjectTypes, $exportSourceStatusActive);
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
?>
