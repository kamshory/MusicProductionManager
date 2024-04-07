<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="midi")
 */
class Midi extends MagicObject
{
	/**
	 * Midi ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="midi_id", type="varchar(50)", length=50, nullable=false)
	 * @Label(content="Midi ID")
	 * @var string
	 */
	protected $midiId;

	/**
	 * Random Midi ID
	 * 
	 * @Column(name="random_midi_id", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Random Midi ID")
	 * @var string
	 */
	protected $randomMidiId;

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
	 * Artist Vocal
	 * 
	 * @Column(name="artist_vocalist", type="varchar(50)", length=50, nullable=true)
	 * @Label(content="Artist Vocal")
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
	 * Lyric
	 * 
	 * @Column(name="lyric", type="longtext", nullable=true)
	 * @Label(content="Lyric")
	 * @var string
	 */
	protected $lyric;

	/**
	 * Comment
	 * 
	 * @Column(name="comment", type="longtext", nullable=true)
	 * @Label(content="Comment")
	 * @var string
	 */
	protected $comment;

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
	 * @var boolean
	 */
	protected $active;

}