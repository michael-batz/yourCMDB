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

	const ENTITY_PATH = "yourCMDB/entities";
	const ENTITY_NAMESPACE = "yourCMDB\entities";
	const ENTITY_NAMESPACE_ALIAS = "yourCMDB";
	const DEVELOPMENT_MODE = true;

	/**
	* creates a new ORM Controller
	*/
	public function __construct()
	{
		//get configuration for connection
		$config = new CmdbConfig();
		$configDatastore = $config->getDatastoreConfig();
		$connectionParameters = array(
			'driver'   => $configDatastore->getDriver(),
			'host'     => $configDatastore->getServer(),
			'port'     => $configDatastore->getPort(),
			'dbname'   => $configDatastore->getDatabaseName(),
			'user'     => $configDatastore->getUser(),
			'password' => $configDatastore->getPassword()
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

	public function getEntityManager()
	{
		return $this->entityManager;
	}
}
?>
