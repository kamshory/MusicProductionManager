<?php

use MagicObject\Database\PicoDatabase;
use MagicObject\Database\PicoDatabaseCredentials;
use MusicProductionManager\App\ShutdownManager;
use MusicProductionManager\Config\ConfigApp;

require_once dirname(__DIR__)."/inc.lib/vendor/autoload.php";

$cfg = new ConfigApp(null, true);
$cfg->loadYamlFile(dirname(__DIR__)."/.cfg/app.yml", true, true);

$databaseCredentials = new PicoDatabaseCredentials($cfg->getDatabase());

$database = new PicoDatabase($databaseCredentials, 
function($sql, $type) //NOSONAR
{
    // callback when execute query that modify data
}, 
function($sql) //NOSONAR
{
    // callback when execute all query
});

try
{
    $database->connect();
    $shutdownManager = new ShutdownManager($database);
    $shutdownManager->registerShutdown();
}
catch(Exception $e)
{
    // do nothing
}

