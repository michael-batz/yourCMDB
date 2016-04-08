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
namespace yourCMDB\helper;


/**
* collection of helper functions for substitution of variables in strings
* a variable is defined in the following format: %varname%
* @author Michael Batz <michael@yourcmdb.org>
*/
class VariableSubstitution
{

	/**
	* substitute all object fields in the given input string
	* @param string $input			input string
	* @param CmdbObject $object		CmdbObject
	* @param boolean $failOnMissingVars	fail, if some variables could not be replaced
	* @param int $ignoreFailOnGoodCount	ignore fail, if this count of variables could be replaced
	* @return string		string with replaced variables
	*/
	public static function substituteObjectVariables($input, \yourCMDB\entities\CmdbObject $object, $failOnMissingVars=false, $ignoreFailOnGoodCount=0)
	{
		$variables = Array();
		$variables['yourCMDB_object_id'] = $object->getId();
		$variables['yourCMDB_object_type'] = $object->getType();
		foreach($object->getFields()->getKeys() as $fieldname)
		{
			$fieldvalue = $object->getFieldValue($fieldname);
			$variables[$fieldname] = $fieldvalue;
		}

		$output = self::substitute($input, $variables, $failOnMissingVars, $ignoreFailOnGoodCount);
		return $output;
	}

	/**
	* substitute all variables in the given input string
	* @param string $input			input string
	* @param array $variables		associative array variableName -> variableValue
	* @param boolean $failOnMissingVars	fail, if some variables could not be replaced
	* @param int $ignoreFailOnGoodCount	ignore fail, if this count of variables could be replaced
	* @return string			string with replaced variables, empty string on errors
	*/
	public static function substitute($input, $variables, $failOnMissingVars=false, $ignoreFailOnGoodCount=0)
	{
		$countVars = 0;
		$countReplacedVars = 0 ;
		$output = preg_replace_callback("/%(.+?)%/",
						function ($pregResult) use ($variables, &$countVars, &$countReplacedVars)
						{
							$varName = $pregResult[1];
							$value = $pregResult[0];
							$countVars++;
							if(isset($variables[$varName]))
							{
								$value = $variables[$varName];
								if($value != "")	
								{
									$countReplacedVars++;
								}
							}
							return $value;
						}, 
						$input);
		//error handling, if configured and variables were found
		if($failOnMissingVars && $countVars > 0)
		{
			//if variables were found some variables could not be replaced, fail
			if($countReplacedVars < $countVars )
			{
				//check, if the error should be ignored
				if(!($ignoreFailOnGoodCount > 0 && $countReplacedVars >= $ignoreFailOnGoodCount))
				{
					return "";
				}
			}
		}

		return $output;
	}

}
?>
