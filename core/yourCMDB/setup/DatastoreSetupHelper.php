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
namespace yourCMDB\setup;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use yourCMDB\orm\OrmController;
use yourCMDB\config\CmdbConfig;

/**
* initializes a yourCMDB datastore
* @author Michael Batz <michael@yourcmdb.org>
*/
class DatastoreSetupHelper
{
	/**
	* creates a new DatastoreSetupHelper
	*/
	public function __construct()
	{
		;
	}

	/**
	* creates the schema in the configured datastore
	*/
	public function createSchema()
	{
		//get instances
		$ormController = OrmController::create();
		$entityManager = $ormController->getEntityManager();
		$schemaTool = new SchemaTool($entityManager);
		$classes = $entityManager->getMetadataFactory()->getAllMetadata();

		//create schema
		$schemaTool->createSchema($classes);

		//ToDo: set version for doctrine migrations
	}

	/**
	* tries to repair the schema in the configured datastore
	*/
	public function repairSchema()
	{
		//get instances
		$ormController = OrmController::create();
		$entityManager = $ormController->getEntityManager();
		$schemaTool = new SchemaTool($entityManager);
		$classes = $entityManager->getMetadataFactory()->getAllMetadata();

		//create schema
		$schemaTool->updateSchema($classes);
	}

	/**
	* checks if the schema in configured datastore is in sync with doctrine entities
	* @return boolean	true, if the schema is coorect
	*/
	public function checkSchema()
	{
		//get instances
		$ormController = OrmController::create();
		$entityManager = $ormController->getEntityManager();
		$schemaValidator = new SchemaValidator($entityManager);

		//check schema
		return $schemaValidator->schemaInSyncWithMetadata();
	}

	/**
	* migrates the schema in configured datastore to the newest version
	*/
	public function migrateSchema()
	{
		//get instances
		$scriptBaseDir = realpath(dirname(__FILE__));
		$ormController = OrmController::create();
		$connection = $ormController->getEntityManager()->getConnection();

		//fix to handle enum types for migrating old yourCMDB versions
		$connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

		//migration configuration
		$migrationConfig = new Configuration($connection);
		$migrationConfig->setName("yourCMDB datastore migrations");
		$migrationConfig->setMigrationsTableName("yourcmdb_migration_versions");
		$migrationConfig->setMigrationsDirectory("$scriptBaseDir/DatastoreMigrations");
		$migrationConfig->setMigrationsNamespace("yourCMDB\setup\DatastoreMigrations");
		$migrationConfig->registerMigrationsFromDirectory("$scriptBaseDir/DatastoreMigrations");
		$schemaMigration = new Migration($migrationConfig);

		//run migration
		$schemaMigration->migrate();
	}
}
?>
