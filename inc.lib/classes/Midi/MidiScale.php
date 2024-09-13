<?php

namespace Midi;

/**
 * Rescale MIDI
 * Example
 * $midi = new MidiScale();
 * $midi->importMid("song.mid");
 * $rescaled = $midi->rescale(2, 1);
 * $rescaled->saveMidFile("song-rescaled.mid");
 */
class MidiScale extends Midi
{

    /**
     * Numerator
     *
     * @var integer
     */
    protected $numerator = 1;
    
    /**
     * Denominator
     *
     * @var integer
     */
    protected $denominator = 1;

    /**
     * Rescale MIDI while maintaining duration
     *
     * @param integer $numerator
     * @param integer $denominator
     * @return self
     */
    public function rescale($numerator = 1, $denominator = 1)
	{
        if($numerator == 0 || $denominator == 0)
        {
            throw new InvalidScaleException("Invalid scale (numerator = $numerator, denominator = $denominator)");
        }
        $this->numerator = $numerator;
        $this->denominator = $denominator;
		$rescaled = clone $this;
        $rescaled->setTimebase((int) ($rescaled->getTimebase() * $denominator / $numerator));      
        return $rescaled;
	}
    
    /**
	 * Override getMid
	 *
	 * @return string
	 */
	public function getMidRescaled()
	{
		$tracks = $this->getTracks();
		$tc = count($tracks);
		$type = ($tc > 1) ? 1 : 0;
		$midStr = "MThd\0\0\0\6\0" . chr($type) . $this->_getBytes($tc, 2) . $this->_getBytes($this->getTimebase(), 2);
		## echo "".$this->timebase." ".__LINE__."\r\n";
		for ($i = 0; $i < $tc; $i++) {
			$track = $tracks[$i];
			$mc = count($track);
			$time = 0;
			$midStr .= "MTrk";
			$trackStart = strlen($midStr);

			$last = '';

			for ($j = 0; $j < $mc; $j++) {
				$line = $track[$j];
                
                // update tempo
                $line = $this->rescaleTempo($line, $this->numerator, $this->denominator);
                
				$t = $this->_getTime($line);
				$dt = $t - $time;

				// A: IGNORE EVENTS WITH INCORRECT TIMESTAMP
				if ($dt < 0) {
					continue;
				}

				$time = $t;
				$midStr .= $this->_writeVarLen($dt);

				// repetition, same event, same channel, omit first byte (smaller file size)
				$str = $this->_getMsgStr($line);
				$start = ord($str[0]);
				if ($start >= 0x80 && $start <= 0xEF && $start == $last) {
					$str = substr($str, 1);
				}
				$last = $start;

				$midStr .= $str;
			}
			$trackLen = strlen($midStr) - $trackStart;
			$midStr = substr($midStr, 0, $trackStart) . $this->_getBytes($trackLen, 4) . substr($midStr, $trackStart);
		}
		return $midStr;
	}
    
    /**
	 * Save MIDI song as Standard MIDI File
	 *
	 * @param string $midPath
	 * @param integer $chmod
	 * @return void
	 */
	public function saveMidFile($midPath, $chmod = 0755)
	{
		if (count($this->tracks) < 1) {
			$this->_err('MIDI song has no tracks');
		}
		$smf = fopen($midPath, "wb"); // SMF
		fwrite($smf, $this->getMidRescaled());
		fclose($smf);
		if ($chmod !== false) {
			@chmod($midPath, $chmod);
		}
	}
    
    /**
     * Rescale tempo
     *
     * @param string $line
     * @param integer $numerator
     * @param integer $denominator
     * @return string
     */
    public function rescaleTempo($line, $numerator, $denominator)
    {
        if(stripos($line, 'Tempo') === false)
        {
            return $line;
        }
        if($numerator == 0 || $denominator == 0)
        {
            throw new InvalidScaleException("Invalid scale (numerator = $numerator, denominator = $denominator)");
        }
        $arr = explode(' ', $line, 3);
        $arr[2] = (int) ($arr[2] * $denominator / $numerator);
        return implode(' ', $arr);
    }

    /**
     * Get numerator
     *
     * @return  integer
     */ 
    public function getNumerator()
    {
        return $this->numerator;
    }

    /**
     * Set numerator
     *
     * @param  integer  $numerator  Numerator
     *
     * @return  self
     */ 
    public function setNumerator($numerator)
    {
        $this->numerator = $numerator;

        return $this;
    }

    /**
     * Get denominator
     *
     * @return  integer
     */ 
    public function getDenominator()
    {
        return $this->denominator;
    }

    /**
     * Set denominator
     *
     * @param  integer  $denominator  Denominator
     *
     * @return  self
     */ 
    public function setDenominator($denominator)
    {
        $this->denominator = $denominator;

        return $this;
    }
}
