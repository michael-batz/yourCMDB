<?php

/**
* A link between two CmdbObjects
* @author Michael Batz <michael@yourcmdb.org>
* @Entity
*/
class CmdbObjectLink
{
	/**
	* First object
	* @ManyToOne(targetEntity="CmdbObject")
	* @Id
	*/
	private $objectA;

	/**
	* Second object
	* @ManyToOne(targetEntity="CmdbObject")
	* @Id
	*/
	private $objectB;

	/**
	* Creates a new link between two objects
	* @param CmdbObject $objectA	first object
	* @param CmdbObject $objectB	second object
	*/
	public function __construct($objectA, $objectB)
	{
		$this->objectA = $objectA;
		$this->objectB = $objectB;
	}

	/**
	* Returns the first object of the link
	* @return CmdbObject	first object of the link
	*/
	public function getObjectA()
	{
		return $this->objectA;
	}

	/**
	* Returns the second object of the link
	* @return CmdbObject	second object of the link
	*/
	public function getObjectB()
	{
		return $this->objectB;
	}

}
?>
