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
namespace yourCMDB\setup;

use yourCMDB\controller\AccessGroupController;
use yourCMDB\security\AuthenticationProviderLocal;
use \Exception;

/**
* creates initial user and access groups
* @author Michael Batz <michael@yourcmdb.org>
*/
class UserSetupHelper
{

	/**
	* creates a new UserSetupHelper
	*/
	public function __construct()
	{
		;
	}

	/**
	* checks, if no access groups exists in datastore
	* @return boolean	true, if no access groups exists in datastore
	*/
	public function checkNoAccessGroups()
	{
		$accessGroupController = AccessGroupController::create();
		$accessGroups = $accessGroupController->getAccessGroups();
		if(count($accessGroups) == 0)
		{
			return true;
		}
		return false;
	}

	/**
	* creates the default access groups in datastore
	* @return boolean	true, if access groups were created
	*			false, if there were errors
	*/
	public function createDefaultAccessGroups()
	{
		try
		{
			$accessGroupController = AccessGroupController::create();
	
			//add access group
			$accessGroupController->addAccessGroup("admin");
			$accessGroupController->addAccessGroup("user");
	
			//add access rights
			$accessGroupController->addAccessRule("admin", "default", 2);
			$accessGroupController->addAccessRule("admin", "admin", 2);
			$accessGroupController->addAccessRule("admin", "rest", 2);
			$accessGroupController->addAccessRule("user", "default", 2);
			$accessGroupController->addAccessRule("user", "admin", 0);
			$accessGroupController->addAccessRule("user", "rest", 0);

			return true;
		}
		catch(Exception $e)
		{
			return false;
		}
	}

	/**
	* creates the default local user in datastore
	* @return boolean	true, if user was created
	*			false, if there were errors
	*/
	public function createDefaultUser()
	{
		try
		{
			$localAuthProvider = new AuthenticationProviderLocal(null);
			$localAuthProvider->addUser("admin", "yourcmdb", "admin");

			return true;
		}
		catch(Exception $e)
		{
			return false;
		}
	}
}
?>
