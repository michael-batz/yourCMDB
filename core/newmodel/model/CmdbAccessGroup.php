<?php

use Doctrine\Common\Collections\ArrayCollection;

/**
* An access group for yourCMDB
* @author Michael Batz <michael@yourcmdb.org>
* @Entity
*/
class CmdbAccessGroup
{

	/**
	* name of the access group
	* @Column(type="string", length=64)
	* @Id
	*/
	private $name;

	/**
	* access rules
	* @OneToMany(targetEntity="CmdbAccessRule", mappedBy="accessgroup", indexBy="applicationPart", cascade={"persist", "remove"})
	*/
	private $accessRules;

	/**
	* Creates a new access group
	* @param string $name	name of the access group
	*/
	public function __construct($name)
	{
		$this->name = $name;
		$this->accessRules = new ArrayCollection();
	}

	/**
	* Returns the name of the access group
	* @return string	name of the access group
	*/
	public function getName()
	{
		return $this->name;
	}

	/**
	* Returns the access rules
	* @return ArrayCollection	access rules
	*/
	public function getAccessRules()
	{
		return $this->accessRules;
	}
}
?>
