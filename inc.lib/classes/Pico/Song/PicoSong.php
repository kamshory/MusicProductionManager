<?php
namespace Pico\Song;

use Exception;
use Pico\Database\PicoDatabase;
use Pico\Database\PicoDatabaseQueryBuilder;
use \PDO as PDO;


class PicoSong
{
    /**
     * Database connection
     * @var PicoDatabase
     */
    private $database;

    /**
     * Constructor
     *
     * @param PicoDatabase $database
     * @param string $song_id
     */
    public function __construct($database)
    {
        $this->database = $database;
    }

    /**
     * Song
     * @var Song
     */
    private $song;

    /**
     * Get song
     *
     * @param string $song_id
     * @return Song|null
     */
    public function getSong($song_id)
    {
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $song_id_filtered = $queryBuilder->escapeSQL($song_id);
        $sql = $queryBuilder->newQuery()
            ->select("song.*")
            ->from("song")
            ->where("song.song_id = '$song_id_filtered' ");
        try
        {
            $stmt = $this->database->executeQuery($sql);
            if($stmt->rowCount() > 0)
            {
                $song_data = $stmt->fetch(PDO::FETCH_ASSOC);
                return new Song($song_data, $this->database);
            }
        }
        catch(Exception $e)
        {
           
        }
        return null;
    }
    /**
     * Get song list
     *
     * @param integer $active
     * @return Song|null
     */
    public function getSongList($active = -1)
    {
        $queryBuilder = new PicoDatabaseQueryBuilder($this->database);
        $filter = "";
        if($active == 1 || $active == 0)
        {
            $filter .= " AND song.active = '$active' ";
        }
        $sql = $queryBuilder->newQuery()
            ->select("song.*")
            ->from("song")
            ->where("(1=1) ".$filter);
        try
        {
            $stmt = $this->database->executeQuery($sql);
            if($stmt->rowCount() > 0)
            {
                $song_data = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->song = new Song($song_data);
            }
        }
        catch(Exception $e)
        {
           
        }
        return null;
    }
}