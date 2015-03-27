<?php

$scriptBaseDir = dirname(__FILE__);
$coreBaseDir = realpath("$scriptBaseDir/..");

require_once "$coreBaseDir/libs/composer/vendor/autoload.php";

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
