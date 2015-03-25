<?php

/**
* A field of a CMDB object
* @author Michael Batz <michael@yourcmdb.org>
* @Entity
*/
class CmdbObjectField
{
	/**
	* CMDB object of this field
	* @ManyToOne(targetEntity="CmdbObject", inversedBy="fields", cascade={"persist"})
	* @Id
	*/
	private $object;

	/**
	* field key / name of the field
	* @Column(type="string", length=64)
	* @Id
	*/
	private $key;

	/**
	* value of the field
	* @Column(type="text", nullable=true)
	*/
	private $value;

	/**
	* Creates a new field for a given object
	* @param CmdbObject $object	object for the field
	* @param string $key		name of the field
	* @param string $value		value of the field
	*/
	public function __construct($object, $key, $value)
	{
		$this->object = $object;
		$this->key = $key;
		$this->value = $value;
	}

	/**
	* Returns the attached object
	* @return CmdbObject 	the attached object
	*/
	public function getObject()
	{
		return $this->object;
	}

	/**
	* Returns the key of the field
	* @return string	name of the field
	*/
	public function getKey()
	{
		return $this->key;
	}

	/**
	* Returns the value of the field
	* @return string	value of the field
	*/
	public function getValue()
	{
		return $this->value;
	}

	/**
	* Sets a new value for the field
	* @param string $value	the new value of the field
	*/
	public function setValue($value)
	{
		$this->value = $value;
	}
}
?>
