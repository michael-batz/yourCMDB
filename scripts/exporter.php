#! /usr/bin/php5
<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2014 Michael Batz
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
* yourCMDB exporter - runs an export of objects
* @author Michael Batz <michael@yourcmdb.org>
*/

	/**
        * autoloading of classes
        */
        function __autoload($className)
        {
		$scriptBaseDir = dirname(__FILE__);
                $coreBaseDir = realpath("$scriptBaseDir/../core");
                $paths = array('', 'model', 'config', 'controller', 'libs', 'rest', 'exporter');
                $filename = $className.'.php';
                foreach($paths as $path)
                {
                        if(file_exists("$coreBaseDir/$path/$filename"))
                        {
                                include "$coreBaseDir/$path/$filename";
                        }
                }
        }

	/**
	* print usage of exporter script
	*/
	function printUsage()
	{
		echo "yourCMDB Exporter\n";
		echo "Usage: exporter.php <export taskname>\n";
		echo "<export taskname> is the name of the export task to execute defined in exporter-configuration.xml\n\n";
	}

	//get taskname from script parameter
	$taskname = "";
	if($_SERVER['argc'] >= 2)
	{
		$taskname = $_SERVER['argv'][1];
	}
	else
	{
		printUsage();
		exit();
	}

	//check if taskname is valid
	try
	{
		new Exporter($taskname);
	}
	catch(Exception $e)
	{
		echo "yourCMDB Exporter\n";
		echo "Error starting Exporter\n";
		echo "Please check exporter-configuration.xml\n";
		echo "Error Detail: $e";
	}

?>

