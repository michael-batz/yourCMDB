<?php

$scriptBaseDir = dirname(__FILE__);
$coreBaseDir = realpath("$scriptBaseDir/..");

require_once "ClassLoader.php";
new ClassLoader("Doctrine\Common", "$coreBaseDir/libs/composer/vendor/doctrine/common/lib");
new ClassLoader("Doctrine\Common\Cache", "$coreBaseDir/libs/composer/vendor/doctrine/cache/lib");
new ClassLoader("Doctrine\Common\Collections", "$coreBaseDir/libs/composer/vendor/doctrine/collections/lib");
new ClassLoader("Doctrine\Common\Annotations", "$coreBaseDir/libs/composer/vendor/doctrine/annotations/lib");
new ClassLoader("Doctrine\Common\Lexer", "$coreBaseDir/libs/composer/vendor/doctrine/lexer/lib");
new ClassLoader("Doctrine\Common\Inflector", "$coreBaseDir/libs/composer/vendor/doctrine/inflector/lib");
new ClassLoader("Doctrine\DBAL", "$coreBaseDir/libs/composer/vendor/doctrine/dbal/lib");
new ClassLoader("Doctrine\ORM", "$coreBaseDir/libs/composer/vendor/doctrine/orm/lib");
//require_once "$coreBaseDir/libs/composer/vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths = array("$coreBaseDir/newmodel/model");
$isDevMode = false;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'cmdb',
    'password' => 'cmdb',
    'dbname'   => 'yourcmdb2',
);

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);

//ToDo: debug off
//$config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());

$entityManager = EntityManager::create($dbParams, $config);

?>
