<?php

use MusicProductionManager\Utility\UserUtil;

require_once "inc/app.php";
require_once "inc/session.php";

UserUtil::logUserActivity($cfg, $database, $currentLoggedInUser->getUserId(), "Logout from system", null, null, true);

$sessions->destroy();