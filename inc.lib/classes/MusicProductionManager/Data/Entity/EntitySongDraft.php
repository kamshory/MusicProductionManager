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
     * @var string
     */
    protected $songDraftId;
    
    /**
     * Parent ID
     * 
     * @Column(name="parent_id", type="varchar(40)", length=40, nullable=true)
     * @var string
     */
    protected $parentId;

    /**
     * Random ID
     * 
     * @Column(name="random_id", type="varchar(40)", length=40, nullable=true)
     * @var string
     */
    protected $randomId;

    /**
	 * Artist ID
	 * 
	 * @Column(name="artist_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $artistId;

    /**
	 * Artist
	 * 
	 * @JoinColumn(name="artist_id")
	 * @var Artist
	 */
	protected $artist;

    /**
     * Name
     * 
     * @Column(name="name", type="varchar(100)", length=100, nullable=true)
     * @var string
     */
    protected $name;

    /**
     * Title
     * 
     * @Column(name="title", type="text", nullable=true)
     * @var string
     */
    protected $title;

    /**
     * Lyric
     * 
     * @Column(name="lyric", type="longtext", nullable=true)
     * @var string
     */
    protected $lyric;

    /**
	 * Rating
	 * 
	 * @Column(name="rating", type="float", nullable=true)
	 * @var double
	 */
	protected $rating;

    /**
     * Duration
     * 
     * @Column(name="duration", type="float", nullable=true)
     * @var double
     */
    protected $duration;

    /**
     * File Path
     * 
     * @Column(name="file_path", type="text", nullable=true)
     * @var string
     */
    protected $filePath;

    /**
	 * File Size
	 * 
	 * @Column(name="file_size", type="bigint(20)", length=20, nullable=true)
	 * @var integer
	 */
	protected $fileSize;

    /**
     * Sha1 File
     * 
     * @NotNull
     * @Column(name="sha1_file", type="varchar(40)", length=40, nullable=false)
     * @var string
     */
    protected $sha1File;

    /**
     * Read Count
     * 
     * @NotNull
     * @Column(name="read_count", type="int(11)", length=11, nullable=false)
     * @var integer
     */
    protected $readCount;

    /**
     * Time Create
     * 
     * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
     * @var string
     */
    protected $timeCreate;

    /**
     * Time Edit
     * 
     * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
     * @var string
     */
    protected $timeEdit;

    /**
     * Admin Create
     * 
     * @Column(name="admin_create", type="varchar(40)", length=40, nullable=true, updatable=false)
     * @var string
     */
    protected $adminCreate;

    /**
     * Creator
     * 
     * @JoinColumn(name="admin_create")
     * @var User
     */
    protected $creator;

    /**
     * Admin Edit
     * 
     * @Column(name="admin_edit", type="varchar(40)", length=40, nullable=true)
     * @var string
     */
    protected $adminEdit;

    /**
     * IP Create
     * 
     * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
     * @var string
     */
    protected $ipCreate;

    /**
     * IP Edit
     * 
     * @Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)
     * @var string
     */
    protected $ipEdit;

    /**
     * Active
     * 
     * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
     * @DefaultColumn(value="1")
     * @var bool
     */
    protected $active;
}
