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

use yourCMDB\entities\CmdbObject;

/**
* Export API - External System: oxidized configuration backup software
* Creates an inventory for oxidzed
* Also exports the specified ip address for proxy_ssh if ssh_proxy_enabled is true or 127.0.0.1 if ssh_proxy_enabled is false.
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExternalSystemOxidized implements ExternalSystem
{
	//ExportVariables
	private $variables;

	//store for oxidized hostvars
	private $oxidizedHostvars;

	//username for tacacs
	private $tacacsUsername;

	//password for tacacs
	private $tacacsPassword;

	public function setUp(ExportDestination $destination, ExportVariables $variables) {
		//get variables
		$this->variables = $variables;

		//initialize store for hostvars
		$this->oxidizedHostvars = Array();

		//get parameters for tacacs login
		$this->tacacsUsername = $destination->getParameterValue("tacacs_username");
		$this->tacacsPassword = $destination->getParameterValue("tacacs_password");
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object) {
		//get id (yourCMDB 0.14 or higher) or hostname (any version)
		//$id = $this->variables->getVariable("id")->getValue($object);
		$hostname = $this->variables->getVariable("oxidized_hostname")->getValue($object);
		//initialize store for iterating hostvars
		$hostvars = Array();

		//walk through all variables
		foreach($this->variables->getVariableNames() as $variableName) {
			//filter only usefull variables for oxidized
			if(preg_match('/^oxidized_(.*)$/', $variableName, $matches) == 1) {
				$hostvarName = $matches[1];
				//set ssh_proxy to given ip address when desired, otherwise set to nil
				if(strcmp($hostvarName, "ssh_proxy_ip") == 0) {
					if(strcmp($this->variables->getVariable("ssh_proxy_enabled")->getValue($object), "true") == 0) {
						$hostvars[$hostvarName] = $this->variables->getVariable($variableName)->getValue($object);
					}
					else {
						$hostvars[$hostvarName] = nil;
					}
				}
				//set username depending on tacacs
				else if(strcmp($hostvarName, "username") == 0) {
					if(strcmp($this->variables->getVariable("tacacs_enabled")->getValue($object), "true") == 0) {
						$hostvars[$hostvarName] = $this->tacacsUsername;
					}
					else {
						$hostvars[$hostvarName] = $this->variables->getVariable($variableName)->getValue($object);
					}
				}
				//set password depending on tacacs
				else if(strcmp($hostvarName, "password") == 0) {
					if(strcmp($this->variables->getVariable("tacacs_enabled")->getValue($object), "true") == 0) {
						$hostvars[$hostvarName] = $this->tacacsPassword;
					}
					else {
						$hostvars[$hostvarName] = $this->variables->getVariable($variableName)->getValue($object);
					}
				}
				else {
					$hostvars[$hostvarName] = $this->variables->getVariable($variableName)->getValue($object);
				}
			}
		}

    //get group of current object
    $hostvars["group"] = $object->getType();

    //write hostvars to oxidized hostvars store, use either $id or $hostname
    $this->oxidizedHostvars[$hostname] = $hostvars;
	}

	public function finishExport() {
		echo json_encode(array_values($this->oxidizedHostvars));
	}
}
?>