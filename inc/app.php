<?php

use MagicObject\Database\PicoDatabase;
use MusicProductionManager\App\ShutdownManager;
use MusicProductionManager\Config\ConfigApp;
use MusicProductionManager\Config\DbCredentials;

require_once dirname(__DIR__)."/inc.lib/vendor/autoload.php";

$cfg = new ConfigApp(null, true);
$cfg->loadYamlFile(dirname(__DIR__)."/.cfg/app.yml", true, true, true);


$databaseCredentials = new DbCredentials($cfg->getDatabase(), function(){
    return bin2hex("783ery238rfhicihsc8ys9cw3sfuifh8");
});


$database = new PicoDatabase($databaseCredentials, 
    function($sql, $type) //NOSONAR
    {
        // callback when execute query that modify data
    }, 
    function($sql) //NOSONAR
    {
        // callback when execute all query
    }
);

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

