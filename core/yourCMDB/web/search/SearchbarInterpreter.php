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
namespace yourCMDB\web\search;

use yourCMDB\config\CmdbConfig;
use yourCMDB\controller\ObjectController;
use yourCMDB\exceptions\CmdbObjectNotFoundException;

/**
* Interpreter for yourCMDB searchbar
* tries to guess the best search filter from the users text input
* Usage: 
* - use __construct() to create a new object
* - use returnSearchFilter to get the SearchFilter object based on the user input
*
* @author Michael Batz <michael@yourcmdb.org>
*/
class SearchbarInterpreter
{
	//user input text in searchbar
	private $userInput;

	//result SearchFilter;
	private $searchFilter;

	//CmdbObject, if found ObjectID in user input
	private $interpretedObject;

	/**
	* Creates a new SearchbarInterpreter for the given user input
	* @param string $userInput	text input of the user
	*/
	public function __construct($userInput)
	{
		//setup variables
		$this->userInput = $userInput;
		$this->searchFilter = new SearchFilter();
		$this->interpretedObject = null;

		//start with interpreter
		$this->interpreteUserInput();
	}

	private function interpreteUserInput()
	{
		//parse input string
		$inputArray = array_filter(explode(" ", $this->userInput));

		//try to find object ID in user input
		$this->interpreteObjectId($inputArray);

		//try to find object types
		$interpreterResult = $this->interpreteObjectTypes($inputArray);

		//if interpreter was not successful, use default
		if(!$interpreterResult)
		{
			$this->interpreteDefault();
		}

		//add default filter
		$this->searchFilter->addFilter("status=A");
		
	}

	/**
	* Interpreter:
	* Tries to find object types in search string and adds the given filter
	* @return boolean	true if it was successful
	*			false, if it was not successful
	*/
	private function interpreteObjectTypes($inputArray)
	{
		//get CMDB Config
		$config = CmdbConfig::create();

		//find object types in input string
		$interpretedTypes = Array();
                $interpretedText = Array();
		$objectTypes = $config->getObjectTypeConfig()->getAllTypes();
		//walk through every element of inputArray
                for($i = 0; $i < count($inputArray); $i++)
                {
                        $countMatches = count($interpretedTypes);
			//walk over all existing object types
                        foreach($objectTypes as $objectType)
                        {
				//check, if there is a partial match with the object type
				//also check, if input element is min 40% of object type length
                                if(	(stripos($objectType, $inputArray[$i]) !== FALSE) &&
					(strlen($inputArray[$i]) / strlen($objectType) > 0.4))
                                {
                                        $interpretedTypes[] = $objectType;
                                }
                        }
			//if no match, add input element to search text filter
                        if(count($interpretedTypes) == $countMatches)
                        {
                                $interpretedText[] = $inputArray[$i];
                        }
                }
		//check if object detection was successful
		if(count($interpretedText) > 0)
		{
			//filter for text
			$filteredText = implode(" ", $interpretedText);
			$this->searchFilter->addFilter("text=$filteredText");

			//filter for objects
			foreach($interpretedTypes as $interpretedType)
			{
				$this->searchFilter->addFilter("type=$interpretedType");
			}

			return true;
		}

		//return false, if detection was not successful
		return false;
	}

	/**
	* Interpreter:
	* uses the whole user input as search text
	* @return boolean	true if it was successful
	*			false, if it was not successful
	*/
	private function interpreteDefault()
	{
		$this->searchFilter->addFilter("text=$this->userInput");
		return true;
	}

	/**
	* Interpreter:
	* tries to find a valid object ID in user input
	* @return boolean	true if it was successful
	*			false, if it was not successful
	*/
	public function interpreteObjectId($inputArray)
	{
		//if user input is a single numeric word
		if(count($inputArray) == 1 && is_numeric($inputArray[0]))
		{
			//try to find object
			try
			{
				$objectController = ObjectController::create();
				$this->interpretedObject = $objectController->getObject($inputArray[0], "yourCMDB API");
				return true;
			}
			//doing nothing on Exception
			catch(CmdbObjectNotFoundException $e)
			{
				return false;
			}
		}
		return false;
		
	}

	/**
	* Returns a SearchFilter object for the users text input
	* @return SearchFilter		SearchFilter object with the 
	*				suggested filter
	*/
	public function returnSearchFilter()
	{
		return $this->searchFilter;
	}

	/**
	* Returns a CmdbObject that was interpreted in user input
	* @return CmdbObject		CmdbObject that was interpreted or
	*				null, if no object could be interpreted
	*/
	public function returnInterpretedObject()
	{
		return $this->interpretedObject;
	}

}
?>
