<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$scriptBaseDir = dirname(__FILE__);
$coreBaseDir = realpath("$scriptBaseDir/../../../../../");

require_once "$coreBaseDir/newmodel/bootstrap.php";

return ConsoleRunner::createHelperSet($entityManager);
