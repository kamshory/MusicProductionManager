<?php

namespace MusicProductionManager\Utility;

use Exception;
use MagicObject\Database\PicoDatabase;
use MagicObject\Exceptions\NoRecordFoundException;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\Rating;
use MusicProductionManager\Data\Entity\SongUpdateHistory;

class SongUtil
{
    /**
     * Set rating
     *
     * @param PicoDatabase $database
     * @param string $songId
     * @param string $userId
     * @param string $time
     * @return void
     */
    public static function setRating($database, $songId, $userId, $rating, $time)
    {
        $songRating = new Rating(null, $database);
        try
        {
            $songRating->findOneBySongIdAndUserId($songId, $userId);
            $songRating->setRating($rating);
            $songRating->setTimeEdit($time);
            $songRating->update();
        }
        catch(NoRecordFoundException $e)
        {
            $songRating->setSongId($songId);
            $songRating->setUserId($userId);
            $songRating->setRating($rating);
            $songRating->setTimeCreate($time);
            $songRating->setTimeEdit($time);
            $songRating->insert();
        }
        
        catch(Exception $e)
        {
            $songRating->setSongId($songId);
            $songRating->setUserId($userId);
            $songRating->setRating($rating);
            $songRating->setTimeCreate($time);
            $songRating->setTimeEdit($time);
            $songRating->insert();
        }
    }
    /**
     * Get song rating
     *
     * @param PicoDatabase $database
     * @param string $songId
     * @return float
     */
    public static function getRating($database, $songId)
    {
        $rating = new Rating(null, $database);
        try
        {
            $ratings = $rating->findBySongId($songId);
            $result = $ratings->getResult();
            $sum = 0;
            foreach($result as $row)
            {
                $sum += $row->getRating();
            }
            if(empty($result))
            {
                $allRating = 0.0;
            }
            else
            {
                $allRating = $sum / count($result);
            }
        }
        catch(Exception $e)
        {
            $allRating = 0;
        }
        return $allRating;
    }
    
    /**
     * Get title of song
     *
     * @param EntitySong $song
     * @return string
     */
    public static function getPdfTitle($song)
    {
        if($song->hasValueTitle())
        {
            $songTitle = $song->getTitle();
        }
        else
        {
            $songTitle = $song->getName();
        }
        if($song->hasValueComposer())
        {
            $songTitle .= ' by '.$song->getComposer()->getName();
        }
        return $songTitle;
    }
    
    public static function updateSong($database, $songId, $userId, $action, $time, $ip)
    {
        $history = new SongUpdateHistory(null, $database);
        $history->setSongId($songId);
        $history->setUserId($userId);
        $history->setAction($action);
        $history->setTimeUpdate($time);
        $history->setIpUpdate($ip);
        $history->insert();
    }
}