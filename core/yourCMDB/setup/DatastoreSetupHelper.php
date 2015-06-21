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
	* checks if the schema in configured datastore is correct
	* @return boolean	true, if the schema is coorect
	*/
	public function checkSchema()
	{
		//get instances
		$ormController = OrmController::create();
		$entityManager = $ormController->getEntityManager();
		$schemaValidator = new SchemaValidator($entityManager);

		return $schemaValidator->schemaInSyncWithMetadata();
	}
}
?>
