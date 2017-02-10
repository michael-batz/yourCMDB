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

use \SoapClient;
use yourCMDB\entities\CmdbObject;

/**
* Export API - External System Axiros ACS
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExternalSystemCustomAxirosAcs implements ExternalSystem
{
	//ExportDestination
	private $destination;

	//ExportVariables
	private $variables;

	//Axiros API URL
	private $axirosApiUrl;

	//Axiros API user
	private $axirosApiUser;

	//Axiros API password
	private $axirosApiPassword;

	//username prefix for voice service
	private $prefixUsernameVoice;

	//username prefix for pppoe service
	private $prefixUsernamePppoe;

	//registrar for voice service
	private $voiceRegistrar;

	//SOAP client
	private $soapClient;

	//existing services voice
	private $existingServicesVoice;

	//existing services pppoe
	private $existingServicesPPPoE;

	//existing services meta
	private $existingServicesMeta;

	//create services voice
	private $createServicesVoice;

	//create services ppoe
	private $createServicesPPPoE;

	//create services meta
	private $createServicesMeta;

	//update services voice
	private $updateServicesVoice;

	//update services ppoe
	private $updateServicesPPPoE;

	//update services meta
	private $updateServicesMeta;

	//recreate services voice
	private $recreateServicesVoice;

	//recreate services ppoe
	private $recreateServicesPPPoE;

	public function setUp(ExportDestination $destination, ExportVariables $variables)
	{
		//save parameters
		$this->destination = $destination;
		$this->variables = $variables;

		//check, if parameters are set
		$parameterKeys = $destination->getParameterKeys();
		if(!(	in_array("apiUrl", $parameterKeys) && 
			in_array("apiUser", $parameterKeys) && 
			in_array("apiPassword", $parameterKeys)))
		{
			throw new ExportExternalSystemException("Parameters for ExternalSystem not set correctly");
		}

		//get parameters for Axiros API access
		$this->axirosApiUrl = $destination->getParameterValue("apiUrl");
		$this->axirosApiUser = $destination->getParameterValue("apiUser");
		$this->axirosApiPassword = $destination->getParameterValue("apiPassword");
		$this->prefixUsernamePppoe = $destination->getParameterValue("prefixUsernamePppoe");
		$this->prefixUsernameVoice = $destination->getParameterValue("prefixUsernameVoice");
		$this->voiceRegistrar = $destination->getParameterValue("voiceRegistrar");

		//create Soap client
		$soapOptions = Array();
		$soapOptions["login"] = $this->axirosApiUser;
		$soapOptions["password"] = $this->axirosApiPassword;
		$this->soapClient = new SoapClient($this->axirosApiUrl, $soapOptions);

		// get all services of type metadata
		$this->existingServicesMeta = Array();
		$metadata_list = $this->soapClient->getList_metadata();
		foreach($metadata_list["result"]->Services as $metadataService)
		{
			//define parameters
			$id = "";
			$serviceDefinition = Array();
			$serviceDefinition["identifiers"] = Array();
			$serviceDefinition["parameters"] = Array();

			//create identifiers
			$serviceDefinition["identifiers"]["cid"] = $metadataService->cid;
			$serviceDefinition["identifiers"]["cid2"] = $metadataService->cid2;
			$serviceDefinition["identifiers"]["cpeid"] = $metadataService->cpeid;

			//create parameters
			$serviceDefinition["parameters"]["name"] = $metadataService->properties->name;

			//create internal id
			$id = $metadataService->cpeid;;

			//add to service list
			$this->existingServicesMeta[$id] = $serviceDefinition;
		}

		// get all services of type pppoe
		$this->existingServicesPPPoE = Array();
		$pppoe_list = $this->soapClient->getList_pppoe();
		foreach($pppoe_list["result"]->Services as $pppoeService)
		{
			//define parameters
			$id = "";
			$serviceDefinition = Array();
			$serviceDefinition["identifiers"] = Array();
			$serviceDefinition["parameters"] = Array();
			$serviceDefinition["status"] = $pppoeService->status;

			//create identifiers
			$serviceDefinition["identifiers"]["cpeid"] = $pppoeService->cpeid;

			//create parameters
			$serviceDefinition["parameters"]["username"] = $pppoeService->properties->username;
			$serviceDefinition["parameters"]["password"] = $pppoeService->properties->password;
			$serviceDefinition["parameters"]["downstream_rate"] = $pppoeService->properties->downstream_rate;
			$serviceDefinition["parameters"]["upstream_rate"] = $pppoeService->properties->upstream_rate;
			//check pending parameters
			if(isset($pppoeService->pending_properties->username))
			{
				$serviceDefinition["parameters"]["username"] = $pppoeService->pending_properties->username;
			}
			if(isset($pppoeService->pending_properties->password))
			{
				$serviceDefinition["parameters"]["password"] = $pppoeService->pending_properties->password;
			}
			if(isset($pppoeService->pending_properties->downstream_rate))
			{
				$serviceDefinition["parameters"]["downstream_rate"] = $pppoeService->pending_properties->downstream_rate;
			}
			if(isset($pppoeService->pending_properties->upstream_rate))
			{
				$serviceDefinition["parameters"]["upstream_rate"] = $pppoeService->pending_properties->upstream_rate;
			}

			//create internal id
			$id = $pppoeService->cpeid;

			//add to service list
			$this->existingServicesPPPoE[$id] = $serviceDefinition;
		}

		// get all services of type voice
		$this->existingServicesVoice = Array();
		$voice_list = $this->soapClient->getList_voice();
		foreach($voice_list["result"]->Services as $voiceService)
		{
			//define parameters
			$id = "";
			$serviceDefinition = Array();
			$serviceDefinition["identifiers"] = Array();
			$serviceDefinition["parameters"] = Array();
			$serviceDefinition["status"] = $voiceService->status;

			//create parameters
			$serviceDefinition["parameters"]["username"] = $voiceService->properties->username;
			$serviceDefinition["parameters"]["password"] = $voiceService->properties->password;
			$serviceDefinition["parameters"]["registrar"] = $voiceService->properties->registrar;
			$serviceDefinition["parameters"]["country_code"] = $voiceService->properties->country_code;
			$serviceDefinition["parameters"]["area_code"] = $voiceService->properties->area_code;
			$serviceDefinition["parameters"]["phone_number"] = $voiceService->properties->phone_number;
			$serviceDefinition["parameters"]["directory_number"] = $voiceService->properties->directory_number;
			$serviceDefinition["parameters"]["main_number"] = $voiceService->properties->main_number;
			//check pending parameters
			if(isset($voiceService->pending_properties->username))
			{
				$serviceDefinition["parameters"]["username"] = $voiceService->pending_properties->username;
			}
			if(isset($voiceService->pending_properties->password))
			{
				$serviceDefinition["parameters"]["password"] = $voiceService->pending_properties->password;
			}
			if(isset($voiceService->pending_properties->registrar))
			{
				$serviceDefinition["parameters"]["registrar"] = $voiceService->pending_properties->registrar;
			}
			if(isset($voiceService->pending_properties->country_code))
			{
				$serviceDefinition["parameters"]["country_code"] = $voiceService->pending_properties->country_code;
			}
			if(isset($voiceService->pending_properties->area_code))
			{
				$serviceDefinition["parameters"]["area_code"] = $voiceService->pending_properties->area_code;
			}
			if(isset($voiceService->pending_properties->phone_number))
			{
				$serviceDefinition["parameters"]["phone_number"] = $voiceService->pending_properties->phone_number;
			}
			if(isset($voiceService->pending_properties->directory_number))
			{
				$serviceDefinition["parameters"]["directory_number"] = $voiceService->pending_properties->directory_number;
			}
			if(isset($voiceService->pending_properties->main_number))
			{
				$serviceDefinition["parameters"]["main_number"] = $voiceService->pending_properties->main_number;
			}
			if($serviceDefinition["parameters"]["main_number"] == null)
			{
				$serviceDefinition["parameters"]["main_number"] = 0;
			}

			//create identifiers
			$serviceDefinition["identifiers"]["cpeid"] = $voiceService->cpeid;
			$serviceDefinition["identifiers"]["directory_number"] = $serviceDefinition["parameters"]["directory_number"];

			//create internal id
			$id = $serviceDefinition["identifiers"]["cpeid"] . "-" . $serviceDefinition["parameters"]["directory_number"];

			//add to service list
			$this->existingServicesVoice[$id] = $serviceDefinition;
		}

		//create variables for services to create or update
		$this->createServicesVoice = Array();
		$this->createServicesPPPoE = Array();
		$this->createServicesMeta = Array();
		$this->updateServicesVoice = Array();
		$this->updateServicesPPPoE = Array();
		$this->updateServicesMeta = Array();
		$this->recreateServicesVoice = Array();
		$this->recreateServicesPPPoE = Array();
	}

	public function addObject(\yourCMDB\entities\CmdbObject $object)
	{
		//get variables
		$varMac = substr($this->variables->getVariable("general_cwmp")->getValue($object), 7, 12);
		$varCustName = $this->variables->getVariable("general_custname")->getValue($object);
		$varCustNumber = $this->variables->getVariable("general_custno")->getValue($object);
		$varCmdbId = $object->getId();
		$varPPPoeEnabled = $this->variables->getVariable("pppoe_enabled")->getValue($object);
		$varPPPoeUser = $this->prefixUsernamePppoe . $object->getId();
		$varPPPoePassword = $this->variables->getVariable("pppoe_password")->getValue($object);
		$varPPPoeUpstream = $this->variables->getVariable("pppoe_upstream")->getValue($object) * 1024;
		$varPPPoeDownstream = $this->variables->getVariable("pppoe_downstream")->getValue($object) * 1024;
		$varVoiceEnabled = $this->variables->getVariable("voice_enabled")->getValue($object);
		$varVoiceUser = $this->prefixUsernameVoice . $object->getId();
		$varVoicePassword = $this->variables->getVariable("voice_password")->getValue($object);
		$varVoiceRegistrar = $this->voiceRegistrar;
		$varVoicePhoneCountryCode = "49";
		$varVoicePhoneAreaCode = $this->variables->getVariable("voice_areacode")->getValue($object);
		$varVoicePhoneNumbers = Array();
		foreach($this->variables->getVariableNames() as $variableName)
		{
			if(preg_match('/^voice_phone_(.*)$/', $variableName, $matches) == 1)
			{
				$varVoicePhoneNumber = $this->variables->getVariable($variableName)->getValue($object);
				if($varVoicePhoneNumber != "")
				{
					$varVoicePhoneNumbers[] = $varVoicePhoneNumber;
				}
			}
		}
		$varVoiceMainNumber = $this->variables->getVariable("voice_phone_1")->getValue($object);

		//normalize parameters
		$varVoicePhoneAreaCode = preg_replace("/^0/", "", $varVoicePhoneAreaCode);

		//add meta service
		$metaServiceId = $varMac;
		$metaService = Array();
		$metaService["identifiers"] = Array();
		$metaService["identifiers"]["cpeid"] = $varMac;
		$metaService["identifiers"]["cid"] = $varCustNumber;
		$metaService["identifiers"]["cid2"] = "CMDB#" . $varCmdbId;
		$metaService["parameters"] = Array();
		$metaService["parameters"]["name"] = $varCustName;
		//check, if service should be created or updated
		if(isset($this->existingServicesMeta[$metaServiceId]))
		{
			//check if update is required
			$diff_parms = array_diff($this->existingServicesMeta[$metaServiceId]["parameters"],
						 $metaService["parameters"]);
			$diff_ids = array_diff($this->existingServicesMeta[$metaServiceId]["identifiers"],
                                               $metaService["identifiers"]);
			if((count($diff_parms) > 0) || (count($diff_ids) > 0))
			{
				//update service
				$this->updateServicesMeta[$metaServiceId] = $metaService;
			}

			//delete meta service from list
			unset($this->existingServicesMeta[$metaServiceId]);
		}
		else
		{
			//create service
			$this->createServicesMeta[$metaServiceId] = $metaService;
		}

		//add PPPoE service, if enabled
		if($varPPPoeEnabled == "true")
		{
			$pppoeServiceId = $varMac;
			$pppoeService = Array();
			$pppoeService["identifiers"] = Array();
			$pppoeService["identifiers"]["cpeid"] = $varMac;
			$pppoeService["parameters"] = Array();
			$pppoeService["parameters"]["username"] = $varPPPoeUser;
			$pppoeService["parameters"]["password"] = $varPPPoePassword;
			$pppoeService["parameters"]["downstream_rate"] = $varPPPoeDownstream;
			$pppoeService["parameters"]["upstream_rate"] = $varPPPoeUpstream;
			//check, if service should be created or updated
			if(isset($this->existingServicesPPPoE[$pppoeServiceId]))
			{
				$diff_parms = array_diff($this->existingServicesPPPoE[$pppoeServiceId]["parameters"],
							 $pppoeService["parameters"]);
				$diff_ids = array_diff($this->existingServicesPPPoE[$pppoeServiceId]["identifiers"],
        	                                       $pppoeService["identifiers"]);
				$status = $this->existingServicesPPPoE[$pppoeServiceId]["status"];

				//check if service already exists and is deactivated
				if($status == 0)
				{
					//recreate service
					$this->recreateServicesPPPoE[$pppoeServiceId] = $pppoeService;
				}
				//check if update is required
				if((count($diff_parms) > 0) || (count($diff_ids) > 0))
				{
					//update service
					$this->updateServicesPPPoE[$pppoeServiceId] = $pppoeService;
				}

				//delete meta service from list
				unset($this->existingServicesPPPoE[$pppoeServiceId]);
			}
			else
			{
				//create service
				$this->createServicesPPPoE[$pppoeServiceId] = $pppoeService;
			}
		}

		//add voice services, if enabled
		if($varVoiceEnabled == "true")
		{
			foreach($varVoicePhoneNumbers as $phoneNumber)
			{
				$phoneNumberDirectory = "+".$varVoicePhoneCountryCode . $varVoicePhoneAreaCode . $phoneNumber;
				$voiceServiceId = $varMac . "-" . $phoneNumberDirectory;
				$voiceService = Array();
				$voiceService["identifiers"] = Array();
				$voiceService["identifiers"]["cpeid"] = $varMac;
				$voiceService["identifiers"]["directory_number"] = $phoneNumberDirectory;
				$voiceService["parameters"] = Array();
				$voiceService["parameters"]["username"] = $varVoiceUser;
				$voiceService["parameters"]["password"] = $varVoicePassword;
				$voiceService["parameters"]["registrar"] = $varVoiceRegistrar;
				$voiceService["parameters"]["country_code"] = $varVoicePhoneCountryCode;
				$voiceService["parameters"]["area_code"] = $varVoicePhoneAreaCode;
				$voiceService["parameters"]["phone_number"] = $phoneNumber;
				$voiceService["parameters"]["directory_number"] = $phoneNumberDirectory;
				$voiceService["parameters"]["main_number"] = "0";
				if($phoneNumber == $varVoiceMainNumber)
				{
					$voiceService["parameters"]["main_number"] = "1";
				}
				//check, if service should be created or updated
				if(isset($this->existingServicesVoice[$voiceServiceId]))
				{
					$diff_parms = array_diff($this->existingServicesVoice[$voiceServiceId]["parameters"],
								 $voiceService["parameters"]);
					$diff_ids = array_diff($this->existingServicesVoice[$voiceServiceId]["identifiers"],
	       	                         	               $voiceService["identifiers"]);
					$status = $this->existingServicesVoice[$voiceServiceId]["status"];
	
					//check if service already exists and is deactivated
					if($status == 0)
					{
						//recreate service
						$this->recreateServicesVoice[$voiceServiceId] = $voiceService;
					}
					//check if update is required
					if((count($diff_parms) > 0) || (count($diff_ids) > 0))
					{
						//update service
						$this->updateServicesVoice[$voiceServiceId] = $voiceService;
					}

					//delete meta service from list
					unset($this->existingServicesVoice[$voiceServiceId]);
				}
				else
				{
					//create service
					$this->createServicesVoice[$voiceServiceId] = $voiceService;
				}
			}
		}

	}

	public function finishExport()
	{
		//create/update/delete meta services
		foreach($this->createServicesMeta as $metaService)
		{
			$result = $this->soapClient->add_metadata($metaService["parameters"], $metaService["identifiers"]);
		}
		foreach($this->updateServicesMeta as $metaService)
		{
			$result = $this->soapClient->update_metadata($metaService["parameters"], $metaService["identifiers"]);
		}
		foreach($this->existingServicesMeta as $metaService)
		{
			$result = $this->soapClient->delete_metadata($metaService["identifiers"]);
		}

		//recreate/create/update/delete pppoe services
		foreach($this->recreateServicesPPPoE as $pppoeService)
		{
			$result = $this->soapClient->reactivate_pppoe($pppoeService["identifiers"]);
			$result = $this->soapClient->modify_pppoe($pppoeService["parameters"], $pppoeService["identifiers"]);
		}
		foreach($this->createServicesPPPoE as $pppoeService)
		{
			$result = $this->soapClient->activate_pppoe($pppoeService["parameters"], $pppoeService["identifiers"]);
		}
		foreach($this->updateServicesPPPoE as $pppoeService)
		{
			$result = $this->soapClient->modify_pppoe($pppoeService["parameters"], $pppoeService["identifiers"]);
		}
		foreach($this->existingServicesPPPoE as $pppoeService)
		{
			if($pppoeService["status"] != 0)
			{
				$result = $this->soapClient->deactivate_pppoe($pppoeService["identifiers"]);
			}
		}

		//recreate/create/update/delete voice services
		foreach($this->recreateServicesVoice as $voiceService)
		{
			$result = $this->soapClient->reactivate_voice($voiceService["identifiers"]);
			$result = $this->soapClient->modify_voice($voiceService["parameters"], $voiceService["identifiers"]);
		}
		foreach($this->createServicesVoice as $voiceService)
		{
			$result = $this->soapClient->activate_voice($voiceService["parameters"], $voiceService["identifiers"]);
		}
		foreach($this->updateServicesVoice as $voiceService)
		{
			$result = $this->soapClient->modify_voice($voiceService["parameters"], $voiceService["identifiers"]);
		}
		foreach($this->existingServicesVoice as $voiceService)
		{
			if($voiceService["status"] != 0)
			{
				$result = $this->soapClient->deactivate_voice($voiceService["identifiers"]);
			}
		}
	}
}
?>
