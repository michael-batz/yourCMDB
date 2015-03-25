<?php

/**
* A CMDB job
* @author Michael Batz <michael@yourcmdb.org>
* @Entity
*/
class CmdbJob
{
	/**
	* ID of the job
	* @Column(type="integer")
	* @Id
	* @GeneratedValue
	*/
	private $id;

	/**
	* action of the job
	* @Column(type="text", nullable=false)
	*/
	private $action;

	/**
	* parameter for action
	* @Column(type="text", nullable=true)
	*/
	private $actionParameter;

	/**
	* timestamp of the job
	* @Column(type="datetime", nullable=true)
	*/
	private $timestamp;

	/**
	* Creates a new CmdbJob
	* @param string $action		action of the job
	* @param string $action		parameter for action
	* @param DateTime $timestamp	timestamp of the job
	*/
	public function __construct($action, $actionParameter, $timestamp)
	{
		$this->action = $action;
		$this->actionParameter = $actionParameter;
		$this->timestamp = $timestamp;
	}

	/**
	* Returns the ID of the job
	* @return int	Id of the job
	*/
	public function getId()
	{
		return $this->id;
	}
	
	/**
	* Returns the action of the job
	* @return string	action of the job
	*/
	public function getAction()
	{
		return $this->action;
	}

	/**
	* Returns the action parameter
	* @return strinf	action parameter
	*/
	public function getActionParameter()
	{
		return $this->actionParameter;
	}

	/**
	* Returns the timestamp of the job
	* @return DateTime	timestamp of the job
	*/
	public function getTimestamp()
	{
		return $this->timestamp;
	}
}
?>
