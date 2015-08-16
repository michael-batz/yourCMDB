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
* Export API - External System: ansible dynamic inventory
* Creates a dynamic inventory for ansible
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExternalSystemAnsible implements ExternalSystem
{
	//ExportVariables
	private $variables;

	//store for created ansible groups
	private $ansibleGroups;

	//store for ansible hostvars
	private $ansibleHostvars;

	public function setUp(ExportDestination $destination, ExportVariables $variables)
	{
		//get variables
		$this->variables = $variables;

		//initialize store for created ansible groups
		$this->ansibleGroups = Array();

		//initialize store for hostvars
		$this->ansibleHostvars = Array();
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object)
	{
		//get hostname for ansible inventory
		$hostname = $this->variables->getVariable("hostname")->getValue($object);

		//walk through all variables to get groups and hostvars
		$groups = Array();
		$hostvars = Array();
		foreach($this->variables->getVariableNames() as $variableName)
		{
			//check if it is a "group_" variable
			if(preg_match('/^group_(.*)$/', $variableName, $matches) == 1)
			{
				$groupName = $matches[1];
				$groupValue = $this->variables->getVariable($variableName)->getValue($object);
				//check if the value is true
				if($groupValue == "true")
				{
					$groups[] = $groupName;
				}
			}

			//check if it is a "hostvar_" variable
			if(preg_match('/^hostvar_(.*)$/', $variableName, $matches) == 1)
			{
				$hostvarName = $matches[1];
				$hostvarValue = $this->variables->getVariable($variableName)->getValue($object);
				$hostvars[$hostvarName] = $hostvarValue;
			}
		}

		//write hostvars to ansible hostvars store
		$this->ansibleHostvars[$hostname] = $hostvars;

		//write to ansible group store
		foreach($groups as $group)
		{
			$this->ansibleGroups[$group]['hosts'][] = $hostname;
		}

	}

	public function finishExport()
	{
		//create JSON and print to Stdout
		$groups = $this->ansibleGroups;
		$groups['_meta']['hostvars'] = $this->ansibleHostvars;
		echo json_encode($groups);
	}
}
?>
