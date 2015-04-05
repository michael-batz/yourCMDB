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

//delete object
/*$objectController->deleteObject(10, "michael");*/


//query objects
$objects = $objectController->getObjectsByType(array("router", "switch"), "hostname", "ASC", "A", 6, 1);
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
?>
