<?php

use Doctrine\Common\Collections\ArrayCollection;

/**
* A CMDB object
* @author Michael Batz <michael@yourcmdb.org>
* @Entity
*/
class CmdbObject
{
	/**
	* ID of the object
	* @Column(type="integer")
	* @Id
	* @GeneratedValue
	*/
	private $id;

	/**
	* type of the object
	* @Column(type="string", length=64, nullable=false)
	*/
	private $type;

	/**
	* state of the object
	* @Column(type="string", length=1, nullable=false)
	*/
	private $status;

	/**
	* fields of the object
	* @OneToMany(targetEntity="CmdbObjectField", mappedBy="object", indexBy="fieldkey", cascade={"persist", "remove"})
	*/
	private $fields;

	/**
	* Creates a new CmdbObject
	* @param string $type 		type of the object
	* @param string $status		state of the object
	*/
	public function __construct($type, $status)
	{
		$this->type = $type;
		$this->status = $status;
		$this->fields = new ArrayCollection();
	}

	/**
	* Returns the object's ID
	* @return int	ID of the object
	*/
	public function getId()
	{
		return $this->id;
	}

	/**
	* Returns the object's type
	* @return string	type of the object
	*/
	public function getType()
	{
		return $this->type;
	}

	/**
	* Returns the status of the object
	* @return string	state of the object
	*/
	public function getStatus()
	{
		return $this->status;
	}

	/**
	* Returns an ArrayCollection of object fields
	* @return ArrayCollection	object fields
	*/
	public function getFields()
	{
		return $this->fields;
	}

	/**
	* Sets the state of the object
	* @param string $state	the new state of the object
	*/
	public function setStatus($status)
	{
		$this->status = $status;
	}
}
?>
