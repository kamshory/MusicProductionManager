<?php

use MagicObject\Database\PicoDatabaseCredentials;
use WS\Applications\WSDashboardServerAdmin;
use WS\Config\ConfigApp;

date_default_timezone_set("Asia/Jakarta");
require_once "vendor/autoload.php";

$baseConfigDir = dirname(__DIR__) . "/.cfg";

$cfg = new ConfigApp(null, true);
$cfg->loadYamlFile($baseConfigDir . "/app.yml", true, true);
$conf = new ConfigApp(null, true);
$conf->loadIniFile(__DIR__ . "/.cfg/dashboard.ini", true, true);
$dbconf = new PicoDatabaseCredentials($cfg->getDatabase());
if ($conf->getSessionSavePath() == null || $conf->getSessionSavePath() == "") {
    $conf->setSessionSavePath(session_save_path());
}
$conf->setIniWebPath($baseConfigDir . "/config.cfg");

$wss = new WSDashboardServerAdmin($conf, $dbconf, $conf->getServerHostAdmin(), $conf->getServerPortAdmin());
$wss->run();
