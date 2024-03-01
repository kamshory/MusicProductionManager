<?php

use MagicObject\Database\PicoDatabaseCredentials;
use MagicObject\Util\PicoEnvironmentVariable;
use MusicProductionManager\Config\ConfigApp;
use MusicProductionManager\Util\WebsocketClient;


require_once "inc/app.php";


$env = new PicoEnvironmentVariable();

// Prepare app configuration
$iniApp = parse_ini_file(__DIR__."/wsserver/dashboard.ini");
$conf = new ConfigApp();

foreach($iniApp as $key=>$val)
{
    $value = $env->replaceWithEnvironmentVariable($val);
    $conf->set($key, $value);
}

// Prepare app configuration
$dbconf = new PicoDatabaseCredentials();


// time_zone_system=Asia/Jakarta
$timeZone = $dbconf->getTimeZoneSystem();
if($timeZone != null && !empty($timeZone))
{
    // Update time zone
    date_default_timezone_set($timeZone);
}

$host = $conf->getServerHostAdmin();
$port = $conf->getServerPortAdmin();
$path = "/";
$ssl = false;
$timeout = 30;
$username = 'endang';
$password = 'endang';

$persistant = false;
$context = null;
$headers = array(
    'Authorization: Basic '.base64_encode($username.':'.$password)
);
$websocketAdmin = WebsocketClient::websocketOpen($host, $port, $headers, $error_string, $timeout, $ssl, $persistant, $path, $context);
if($websocketAdmin !== false)
{
    $message = json_encode(
        array(
            'command'=>'forward', 
            'recipient'=>'endang',
            'data'=>array(
                'apa'=>'iya'
            )
        )
    );
    WebsocketClient::websocketWrite($websocketAdmin, $message);
    fclose($websocketAdmin);
}
