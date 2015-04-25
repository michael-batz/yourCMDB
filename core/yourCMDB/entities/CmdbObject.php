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
	* Returns the value of a specific field
	* @param string $fieldname 	name of the field
	* @return string		value of the field or
	*				an empty string, if the field does not exist
	*/
	public function getFieldvalue($fieldname)
	{
		$field = $this->fields->get($fieldname);
		if($field == null)
		{
			return "";
		}
		return $field->getFieldvalue();
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
