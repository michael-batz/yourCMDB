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
namespace yourCMDB\exporter;

use yourCMDB\orm\OrmController;
use yourCMDB\controller\ObjectController;
use yourCMDB\config\CmdbConfig;
use \Exception;

/**
* Export API - a variable for an export task
* @author Michael Batz <michael@yourcmdb.org>
*/
class ExportVariable
{
	//name of variable
	private $name;

	//default value
	private $defaultValue;

	//fields by object type to get the value
	//Array objectType -> name 
	//                 -> refobjectfield
	private $fieldValue;


	function __construct($name, $defaultValue, $fieldValue)
	{
		$this->name = $name;
		$this->defaultValue = $defaultValue;
		$this->fieldValue = $fieldValue;
	}

	/**
	* Returns the name of variable
	*/
	public function getName()
	{
		return $this->name;
	}

	/**
	* Returns the value of variable for the given CmdbObject
	* @param CmdbObject $object	the object to get the value
	*/
	public function getValue(\yourCMDB\entities\CmdbObject $object)
	{
		//get ObjectController
		$objectController = ObjectController::create();

		//get object type config
		$config = CmdbConfig::create();
		$configObjecttype = $config->getObjectTypeConfig();
		$value = $this->defaultValue;

		//if there is a configuration for that object type
		//use the content of the specified field as value
		$objectType = $object->getType();
		if(isset($this->fieldValue[$objectType]['name']))
		{
			$fieldname = $this->fieldValue[$objectType]['name'];

			//check special fieldnames
			switch($fieldname)
			{
				case "yourCMDB_object_id":
					$value = $object->getId();
					break;

				case "yourCMDB_object_type":
					$value = $object->getType();
					break;

                default:
                    //simply use the field value
					$value = $object->getFieldvalue($fieldname);

                    //check if field is an object reference (type objectref) 
                    //and refobjectfield in exporter config is defined
                    if(
                        (preg_match('/objectref-.*/', $configObjecttype->getFieldType($objectType, $fieldname)) == 1)
                        && ($this->fieldValue[$objectType]['refobjectfield'] != "")
                    )
					{
						try
						{
							//get referenced object
                            $refObject = $objectController->getObject($value, "yourCMDB-exporter");

                            //get object field path from config entry "refobjectfield"
							$refFieldname = $this->fieldValue[$objectType]['refobjectfield'];
                            foreach(preg_split("#\.#", $refFieldname) as $refFieldnameElement)
                            {
                                //get value of referenced field
                                $value = $refObject->getFieldvalue($refFieldnameElement);
                                //if referenced field is a reference itself -> dereference
                                if(preg_match('/objectref-.*/', $configObjecttype->getFieldType($refObject->getType(), 
                                              $refFieldnameElement)) == 1)
                                {
                                    $refObject = $objectController->getObject($value, "yourCMDB-exporter");
                                }
							}
						}
						catch(Exception $e)
						{
							;
                        }
					}
			}
		}

		//return the value
		return $value;
    }

}
?>
