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
use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use yourCMDB\orm\OrmController;
use yourCMDB\config\CmdbConfig;
use yourCMDB\info\InfoController;
use \Exception;

/**
* initializes a yourCMDB datastore
* @author Michael Batz <michael@yourcmdb.org>
*/
class DatastoreSetupHelper
{

	//constant: table name for doctrine migrations
	const MIGRATIONS_TABLE_NAME = "yourcmdb_migration_versions";

	//constant: namespace for doctrine migrations
	const MIGRATIONS_NAMESPACE = "yourCMDB\setup\DatastoreMigrations";

	/**
	* creates a new DatastoreSetupHelper
	*/
	public function __construct()
	{
		;
	}

	/**
	* creates the database version string from yourCMDB version
	* @return string	database version string
	*/
	private function createDatabaseVersionString()
	{
		$infoController = new InfoController();
		$versionMajor = $infoController->getMajorVersionNumber();
		$versionMinor = $infoController->getMinorVersionNumber();

		$versionString = "";
		if($versionMajor < 10)
		{
			$versionString .= "0";
		}
		$versionString .= $versionMajor;
		if($versionMinor < 10)
		{
			$versionString .= "0";
		}
		$versionString .= $versionMinor;
	
		return $versionString;	
	}

	/**
	* creates and returns the configuration for doctrine migrations
	* @return Doctrine\DBAL\Migrations\Configuration\Configuration
	*/
	private function getMigrationsConfiguration()
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
		$migrationConfig->setMigrationsTableName(self::MIGRATIONS_TABLE_NAME);
		$migrationConfig->setMigrationsDirectory("$scriptBaseDir/DatastoreMigrations");
		$migrationConfig->setMigrationsNamespace(self::MIGRATIONS_NAMESPACE);
		$migrationConfig->registerMigrationsFromDirectory("$scriptBaseDir/DatastoreMigrations");

		//return migrations configuration
		return $migrationConfig;
	}

	/**
	* checks connection to datastore using datastore configuration
	*/
	public function checkConnection()
	{	
		//get instances
		$ormController = OrmController::create();
		$entityManager = $ormController->getEntityManager();
		$connection = $entityManager->getConnection();

		//fix to handle enum types for migrating old yourCMDB versions
		$connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

		//try to connect to database
		try
		{
			$connection->connect();
			return true;
		}
		catch(Exception $e)
		{
			return false;
		}
	}

	/**
	* checks if database is empty
	*/
	public function checkIsEmptyDatabase()
	{
		//get instances
		$ormController = OrmController::create();
		$entityManager = $ormController->getEntityManager();
		$connection = $entityManager->getConnection();

		//fix to handle enum types for migrating old yourCMDB versions
		$connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

		$schemaManager = $connection->getSchemaManager();

		$datastoreTables = $schemaManager->listTables();
		if(count($datastoreTables) == 0)
		{
			return true;
		}
		return false;
	}

	/**
	* checks if the schema in configured datastore is in sync with doctrine entities
	* @return boolean	true, if the schema is correct
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

		//set version for doctrine migrations
		$migrationConfig = $this->getMigrationsConfiguration();
		$migrationVersionString = $this->createDatabaseVersionString();
		$migrationVersion = $migrationConfig->getVersion($migrationVersionString);
		$migrationVersion->markMigrated();
		
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

		//set version for doctrine migrations
		$migrationConfig = $this->getMigrationsConfiguration();
		$migrationVersionString = $this->createDatabaseVersionString();
		$migrationVersion = $migrationConfig->getVersion($migrationVersionString);
		$migrationVersion->markMigrated();
	}

	/**
	* migrates the schema in configured datastore to the newest version
	*/
	public function migrateSchema()
	{
		//get instances
		$migrationConfig = $this->getMigrationsConfiguration();
		$schemaMigration = new Migration($migrationConfig);

		//run migration
		$schemaMigration->migrate();
	}
}
?>
