<?php

/**
* a log entry for a change of a specific CMDB object
* @author Michael Batz <michael@yourcmdb.org>
* @Entity
*/
class CmdbObjectLogEntry
{
	/**
	* CMDB object of the log entry
	* @ManyToOne(targetEntity="CmdbObject")
	* @Id
	*/
	private $object;

	/**
	* timestamp of the log entry
	* @Column(type="datetime")
	* @Id
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
