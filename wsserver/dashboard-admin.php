<?php

use WS\Applications\Config;
use WS\Applications\WSDashboardServerAdmin;
use WS\Database\PicoDatabaseCredentials;
use WS\Utils\PicoEnvironmentVariable;

date_default_timezone_set("Asia/Jakarta");
require_once "vendor/autoload.php";

$env = new PicoEnvironmentVariable();

// Prepare app configuration
$iniApp = parse_ini_file(__DIR__."/dashboard.ini");
$conf = new Config();

foreach($iniApp as $key=>$val)
{
    $value = $env->replaceWithEnvironmentVariable($val);
    $conf->set($key, $value);
}

// Prepare app configuration
$iniDb = parse_ini_file(__DIR__."/db.ini");
$dbconf = new PicoDatabaseCredentials();

foreach($iniDb as $key=>$val)
{
    $value = $env->replaceWithEnvironmentVariable($val);
    $dbconf->set($key, $value);
}

// time_zone_system=Asia/Jakarta
$timeZone = $dbconf->getTimeZoneSystem();
if($timeZone != null && !empty($timeZone))
{
    // Update time zone
    date_default_timezone_set($timeZone);
}

if($conf->getSessionSavePath() == null || $conf->getSessionSavePath() == "")
{
    $conf->setSessionSavePath(session_save_path());
}

$conf->setIniWebPath(dirname(__DIR__)."/.cfg/config.cfg");

$wss = new WSDashboardServerAdmin($conf->getServerHostAdmin(), $conf->getServerPortAdmin(), $conf, $dbconf);
$wss->run();
