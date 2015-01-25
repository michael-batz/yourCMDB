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
* Interface for persisting objects
* @author Michael Batz <michael@yourcmdb.org>
*/
interface DataStoreInterface
{

	public function isObject($id, $type=null);

	public function getObject($id);

	public function addObject(CmdbObject $cmdbObject);

	public function changeObjectFields($id, $newFields);
	
	public function changeObjectStatus($id, $newStatus);
	
	public function deleteObject($id);

	public function getObjectsByType($type, $sortfield="", $sorttype="asc", $activeOnly=true, $max=0, $start=0);
	
	public function getObjectsByField($fieldname, $fieldvalue, $types=null, $activeOnly=true, $max=0, $start=0);
	
	public function getObjectsByFieldvalue($searchstring, $types=null, $activeOnly=true, $max=0, $start=0);
	
	public function getObjectLinks($id);

	public function getLinkedObjects($id);

	public function addObjectLink($idA, $idB);

	public function deleteObjectLink($idA, $idB);

	public function getObjectCounts($type);

	public function getAllFieldValues($objecttype=null, $fieldname=null, $searchstring=null, $limit=10);
	
	public function getObjectLog($objectId);

	public function getNNewestObjects($n);

	public function getNLastChangedObjects($n);

	public function addJob(CmdbJob $job, int $timestamp = null);

	public function getAndRemoveJobs();
	
	public function getObjectReferences($objectId);

	public function addUser(CmdbLocalUser $user);
	
	public function changeUser($username, CmdbLocalUser $newuser);

	public function deleteUser($username);

	public function getUser($username);

	public function getUsers();

	public function getAccessRights($accessgroup, $applicationparts);
}
?>
