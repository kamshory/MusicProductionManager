<?php

use MusicProductionManager\Data\Entity\Song;

require_once "inc/auth-with-login-form.php";

$song = new Song(null, $database);
