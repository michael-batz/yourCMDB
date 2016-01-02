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


/**
* PSR-0 compatible class loader for yourCMDB
* @author Michael Batz <michael@yourcmdb.org>
*
*/
class ClassLoader
{
	//prefix of the namespace
	private $namespacePrefix;

	//base directory where the classes are stores
	private $baseDirectory;

	//constant for namespace seperator
	const NAMESPACE_SEPERATOR = '\\';

	//constant for directory seperator
	const DIRECTORY_SEPERATOR = '/';

	/**
	* create a new ClassLoader instance and register as autoload function
	* @param string namespacePrefix		prefix of a namespace (e.g. "Doctrine\ORM")
	* @param string baseDirectory		base directory for finding classes
	*/
	public function __construct($namespacePrefix, $baseDirectory)
	{
		$this->namespacePrefix = $namespacePrefix;
		$this->baseDirectory = $baseDirectory;
		$this->register();
	}

	/**
	* function for loading a class
	* @param string $className	fully qualified class name
	*/
	public function loadClass($className)
	{
		//remove leading seperators
		$className = ltrim($className, self::NAMESPACE_SEPERATOR);

		//check the namespace prefix
		$namespacePrefixNormalized = str_replace(self::NAMESPACE_SEPERATOR, self::DIRECTORY_SEPERATOR, $this->namespacePrefix);
		$classNameNormalized = str_replace(self::NAMESPACE_SEPERATOR, self::DIRECTORY_SEPERATOR, $className);
		if(preg_match("#^$namespacePrefixNormalized.*?$#", $classNameNormalized) === 1)
		{
			//build directory structure
			$filename = $this->baseDirectory . self::DIRECTORY_SEPERATOR . $classNameNormalized . ".php";

			//load file if available
			if(is_readable($filename))
			{
				require($filename);
			}
		}
	}

	/**
	* register the loadClass() function as autoload function
	*/
	private function register()
	{
		spl_autoload_register(array($this, "loadClass"));
	}

}
?>
