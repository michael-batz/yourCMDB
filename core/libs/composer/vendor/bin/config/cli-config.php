<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use yourCMDB\orm\OrmController;

$scriptBaseDir = dirname(__FILE__);
$coreBaseDir = realpath("$scriptBaseDir/../../../../../");

require_once "$coreBaseDir/bootstrap.php";

$ormController = OrmController::create();
$entityManager = $ormController->getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
