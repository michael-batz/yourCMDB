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
		$this->addSql($customSql);

		//remove foreign keys
		$customSql = "ALTER TABLE CmdbObjectField DROP FOREIGN KEY CmdbObjectField_ibfk_1";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLog DROP FOREIGN KEY CmdbObjectLog_ibfk_1";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLink DROP FOREIGN KEY CmdbObjectLink_ibfk_1";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLink DROP FOREIGN KEY CmdbObjectLink_ibfk_2";
		$this->addSql($customSql);

		//CmdbObject
		$customSql = "ALTER TABLE CmdbObject CHANGE assetid id INT NOT NULL AUTO_INCREMENT";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObject CHANGE active status VARCHAR(1) COLLATE utf8_unicode_ci NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObject MODIFY type varchar(64) COLLATE utf8_unicode_ci NOT NULL";
		$this->addSql($customSql);

		//CmdbObjectField
		$customSql = "ALTER TABLE CmdbObjectField MODIFY fieldvalue longtext COLLATE utf8_unicode_ci";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectField CHANGE assetid object_id INT NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectField MODIFY fieldkey varchar(64) COLLATE utf8_unicode_ci NOT NULL";

		//CmdbObjectLink
		$customSql = "ALTER TABLE CmdbObjectLink CHANGE assetidA objectA_id INT NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLink CHANGE assetidB objectB_id INT NOT NULL";
		$this->addSql($customSql);
				
		//CmdbObjectLogEntry
		$customSql = "RENAME TABLE CmdbObjectLog TO CmdbObjectLogEntry";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLogEntry CHANGE assetid object_id INT NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLogEntry MODIFY action VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLogEntry CHANGE date timestamp datetime NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLogEntry ADD description LONGTEXT COLLATE utf8_unicode_ci";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLogEntry ADD user VARCHAR(64) COLLATE utf8_unicode_ci DEFAULT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbObjectLogEntry ADD id INT PRIMARY KEY AUTO_INCREMENT";
		$this->addSql($customSql);
		$customSql = "UPDATE CmdbObjectLogEntry SET action='create' WHERE action='add'";
		$this->addSql($customSql);

		//CmdbLocalUser
		$customSql = "ALTER TABLE CmdbLocalUser MODIFY username varchar(255) COLLATE utf8_unicode_ci NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbLocalUser CHANGE passwordhash passwordHash longtext COLLATE utf8_unicode_ci NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbLocalUser CHANGE accessgroup accessGroup longtext COLLATE utf8_unicode_ci NOT NULL";
		$this->addSql($customSql);

		//CmdbJob
		$customSql = "ALTER TABLE CmdbJob CHANGE jobid id int(11) NOT NULL AUTO_INCREMENT";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbJob MODIFY action longtext COLLATE utf8_unicode_ci NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbJob MODIFY actionParameter longtext COLLATE utf8_unicode_ci";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbJob MODIFY timestamp datetime DEFAULT NULL";
		$this->addSql($customSql);

		//CmdbAccessRule
		$customSql = "RENAME TABLE CmdbAccessRules TO CmdbAccessRule";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbAccessRule CHANGE accessgroup accessgroup_id varchar(64) COLLATE utf8_unicode_ci NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbAccessRule CHANGE applicationpart applicationPart varchar(255) COLLATE utf8_unicode_ci NOT NULL";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbAccessRule MODIFY access int(11) NOT NULL";
		$this->addSql($customSql);

		//CmdbAccessGroup
		$customSql = "CREATE TABLE CmdbAccessGroup LIKE CmdbAccessRule";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbAccessGroup DROP COLUMN applicationPart";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbAccessGroup DROP COLUMN access";
		$this->addSql($customSql);
		$customSql = "INSERT CmdbAccessGroup SELECT distinct accessgroup_id FROM CmdbAccessRule";
		$this->addSql($customSql);
		$customSql = "ALTER TABLE CmdbAccessGroup CHANGE accessgroup_id name varchar(64) COLLATE utf8_unicode_ci NOT NULL";
		$this->addSql($customSql);


		//ToDo: add primary key, foreign keys, index
		
	}

	public function down(Schema $schema)
	{
		//downgrade not possible
		;
	}
}
?>
