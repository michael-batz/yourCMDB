<?php
/********************************************************************
* This file is part of yourCMDB.
*
* Copyright 2013-2014 Michael Batz
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
* Defines some helper functions for object section in WebUI
* @author Michael Batz <michael@yourcmdb.org>
*/

	/**
	* Creates the HTML element for a given datatype
	* @param $objectType	type of object (used for autocomplete functions)
	* @param $name		name of the field
	* @param $value		value of the field
	* @param $type		type of the field
	* @param $writable	true, if an edit view should be created
	*/
	function showFieldForDataType($objectType, $name, $value, $type, $writable=true)
	{
		//get type parameter (field type in format <type>-<typeparameter>)
		$typeParameter = "";
		if(preg_match('/^(.*?)-(.*)/', $type, $matches) == 1)
		{
			$type = $matches[1];
			$typeParameter = $matches[2];
		}

		switch($type)
		{
			case "boolean":
				showFieldForBoolean($name, $value, $writable);
				break;

			case "date":
				showFieldForDate($name, $value, $writable);
				break;

			case "text":
				showFieldForText($objectType, $name, $value, $writable);
				break;

			case "textarea":
				showFieldForTextarea($name, $value, $writable);
				break;

			case "objectref";
				showFieldForObjectref($typeParameter, $name, $value, $writable);
				break;

			default:
				showFieldForText($objectType, $name, $value, $writable);
				break;
		}
	}

	function showFieldForText($objectType, $name, $value, $writable)
	{
		if($writable)
		{
			?>
				<input id="<?php echo $name; ?>" 
					type="text" 
					name="<?php echo $name; ?>" 
					value="<?php echo htmlspecialchars($value); ?>" 
					onfocus="javascript:showAutocompleter('#<?php echo $name; ?>', 'autocomplete.php?object=object&amp;var1=<?php echo $objectType;?>&amp;var2=<?php echo $name?>')" />
			<?php
		}
		else
		{
			echo htmlspecialchars($value);
		}
	}

	function showFieldForDate($name, $value, $writable)
	{
		if($writable)
		{
			?>
				<input id="<?php echo $name; ?>" 
					type="text" 
					name="<?php echo $name; ?>" 
					value="<?php echo $value; ?>" 
					onfocus="javascript:showDatepicker('#<?php echo $name; ?>')" />
			<?php
		}
		else
		{
			echo htmlspecialchars($value);
		}
	}

	function showFieldForTextarea($name, $value, $writable)
	{
		if($writable)
		{
			?>
				<textarea id="<?php echo $name; ?>" name="<?php echo $name; ?>" ><?php echo htmlspecialchars($value); ?></textarea>
			<?php
		}
		else
		{
			echo nl2br(htmlspecialchars($value));
		}
	}

	function showFieldForBoolean($name, $value, $writable)
	{
		$checkboxString = "<input type=\"checkbox\" id=\"$name\" name=\"$name\" value=\"true\" ";
		if($value == "TRUE" || $value == "true" || $value == "1")
		{
			$checkboxString.= "checked=\"checked\" ";
		}
		if(!$writable)
		{
			$checkboxString.= "disabled=\"disabled\" ";
		}
		$checkboxString.= "/>";
		echo $checkboxString;
	}

	function showFieldForObjectref($typeParameter, $name, $value, $writable)
	{
		//use global datastore variable
		global $datastore, $config;

		//get summary fields for object type
		$summaryFields = array_keys($config->getObjectTypeConfig()->getSummaryFields($typeParameter));

		//get summary of referenced object
		$refObjectSummary = "";
		try
		{
				$refObject = $datastore->getObject($value);
				if($refObject->getType() == $typeParameter)
				{
					foreach($summaryFields as $summaryField)
					{
						$refObjectSummary .= $refObject->getFieldValue($summaryField);
						$refObjectSummary .= " ";
					}
					$refObjectSummary .= "($typeParameter: $value)";
				}
		}
		//do nothing on exception
		catch(Exception $e)
		{
			;
		}

		//print header, null value and current value
		if($writable)
		{
			echo "<select name=\"$name\" size=\"1\">";
		}
		else
		{
			echo "<select name=\"$name\" size=\"1\" disabled=\"disabled\">";
		}
		echo "<option value=\"\"></option>";
		if($refObjectSummary != "")
		{
			echo "<option value=\"$value\" selected=\"selected\">$refObjectSummary</option>";
		}

		if($writable)
		{
			//get all objects by type
			$refAllObjects = $datastore->getObjectsByType($typeParameter);
			foreach($refAllObjects as $refAllObject)
			{
				$refAllObjectId = $refAllObject->getId();
				$refAllObjectSummary = "";
				foreach($summaryFields as $summaryField)
				{
					$refAllObjectSummary .= $refAllObject->getFieldValue($summaryField);
					$refAllObjectSummary .= " ";
				}
				$refAllObjectSummary .= "($typeParameter: $refAllObjectId)";
				echo "<option value=\"$refAllObjectId\">$refAllObjectSummary</option>";
			}
		}

		//print footer
		echo "</select>";

		if(!$writable)
		{
			//print link to referenced object
			if($value != "")
			{
				echo "<a href=\"object.php?id=$value\">";
				echo gettext("show");
				echo "</a>";
			}
		}
	}
?>
