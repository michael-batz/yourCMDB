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
namespace yourCMDB\entities;

use \DateTime;

/**
* a log entry for a change of a specific CMDB object
* @author Michael Batz <michael@yourcmdb.org>
* @Entity
*/
class CmdbObjectLogEntry
{
	/**
	* ID of the logentry
	* @Column(type="integer")
	* @Id
	* @GeneratedValue
	*/
	private $id;

	/**
	* CMDB object of the log entry
	* @ManyToOne(targetEntity="CmdbObject")
	*/
	private $object;

	/**
	* timestamp of the log entry
	* @Column(type="datetime")
	*/
	private $timestamp;

	/**
	* action of the log entry
	* @Column(type="string", length=64, nullable=false)
	*/
	private $action;

	/**
	* description of changes
	* @Column(type="text", nullable=true)
	*/
	private $description;

	/**
	* username of user, that made the change
	* @Column(type="string", length=64, nullable=true)
	*/
	private $user;

	/**
	* Creates a new log entry for a CmdbObject
	* @param CmdbObject $object	object for the log entry
	* @param string $action		action for the log entry
	* @param string $description	description of the changes
	* @param string $username	name of the user, that made the change
	*/
	public function __construct($object, $action, $description, $user)
	{
		$this->object = $object;
		$this->timestamp = new DateTime();
		$this->action = $action;
		$this->description = $description;
		$this->user = $user;
	}

	/**
	* Returns the CmdbObject
	* @return CmdbObject 	object of the log entry
	*/
	public function getObject()
	{
		return $this->object;
	}

	/**
	* Returns the timestamp
	* @return DateTime	timestamp of the log entry
	*/
	public function getTimestamp()
	{
		return $this->timestamp;
	}

	/**
	* Returns the action of the log entry
	* @return string	action of the log entry
	*/
	public function getAction()
	{
		return $this->action;
	}

	/**
	* Returns the decription of the log entry
	* @return string	description of the log entry
	*/
	public function getDescription()
	{
		return $this->description;
	}

	/**
	* Returns the username of the log entry
	* @return string	username that made the change
	*/
	public function getUser()
	{
		return $this->user;
	}

}
?>
