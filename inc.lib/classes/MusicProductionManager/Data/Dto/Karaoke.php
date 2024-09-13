<?php

namespace MusicProductionManager\Data\Dto;

use MagicObject\SetterGetter;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\Song;

/**
 * @JSON(property-naming-strategy=SNAKE_CASE)
 */
class Karaoke extends SetterGetter
{
	/**
	 * Song ID
	 * 
	 * @Label(content="Song ID")
	 * @var string
	 */
	protected $songId;

	/**
	 * Random Song ID
	 * 
	 * @Label(content="Random Song ID")
	 * @var string
	 */
	protected $randomSongId;

	/**
	 * Name
	 * 
	 * @Label(content="Name")
	 * @var string
	 */
	protected $name;

	/**
	 * Title
	 * 
	 * @Label(content="Title")
	 * @var string
	 */
	protected $title;

	/**
	 * Album ID
	 * 
	 * @Label(content="Album ID")
	 * @var string
	 */
	protected $albumId;

	/**
	 * Track Number
	 * 
	 * @Label(content="Track Number")
	 * @var integer
	 */
	protected $trackNumber;

	/**
	 * Artist Vocal
	 * 
	 * @Label(content="Artist Vocal")
	 * @var string
	 */
	protected $artistVocalist;

	/**
	 * Artist Composer
	 * 
	 * @Label(content="Artist Composer")
	 * @var string
	 */
	protected $artistComposer;

	/**
	 * Artist Arranger
	 * 
	 * @Label(content="Artist Arranger")
	 * @var string
	 */
	protected $artistArranger;

	/**
	 * File Path
	 * 
	 * @Label(content="File Path")
	 * @var string
	 */
	protected $filePath;

	/**
	 * File Name
	 * 
	 * @Label(content="File Name")
	 * @var string
	 */
	protected $fileName;

	/**
	 * File Type
	 * 
	 * @Label(content="File Type")
	 * @var string
	 */
	protected $fileType;

	/**
	 * File Extension
	 * 
	 * @Label(content="File Extension")
	 * @var string
	 */
	protected $fileExtension;

	/**
	 * File Size
	 * 
	 * @Label(content="File Size")
	 * @var integer
	 */
	protected $fileSize;

	/**
	 * File Md5
	 * 
	 * @Label(content="File Md5")
	 * @var string
	 */
	protected $fileMd5;

	/**
	 * File Upload Time
	 * 
	 * @Label(content="File Upload Time")
	 * @var string
	 */
	protected $fileUploadTime;

	/**
	 * First Upload Time
	 * 
	 * @Label(content="First Upload Time")
	 * @var string
	 */
	protected $firstUploadTime;

	/**
	 * Last Upload Time
	 * 
	 * @Label(content="Last Upload Time")
	 * @var string
	 */
	protected $lastUploadTime;

	/**
	 * Last Upload Time Midi
	 * 
	 * @Label(content="Last Upload Time Midi")
	 * @var string
	 */
	protected $lastUploadTimeMidi;

	/**
	 * Last Upload Time Xml
	 * 
	 * @Label(content="Last Upload Time Xml")
	 * @var string
	 */
	protected $lastUploadTimeXml;

	/**
	 * Last Upload Time Pdf
	 * 
	 * @Label(content="Last Upload Time Pdf")
	 * @var string
	 */
	protected $lastUploadTimePdf;

	/**
	 * File Path Midi
	 * 
	 * @Label(content="File Path Midi")
	 * @var string
	 */
	protected $filePathMidi;

	/**
	 * File Path Xml
	 * 
	 * @Label(content="File Path Xml")
	 * @var string
	 */
	protected $filePathXml;

	/**
	 * File Path Pdf
	 * 
	 * @Label(content="File Path Pdf")
	 * @var string
	 */
	protected $filePathPdf;

	/**
	 * Duration
	 * 
	 * @Label(content="Duration")
	 * @var double
	 */
	protected $duration;

	/**
	 * Genre ID
	 * 
	 * @Label(content="Genre ID")
	 * @var string
	 */
	protected $genreId;

	/**
	 * Bpm
	 * 
	 * @Label(content="Bpm")
	 * @var double
	 */
	protected $bpm;

	/**
	 * Time Signature
	 * 
	 * @Label(content="Time Signature")
	 * @var string
	 */
	protected $timeSignature;

