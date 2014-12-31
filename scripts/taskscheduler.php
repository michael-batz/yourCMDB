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
* yourCMDB TaskScheduler - runs created jobs from database
* @author Michael Batz <michael@yourcmdb.org>
*/

	/**
        * autoloading of classes
        */
        function __autoload($className)
        {
		$scriptBaseDir = dirname(__FILE__);
                $coreBaseDir = realpath("$scriptBaseDir/../core");
                $paths = array('', 'model', 'config', 'controller', 'libs', 'rest', 'exporter', 'taskscheduler');
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
	* print usage of TaskScheduler script
	*/
	function printUsage()
	{
		echo "yourCMDB TaskScheduler\n";
		echo "Usage: taskscheduler.php\n";
	}


	//run jobs
	$taskScheduler = new TaskScheduler();
	$taskScheduler->executeJobs();
	
?>

