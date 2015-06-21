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
namespace yourCMDB\setup\DatastoreMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;


/**
* DataStore migration for yourCMDB 0.9
* naming convention for classes: VerionXXYY
* 	where 	XX -> major version number
*		YY -> minor version number
* @author Michael Batz <michael@yourcmdb.org>
*/
class Version0009 extends AbstractMigration
{
	public function up(Schema $schema)
	{
		//ToDo: remove comments

		//remove deleted objects
		$customSql = "DELETE FROM CmdbObject WHERE active='D'";
		//$this->addSql($customSql);

		//remove foreign keys
		$customSql = "ALTER TABLE CmdbObjectField DROP FOREIGN KEY CmdbObjectField_ibfk_1";
		//$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLog DROP FOREIGN KEY CmdbObjectLog_ibfk_1";
		//$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLink DROP FOREIGN KEY CmdbObjectLink_ibfk_1";
		//$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLink DROP FOREIGN KEY CmdbObjectLink_ibfk_2";
		//$this->addSql($customSql);

		//CmdbObject
		$customSql = "ALTER TABLE CmdbObject CHANGE assetid id INT NOT NULL AUTO_INCREMENT";
		//$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObject CHANGE active status VARCHAR(1)";
		//$this->addSql($customSql);

		//CmdbObjectField
		$customSql = "ALTER TABLE CmdbObjectField MODIFY fieldvalue LONGTEXT";
		//$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectField CHANGE assetid object_id INT NOT NULL";
		//$this->addSql($customSql);

		//CmdbObjectLink
		$customSql = "ALTER TABLE CmdbObjectLink CHANGE assetidA objectA_id INT NOT NULL";
		//$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLink CHANGE assetidB objectB_id INT NOT NULL";
		//$this->addSql($customSql);
				

		//ToDo: add primary key, foreign keys, index
		
	}

	public function down(Schema $schema)
	{
		//downgrade not possible
		;
	}
}
?>
