#! /usr/bin/php
<?php
use yourCMDB\entities\CmdbObject;
use yourCMDB\entities\CmdbJob;
use yourCMDB\entities\CmdbLocalUser;
use yourCMDB\controller\ObjectController;
use yourCMDB\controller\ObjectLinkController;
use yourCMDB\controller\ObjectLogController;
use yourCMDB\controller\JobController;
use yourCMDB\controller\LocalUserController;

require "bootstrap.php";

$objectController = ObjectController::create($entityManager);
$objectLinkController = ObjectLinkController::create($entityManager);
$objectLogController = ObjectLogController::create($entityManager);
$jobController = JobController::create($entityManager);
$userController = LocalUserController::create($entityManager);

//addObject()
/*$fields = Array();
$fields['ip'] = "192.168.0.1";
$fields['hostname'] = "router1";
$objectController->addObject("router", "A", $fields, "michael");
*/

//getObject()
//try
/*{
	$object = $objectController->getObject(677, "michael");
	print_r($object);
}
catch(Exception $e)
{
	echo "Object not found";
}*/

//updateObject()
/*try
{
	$fields = Array();
	$fields['ip'] = "192.168.0.33";
	$fields['hostname'] = "router34";
	$fields['admin'] = "Michael";
	$objectController->updateObject(6, "A", $fields, "michael");
}
catch(Exception $e)
{
	echo "Object not found";
}*/

//delete object
//$objectController->deleteObject(3, "michael");


//query objects
//$objects = $objectController->getObjectsByFieldvalue(array("router", "34"), array("router", "switch"), "A", 0, 0, "michael");
/*$objects = $objectController->getLastChangedObjects(null, 0, 0, "michael");

foreach($objects as $object)
{
	echo "ID  ";
	echo $object->getId();
	echo ";";
	$fields = $object->getFields();
	foreach($fields->getKeys() as $fieldkey)
	{
	        $fieldvalue = $object->getFields()->get($fieldkey)->getFieldvalue();
       		echo "$fieldkey = $fieldvalue; ";
	}
	echo "\n";
}*/


/*$objectA = $objectController->getObject(4, "michael");
$objectB = $objectController->getObject(7, "michael");
$strings = $objectLinkController->addObjectLink($objectA, $objectB, "michael");
*/


//$objectLinkController->deleteObjectLink($objectA, $objectB, "michael");


//get object log
/*$object = $objectController->getObject(6, "michael");
$objectLog = $objectLogController->getLogEntries($object, 0, 0, "michael");
foreach($objectLog as $logEntry)
{
	echo $logEntry->getDescription();
	echo "\n";
}*/

//add job
/*$job = new CmdbJob("testjob", null, null);
$jobController->addJob($job);
*/
//get jobs
/*$jobResults = $jobController->getAndRemoveJobs();
foreach($jobResults as $jobResult)
{
	echo "Job: " . $jobResult->getAction() . "; " .$jobResult->getTimestamp(). "\n";
}*/

//add user
//$userController->addUser(new CmdbLocalUser("admin1", "admin", "test123"));

//get user
//$user = $userController->getUser("admin1");

//change user
/*$user->setAccessGroup("user");
$userController->changeUser($user);*/

//delete user
$userController->deleteUser("admin1");

?>
