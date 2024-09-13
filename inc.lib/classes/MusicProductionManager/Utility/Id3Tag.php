<?php

namespace MusicProductionManager\Utility;

class Id3Tag
{
    private $tags = array();
    
    public function addTitle($title)
    {
        $this->tags['title'] = array($title);
    }
    public function addArtist($artist)
    {
        $this->tags['artist'] = array($artist);
    }
    public function addAlbum($album)
    {
        $this->tags['album'] = array($album);
    }
    public function addComment($comment)
    {
        $this->tags['comment'] = array($comment);
    }
    public function addYear($year)
    {
        $this->tags['year'] = array((int) $year);
    }

    public function addPicture($picture, $mime, $description)
    {
        $this->tags['attached_picture'] = array(
            array (
                'data'=> $picture,
                'picturetypeid'=> 3,
                'mime'=> $mime,
                'description' => $description
            )
        );
    }
    

    /**
     * Get the value of tags
     */ 
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set the value of tags
     *
     * @return  self
     */ 
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }
}