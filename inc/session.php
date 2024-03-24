<?php

use MagicObject\SecretObject;
use MagicObject\Session\PicoSession;

require_once dirname(__DIR__)."/inc.lib/vendor/autoload.php";

$sessConf = new SecretObject($cfg->getSession());
$sessions = PicoSession::getInstance($sessConf->getName());
$sessions->startSession();
