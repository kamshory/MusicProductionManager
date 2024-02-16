<?php

namespace Pico\Utility;

use Pico\Data\Entity\Song;
use Pico\Database\PicoDatabase;

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
        $pageData = $song->findByAlbumId($albumId);
        $rows = $pageData->getResult();
        $duration = 0;
        foreach($rows as $row)
        {
            $duration += $row->getDuration();
        }
        return $duration;
    }
}