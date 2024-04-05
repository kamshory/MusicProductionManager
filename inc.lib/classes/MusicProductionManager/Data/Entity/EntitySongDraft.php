<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="song_draft")
 */
class EntitySongDraft extends MagicObject
{
    /**
     * Song Draft ID
     * 
     * @Id
     * @GeneratedValue(strategy=GenerationType.UUID)
     * @NotNull
     * @Column(name="song_draft_id", type="varchar(40)", length=40, nullable=false)
     * @Label(content="Song Draft ID")
     * @var string
     */
    protected $songDraftId;

    /**
     * Parent ID
     * 
     * @Column(name="parent_id", type="varchar(40)", length=40, nullable=true)
     * @Label(content="Parent ID")
     * @var string
     */
    protected $parentId;

    /**
     * Random ID
     * 
     * @Column(name="random_id", type="varchar(40)", length=40, nullable=true)
     * @Label(content="Random ID")
     * @var string
     */
    protected $randomId;

    /**
	 * Artist ID
	 * 
	 * @Column(name="artist_id", type="varchar(40)", length=40, nullable=true)
	 * @Label(content="Artist ID")
	 * @var string
	 */
	protected $artistId;

    /**
	 * Artist
	 * 
	 * @JoinColumn(name="artist_id")
	 * @Label(content="Artist")
	 * @var Artist
	 */
	protected $artist;

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
     * Lyric
     * 
     * @Column(name="lyric", type="longtext", nullable=true)
     * @Label(content="Lyric")
     * @var string
     */
    protected $lyric;

    /**
	 * Rating
	 * 
	 * @Column(name="rating", type="float", nullable=true)
	 * @Label(content="Rating")
	 * @var double
	 */
	protected $rating;

    /**
     * Duration
     * 
     * @Column(name="duration", type="float", nullable=true)
     * @Label(content="Duration")
     * @var double
     */
    protected $duration;

    /**
     * File Path
     * 
     * @Column(name="file_path", type="text", nullable=true)
     * @Label(content="File Path")
     * @var string
     */
    protected $filePath;

    /**
	 * File Size
	 * 
	 * @Column(name="file_size", type="bigint(20)", length=20, nullable=true)
	 * @Label(content="File Size")
	 * @var integer
	 */
	protected $fileSize;

    /**
     * Sha1 File
     * 
     * @NotNull
     * @Column(name="sha1_file", type="varchar(40)", length=40, nullable=false)
     * @Label(content="Sha1 File")
     * @var string
     */
    protected $sha1File;

    /**
     * Read Count
     * 
     * @NotNull
     * @Column(name="read_count", type="int(11)", length=11, nullable=false)
     * @Label(content="Read Count")
     * @var integer
     */
    protected $readCount;

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
     * Admin Create
     * 
     * @Column(name="admin_create", type="varchar(40)", length=40, nullable=true, updatable=false)
     * @Label(content="Admin Create")
     * @var string
     */
    protected $adminCreate;

    /**
     * Creator
     * 
     * @JoinColumn(name="admin_create")
     * @Label(content="Creator")
     * @var User
     */
    protected $creator;

    /**
     * Admin Edit
     * 
     * @Column(name="admin_edit", type="varchar(40)", length=40, nullable=true)
     * @Label(content="Admin Edit")
     * @var string
     */
    protected $adminEdit;

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
     * Active
     * 
     * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
     * @DefaultColumn(value="1")
     * @Label(content="Active")
     * @var bool
     */
    protected $active;
}
