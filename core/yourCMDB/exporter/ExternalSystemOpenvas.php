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
* Export API - External System: OpenVAS Targets and Tasks
* Creates targets and tasks for OpenVAS using the OpenVAS
* Manager Protocol (OMP)
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExternalSystemOpenvas implements ExternalSystem
{
	//ExportVariables
	private $variables;

	//OMP Hostname
	private $ompHost;

	//OMP Port
	private $ompPort;
	
	//OMP Username
	private $ompUser;

	//OMP Password
	private $ompPassword;

	//prefix for OpenVAS target and task names
	private $namespacePrefix;

	//name of the OpenVAS scanner
	private $scannerName;

	//name of the OpenVAS scan config to use
	private $configName;

	//store for targets and tasks information
	private $openvasTasks;


	public function setUp(ExportDestination $destination, ExportVariables $variables)
	{
		//get variables
		$this->variables = $variables;

		//check, if parameters are set
		$parameterKeys = $destination->getParameterKeys();
		if(!(	in_array("ompHost", $parameterKeys) && 
			in_array("ompPort", $parameterKeys) && 
			in_array("ompUser", $parameterKeys) &&
			in_array("ompPassword", $parameterKeys) &&
			in_array("scannerName", $parameterKeys) &&
			in_array("configName", $parameterKeys)))
		{
			throw new ExportExternalSystemException("Parameters for ExternalSystem not set correctly");
		}

		//get parameters for OpenVAS access
		$this->ompHost = $destination->getParameterValue("ompHost");
		$this->ompPort = $destination->getParameterValue("ompPort");
		$this->ompUser = $destination->getParameterValue("ompUser");
		$this->ompPassword = $destination->getParameterValue("ompPassword");

		//setup namespace for OpenVAS targets and tasks
		$this->namespacePrefix = "yourCMDB_";
		if(in_array("namespacePrefix", $parameterKeys))
		{
			$this->namespacePrefix = $destination->getParameterValue("namespacePrefix");
		}

		//setup OpenVAS scannerName and configName
		$this->scannerName = $destination->getParameterValue("scannerName");
		$this->configName = $destination->getParameterValue("configName");
		

		//init store for OpenVAS tasks
		$this->openvasTasks = Array();
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object)
	{
		//create taskname
		$taskname = $this->namespacePrefix;
		$taskname.= $object->getId() . " ";
		$taskname.= $this->variables->getVariable("hostname")->getValue($object) . " ";
		$taskname.= $this->variables->getVariable("taskname")->getValue($object);

		//get IP
		$ip = $this->variables->getVariable("ip")->getValue($object);

		//only export as task, if IP is valid
		if(filter_var($ip, FILTER_VALIDATE_IP) !== FALSE)
		{
			//save information in OpenVAS tasks
			$this->openvasTasks[$taskname] = $ip;
		}

	}

	public function finishExport()
	{
		//open connection
		$ompConnection = fsockopen("tls://$this->ompHost", $this->ompPort);
		if(!$ompConnection)
		{
			throw new ExportExternalSystemException("Error connecting to host $this->ompHost on Port $ompPort");
		}
		//set non blocking mode
		stream_set_blocking($ompConnection, false);

		//omp: authentication
		$result = $this->ompAuthenticate($ompConnection, $this->ompUser, $this->ompPassword);

		//omp: get scan config
		$scanConfigId = $this->ompGetConfigId($ompConnection, $this->configName);
		$scannerId = $this->ompGetScannerId($ompConnection, $this->scannerName);

		//omp: get all exististing OpenVAS tasks and targets in namespace
		$existingTargets = $this->ompGetTargets($ompConnection);
		$existingTasks = $this->ompGetTasks($ompConnection);

		//walk through all tasks for export
		foreach(array_keys($this->openvasTasks) as $taskName)
		{
			//if task exists
			if(isset($existingTasks[$taskName]))
			{
				//get data
				$existingTaskId = $existingTasks[$taskName]['id'];
				$existingTargetId = $existingTasks[$taskName]['targetId'];
				$existingTargetName = $existingTargets[$existingTargetId]['name'];
				$existingTargetHosts = $existingTargets[$existingTargetId]['hosts'];
				$createTargetHosts = $this->openvasTasks[$taskName];

				//check, if hostlist has changed
				if($existingTargetHosts != $createTargetHosts)
				{
					//delete old task
					$this->ompDeleteTask($ompConnection, $existingTaskId);

					//delete old target
					$this->ompDeleteTarget($ompConnection, $existingTargetId);

					//create a new target with new hostlist
					$createTargetId = $this->ompCreateTarget($ompConnection, $existingTargetName, $createTargetHosts);

					//create a new task
					$this->ompCreateTask($ompConnection, $existingTargetName, $createTargetId, $scannerId, $scanConfigId);
				}

				//remove target and task from lists
				unset($existingTasks[$taskName]);
				unset($existingTargets[$existingTargetId]);
			}
			//if task does not exsist
			else
			{
				//create target
				$createTargetName = $taskName;
				$createTargetHosts = $this->openvasTasks[$taskName];
				$createTargetId = $this->ompCreateTarget($ompConnection, $createTargetName, $createTargetHosts);

				//create task
				$this->ompCreateTask($ompConnection, $createTargetName, $createTargetId, $scannerId, $scanConfigId);
			}
		}

		//walk through all tasks that still exists in OpenVAS but not in yourCMDB export
		foreach($existingTasks as $existingTask)
		{
			//remove task
			$this->ompDeleteTask($ompConnection, $existingTask['id']);
		}
		//walk through all targets that still exists in OpenVAS but not in yourCMDB export
		foreach(array_keys($existingTargets) as $existingTargetId)
		{
			//remove target
			$this->ompDeleteTarget($ompConnection, $existingTargetId);
		}

		//close connection
		fclose($ompConnection);
	}


	/**
	* send xml request on an existing connection and gets and returns 
	* the repsonse xml
	* @param resource $connection		open socket connection
	* @param string $request	XML request
	* @return string		XML response
	*/
	private function sendRequest($connection, $request)
	{
		//send request
		fwrite($connection, $request, strlen($request));
		fflush($connection);

		//get response
		libxml_use_internal_errors(true);
		$response = "";
		$timeIntervall = 0;
		$timeStart = time();
		while(!feof($connection) && ($timeIntervall <= 5))
		{
			$responseLength = strlen($response);
			$response .= fread($connection, 8192);
			if(strlen($response) > $responseLength)
			{
				$timeStart = time();
			}
			$timeNow = time();
			$timeIntervall = $timeNow - $timeStart;
			if(simplexml_load_string($response) !== FALSE && strlen($response) > 0)
			{
				break;
			}
		}
		return $response;
	}

	/**
	* OMP helper: user authentication
	* authenticates the user with the given username and password
	* @param resource $connection		connection to OpenVAS server
	* @param string $username		OpenVAS user
	* @param string $password		OpenVAS password
	* @throws ExportExternalSystemException	if authentication failed
	*/
	private function ompAuthenticate($connection, $username, $password)
	{
		$requestXml = "<authenticate><credentials>";
		$requestXml.= "<username>$this->ompUser</username>";
		$requestXml.= "<password>$this->ompPassword</password>";
		$requestXml.= "</credentials></authenticate>";

		$responseXml = $this->sendRequest($connection, $requestXml);
		$responseObject = simplexml_load_string($responseXml);
		$authenticationStatus = $responseObject[0]['status'];
		if($authenticationStatus != 200)
		{
			throw new ExportExternalSystemException("OMP authentication error with username $this->ompUser");
		}
	}

	/**
	* OMP helper: get all existing targets with namespace prefix
	* @param resource $connection	connection to OpenVAS server
	* @return array			Array with targets
	* @throws ExportExternalSystemException	if there was an error
	*/
	private function ompGetTargets($connection)
	{
		//send request
		$requestXml = "<get_targets />";
		$responseXml = $this->sendRequest($connection, $requestXml);

		//check response
		$responseObject = simplexml_load_string($responseXml);
		$responseStatus = $responseObject[0]['status'];
		if($responseStatus != 200)
		{
			throw new ExportExternalSystemException("Error getting targets with OMP: $responseStatus");
		}

		//generate output data
		$targets = Array();
		foreach($responseObject->target as $target)
		{
			//get values from XML
			$targetId = (string) $target['id'];
			$targetName = (string) $target->name[0];
			$targetHosts = (string) $target->hosts[0];

			//check, if target name is in configured namespace
			if($this->namespacePrefix == "" || (strpos($targetName, $this->namespacePrefix) === 0))
			{
				//create array
				$targets[$targetId] = Array();
				$targets[$targetId]['name'] = $targetName;
				$targets[$targetId]['hosts'] = $targetHosts;
			}
		}

		//return output
		return $targets;
	}

	/**
	* OMP helper: get all existing tasks with namespace prefix
	* @param resource $connection	connection to OpenVAS server
	* @return array			Array with tasks
	* @throws ExportExternalSystemException	if there was an error
	*/
	private function ompGetTasks($connection)
	{
		//send request
		$requestXml = "<get_tasks />";
		$responseXml = $this->sendRequest($connection, $requestXml);

		//check response
		$responseObject = simplexml_load_string($responseXml);
		$responseStatus = $responseObject[0]['status'];
		if($responseStatus != 200)
		{
			throw new ExportExternalSystemException("Error getting tasks with OMP: $responseStatus");
		}

		//generate output data
		$tasks = Array();
		foreach($responseObject->task as $task)
		{
			//get values from XML
			$taskId = (string) $task['id'];
			$taskName = (string) $task->name[0];
			$taskTargetId = (string) $task->target[0]['id'];

			//check, if task name is in configured namespace
			if($this->namespacePrefix == "" || (strpos($taskName, $this->namespacePrefix) === 0))
			{
				//create array
				$tasks[$taskName] = Array();
				$tasks[$taskName]['id'] = $taskId;
				$tasks[$taskName]['targetId'] = $taskTargetId;
			}
		}

		//return output
		return $tasks;
	}

	/**
	* OMP helper: create an OpenVAS target
	* @param resource $connection	connection to OpenVAS server
	* @param string $name		target name
	* @param string $hosts		target host list
	* @return string		ID of the created target
	* @throws ExportExternalSystemException	if there was an error
	*/
	private function ompCreateTarget($connection, $name, $hosts)
	{
		//send request
		$requestXml = "<create_target>";
		$requestXml.= "<name>$name</name>";
		$requestXml.= "<hosts>$hosts</hosts>";
		$requestXml.= "</create_target>";
		$responseXml = $this->sendRequest($connection, $requestXml);

		//check response
		$responseObject = simplexml_load_string($responseXml);
		$responseStatus = $responseObject[0]['status'];
		if($responseStatus > 202)
		{
			throw new ExportExternalSystemException("Error creating target with OMP: $responseStatus");
		}
		$responseId = $responseObject[0]['id'];

		//return ID of created target
		return $responseId;
	}

	/**
	* OMP helper: create an OpenVAS task
	* @param resource $connection	connection to OpenVAS server
	* @param string $name		task name
	* @param string $targetId	target ID
	* @param string $scannerId	scanner ID
	* @param string $configId	config ID
	* @return string		ID of the created task
	* @throws ExportExternalSystemException	if there was an error
	*/
	private function ompCreateTask($connection, $name, $targetId, $scannerId, $configId)
	{
		//send request
		$requestXml = "<create_task>";
		$requestXml.= "<name>$name</name>";
		$requestXml.= "<config id=\"$configId\" />";
		$requestXml.= "<target id=\"$targetId\" />";
		$requestXml.= "<scanner id=\"$scannerId\" />";
		$requestXml.= "</create_task>";
		$responseXml = $this->sendRequest($connection, $requestXml);

		//check response
		$responseObject = simplexml_load_string($responseXml);
		$responseStatus = $responseObject[0]['status'];
		if($responseStatus > 202)
		{
			throw new ExportExternalSystemException("Error creating task with OMP: $responseStatus");
		}
		$responseId = $responseObject[0]['id'];

		//return ID of created task
		return $responseId;
	}

	/**
	* OMP helper: delete an OpenVAS task
	* @param resource $connection	connection to OpenVAS server
	* @param string $id		task ID
	* @throws ExportExternalSystemException	if there was an error
	*/
	private function ompDeleteTask($connection, $id)
	{
		//send request
		$requestXml = "<delete_task task_id=\"$id\" ultimate=\"true\" />";
		$responseXml = $this->sendRequest($connection, $requestXml);

		//check response
		$responseObject = simplexml_load_string($responseXml);
		$responseStatus = $responseObject[0]['status'];
		if($responseStatus > 202)
		{
			throw new ExportExternalSystemException("Error deleting task with OMP: $responseStatus");
		}

	}


	/**
	* OMP helper: delete an OpenVAS target
	* @param resource $connection	connection to OpenVAS server
	* @param string $id		target ID
	* @throws ExportExternalSystemException	if there was an error
	*/
	private function ompDeleteTarget($connection, $id)
	{
		//send request
		$requestXml = "<delete_target target_id=\"$id\" ultimate=\"true\" />";
		$responseXml = $this->sendRequest($connection, $requestXml);

		//check response
		$responseObject = simplexml_load_string($responseXml);
		$responseStatus = $responseObject[0]['status'];
		if($responseStatus > 202)
		{
			throw new ExportExternalSystemException("Error deleting target with OMP: $responseStatus");
		}

	}

	/**
	* OMP helper: gets the ID of an OpenVAS scan configuration
	* @param resource $connection	connection to OpenVAS server
	* @param string $name		name of the config
	* @return string		ID of the OpenVAS scan configuration
	* @throws ExportExternalSystemException	if there was an error
	*/
	private function ompGetConfigId($connection, $name)
	{
		//send request
		$requestXml = "<get_configs />";
		$responseXml = $this->sendRequest($connection, $requestXml);

		//check response
		$responseObject = simplexml_load_string($responseXml);
		$responseStatus = $responseObject[0]['status'];
		if($responseStatus > 202)
		{
			throw new ExportExternalSystemException("Error getting configuration ID with OMP: $responseStatus");
		}
		$responseConfigId = "";
		foreach($responseObject[0]->config as $config)
		{
			$configId = (string)$config['id'];
			$configName = (string)$config->name[0];
			if($configName == $name)
			{
				$responseConfigId = $configId;
			}
		}
		if($responseConfigId == "")
		{
			throw new ExportExternalSystemException("Error getting configuration ID with OMP: Configuration $name not found");
		}

		//return output
		return $responseConfigId;
	}

	/**
	* OMP helper: gets the ID of an OpenVAS scanner
	* @param resource $connection	connection to OpenVAS server
	* @param string $name		name of the scanner
	* @return string		ID of the OpenVAS scanner
	* @throws ExportExternalSystemException	if there was an error
	*/
	private function ompGetScannerId($connection, $name)
	{
		//send request
		$requestXml = "<get_scanners />";
		$responseXml = $this->sendRequest($connection, $requestXml);

		//check response
		$responseObject = simplexml_load_string($responseXml);
		$responseStatus = $responseObject[0]['status'];
		if($responseStatus > 202)
		{
			throw new ExportExternalSystemException("Error getting scanner ID with OMP: $responseStatus");
		}
		$responseScannerId = "";
		foreach($responseObject[0]->scanner as $scanner)
		{
			$scannerId = (string)$scanner['id'];
			$scannerName = (string)$scanner->name[0];
			if($scannerName == $name)
			{
				$responseScannerId = $scannerId;
			}
		}
		if($responseScannerId == "")
		{
			throw new ExportExternalSystemException("Error getting scanner ID with OMP: Scanner $name not found");
		}

		//return output
		return $responseScannerId;
	}

}
?>
