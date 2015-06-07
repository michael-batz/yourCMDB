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
namespace yourCMDB\security;

use yourCMDB\controller\LocalUserController;
use yourCMDB\entities\CmdbLocalUser;
use \Exception;

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
		$localUserController = LocalUserController::create();

		try
		{
			$userobject = $localUserController->getUser($username);
			$passwordHash = $this->createHash($username, $password);
			if($userobject != null && $userobject->getPasswordHash() == $passwordHash)
			{
				return true;
			}
		}
		catch(Exception $e)
		{
			//doing nothing
		}
		return false;
	}

	public function getAccessGroup($username)
	{
		$localUserController = LocalUserController::create();

		$userobject = $localUserController->getUser($username);
		if($userobject == null)
		{
			return null;
		}
		return $userobject->getAccessGroup();
	}

	public function addUser($username, $password, $accessgroup)
	{
		//check if username and password is a valid value
		if($username == "" || $password == "")
		{
			throw new SecurityChangeUserException("Inavlid username or password");
		}

		$passwordHash = $this->createHash($username, $password);
		$localUserController = LocalUserController::create();
		return $localUserController->addUser(new CmdbLocalUser($username, $passwordHash, $accessgroup));
	}

	public function getUser($username)
	{
		$localUserController = LocalUserController::create();
		return $localUserController->getUser($username);
	}

	public function getUsers()
	{
		$localUserController = LocalUserController::create();
		return $localUserController->getUsers();
	}

	public function deleteUser($username)
	{
		$localUserController = LocalUserController::create();
		return $localUserController->deleteUser($username);
	}

	public function resetPassword($username, $newPassword)
	{
		//check if username and password is a valid value
		if($newPassword == "")
		{
			throw new SecurityChangeUserException("Inavlid username or password");
		}

		//update user object
		$newPasswordHash = $this->createHash($username, $newPassword);
		$userobject = $this->getUser($username);
		$userobject->setPasswordHash($newPasswordHash);

		//change user in datastore
		$localUserController = LocalUserController::create();
		return $localUserController->changeUser($userobject);
	}

	public function setAccessGroup($username, $newAccessGroup)
	{
		//update user object
		$userobject = $this->getUser($username);
		$userobject->setAccessGroup($newAccessGroup);

		//change user in datastore
		$localUserController = LocalUserController::create();
		return $localUserController->changeUser($userobject);
	}


	private function createHash($username, $password)
	{
		$passwordHash = hash("sha256", "yourcmdb".$username.$password);
		return $passwordHash;
	}
}
?>
