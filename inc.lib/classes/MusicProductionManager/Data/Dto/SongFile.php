<?php

namespace MusicProductionManager\Data\Dto;

use MagicObject\SetterGetter;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\Song;

class SongFile extends SetterGetter
{
    /**
     * Song ID
     *
     * @var string
     */
    public $songId = null;
    
    /**
     * MP3 file path
     *
     * @var string
     */
    protected $mp3Path = "";
    
    /**
     * MP3 file exists
     *
     * @var boolean
     */
    protected $mp3Exists = false;

    /**
     * MIDI file path
     *
     * @var string
     */
    protected $midiPath = "";
    
    /**
     * MIDI file exists
     *
     * @var boolean
     */
    protected $midiExists = false;

    /**
     * PDF file path
     *
     * @var string
     */
    protected $pdfPath = "";
    
    /**
     * PDF file exists
     *
     * @var boolean
     */
    protected $pdfExists = false;
    
    /**
     * XML file path
     *
     * @var string
     */
    protected $xmlPath = "";
    
    /**
     * XML file exists
     *
     * @var boolean
     */
    protected $xmlExists = false;
    
    /**
     * Constructor
     *
     * @param Song|EntitySong $song
     */
    public function __construct($song)
    {
        $this->songId = $song->getSongId();
        
        $this->mp3Path = $song->getFilePath();
        $this->midiPath = $song->getFilePathMidi();
        $this->pdfPath = $song->getFilePathPdf();
        $this->xmlPath = $song->getFilePathXml();

        $this->mp3Exists = $song->getFilePath() != null;
        $this->midiExists = $song->getFilePathMidi() != null;
        $this->pdfExists = $song->getFilePathPdf() != null;
        $this->xmlExists = $song->getFilePathXml() != null;
    }
}