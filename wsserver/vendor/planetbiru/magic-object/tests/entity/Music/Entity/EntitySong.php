<?php

namespace Music\Entity;

use MagicObject\MagicObject;

/**
 * EntitySong is entity of table song. You can join this entity to other entity using annotation JoinColumn. 
 * Visit https://github.com/Planetbiru/MagicObject/blob/main/tutorial.md#entity
 * 
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE, prettify=false)
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
	 * @Label(content="Song ID")
	 * @var string
	 */
	protected $songId;

	/**
	 * Random Song ID
	 * 
	 * @Column(name="random_song_id", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Random Song ID")
	 * @var string
	 */
	protected $randomSongId;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Name")
	 * @var string
	 */
	protected $name;

	/**
	 * Title
	 * 
	 * @Column(name="title", type="text", nullable=true)
	 * @Label(content="Title")
	 * @var string
	 */
	protected $title;

	/**
	 * Album ID
	 * 
	 * @Column(name="album_id", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Album ID")
	 * @var string
	 */
	protected $albumId;

	/**
	 * Track Number
	 * 
	 * @Column(name="track_number", type="int(11)", length=11, nullable=true)
	 * @Label(content="Track Number")
	 * @var integer
	 */
	protected $trackNumber;

	/**
	 * Producer ID
	 * 
	 * @Column(name="producer_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Producer ID")
	 * @var string
	 */
	protected $producerId;

	/**
	 * Artist Vocalist
	 * 
	 * @Column(name="artist_vocalist", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Artist Vocalist")
	 * @var string
	 */
	protected $artistVocalist;

	/**
	 * Artist Composer
	 * 
	 * @Column(name="artist_composer", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Artist Composer")
	 * @var string
	 */
	protected $artistComposer;

	/**
	 * Artist Arranger
	 * 
	 * @Column(name="artist_arranger", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Artist Arranger")
	 * @var string
	 */
	protected $artistArranger;

	/**
	 * File Path
	 * 
	 * @Column(name="file_path", type="text", nullable=true)
	 * @Label(content="File Path")
	 * @var string
	 */
	protected $filePath;

	/**
	 * File Name
	 * 
	 * @Column(name="file_name", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="File Name")
	 * @var string
	 */
	protected $fileName;

	/**
	 * File Type
	 * 
	 * @Column(name="file_type", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="File Type")
	 * @var string
	 */
	protected $fileType;

	/**
	 * File Extension
	 * 
	 * @Column(name="file_extension", type="varchar(20)", length=20, nullable=true)
	 * @Label(content="File Extension")
	 * @var string
	 */
	protected $fileExtension;

	/**
	 * File Size
	 * 
	 * @Column(name="file_size", type="bigint(20)", length=20, nullable=true)
	 * @Label(content="File Size")
	 * @var integer
	 */
	protected $fileSize;

	/**
	 * File Md5
	 * 
	 * @Column(name="file_md5", type="varchar(32)", length=32, nullable=true)
	 * @Label(content="File Md5")
	 * @var string
	 */
	protected $fileMd5;

	/**
	 * File Upload Time
	 * 
	 * @Column(name="file_upload_time", type="timestamp", length=19, nullable=true)
	 * @Label(content="File Upload Time")
	 * @var string
	 */
	protected $fileUploadTime;

	/**
	 * First Upload Time
	 * 
	 * @Column(name="first_upload_time", type="timestamp", length=19, nullable=true)
	 * @Label(content="First Upload Time")
	 * @var string
	 */
	protected $firstUploadTime;

	/**
	 * Last Upload Time
	 * 
	 * @Column(name="last_upload_time", type="timestamp", length=19, nullable=true)
	 * @Label(content="Last Upload Time")
	 * @var string
	 */
	protected $lastUploadTime;

	/**
	 * File Path Midi
	 * 
	 * @Column(name="file_path_midi", type="text", nullable=true)
	 * @Label(content="File Path Midi")
	 * @var string
	 */
	protected $filePathMidi;

	/**
	 * Last Upload Time Midi
	 * 
	 * @Column(name="last_upload_time_midi", type="timestamp", length=19, nullable=true)
	 * @Label(content="Last Upload Time Midi")
	 * @var string
	 */
	protected $lastUploadTimeMidi;

	/**
	 * File Path Xml
	 * 
	 * @Column(name="file_path_xml", type="text", nullable=true)
	 * @Label(content="File Path Xml")
	 * @var string
	 */
	protected $filePathXml;

	/**
	 * Last Upload Time Xml
	 * 
	 * @Column(name="last_upload_time_xml", type="timestamp", length=19, nullable=true)
	 * @Label(content="Last Upload Time Xml")
	 * @var string
	 */
	protected $lastUploadTimeXml;

	/**
	 * File Path Pdf
	 * 
	 * @Column(name="file_path_pdf", type="text", nullable=true)
	 * @Label(content="File Path Pdf")
	 * @var string
	 */
	protected $filePathPdf;

	/**
	 * Last Upload Time Pdf
	 * 
	 * @Column(name="last_upload_time_pdf", type="timestamp", length=19, nullable=true)
	 * @Label(content="Last Upload Time Pdf")
	 * @var string
	 */
	protected $lastUploadTimePdf;

	/**
	 * Duration
	 * 
	 * @Column(name="duration", type="float", nullable=true)
	 * @Label(content="Duration")
	 * @var double
	 */
	protected $duration;

	/**
	 * Genre ID
	 * 
	 * @Column(name="genre_id", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Genre ID")
	 * @var string
	 */
	protected $genreId;

	/**
	 * Bpm
	 * 
	 * @Column(name="bpm", type="float", nullable=true)
	 * @Label(content="Bpm")
	 * @var double
	 */
	protected $bpm;

	/**
	 * Time Signature
	 * 
	 * @Column(name="time_signature", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Time Signature")
	 * @var string
	 */
	protected $timeSignature;

	/**
	 * Subtitle
	 * 
	 * @Column(name="subtitle", type="longtext", nullable=true)
	 * @Label(content="Subtitle")
	 * @var string
	 */
	protected $subtitle;

	/**
	 * Subtitle Complete
	 * 
	 * @Column(name="subtitle_complete", type="tinyint(1)", length=1, nullable=true)
	 * @Label(content="Subtitle Complete")
	 * @var boolean
	 */
	protected $subtitleComplete;

	/**
	 * Lyric Midi
	 * 
	 * @Column(name="lyric_midi", type="longtext", nullable=true)
	 * @Label(content="Lyric Midi")
	 * @var string
	 */
	protected $lyricMidi;

	/**
	 * Lyric Midi Raw
	 * 
	 * @Column(name="lyric_midi_raw", type="longtext", nullable=true)
	 * @Label(content="Lyric Midi Raw")
	 * @var string
	 */
	protected $lyricMidiRaw;

	/**
	 * Vocal Guide
	 * 
	 * @Column(name="vocal_guide", type="longtext", nullable=true)
	 * @Label(content="Vocal Guide")
	 * @var string
	 */
	protected $vocalGuide;

	/**
	 * Vocal
	 * 
	 * @Column(name="vocal", type="tinyint(1)", length=1, nullable=true)
	 * @Label(content="Vocal")
	 * @var boolean
	 */
	protected $vocal;

	/**
	 * Instrument
	 * 
	 * @Column(name="instrument", type="longtext", nullable=true)
	 * @Label(content="Instrument")
	 * @var string
	 */
	protected $instrument;

	/**
	 * Midi Vocal Channel
	 * 
	 * @Column(name="midi_vocal_channel", type="int(11)", length=11, nullable=true)
	 * @Label(content="Midi Vocal Channel")
	 * @var integer
	 */
	protected $midiVocalChannel;

	/**
	 * Rating
	 * 
	 * @Column(name="rating", type="float", nullable=true)
	 * @Label(content="Rating")
	 * @var double
	 */
	protected $rating;

	/**
	 * Comment
	 * 
	 * @Column(name="comment", type="longtext", nullable=true)
	 * @Label(content="Comment")
	 * @var string
	 */
	protected $comment;

	/**
	 * Image Path
	 * 
	 * @Column(name="image_path", type="text", nullable=true)
	 * @Label(content="Image Path")
	 * @var string
	 */
	protected $imagePath;

	/**
	 * Last Upload Time Image
	 * 
	 * @Column(name="last_upload_time_image", type="timestamp", length=19, nullable=true)
	 * @Label(content="Last Upload Time Image")
	 * @var string
	 */
	protected $lastUploadTimeImage;

	/**
	 * Time Create
	 * 
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @Label(content="Time Create")
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * Time Edit
	 * 
	 * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
	 * @Label(content="Time Edit")
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * IP Create
	 * 
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @Label(content="IP Create")
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * IP Edit
	 * 
	 * @Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="IP Edit")
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * Admin Create
	 * 
	 * @Column(name="admin_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @Label(content="Admin Create")
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * Admin Edit
	 * 
	 * @Column(name="admin_edit", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Admin Edit")
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @Label(content="Active")
	 * @var boolean
	 */
	protected $active;

}