	/**
	 * Subtitle
	 * 
	 * @Label(content="Subtitle")
	 * @var string
	 */
	protected $subtitle;

	/**
	 * Subtitle Complete
	 * 
	 * @var boolean
	 */
	protected $subtitleComplete;

	/**
	 * Lyric Midi
	 * 
	 * @Label(content="Lyric Midi")
	 * @var string
	 */
	protected $lyricMidi;

	/**
	 * Lyric Midi Raw
	 * 
	 * @Label(content="Lyric Midi Raw")
	 * @var string
	 */
	protected $lyricMidiRaw;

	/**
	 * Vocal
	 * 
	 * @var boolean
	 */
	protected $vocal;

	/**
	 * Instrument
	 * 
	 * @Label(content="Instrument")
	 * @var string
	 */
	protected $instrument;

	/**
	 * Midi Vocal Channel
	 * 
	 * @Label(content="Midi Vocal Channel")
	 * @var integer
	 */
	protected $midiVocalChannel;

	/**
	 * Rating
	 * 
	 * @Label(content="Rating")
	 * @var double
	 */
	protected $rating;

	/**
	 * Comment
	 * 
	 * @Label(content="Comment")
	 * @var string
	 */
	protected $comment;

	/**
	 * Image Path
	 * 
	 * @Label(content="Image Path")
	 * @var string
	 */
	protected $imagePath;

	/**
	 * Time Create
	 * 
	 * @Label(content="Time Create")
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * Time Edit
	 * 
	 * @Label(content="Time Edit")
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * IP Create
	 * 
	 * @Label(content="IP Create")
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * IP Edit
	 * 
	 * @Label(content="IP Edit")
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * Admin Create
	 * 
	 * @Label(content="Admin Create")
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * Admin Edit
	 * 
	 * @Label(content="Admin Edit")
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * Active
	 * 
	 * @var boolean
	 */
	protected $active;

    /**
     * Construct SongDto from Song and not copy other properties
     * 
     * @param Song|EntitySong $input
     * @return self
     */
    public static function valueOf($input)
    {
        $output = new self();
        $output->setSongId($input->getSongId());
        $output->setRandomSongId($input->getRandomSongId());
        $output->setName($input->getName());
        $output->setTitle($input->getTitle());
        $output->setAlbumId($input->getAlbumId());
        $output->setTrackNumber($input->getTrackNumber());
        $output->setArtistVocalist($input->getArtistVocalist());
        $output->setArtistComposer($input->getArtistComposer());
        $output->setArtistArranger($input->getArtistArranger());
        $output->setFileName($input->getFileName());
        $output->setFileType($input->getFileType());
        $output->setFileExtension($input->getFileExtension());
        $output->setFileSize($input->getFileSize());
        $output->setFileMd5($input->getFileMd5());
        $output->setFileUploadTime($input->getFileUploadTime());
        $output->setFirstUploadTime($input->getFirstUploadTime());
        $output->setLastUploadTime($input->getLastUploadTime());
        $output->setLastUploadTimeMidi($input->getLastUploadTimeMidi());
        $output->setLastUploadTimeXml($input->getLastUploadTimeXml());
        $output->setLastUploadTimePdf($input->getLastUploadTimePdf());
        $output->setDuration($input->getDuration());
        $output->setGenreId($input->getGenreId());
        $output->setBpm($input->getBpm());
        $output->setTimeSignature($input->getTimeSignature());
        $output->setSubtitle($input->getSubtitle());
        $output->setLyricComplete($input->getLyricComplete());
        $output->setVocal($input->getVocal());
        $output->setInstrument($input->getInstrument());
        $output->setMidiVocalChannel($input->getMidiVocalChannel());
        $output->setRating($input->getRating());
        $output->setComment($input->getComment());
        $output->setImagePath($input->getImagePath());
        $output->setTimeCreate($input->getTimeCreate());
        $output->setTimeEdit($input->getTimeEdit());
        $output->setIpCreate($input->getIpCreate());
        $output->setIpEdit($input->getIpEdit());
        $output->setAdminCreate($input->getAdminCreate());
        $output->setAdminEdit($input->getAdminEdit());
        $output->setActive($input->getActive());
        return $output;
	}


}