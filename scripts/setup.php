#! /usr/bin/php5
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
* yourCMDB setup
* @author Michael Batz <michael@yourcmdb.org>
*/

use yourCMDB\setup\DatastoreSetupHelper;

//load bootstrap
$scriptBaseDir = dirname(__FILE__);
$coreBaseDir = realpath("$scriptBaseDir/../core");
include "$coreBaseDir/bootstrap.php";

//setup execution
intro();
databaseSetup();
configHelp();

function intro()
{
	echo "Welcome to yourCMDB\n";
	echo "--------------------\n\n";
	echo "This setup script will guide you through the setup process.\n\n";
}

function databaseSetup()
{
	$datastoreSetup = new DatastoreSetupHelper();

	//ToDo: check database connection

	//ToDo: check, if database if empty
	//$datastoreSetup->migrateSchema();
	//$datastoreSetup->repairSchema();
	//$datastoreSetup->checkSchema();
}

function configHelp()
{
	echo "Please follow the yourCMDB wiki to start with a configuration\n";
}

?>
