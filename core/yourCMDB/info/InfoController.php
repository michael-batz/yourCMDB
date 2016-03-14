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
namespace yourCMDB\info;


/**
* controller for accessing informations about yourCMDB
* singleton: use OrmController::create() for getting an instance
* @author Michael Batz <michael@yourcmdb.org>
*/
class InfoController
{
	//constant: yourCMDB version
	const CMDB_VERSION_MAJOR = "0";
	const CMDB_VERSION_MINOR = "13";
	const CMDB_VERSION_FLAG = "prod";

	/**
	* creates a new Info Controller
	*/
	public function __construct()
	{
		;
	}

	/**
	* Returns the version string
	* @return string	version string
	*/
	public function getVersion()
	{
		$versionString = self::CMDB_VERSION_MAJOR. ".";
		$versionString.= self::CMDB_VERSION_MINOR. "-";
		$versionString.= self::CMDB_VERSION_FLAG;
		return $versionString;
	}

	/**
	* Returns the major version number
	* @return int
	*/
	public function getMajorVersionNumber()
	{
		return self::CMDB_VERSION_MAJOR;
	}

	/**
	* Returns the minor version number
	* @return int
	*/
	public function getMinorVersionNumber()
	{
		return self::CMDB_VERSION_MINOR;
	}

}
?>
