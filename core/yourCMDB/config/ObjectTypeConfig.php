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
namespace yourCMDB\config;

/**
* Class for access to object type configuration
* @author Michael Batz <michael@yourcmdb.org>
*/

class ObjectTypeConfig
{

	//groups of object types
	private $groups;

	//object types
	private $objectTypes;

	//object fields
	private $objectFields;

	//static object information
	private $objectStatic;

	//external links for object
	private $objectExternalLinks;

	//custom event definitions
	private $objectCustomEvents;

	/**
	* creates a ObjectTypeConfig object from xml file objecttype-configuration.xml
	*/
	public function __construct($xmlfile)
	{
		//get XML String
		$xmlstring = file_get_contents($xmlfile);
		$xmlstring = preg_replace_callback('#<includeconfig(.*?)file="(.*?)"(.*?)/>#',function ($match) use ($xmlfile){return file_get_contents(dirname($xmlfile)."/".$match[2]);},$xmlstring);
		$xmlobject = simplexml_load_string($xmlstring);

		//get all object types and their fields
		$this->objectTypes = Array();
		$this->objectFields = Array();
		$this->objectStatic = Array();
		$this->objectExternalLinks = Array();
		foreach($xmlobject->xpath('//object-type') as $objectType)
		{
			//save object type
			$objectName = (string)$objectType['name'];
			$this->objectTypes[] = $objectName;


			//save object fields
			foreach($objectType[0]->fields->fieldgroup as $fieldGroup)
			{
				$fieldGroupName = (string)$fieldGroup['name'];
				foreach($fieldGroup->field as $field)
				{
					$fieldName = (string)$field['name'];
					$fieldType = (string)$field['type'];
					$fieldLabel = (string)$field['label'];
					$fieldDefaultValue = (string)$field['default'];
					$fieldIsSummary = false;
					if(isset($field['summaryfield']) && $field['summaryfield'] == "true")
					{
						$fieldIsSummary = true;
					}
					$this->objectFields[$objectName][$fieldGroupName][$fieldName]['name'] = $fieldName;
					$this->objectFields[$objectName][$fieldGroupName][$fieldName]['type'] = $fieldType;
					$this->objectFields[$objectName][$fieldGroupName][$fieldName]['label'] = $fieldLabel;
					$this->objectFields[$objectName][$fieldGroupName][$fieldName]['default'] = $fieldDefaultValue;
					$this->objectFields[$objectName][$fieldGroupName][$fieldName]['summary'] = $fieldIsSummary;
				}
			}

			//save static object information
			if(isset($objectType[0]->static[0]))
			{
				foreach($objectType[0]->static[0] as $staticField)
				{
					$staticFieldName = $staticField->getName();
					$staticFieldValue = (string) $staticField;
					$this->objectStatic[$objectName][$staticFieldName] = $staticFieldValue;
				}
			}

			//save external links for object
			if(isset($objectType[0]->links[0]))
			{
				foreach($objectType[0]->links[0] as $objectLink)
				{
					$linkName = (string)$objectLink['name'];
					$linkHref = (string)$objectLink['href'];
					$this->objectExternalLinks[$objectName][] = Array('name' => $linkName, 'href' => $linkHref);
				}
			}

			//save custom event definitions for object
			if(isset($objectType[0]->eventdefs[0]))
			{
				foreach($objectType[0]->eventdefs[0] as $objectEvent)
				{
					$eventName = (string)$objectEvent['name'];
					$eventLabel = (string)$objectEvent['label'];
					$this->objectCustomEvents[$objectName][] = Array('name' => $eventName, 'label' => $eventLabel);
				}
			}

		}

		//get object type groups
		$this->groups = Array();
		foreach($xmlobject->xpath('//group') as $group)
		{
			//get group name
			$groupName = (string)$group['name'];


			//get all object types of the group
			foreach($group[0]->{'object-type'} as $groupObjectType)
			{
				$groupObjectTypeName = (string)$groupObjectType['name'];
				$this->groups[$groupName][] = $groupObjectTypeName;
			}
		}
	}


	/**
	* Returns an array with all groups of object types and their object types
	* @returns 	Array in form groupname => array(object-types of group)
	*/
	public function getObjectTypeGroups()
	{
		return $this->groups;
	}


	/**
	* Returns an array of all known object types
	*/
	public function getAllTypes()
	{
		return $this->objectTypes;
	}

	/**
	* Returns an array with all fields of a specific object type
	* @param $objectType 	object type for getting the fields
	* @returns 		array with fieldname->datatype
	*/
	public function getFields($objectType)
	{
		$output = Array();
		foreach($this->objectFields[$objectType] as $group)
		{
			foreach($group as $field)
			{
				$fieldname = $field['name'];
				$fieldtype = $field['type'];
				$output[$fieldname] = $fieldtype;
			}
		}
		return $output;
	}

