<?php

use Pico\Data\Entity\Song;

require_once "inc/auth-with-login-form.php";

$song = new Song(null, $database);
