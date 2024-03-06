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
     * @var string
     * @Column(name=midi_id)
     * @Id
     * @NotNull
     */
    protected $midiId;    

    /**
     * Random midi ID
     *
     * @var string
     * @Column(name=random_midi_id)
     */
    protected $randomMidiId;

    /**
     * Title
     *
     * @var string
     * @Column(name=title)
     */
    protected $title;

    /**
     * Album ID
     *
     * @var string
     * @Column(name=album_id)
     */
    protected $albumId;    
    
    /**
     * Album
     *
     * @var Album
     * @JoinColumn (name=album_id)
     */
    protected $album;


    /**
     * Artist Vocal
     *
     * @var string
     * @Column(name=artist_vocalist)
     */
    protected $artistVocalId;
    
    
    /**
     * Artist Vocal
     *
     * @var Artist
     * @JoinColumn (name=artist_vocalist)
     */
    protected $artistVocalist;

    /**
     * Artist Composer
     *
     * @var string
     * @Column(name=artist_composer)
     */
    protected $artistComposer;
    
    /**
     * Artist Composer
     *
     * @var Artist
     * @JoinColumn (name=artist_composer)
     */
    protected $artistComposer;

    /**
     * Artist Arranger
     *
     * @var string
     * @Column(name=artist_arranger)
     */
    protected $artistArrangerId;
    
    /**
     * Artist Arranger
     *
     * @var Artist
     * @JoinColumn (name=artist_arranger)
     */
    protected $artistArranger;

    /**
     * File path
     *
     * @var string
     * @Column(name=file_path)
     */
    protected $filePath;

    /**
     * File base name
     *
     * @var string
     * @Column(name=file_name)
     */
    protected $fileName;

    /**
     * File type
     *
     * @var string
     * @Column(name=file_type)
     */
    protected $fileType;

    /**
     * File extension
     *
     * @var string
     * @Column(name=file_extension)
     */
    protected $fileExtension;

    /**
     * File size
     *
     * @var integer
     * @Column(name=file_size)
     */
    protected $fileSize;

    /**
     * File MD5
     *
     * @var string
     * @Column(name=file_md5)
     */
    protected $fileMd5;

    /**
     * File upload time
     *
     * @var string
     * @Column(name=file_upload_time)
     */
    protected $fileUploadTime;

    /**
     * File size
     *
     * @var float
     * @Column(name=duration)
     */
    protected $duration;

    /**
     * Genre ID
     *
     * @var string
     * @Column(name=genre_id)
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
     * Lyric
     *
     * @var string
     * @Column(name=lyric)
     */
    protected $lyric;

    /**
     * Comment
     *
     * @var string
     * @Column(name=comment)
     */
    protected $comment;

    /**
     * Rate
     *
     * @var float
     * @Column(name=rate)
     */
    protected $rate;

    /**
     * Time create
     *
     * @var string
     * @Column(name=time_create)
     */
    protected $timeCreate;

    /**
     * Time edit
     *
     * @var string
     * @Column(name=time_edit)
     */
    protected $timeEdit;

    /**
     * IP create
     *
     * @var string
     * @Column(name=ip_create)
     */
    protected $ipCreate;

    /**
     * IP edit
     *
     * @var string
     * @Column(name=ip_edit)
     */
    protected $ipEdit;

    /**
     * Admin create
     *
     * @var string
     * @Column(name=admin_create)
     */
    protected $adminCreateId;
    
    /**
     * Admin create
     *
     * @var User
     * @JoinColumn (name=admin_create)
     */
    protected $adminCreate;

    /**
     * Admin edit
     *
     * @var string
     * @Column(name=admin_edit)
     */
    protected $adminEditId;
    
    /**
     * Admin edit
     *
     * @var User
     * @JoinColumn (name=admin_edit)
     */
    protected $adminEdit;

    /**
     * Active
     * @var bool
     * @Column(name=active)
     */
    protected $active;
}