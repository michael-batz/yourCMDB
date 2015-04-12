#! /usr/bin/php
<?php

require "bootstrap.php";
require "model/CmdbObject.php";
require "model/CmdbObjectLink.php";
require "model/CmdbObjectField.php";
require "model/CmdbObjectLogEntry.php";
require "model/CmdbJob.php";
require "controller/ObjectController.php";
require "controller/ObjectLinkController.php";
require "controller/ObjectLogController.php";
require "controller/JobController.php";
require "exceptions/CmdbObjectNotFoundException.php";
require "exceptions/CmdbObjectLinkNotAllowedException.php";
require "exceptions/CmdbObjectLinkNotFoundException.php";


$objectController = ObjectController::create($entityManager);
$objectLinkController = ObjectLinkController::create($entityManager);
$objectLogController = ObjectLogController::create($entityManager);
$jobController = JobController::create($entityManager);

//addObject()
/*$fields = Array();
$fields['ip'] = "192.168.0.1";
$fields['hostname'] = "router1";
$objectController->addObject("router", "A", $fields, "michael");
*/

//getObject()
/*try
{
	$object = $objectController->getObject(8, "michael");
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
	$objectController->updateObject(10, "A", $fields, "michael");
}
catch(Exception $e)
{
	echo "Object not found";
}*/

//delete object
//$objectController->deleteObject(13, "michael");


//query objects
//$objects = $objectController->getObjectsByFieldvalue(array("router", "34"), array("router", "switch"), "A", 0, 0, "michael");
/*$objects = $objectController->getLastCreatedObjects(null, 0, 0, "michael");
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
}
*/

/*$objectA = $objectController->getObject(13, "michael");
$objectB = $objectController->getObject(4, "michael");
$strings = $objectLinkController->addObjectLink($objectA, $objectB, "michael");
*/

/*
$objectLinkController->deleteObjectLink($objectA, $objectB, "michael");
*/

//get object log
/*$object = $objectController->getObject(10, "michael");
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
?>
