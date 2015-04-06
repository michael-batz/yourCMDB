#! /usr/bin/php
<?php

require "bootstrap.php";
require "model/CmdbObject.php";
require "model/CmdbObjectLink.php";
require "model/CmdbObjectField.php";
require "model/CmdbObjectLogEntry.php";
require "controller/ObjectController.php";
require "controller/ObjectLinkController.php";
require "exceptions/CmdbObjectNotFoundException.php";
require "exceptions/CmdbObjectLinkNotAllowedException.php";


$objectController = ObjectController::create($entityManager);
$objectLinkController = ObjectLinkController::create($entityManager);

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
$objects = $objectController->getObjectsByFieldvalue(array("router", "33"), array("router", "switch"), "A", 0, 0, "michael");
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

/*$objectA = $objectController->getObject(3, "michael");
$objectB = $objectController->getObject(4, "michael");
$strings = $objectLinkController->addObjectLink($objectA, $objectB, "michael");
*/
?>
