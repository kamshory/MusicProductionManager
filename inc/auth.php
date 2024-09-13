<?php

use MusicProductionManager\Data\Entity\EntityUser;

require_once __DIR__."/app.php";
require_once __DIR__."/session.php";

$currentLoggedInUser = new EntityUser(null, $database);
if(isset($sessions) && $sessions->isSessionStarted() && isset($sessions->suser) && isset($sessions->spass))
{
    try
    {
        $username = $sessions->suser;
        $password = $sessions->spass;
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