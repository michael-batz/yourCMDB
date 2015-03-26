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
	private $fieldkey;

	/**
	* value of the field
	* @Column(type="text", nullable=true)
	*/
	private $fieldvalue;

	/**
	* Creates a new field for a given object
	* @param CmdbObject $object	object for the field
	* @param string $fieldkey		name of the field
	* @param string $fieldvalue		value of the field
	*/
	public function __construct($object, $fieldkey, $fieldvalue)
	{
		$this->object = $object;
		$this->fieldkey = $fieldkey;
		$this->fieldvalue = $fieldvalue;
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
	public function getFieldkey()
	{
		return $this->fieldkey;
	}

	/**
	* Returns the value of the field
	* @return string	value of the field
	*/
	public function getFieldvalue()
	{
		return $this->fieldvalue;
	}

	/**
	* Sets a new value for the field
	* @param string $fieldvalue	the new value of the field
	*/
	public function setFieldvalue($fieldvalue)
	{
		$this->fieldvalue = $fieldvalue;
	}
}
?>
