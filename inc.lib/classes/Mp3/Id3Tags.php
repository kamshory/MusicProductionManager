<?php

namespace Mp3;

class  Id3Tags
{
    protected $fileName = "";
    const TAG_TITLE = "TIT2";
    const TAG_ARTIST = "TPE1";
    const TAG_GROUP = "TPE2";
    const TAG_ALBUM = "TALB";
    const TAG_GENRE = "TCON";
    const TAG_TRACK_NO = "TRCK";
    const TAG_YEAR = "TYER";
    
    protected $frames = array(
        self::TAG_ALBUM=> "The Ultimate Experience",
        self::TAG_TRACK_NO=>"1",
        self::TAG_TITLE=>"All along the watchtower",
        self::TAG_ARTIST=>"Jimi Hendrix",
        self::TAG_GROUP=>"",
        self::TAG_YEAR=>"19xx",
        self::TAG_GENRE=>"Rock"
    );
    
    /**
     * Hex tag length
     *
     * @param integer $int
     * @return string
     */
    public function setHexTagLen($int) {
        $n = pow(128, 3);
        $intVar = $int;
        $m = "";
        for ($i=0;$i<4;$i++) {
            $m .= chr(floor($intVar/$n));
            $intVar = $intVar % $n;
            $n=$n/128;
        }
        return $m;
    } 

    /**
     * WRITE ID3 TAGS (Write MP3 [v1, v2]
     *
     * @param string $mp3
     * @return void
     */
    public function writeTags($mp3) {
        $fl = file_get_contents($mp3);
        $header = substr($fl, 0, 10);
        $tagLen = $this->calcDecTagLen(substr($header, 6, 4));
        $music = substr($fl,$tagLen+10,-128);
        # Can use input Header for output but you may
        # wish to change the output filename for testing
            $tagLen = 1024; # or whatever you like >your actual
            $header = substr($header,0,6).$this->setHexTagLen($tagLen);
            file_put_contents($mp3, $this->mkV2Tag($header,$tagLen).$music.$this->mkV1Tag());
    }

    /**
     * Create the V2 tag
     *
     * @param string $header
     * @param string $tagLen
     * @return void
     */
    public function mkV2Tag($header, $tagLen) {
        $null = chr(0);
        $nl3 = $null.$null.$null;            # 0 bytes for flags and encoding
        $out = "";
        foreach($this->frames as $tagKey=>$tagValue) {
            $n=strlen($tagValue)+1;
            $out.= $tagKey.$this->mkFrmLen($n).$nl3.$tagValue;
        }
        return $header.str_pad($out, $tagLen, $null);
    }
    
    /**
     * Calculate Tag Length from bytes 6-10 of existing header
     *
     * @param string $word
     * @return integer
     */
    public function calcDecTagLen($word) {
        $m = 1;
        $int = 0;
        for ($i=strlen($word)-1;$i>-1;$i--) {
            $int +=$m*ord($word[$i]);
            $m=$m*128;
        }
        return $int;
    }
    
    /**
     * Make the 4 byte frame length value for the V2tag
     *
     * @param integer $int
     * @return string
     */
    public function mkFrmLen($int) {
        $hx = "";
        while ($int>0) {
            $n = $int % 256;
            $hx = chr($n).$hx;
            $int=floor($int/256);
        }
        return str_pad($hx,4,chr(0),STR_PAD_LEFT);
    }
    
    /**
     * Create the 128 byte V1 tag
     *
     * @return string
     */
    public function mkV1Tag() {
        $n = pow(128, 3); 
        $tagOut = "TAG".
            $this->adj($this->frames[self::TAG_TITLE]).
            $this->adj($this->frames[self::TAG_ARTIST]).
            $this->adj($this->frames[self::TAG_ALBUM]).
            str_pad($this->frames[self::TAG_YEAR], 4).
            str_pad(" ", 29).
            chr($this->frames[self::TAG_TRACK_NO]).
            chr($n);
        return $tagOut;
    }
    
    /**
     * Pad the header to 30 characters
     *
     * @param string $str
     * @return string
     */
    public function adj($str) {
        return substr(str_pad($str,30,chr(0)),0,30);
    } 
}