	/**
	* Returns an array with all summary fields of a specific object type
	* @param $objectType	object type for getting the summary fields
	* @returns 		array with fieldname->datatype
	*/
	public function getSummaryFields($objectType)
	{
		$output = Array();
		foreach($this->objectFields[$objectType] as $group)
		{
			foreach($group as $field)
			{
				if($field['summary'])
				{	
					$fieldname = $field['name'];
					$fieldtype = $field['type'];
					$output[$fieldname] = $fieldtype;
				}
			}
		}
		return $output;
	}


	/**
	* Returns an array with all field groups of a specific object type
	* @param $objectType	object type for getting the summary fields
	* @returns 		array with field groups
	*/
	public function getFieldGroups($objectType)
	{
		$output = Array();
		foreach(array_keys($this->objectFields[$objectType]) as $group)
		{
			$output[] = $group;
		}

		return $output;
	}


	/**
	* Returns an array with all fields of a specific object type and fieldgroup
	* @param $objectType 	object type for getting the fields
	* @param $groupname 	name of the field group 
	* @returns 		array with fieldname->datatype
	*/
	public function getFieldGroupFields($objectType, $groupname)
	{
		$output = Array();
		foreach($this->objectFields[$objectType][$groupname] as $field)
		{
			$fieldname = $field['name'];
			$fieldtype = $field['type'];
			$output[$fieldname] = $fieldtype;
		}
		return $output;

	}


	/**
	* Returns the datatype of a specific field 
	* @param $objectType 	object type
	* @param $fieldname 	field name
	* @returns 		datatype of the field
	*/
	public function getFieldType($objectType, $fieldname)
	{
		//default value
		$type = "text";

		//search in all groups of the object
		foreach($this->objectFields[$objectType] as $group)
		{
			if(isset($group[$fieldname]['type']) && $group[$fieldname]['type'] != "")
			{
				$type = $group[$fieldname]['type'];
			}
		}
	
		//return
		return $type;
	}


	/**
	* Returns the label of a specific field 
	* @param $objectType 	object type
	* @param $fieldname 	field name
	* @returns 		label of the field
	*/
	public function getFieldLabel($objectType, $fieldname)
	{
		//default value
		$label = $fieldname;

		//search in all groups of the object
		foreach($this->objectFields[$objectType] as $group)
		{
			if(isset($group[$fieldname]['label']) && $group[$fieldname]['label'] != "")
			{
				$label = $group[$fieldname]['label'];
			}
		}
	
		//return
		return $label;
	}

	/**
	* Returns the default value of a specific field 
	* @param $objectType 	object type
	* @param $fieldname 	field name
	* @returns 		default value of the field
	*/
	public function getFieldDefaultValue($objectType, $fieldname)
	{
		//default value
		$default = "";

		//search in all groups of the object
		foreach($this->objectFields[$objectType] as $group)
		{
			if(isset($group[$fieldname]['default']))
			{
				$default = $group[$fieldname]['default'];
			}
		}
	
		//return
		return $default;
	}


	/**
	* Returns the value of a static object field 
	* @param $objectType 	object type
	* @param $fieldname 	field name
	* @returns 		value of a static object field or empty string of not set
	*/
	public function getStaticFieldValue($objectType, $fieldname)
	{
		//default value (empty string)
		$output = "";

		if(isset($this->objectStatic[$objectType][$fieldname]))
		{
			$output = $this->objectStatic[$objectType][$fieldname];
		}

		return $output;
	}

	/**
	* Returns object links 
	* @param $objectType 	object type
	* @returns 		array of object links (Array(name, href))
	*/
	public function getObjectLinks($objectType)
	{
		//default value: empty array
		$output = Array();

		//check, if links were set for the given object type
		if(isset($this->objectExternalLinks[$objectType]))
		{
			$output = $this->objectExternalLinks[$objectType];
		}

		return $output;
	}

	/**
	* Returns object custom events
	* @param $objectType 	object type
	* @returns 		array of object events (Array(name, label))
	*/
	public function getObjectEvents($objectType)
	{
		//default value: empty array
		$output = Array();

		//check, if events were set for the given object type
		if(isset($this->objectCustomEvents[$objectType]))
		{
			$output = $this->objectCustomEvents[$objectType];
		}

		return $output;
	}

	/**
	* Gets all object-field pairs where field has a specific data type
	* @returns array(objectName,fieldName)
	*/
	public function getFieldsByType($dataType)
	{
		$output = Array();
	
		//walk through all object types
		foreach(array_keys($this->objectFields) as $objectType)
		{
			//walk through all fields of th specific object type
			foreach($this->objectFields[$objectType] as $fieldGroup)
			{
				foreach(array_keys($fieldGroup) as $fieldName)
				{
					if($fieldGroup[$fieldName]['type'] == $dataType)
					{
						$output[] = Array($objectType, $fieldName);
					}
				}
			}
		}

		//return output
		return $output;
	}
}

?>
