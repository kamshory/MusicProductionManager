<?php

use Pico\Config\ConfigApp;
use Pico\Database\PicoDatabase;
use Pico\Database\PicoDatabaseCredentials;

require_once dirname(__DIR__)."/inc.lib/vendor/autoload.php";

$cfg = new ConfigApp(null, true);
$cfg->loadYamlFile(dirname(__DIR__)."/.cfg/app.yml", true, true);

$databaseCredentials = new PicoDatabaseCredentials($cfg->getDatabase());
$database = new PicoDatabase($databaseCredentials);
try
{
    $database->connect();
}
catch(Exception $e)
{
    // do nothing
}