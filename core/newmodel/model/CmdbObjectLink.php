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
