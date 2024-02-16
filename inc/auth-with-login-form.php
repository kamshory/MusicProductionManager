<?php

use Pico\Data\Entity\User;

require_once __DIR__."/app.php";
require_once __DIR__."/session.php";

$currentLoggedInUser = new User(null, $database);
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
        require_once dirname(__DIR__)."/login.php";
        exit();
    }
}
else
{
    require_once dirname(__DIR__)."/login.php";
    exit();
}