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
namespace yourCMDB\exporter;

use yourCMDB\entities\CmdbObject;

/**
* Export API - External System
* Dummy for external system to demonstrate the functionality of the
* export API
* Only prints some infomation about exportet objects to STDOUT
* The following export variables can be used
* - dummy1
* - dummy2
* - dummy3
* - dummy4
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExternalSystemDummy implements ExternalSystem
{
	//ExportDestination
	private $destination;

	//ExportVariables
	private $variables;

	public function setUp(ExportDestination $destination, ExportVariables $variables)
	{
		//save parameters
		$this->destination = $destination;
		$this->variables = $variables;

		//print info message
		echo "Export API Demo\n";
		echo "Dummy for external system\n";
		echo "prints only information about exported objects\n\n";

		//print parameters
		echo "External System: Dummy\n";
		echo "Defined Parameters:\n";
		foreach($destination->getParameterKeys() as $key)
		{
			echo "key $key:\t\t\tvalue ".$destination->getParameterValue($key)."\n";
		}
		echo "\n";
		
		//print export start
		echo "Start of dummy export\n";
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object)
	{
		//get object information
		$objectType = $object->getType();
		$objectId = $object->getId();
		$objectStatus = $object->getStatus();

		//get values of ExportVariables dummy1-dummy4
		$dummy1Val = "null";
		$dummy2Val = "null";
		$dummy3Val = "null";
		$dummy4Val = "null";
		$dummy1 = $this->variables->getVariable("dummy1");
		$dummy2 = $this->variables->getVariable("dummy2");
		$dummy3 = $this->variables->getVariable("dummy3");
		$dummy4 = $this->variables->getVariable("dummy4");
		if($dummy1 != null)
		{
			$dummy1Val = $dummy1->getValue($object);
		}
		if($dummy2 != null)
		{
			$dummy2Val = $dummy2->getValue($object);
		}
		if($dummy3 != null)
		{
			$dummy3Val = $dummy3->getValue($object);
		}
		if($dummy4 != null)
		{
			$dummy4Val = $dummy4->getValue($object);
		}

		//print object information and variable values
		echo "exporting object $objectId \tof type $objectType \tand status $objectStatus. ";
		echo "values of variables dummy1-4: $dummy1Val, $dummy2Val, $dummy3Val, $dummy4Val.\n";
	}

	public function finishExport()
	{
		//print export end
		echo "End of dummy export\n";
	}
}
?>
