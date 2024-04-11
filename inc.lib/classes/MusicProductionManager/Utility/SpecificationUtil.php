<?php

namespace MusicProductionManager\Utility;

use MagicObject\Database\PicoPredicate;
use MagicObject\Database\PicoSpecification;
use MagicObject\Request\PicoRequestBase;

/**
 * Specification utility
 */
class SpecificationUtil
{
    /**
     * Create MIDI specification
     * @param PicoRequestBase $name
     * @return PicoSpecification
     */
    public static function createMidiSpecification($inputGet)
    {
        $spesification = new PicoSpecification();

        if($inputGet->getMidiId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('midiId', $inputGet->getMidiId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getGenreId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('genreId', $inputGet->getGenreId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getAlbumId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('albumId', $inputGet->getAlbumId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getName() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('name', PicoPredicate::generateCenterLike($inputGet->getName()));
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getTitle() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('title', PicoPredicate::generateCenterLike($inputGet->getTitle()));
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getArtistVocalistId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('artistVocalId', $inputGet->getArtistVocalistId());
            $spesification->addAnd($predicate1);
        }
        
        return $spesification;
    }

    /**
     * Create Song specification
     * @param PicoRequestBase $inputGet
     * $@param array|null $additional
     * @return PicoSpecification
     */
    public static function createSongSpecification($inputGet, $additional = null) //NOSONAR
    {
        $spesification = new PicoSpecification();

        if($inputGet->getSongId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('songId', $inputGet->getSongId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getGenreId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('genreId', $inputGet->getGenreId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getAlbumId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('albumId', $inputGet->getAlbumId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getProducerId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('producerId', $inputGet->getProducerId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getName() != "" || $inputGet->getTitle() != "")
        {
            $spesificationTitle = new PicoSpecification();
            
            if($inputGet->getName() != "")
            {
                $predicate1 = new PicoPredicate();
                $predicate1->like('name', PicoPredicate::generateCenterLike($inputGet->getName()));
                $spesificationTitle->addOr($predicate1);
                
                $predicate2 = new PicoPredicate();
                $predicate2->like('title', PicoPredicate::generateCenterLike($inputGet->getName()));
                $spesificationTitle->addOr($predicate2);
            }
            if($inputGet->getTitle() != "")
            {
                $predicate3 = new PicoPredicate();
                $predicate3->like('name', PicoPredicate::generateCenterLike($inputGet->getTitle()));
                $spesificationTitle->addOr($predicate3);
                
                $predicate4 = new PicoPredicate();
                $predicate4->like('title', PicoPredicate::generateCenterLike($inputGet->getTitle()));
                $spesificationTitle->addOr($predicate4);
            }
            
            $spesification->addAnd($spesificationTitle);
        }

        if($inputGet->getSubtitle() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('subtitle', PicoPredicate::generateCenterLike($inputGet->getSubtitle()));
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getVocalist() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('artistVocalist', $inputGet->getVocalist());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getSubtitleComplete() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('subtitleComplete', $inputGet->getSubtitleComplete());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getVocal() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('vocal', $inputGet->getVocal());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getActive() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('active', $inputGet->getActive());
            $spesification->addAnd($predicate1);
        }

        if(isset($additional) && is_array($additional))
        {
            foreach($additional as $key=>$value)
            {
                $predicate2 = new PicoPredicate();          
                $predicate2->equals($key, $value);
                $spesification->addAnd($predicate2);
            }
        }
        
        return $spesification;
    }

    /**
     * Create Song specification
     * @param PicoRequestBase $inputGet
     * $@param array|null $additional
     * @return PicoSpecification
     */
    public static function createReferenceSpecification($inputGet, $additional = null)
    {
        $spesification = new PicoSpecification();

        if($inputGet->getReferenceId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('referenceId', $inputGet->getReferenceId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getGenreId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('genreId', $inputGet->getGenreId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getAlbum() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('album', $inputGet->getAlbum());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getTitle() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('title', PicoPredicate::generateCenterLike($inputGet->getTitle()));
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getArtistId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('artistId', $inputGet->getArtistId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getActive() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('active', $inputGet->getActive());
            $spesification->addAnd($predicate1);
        }

        if(isset($additional) && is_array($additional))
        {
            foreach($additional as $key=>$value)
            {
                $predicate2 = new PicoPredicate();          
                $predicate2->equals($key, $value);
                $spesification->addAnd($predicate2);
            }
        }
        
        return $spesification;
    }

    /**
     * Create Song specification
     * @param PicoRequestBase $inputGet
     * $@param array|null $additional
     * @return PicoSpecification
     */
    public static function createSongDraftSpecification($inputGet, $additional = null) //NOSONAR
    {
        $spesification = new PicoSpecification();

        if($inputGet->getSongId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('songId', $inputGet->getSongId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getName() != "" || $inputGet->getTitle() != "")
        {
            $spesificationTitle = new PicoSpecification();
            
            if($inputGet->getName() != "")
            {
                $predicate1 = new PicoPredicate();
                $predicate1->like('name', PicoPredicate::generateCenterLike($inputGet->getName()));
                $spesificationTitle->addOr($predicate1);
                
                $predicate2 = new PicoPredicate();
                $predicate2->like('title', PicoPredicate::generateCenterLike($inputGet->getName()));
                $spesificationTitle->addOr($predicate2);
            }
            if($inputGet->getTitle() != "")
            {
                $predicate3 = new PicoPredicate();
                $predicate3->like('name', PicoPredicate::generateCenterLike($inputGet->getTitle()));
                $spesificationTitle->addOr($predicate3);
                
                $predicate4 = new PicoPredicate();
                $predicate4->like('title', PicoPredicate::generateCenterLike($inputGet->getTitle()));
                $spesificationTitle->addOr($predicate4);
            }
            
            $spesification->addAnd($spesificationTitle);
        }

        if($inputGet->getArtistId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('artistId', $inputGet->getArtistId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getActive() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('active', $inputGet->getActive());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getFrom() != "" && $inputGet->getTo() != "")
        {
            $to = $inputGet->getTo();
            if(strlen($to) < 11)
            {
                $to = $to . " 23:59:59";
            }
            $predicate1 = new PicoPredicate();
            $predicate1->greaterThanOrEquals('time_create', $inputGet->getFrom());
            $spesification->addAnd($predicate1);

            $predicate2 = new PicoPredicate();
            $predicate2->lessThanOrEquals('time_create', $to);
            $spesification->addAnd($predicate2);
        }
        else if($inputGet->getFrom())
        {
            $predicate1 = new PicoPredicate();
            $predicate1->greaterThanOrEquals('time_create', $inputGet->getFrom());
            $spesification->addAnd($predicate1);
        }
        else if($inputGet->getTo() != "")
        {
            $to = $inputGet->getTo();
            if(strlen($to) < 11)
            {
                $to = $to . " 23:59:59";
            }
            $predicate2 = new PicoPredicate();
            $predicate2->lessThanOrEquals('time_create', $to);
            $spesification->addAnd($predicate2);
        }

        if($inputGet->getLyric() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('lyric', PicoPredicate::generateCenterLike($inputGet->getLyric()));
            $spesification->addAnd($predicate1);
        }

        if(isset($additional) && is_array($additional))
        {
            foreach($additional as $key=>$value)
            {
                $predicate2 = new PicoPredicate();          
                $predicate2->equals($key, $value);
                $spesification->addAnd($predicate2);
            }
        }
        
        return $spesification;
    }

    /**
     * Create album specification
     * @param PicoRequestBase $name
     * @return PicoSpecification
     */
    public static function createAlbumSpecification($inputGet)
    {
        $spesification = new PicoSpecification();

        if($inputGet->getAlbumId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('albumId', $inputGet->getAlbumId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getName() != "" || $inputGet->getTitle() != "")
        {
            $spesificationTitle = new PicoSpecification();
            
            if($inputGet->getName() != "")
            {
                $predicate1 = new PicoPredicate();
                $predicate1->like('name', PicoPredicate::generateCenterLike($inputGet->getName()));
                $spesificationTitle->addOr($predicate1);
                
                $predicate2 = new PicoPredicate();
                $predicate2->like('title', PicoPredicate::generateCenterLike($inputGet->getName()));
                $spesificationTitle->addOr($predicate2);
            }
            if($inputGet->getTitle() != "")
            {
                $predicate3 = new PicoPredicate();
                $predicate3->like('name', PicoPredicate::generateCenterLike($inputGet->getTitle()));
                $spesificationTitle->addOr($predicate3);
                
                $predicate4 = new PicoPredicate();
                $predicate4->like('title', PicoPredicate::generateCenterLike($inputGet->getTitle()));
                $spesificationTitle->addOr($predicate4);
            }
            
            $spesification->addAnd($spesificationTitle);
        }
        
        
        if($inputGet->getProducerId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('producerId', $inputGet->getProducerId());
            $spesification->addAnd($predicate1);
        }
        
        return $spesification;
    }

    /**
     * Create genre specification
     * @param PicoRequestBase $name
     * @return PicoSpecification
     */
    public static function createGenreSpecification($inputGet)
    {
        $spesification = new PicoSpecification();

        if($inputGet->getGenreId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('genreId', $inputGet->getGenreId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getName() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('name', PicoPredicate::generateCenterLike($inputGet->getName()));
            $spesification->addAnd($predicate1);
        }
        
        return $spesification;
    }

    /**
     * Create user type specification
     * @param PicoRequestBase $name
     * @return PicoSpecification
     */
    public static function createUserTypeSpecification($inputGet)
    {
        $spesification = new PicoSpecification();

        if($inputGet->getUserTypeId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('userTypeId', $inputGet->getUserTypeId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getName() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('name', PicoPredicate::generateCenterLike($inputGet->getName()));
            $spesification->addAnd($predicate1);
        }
        
        return $spesification;
    }

    /**
     * Create artist specification
     * @param PicoRequestBase $name
     * @return PicoSpecification
     */
    public static function createArtistSpecification($inputGet)
    {
        $spesification = new PicoSpecification();

        if($inputGet->getArtistId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('artistId', $inputGet->getArtistId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getName() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('name', PicoPredicate::generateCenterLike($inputGet->getName()));
            $spesification->addAnd($predicate1);
        }
        
        return $spesification;
    }

    /**
     * Create user specification
     * @param PicoRequestBase $name
     * @return PicoSpecification
     */
    public static function createUserSpecification($inputGet)
    {
        $spesification = new PicoSpecification();

        if($inputGet->getUserId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('userId', $inputGet->getUserId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getName() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('name', PicoPredicate::generateCenterLike($inputGet->getName()));
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getUsername() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('username', PicoPredicate::generateCenterLike($inputGet->getUsername()));
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getEmail() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('email', PicoPredicate::generateCenterLike($inputGet->getEmail()));
            $spesification->addAnd($predicate1);
        }
        
        return $spesification;
    }
}