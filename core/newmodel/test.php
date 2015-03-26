#! /usr/bin/php
<?php

require "bootstrap.php";
require "model/CmdbObject.php";
require "model/CmdbObjectField.php";
require "model/CmdbObjectLogEntry.php";
require "controller/ObjectController.php";
require "exceptions/CmdbObjectNotFoundException.php";


$objectController = ObjectController::create($entityManager);

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
	$fields['hostname'] = "router33";
	$fields['admin'] = "Michael";
	$objectController->updateObject(8, "A", $fields, "michael");
}
catch(Exception $e)
{
	echo "Object not found";
}*/

$objectController->deleteObject(10, "michael");

?>
