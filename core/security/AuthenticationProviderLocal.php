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
* Authentication provider for local user management
* userdata are stored in local yourCMDB database
* @author Michael Batz <michael@yourcmdb.org>
*/
class AuthenticationProviderLocal implements AuthenticationProvider
{

	//datastore
	private $datstore;

	//configuration
	private $config;

	function __construct()
	{
		$config = new CmdbConfig();
		$this->config = $config;

		//create datastore object
		$datastoreClass = $config->getDatastoreConfig()->getClass();
		$this->datastore = new $datastoreClass;
	}

	public function authenticate($username, $password)
	{
		$userobject = $this->datastore->getUser($username);
		$passwordHash = $this->createHash($username, $password);
		if($userobject != null && $userobject->getPasswordHash() == $passwordHash)
		{
			return true;
		}
		return false;
	}

	public function addUser($username, $password)
	{
		$passwordHash = $this->createHash($username, $password);
		return $this->datastore->addUser(new CmdbLocalUser($username, $passwordHash));
	}

	private function createHash($username, $password)
	{
		$passwordHash = hash("sha256", "yourcmdb".$username.$password);
		return $passwordHash;
	}
}
?>
