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
use yourCMDB\setup\UserSetupHelper;
use \Exception;

//load bootstrap
$scriptBaseDir = dirname(__FILE__);
$coreBaseDir = realpath("$scriptBaseDir/../core");
include "$coreBaseDir/bootstrap.php";

//setup execution
intro();
databaseSetup();
userSetup();
configHelp();

function intro()
{
	echo "Welcome to yourCMDB\n";
	echo "--------------------\n\n";
	echo "This setup script will guide you through the setup process.\n";
	echo "\n";
}

function databaseSetup()
{
	echo "DatabaseSetup\n";
	$datastoreSetup = new DatastoreSetupHelper();

	//check database connection
	echo "- checking datastore connection...";
	if($datastoreSetup->checkConnection() == FALSE)
	{
		echo "ERROR\n\n Error connecting to datastore. Please check datastore-configuration.xml. Exit\n";
		exit(-1);
	}
	echo "OK\n";

	//check, if database if empty
	if($datastoreSetup->checkIsEmptyDatabase())
	{
		echo "- empty database: creating schema...";
		$datastoreSetup->createSchema();
		echo "OK\n";
	}
	else
	{
		echo "- nonempty database: trying to upgrade existing schema...";
		try
		{
			$datastoreSetup->migrateSchema();
			$datastoreSetup->repairSchema();
			echo "OK\n";
		}
		catch(Exception $e)
		{
			echo "ERROR\n\n error migrating schema. Exit.\n";
			exit(-1);
		}
	}
	echo "- checking database schema...";
	if($datastoreSetup->checkSchema() == FALSE)
	{
		echo "ERROR\n\nError checking database schema. Not in sync with enities. Exit\n";
		exit(-1);
	}
	echo "OK\n";
	echo "\n";
}

function userSetup()
{
	echo "UserSetup\n";
	$userSetup = new UserSetupHelper();

	//check, if access groups are empty
	echo "- checking if access groups exist in datastore...";
	if($userSetup->checkNoAccessGroups())
	{
		echo "NO\n";
		
		//create default access groups
		echo "- create default access groups...";
		if($userSetup->createDefaultAccessGroups())
		{
			echo "OK\n";
		}
		else
		{
			echo "ERROR\n";
		}

		//create default admin user
		echo "- create default admin user (username: admin, password: yourcmdb)...";
		if($userSetup->createDefaultUser())
		{
			echo "OK\n";
		}
		else
		{
			echo "ERROR\n";
		}

	}
	else
	{
		echo "YES\n";
	}
	echo "\n";
}

function configHelp()
{
	echo "Configuration\n";
	echo "- Please go on following the instructions in yourCMDB wiki to finish the setup)\n";
	echo "\n";
}

?>
