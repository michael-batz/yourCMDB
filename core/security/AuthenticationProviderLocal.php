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
* Authentication provider for local user management
* userdata are stored in local yourCMDB database
* @author Michael Batz <michael@yourcmdb.org>
*/
class AuthenticationProviderLocal implements AuthenticationProvider
{

	function __construct($parameters)
	{
		//no parameters needed - so doing nothing here
		;
	}

	public function authenticate($username, $password)
	{
		$config = new CmdbConfig();
		$datastoreClass = $config->getDatastoreConfig()->getClass();
		$datastore = new $datastoreClass;

		$userobject = $datastore->getUser($username);
		$passwordHash = $this->createHash($username, $password);
		if($userobject != null && $userobject->getPasswordHash() == $passwordHash)
		{
			return true;
		}
		return false;
	}

	public function getAccessGroup($username)
	{
		return "all";
	}

	public function addUser($username, $password)
	{
		$config = new CmdbConfig();
		$datastoreClass = $config->getDatastoreConfig()->getClass();
		$datastore = new $datastoreClass;

		$passwordHash = $this->createHash($username, $password);
		return $datastore->addUser(new CmdbLocalUser($username, $passwordHash));
	}

	private function createHash($username, $password)
	{
		$passwordHash = hash("sha256", "yourcmdb".$username.$password);
		return $passwordHash;
	}
}
?>
