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
* A local user object
* @author Michael Batz <michael@yourcmdb.org>
* @Entity
*/
class CmdbLocalUser
{

	/**
	* username
	* @Column(type="string", length=255)
	* @Id
	*/
	private $username;

	/**
	* password hash
	* @Column(type="text", nullable=false)
	*/
	private $passwordHash;

	/**
	* access group of the user
	* @Column(type="text", nullable=false)
	*/
	private $accessGroup;

	/**
	* Creates a new user object
	* @param string $username	name of the user
	* @param string $passwordHash	password hash
	* @param string $accessGroup	access group of the user
	*/
	public function __construct($username, $passwordHash, $accessGroup)
	{
		$this->username = $username;
		$this->passwordHash = $passwordHash;
		$this->accessGroup = $accessGroup;
	}

	/**
	* Returns the username
	* @return string	name of the user
	*/
	public function getUsername()
	{
		return $this->username;
	}

	/**
	* Returns the password hash
	* @return string	password hash
	*/
	public function getPasswordHash()
	{
		return $this->passwordHash;
	}

	/**
	* Returns the access group
	* @return string	access group of the user
	*/
	public function getAccessGroup()
	{
		return $this->accessGroup;
	}

	/**
	* Sets the password hash for the user
	* @param string $passwordHash	new password hash
	*/
	public function setPasswordHash($passwordHash)
	{
		$this->passwordHash = $passwordHash;
	}

	/**
	* Sets the access group for the user
	* @param string $accessGroup	new access group
	*/
	public function setAccessGroup($accessGroup)
	{
		$this->accessGroup = $accessGroup;
	}
}
?>
