<?php

class ClassLoader
{
	//prefix of the namespace
	private $namespacePrefix;

	//base directory where the classes are stores
	private $baseDirectory;

	const NAMESPACE_SEPERATOR = '\\';

	const DIRECTORY_SEPERATOR = '/';

	public function __construct($namespacePrefix, $baseDirectory)
	{
		$this->namespacePrefix = $namespacePrefix;
		$this->baseDirectory = $baseDirectory;
		$this->register();
	}

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

	public function register()
	{
		spl_autoload_register(array($this, "loadClass"));
	}

}
?>
