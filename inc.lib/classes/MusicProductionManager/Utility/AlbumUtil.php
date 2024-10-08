<?php

namespace MusicProductionManager\Utility;

use Exception;
use MagicObject\Database\PicoDatabase;
use MusicProductionManager\Data\Entity\Song;


class AlbumUtil
{
    /**
     * Get song duration
     *
     * @param PicoDatabase $database
     * @param string $albumId
     * @return float
     */
    public static function getSongDuration($database, $albumId)
    {
        $song = new Song(null, $database);
        try
        {
            $pageData = $song->findByAlbumId($albumId);
            $rows = $pageData->getResult();
            $duration = 0;
            foreach($rows as $row)
            {
                $duration += $row->getDuration();
            }
            return $duration;
        }
        catch(Exception $e)
        {
            return 0;
        }      
    }
}