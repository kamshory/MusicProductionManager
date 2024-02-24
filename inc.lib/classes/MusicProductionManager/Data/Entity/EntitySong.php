<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="song")
 */
class EntitySong extends MagicObject
{
	/**
	 * Song ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="song_id", type="varchar(50)", length=50, nullable=false)
	 * @var string
	 */
	protected $songId;

	/**
	 * @Column(name="random_song_id", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $randomSongId;

	/**
	 * @Column(name="title", type="text", nullable=true)
	 * @var string
	 */
	protected $title;

	/**
	 * @Column(name="album_id", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $albumId;
    
    /**
	 * @JoinColumn(name="album_id")
	 * @var Album
	 */
	protected $album;

	/**
     * @var integer
     * @Column(name=track_number, type="int", length=11, nullable=true)
     */
    protected $trackNumber;


	/**
	 * @Column(name="artist_vocal", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $artistVocalId;
    
    /**
	 * @JoinColumn(name="artist_vocal")
	 * @var Artist
	 */
	protected $artistVocal;

	/**
	 * @Column(name="artist_composer", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $artistComposerId;
    
    /**
	 * @JoinColumn(name="artist_composer")
	 * @var Artist
	 */
	protected $artistComposer;

	/**
	 * @Column(name="artist_arranger", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $artistArrangerId;
    
    /**
	 * @JoinColumn(name="artist_arranger")
	 * @var Artist
	 */
	protected $artistArranger;

	/**
	 * @Column(name="file_path", type="text", nullable=true)
	 * @var string
	 */
	protected $filePath;

	/**
	 * @Column(name="file_name", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $fileName;

	/**
	 * @Column(name="file_type", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $fileType;

	/**
	 * @Column(name="file_extension", type="varchar(20)", length=20, nullable=true)
	 * @var string
	 */
	protected $fileExtension;

	/**
	 * @Column(name="file_size", type="bigint(20)", length=20, nullable=true)
	 * @var integer
	 */
	protected $fileSize;

	/**
	 * @Column(name="file_md5", type="varchar(32)", length=32, nullable=true)
	 * @var string
	 */
	protected $fileMd5;

	/**
	 * @Column(name="file_upload_time", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $fileUploadTime;	
	
	/**
	 * @Column(name="file_path_midi", type="text", nullable=true)
	 * @var string
	 */
	protected $filePathMidi;

	/**
	 * @Column(name="file_path_xml", type="text", nullable=true)
	 * @var string
	 */
	protected $filePathXml;

	/**
	 * @Column(name="file_path_pdf", type="text", nullable=true)
	 * @var string
	 */
	protected $filePathPdf;

	/**
	 * @Column(name="duration", type="float", nullable=true)
	 * @var double
	 */
	protected $duration;

	/**
	 * @Column(name="genre_id", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $genreId;
    
    /**
     * Genre
     *
     * @var Genre
     * @JoinColumn (name=genre_id)
     */
    protected $genre;

	/**
	 * @Column(name="lyric", type="longtext", nullable=true)
	 * @var string
	 */
	protected $lyric;

	/**
	 * @Column(name="lyric_complete", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @var bool
	 */
	protected $lyricComplete;
	
	/**
	 * @Column(name="lyric_midi", type="longtext", nullable=true)
	 * @var string
	 */
	protected $lyricMidi;

	/**
	 * @Column(name="lyric_midi_raw", type="longtext", nullable=true)
	 * @var string
	 */
	protected $lyricMidiRaw;

	/**
	 * @Column(name="vocal", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="0")
	 * @var bool
	 */
	protected $vocal;
	
	/**
	 * @Column(name="instrument", type="longtext", nullable=true)
	 * @var string
	 */
	protected $instrument;
	
	/**
	 * @Column(name="midi_vocal_channel", type="int", length=11, nullable=true)
	 * @var integer
	 */
	protected $midiVocalChannel;

	/**
	 * @Column(name="rating", type="int", length=11, nullable=true)
	 * @var integer
	 */
	protected $rating;

	/**
	 * @Column(name="comment", type="longtext", nullable=true)
	 * @var string
	 */
	protected $comment;

	/**
	 * @Column(name="first_upload_time", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $firstUploadTime;

	/**
	 * @Column(name="last_upload_time", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $lastUploadTime;

	/**
	 * @Column(name="last_upload_time_midi", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $lastUploadTimeMidi;

	/**
	 * @Column(name="last_upload_time_xml", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $lastUploadTimeXml;

	/**
	 * @Column(name="last_upload_time_pdf", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $lastUploadTimePdf;

	/**
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * @Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * @Column(name="admin_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * @Column(name="admin_edit", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var bool
	 */
	protected $active;

}