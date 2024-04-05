<?php
namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * Midi
 * @Entity
 * @Table (name=midi)
 */
class EntityMidi extends MagicObject
{
    /**
     * Midi ID
     * 
     * @Label(content="Midi ID")
     * @var string
     * @Column(name=midi_id)
     * @Id
     * @NotNull
     */
    protected $midiId;

    /**
     * Random midi ID
     * 
     * @Label(content="Random midi ID")
     * @var string
     * @Column(name=random_midi_id)
     */
    protected $randomMidiId;

    /**
     * Title
     * 
     * @Label(content="Title")
     * @var string
     * @Column(name=title)
     */
    protected $title;

    /**
     * Album ID
     * 
     * @Label(content="Album ID")
     * @var string
     * @Column(name=album_id)
     */
    protected $albumId;

    /**
     * Album
     * 
     * @Label(content="Album")
     * @var Album
     * @JoinColumn (name=album_id)
     */
    protected $album;


    /**
     * Artist Vocal
     * 
     * @Label(content="Artist Vocal")
     * @var string
     * @Column(name=artist_vocalist)
     */
    protected $artistVocalist;


    /**
     * Artist Vocal
     * 
     * @Label(content="Artist Vocal")
     * @var Artist
     * @JoinColumn (name=artist_vocalist)
     */
    protected $vocalist;

    /**
     * Artist Composer
     * 
     * @Label(content="Artist Composer")
     * @var string
     * @Column(name=artist_composer)
     */
    protected $artistComposer;

    /**
     * Artist Composer
     * 
     * @Label(content="Artist Composer")
     * @var Artist
     * @JoinColumn (name=artist_composer)
     */
    protected $composer;

    /**
     * Artist Arranger
     * 
     * @Label(content="Artist Arranger")
     * @var string
     * @Column(name=artist_arranger)
     */
    protected $artistArranger;

    /**
     * Artist Arranger
     * 
     * @Label(content="Artist Arranger")
     * @var Artist
     * @JoinColumn (name=artist_arranger)
     */
    protected $arranger;

    /**
     * File path
     * 
     * @Label(content="File path")
     * @var string
     * @Column(name=file_path)
     */
    protected $filePath;

    /**
     * File base name
     * 
     * @Label(content="File base name")
     * @var string
     * @Column(name=file_name)
     */
    protected $fileName;

    /**
     * File type
     * 
     * @Label(content="File type")
     * @var string
     * @Column(name=file_type)
     */
    protected $fileType;

    /**
     * File extension
     * 
     * @Label(content="File extension")
     * @var string
     * @Column(name=file_extension)
     */
    protected $fileExtension;

    /**
     * File size
     * 
     * @Label(content="File size")
     * @var integer
     * @Column(name=file_size)
     */
    protected $fileSize;

    /**
     * File MD5
     * 
     * @Label(content="File MD5")
     * @var string
     * @Column(name=file_md5)
     */
    protected $fileMd5;

    /**
     * File upload time
     * 
     * @Label(content="File upload time")
     * @var string
     * @Column(name=file_upload_time)
     */
    protected $fileUploadTime;

    /**
     * File size
     * 
     * @Label(content="File size")
     * @var float
     * @Column(name=duration)
     */
    protected $duration;

    /**
     * Genre ID
     * 
     * @Label(content="Genre ID")
     * @var string
     * @Column(name=genre_id)
     */
    protected $genreId;

    /**
     * Genre
     * 
     * @Label(content="Genre")
     * @var Genre
     * @JoinColumn (name=genre_id)
     */
    protected $genre;

    /**
     * Lyric
     * 
     * @Label(content="Lyric")
     * @var string
     * @Column(name=lyric)
     */
    protected $lyric;

    /**
     * Comment
     * 
     * @Label(content="Comment")
     * @var string
     * @Column(name=comment)
     */
    protected $comment;

    /**
     * Rate
     * 
     * @Label(content="Rate")
     * @var float
     * @Column(name=rate)
     */
    protected $rate;

    /**
     * Time create
     * 
     * @Label(content="Time create")
     * @var string
     * @Column(name=time_create)
     */
    protected $timeCreate;

    /**
     * Time edit
     * 
     * @Label(content="Time edit")
     * @var string
     * @Column(name=time_edit)
     */
    protected $timeEdit;

    /**
     * IP create
     * 
     * @Label(content="IP create")
     * @var string
     * @Column(name=ip_create)
     */
    protected $ipCreate;

    /**
     * IP edit
     * 
     * @Label(content="IP edit")
     * @var string
     * @Column(name=ip_edit)
     */
    protected $ipEdit;

    /**
     * Admin create
     * 
     * @Label(content="Admin create")
     * @var string
     * @Column(name=admin_create)
     */
    protected $adminCreateId;

    /**
     * Admin create
     * 
     * @Label(content="Admin create")
     * @var User
     * @JoinColumn (name=admin_create)
     */
    protected $adminCreate;

    /**
     * Admin edit
     * 
     * @Label(content="Admin edit")
     * @var string
     * @Column(name=admin_edit)
     */
    protected $adminEditId;

    /**
     * Admin edit
     * 
     * @Label(content="Admin edit")
     * @var User
     * @JoinColumn (name=admin_edit)
     */
    protected $adminEdit;

    /**
     * Active
     * @Label(content="Active")
     * @var bool
     * @Column(name=active)
     */
    protected $active;
}