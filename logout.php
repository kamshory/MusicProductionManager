<?php

use MusicProductionManager\Utility\UserUtil;

require_once "inc/auth.php";

UserUtil::logUserActivity($cfg, $database, $currentLoggedInUser->getUserId(), "Logout from system", null, null, true);

$sessions->destroy();