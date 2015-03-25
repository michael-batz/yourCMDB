<?php

/**
* A rule for an access group
* @author Michael Batz <michael@yourcmdb.org>
* @Entity
*/
class CmdbAccessRule
{
	/**
	* access group for the rule
	* @ManyToOne(targetEntity="CmdbAccessGroup", inversedBy="accessRules", cascade={"persist"})
	* @JoinColumn(referencedColumnName="name")
	* @Id
	*/
	private $accessgroup;

	/**
	* application part of the rule
	* @Column(type="string", length=255)
	* @Id
	*/
	private $applicationPart;

	/**
	* access rights
	* @Column(type="integer", nullable=false)
	*/
	private $access;

	/**
	* creates a new access rule for the given access group
	* @param CmdbAccessGroup $accessgroup	access group
	* @param string $applicationPart	application part
	* @param int $access			access rights
	*/
	public function __construct($accessgroup, $applicationPart, $access)
	{
		$this->accessgroup = $accessgroup;
		$this->applicationPart = $applicationPart;
		$this->access = $access;
	}

	/**
	* Returns the access group of the rule
	* @return CmdbAccessGroup	access group of the rule
	*/
	public function getAccessGroup()
	{
		return $this->accessgroup;
	}

	/**
	* Returns the application part of the rule
	* @return string	application part
	*/
	public function getApplicationPart()
	{
		return $this->applicationPart;
	}

	/**
	* Returns the access rights
	* @return int	access rights
	*/
	public function getAccess()
	{
		return $this->access;
	}

	/**
	* Sets new access rights for the rule
	* @param int $access	new access rights
	*/
	public function setAccess($access)
	{
		$this->access = $access;
	}


}
?>
