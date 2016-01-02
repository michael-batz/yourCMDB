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
namespace yourCMDB\orm;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use yourCMDB\config\CmdbConfig;

/**
* controller for accessing Doctrine ORM
* singleton: use OrmController::create() for getting an instance
* @author Michael Batz <michael@yourcmdb.org>
*/
class OrmController
{
	//OrmController instance for singleton
	private static $ormController;

	//Doctrine entityManager
	private $entityManager;

	const ENTITY_PATH = "yourCMDB/entities";
	const ENTITY_NAMESPACE = 'yourCMDB\entities';
	const ENTITY_NAMESPACE_ALIAS = "yourCMDB";
	const DEVELOPMENT_MODE = true;

	/**
	* private constructor
	* creates a new ORM Controller
	*/
	private function __construct()
	{
		//get configuration for connection
		$config = CmdbConfig::create();
		$configDatastore = $config->getDatastoreConfig();
		$connectionParameters = array(
			'driver'   => $configDatastore->getDriver(),
			'host'     => $configDatastore->getServer(),
			'port'     => $configDatastore->getPort(),
			'dbname'   => $configDatastore->getDatabaseName(),
			'user'     => $configDatastore->getUser(),
			'password' => $configDatastore->getPassword(),
			'charset'  => 'utf8'
		);

		//configuration of Doctrine EntityManager
		$scriptBaseDir = dirname(__FILE__);
		$coreBaseDir = realpath("$scriptBaseDir/../../");
		$paths = array("$coreBaseDir/".self::ENTITY_PATH);
		$config = Setup::createAnnotationMetadataConfiguration($paths, self::DEVELOPMENT_MODE);
		$config->addEntityNamespace(self::ENTITY_NAMESPACE_ALIAS, self::ENTITY_NAMESPACE);

		//create entity manager
		$this->entityManager = EntityManager::create($connectionParameters, $config);
	}

	/**
	* Creates an OrmController
	* @return OrmController	an OrmController instance
	*/
	public static function create()
	{
		//check, if an instance of OrmController already exists
		if(OrmController::$ormController == null)
		{
			OrmController::$ormController = new OrmController();
		}

		//return instance
		return OrmController::$ormController;
	}

	/**
	* Returns the Doctrine EntityManager
	* @return EntityManager	Doctrine EntityManager
	*/
	public function getEntityManager()
	{
		return $this->entityManager;
	}
}
?>
