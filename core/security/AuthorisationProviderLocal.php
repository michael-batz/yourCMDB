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

/**
* user authorisation against local datastore
* @author Michael Batz <michael@yourcmdb.org>
*/
class AuthorisationProviderLocal implements AuthorisationProvider
{
	/**
	* Ask for authorisation
	* @param $accessgroup		accessgroup of the user
	* @param $applicationpart	part of the application the user wants access
	* @return 			access permissions for the application part
	*				0 = no access, 1 = readonly, 2 = read/write
	*/
	public function authorise($accessgroup, $applicationpart)
	{
		$config = new CmdbConfig();
		$datastoreClass = $config->getDatastoreConfig()->getClass();
		$datastore = new $datastoreClass;

		/* 
		* setup array with possible application parts. 
		* Example: objects/router => objects/router, objects, default 
		*/
		$applicationparts = array();
		$applicationparts[] = "default";
		$applicationparts[] = $applicationpart;
		while(strrpos($applicationpart, '/') !== FALSE)
		{
			$newlength = strrpos($applicationpart, '/');
			$applicationpart = substr($applicationpart, 0, $newlength);
			$applicationparts[] = $applicationpart;
		}

		//get permissions from database
		$accessRights = $datastore->getAccessRights($accessgroup, $applicationparts);
		$permissionDefault = -1;
		$permissionBestMatch = -1;
		$matchLevel = 0;
		foreach($accessRights as $accessRight)
		{
			$accessRightAppPart = $accessRight[0];
			$accessRightAccess = $accessRight[1];
			$accessRightMatchLevel = substr_count($accessRightAppPart, '/');
			if($accessRightAppPart == "default")
			{
				$permissionDefault = $accessRightAccess;
			}
			elseif($accessRightMatchLevel >= $matchLevel)
			{
				$permissionBestMatch = $accessRightAccess;
				$matchLevel = $accessRightMatchLevel;
			}
		}

		//calculate permission
		$permission = 0;
		if($permissionDefault > -1)
		{
			$permission = $permissionDefault;
		}
		if($permissionBestMatch > -1)
		{
			$permission = $permissionBestMatch;
		}

		return $permission;		
	}

	/**
        * Returns an array with all known access groups
        */
        public function getAccessgroups()
	{
		$config = new CmdbConfig();
		$datastoreClass = $config->getDatastoreConfig()->getClass();
		$datastore = new $datastoreClass;

		return $datastore->getAccessgroups();
	}

	/**
	* Returns all access rights for the given accessgroup
	* @param $accessgroup		accessgroup of the user
	* @return			Array(Array('applicationpart', 'access permission'))
	*/
	public function getAccessRights($accessgroup)
	{
		$config = new CmdbConfig();
		$datastoreClass = $config->getDatastoreConfig()->getClass();
		$datastore = new $datastoreClass;

		return $datastore->getAccessRights($accessgroup);
	}

	/**
	* Set or update access rights for given accessgroup
	* @param $accessgroup		accessgroup of the user
	* @param $accessRights		Array('applicationpart', 'access permission')
	* @return 			true, if there was no error
	*				false, if there was an error
	*/
	public function setAccessRights($accessgroup, $accessRights)
	{
		$config = new CmdbConfig();
		$datastoreClass = $config->getDatastoreConfig()->getClass();
		$datastore = new $datastoreClass;

		return $datastore->setAccessRights($accessgroup, $accessRights);
	}

	/**
	* Deletes all access rights for given accessgroup
	* @param $accessgroup		accessgroup of the user
	* @return 			true, if there was no error
	*				false, if there was an error
	*/
	public function deleteAccessRights($accessgroup)
	{
		$config = new CmdbConfig();
		$datastoreClass = $config->getDatastoreConfig()->getClass();
		$datastore = new $datastoreClass;

		return $datastore->deleteAccessRights($accessgroup);
	}

}
?>
