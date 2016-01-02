<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2016 Michael Batz
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
