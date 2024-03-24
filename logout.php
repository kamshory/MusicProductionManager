<?php

use MagicObject\Request\InputPost;
use MusicProductionManager\Data\Entity\EntityUser;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\UserUtil;

require_once "inc/app.php";
require_once "inc/session.php";

$sessions->destroy();