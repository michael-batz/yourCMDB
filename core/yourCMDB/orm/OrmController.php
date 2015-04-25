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
namespace yourCMDB\orm;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use yourCMDB\config\CmdbConfig;

/**
* controller for accessing Doctrine ORM
* @author Michael Batz <michael@yourcmdb.org>
*/
class OrmController
{
	//Doctrine entityManager
	private $entityManager;

	/**
	* creates a new ORM Controller
	*/
	public function __construct()
	{
		//ToDo: get values from configuration
		$scriptBaseDir = dirname(__FILE__);
		$coreBaseDir = realpath("$scriptBaseDir");
		$paths = array("$coreBaseDir/yourCMDB/entities");

		//ToDo: devMode off
		$isDevMode = true;

		// the connection configuration
		$dbParams = array(
		'driver'   => 'pdo_mysql',
		'user'     => 'cmdb',
		'password' => 'cmdb',
		'dbname'   => 'yourcmdb2',
		);

		$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
		$config->addEntityNamespace("yourCMDB", "yourCMDB\entities");

		//ToDo: debug off
		//$config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());

		$this->entityManager = EntityManager::create($dbParams, $config);
	}

	public function getEntityManager()
	{
		return $this->entityManager;
	}
}
?>
