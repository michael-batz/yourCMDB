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
namespace yourCMDB\setup\DatastoreMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;


/**
* DataStore migration for yourCMDB 0.14
* naming convention for classes: VerionXXYY
* 	where 	XX -> major version number
*		YY -> minor version number
* @author Michael Batz <michael@yourcmdb.org>
*/
class Version0014 extends AbstractMigration
{
	public function up(Schema $schema)
	{
		//nothing to do, since there are no database changes
		;
	}

	public function down(Schema $schema)
	{
		//downgrade not possible
		;
	}
}
?>
