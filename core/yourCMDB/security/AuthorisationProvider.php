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
namespace yourCMDB\security;

/**
* Interface for user authorisation
* @author Michael Batz <michael@yourcmdb.org>
*/
interface AuthorisationProvider
{
	/**
	* Ask for authorisation
	* @param string $accessgroup		accessgroup of the user
	* @param string $applicationpart	part of the application the user wants access
	* @return int				access permissions for the application part
	*					0 = no access, 1 = readonly, 2 = read/write
	*/
	public function authorise($accessgroup, $applicationpart);
}
?>
