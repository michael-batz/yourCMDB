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

use yourCMDB\controller\ObjectController;
use yourCMDB\config\CmdbConfig;

/**
* Filter for yourCMDB search
* @author Michael Batz <michael@yourcmdb.org>
*/
class SearchFilter
{
	//array for search filters: Array[filterVariable] = filterValue
	private $filter;

	/**
	* creates a new SearchFilter
	*/
	public function __construct()
	{
		$this->filter = Array();
	}


	public function addFilter($filter)
	{
		//urldecode filter
		$filter = urldecode($filter);

		//parse filter and add to array
		if(preg_match("/^(.*?)=(.*)$/", $filter, $matches) === 1)
		{
			$filterVariable = $matches[1];
			$filterValue = $matches[2];
	
			$this->filter[$filterVariable][] = $filterValue;
		}
	}

	public function getFilterValues($filterVariable)
	{
		$output = Array();
		if(isset($this->filter[$filterVariable]))
		{
			$output = $this->filter[$filterVariable];
		}

		return $output;
	}

	/**
	* returns all objects that matches the given filter
	* @return CmdbObject[]	objects that matches the given filter
	*/
	public function getObjects($authUser)
	{
		//get CMDB config
		$config = new CmdbConfig();

		//create conditions from filter: text
		$conditionText = Array();
		if(isset($this->filter['text'][0]))
		{
			$conditionText = array_filter(explode(" ", $this->filter['text'][0]));
		}

		//create conditions from filter: object types
		$conditionTypes = Array();
		$conditionNotTypes = Array();
		$conditionAllTypes = $config->getObjectTypeConfig()->getAllTypes();
		if(isset($this->filter['type']))
		{
			$conditionTypes = $this->filter['type'];
		}
		if(isset($this->filter['notType']) && count($conditionTypes) > 0)
		{
			$conditionTypes = array_diff($conditionTypes, $this->filter['notType']);
		}
		elseif(isset($this->filter['notType']) && count($conditionTypes) == 0)
		{
			$conditionTypes = array_diff($conditionAllTypes, $this->filter['notType']);
		}
		if(count($conditionTypes) == 0)
		{
			$conditionTypes = null;
		}

		//create conditions from filter: status
		$conditionStatus = null;
		if(isset($this->filter['status']))
		{
			$conditionStatus = $this->filter['status'][0];
		}

		//get objects only if a search text is given
		$objects = Array();
		if(count($conditionText) > 0)
		{
			$objectController = ObjectController::create();
			$objects = $objectController->getObjectsByFieldvalue($conditionText, $conditionTypes, $conditionStatus, 0, 0, $authUser);
		}

		return $objects;
	}

	public function getUrlQueryString()
	{
		return $this->createUrlQueryString($this->filter);
	}

	public function getUrlQueryStringWithRemovedFilter($filter)
	{
		//create temp filter
		$tempFilter = $this->filter;

		//parse filter and remove from array
		if(preg_match("/^(.*?)=(.*)$/", $filter, $matches) === 1)
		{
			$filterVariable = $matches[1];
			$filterValue = $matches[2];

			$key = array_search($filterValue, $tempFilter[$filterVariable]);
			if($key !== FALSE)
			{
				unset($tempFilter[$filterVariable][$key]);
			}
		}
		return $this->createUrlQueryString($tempFilter);
	}

	public function getUrlQueryStringWithAddedFilter($filter)
	{
		$queryString = $this->createUrlQueryString($this->filter);
		$queryString .= "&amp;filter[]=" .urlencode($filter);
		return $queryString;
	}

	private function createUrlQueryString($filterArray)
	{
		$queryString = "";
		foreach(array_keys($filterArray) as $filterVariable)
		{
			foreach($filterArray[$filterVariable] as $filterValue)
			{
				$queryString .= "&amp;filter[]=" .urlencode("$filterVariable=$filterValue");
			}
		}
		return $queryString;
	}


}
?>
