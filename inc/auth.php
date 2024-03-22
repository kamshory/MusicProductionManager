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
        
        // Set time zone
        if($currentLoggedInUser->hasValueTimeZone() && $currentLoggedInUser->getTimeZone() != "")
        {
            date_default_timezone_set($currentLoggedInUser->getTimeZone());
		    $timeZoneOffset = date("P");
            $database->setTimeZoneOffset($timeZoneOffset);
        }
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