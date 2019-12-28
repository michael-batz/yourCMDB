#! /usr/bin/php
<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2019 Michael Batz
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
* yourCMDB Migrator - migrates yourCMDB data to DATAGERRY
* Usage: migrator.php
* @author Michael Batz <michael@yourcmdb.org>
*/

use yourCMDB\migrator\Migrator;

//load bootstrap
$scriptBaseDir = dirname(__FILE__);
$coreBaseDir = realpath("$scriptBaseDir/../core");
include "$coreBaseDir/bootstrap.php";


//run jobs
$migrator = new Migrator();
$migrator->startMigration();
	
?>

