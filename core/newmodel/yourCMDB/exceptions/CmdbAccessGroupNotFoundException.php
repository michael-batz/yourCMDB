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
namespace yourCMDB\exceptions;

use \Exception;

/**
* Exception, if a CmdbAccessGroup was not found in datastore
* @author Michael Batz <michael@yourcmdb.org>
*/
class CmdbAccessGroupNotFoundException extends Exception
{

	public function __construct($message, $code = 0)
	{
        	parent::__construct($message, $code);
	}

}
?>
