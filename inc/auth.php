<?php

use MusicProductionManager\Data\Entity\EntityUser;

require_once __DIR__."/app.php";
require_once __DIR__."/session.php";

$currentLoggedInUser = new EntityUser(null, $database);
if(isset($_SESSION) && isset($_SESSION['suser']) && isset($_SESSION['spass']))
{
    try
    {
        $username = $_SESSION['suser'];
        $password = $_SESSION['spass'];
        $currentLoggedInUser->findOneByUsernameAndPasswordAndBlockedAndActive($username, $password, false, true);
    }
    catch(Exception $e)
    {
        exit();
    }
}
else
{
    exit();
}