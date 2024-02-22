<?php

namespace Midi;

use stdClass;

class MidiInstrument extends Midi
{
	/**
	 * Program list
	 *
	 * @var array
	 */
	protected $program = array();
	
	/**
	 * New instrument list
	 *
	 * @var array
	 */
	protected $newInstrumentList = array();
	
	
	/**
	 * Index of program channel
	 *
	 * @var array
	 */
	private $prChIndex = array();

	public function getMidInstrumentList()
	{
		$midi = $this;
		$program = new stdClass();
		$program->tempo = $this->tempo;
		$program->program = new stdClass();
		$program->time = new stdClass();
		$program->program->tracks = array();
		$program->program->parsed = array();
		$program->time->tracks = array();
		$instruments = $this->getInstrumentList();
		foreach ($midi->tracks as $i => $track) {
			$program->program->tracks[$i] = array();
			$j = 0;
			$k = 0;
			$trackName = '';
			foreach ($track as $j => $raw) {
				$arr = explode(' ', $raw, 4);
				$time = $arr[0];
				$event = $arr[1];
				$data = $arr[2];
				if ($event == 'Meta' && $arr[2] == 'TrkName')
				{
					$trackName = $arr[3];
					$trackName = preg_replace('~^"?(.*?)"?$~', '$1', $trackName);
				}
				if ($event == 'PrCh') {

					list(, $ch) = explode('=', $arr[2]);
					list(, $p) = explode('=', $arr[3]);

					$program->program->tracks[$i][$k] = $raw;
					$program->program->parsed[$i][$k] = array(
						'channel' => $ch,
						'program' => $p,
						'instrument' => $instruments[$p],
						'track_name' => $trackName,
						'track_id' => $j
					);
					$k++;
				}
			}
		}
		foreach ($midi->tracks as $i => $track) {
			$program->time->tracks[$i] = array();
			$j = 0;
			$k = 0;
			foreach ($track as $j => $raw) {
				$arr = explode(' ', $raw, 3);
				$time = $arr[0]; // NOSONAR
				$type = $arr[1];
				$data = $arr[2]; // NOSONAR
				if ($type == 'Tempo') {
					$program->time->tracks[$i][$k] = $raw;
					$k++;
				}
			}
		}

		$program->timebase = $this->getTimebase();
		return $program;
	}
	
	/**
	 * Build list from input
	 *
	 * @param array $input
	 * @return self
	 */
	public function updateMidInstrument($input)
	{
		$result = array();
		foreach($input as $track=>$instrumentList)
		{
			if($instrumentList != null && is_array($instrumentList))
			{
				foreach($instrumentList as $idx=>$info)
				{
					$ch = (int) $info->channel;
					$id = (int) $info->index;
					$pr = (int) $info->program;
					if(!isset($result[$ch]))
					{
						$result[$ch] = array();
					}
					$result[$ch][$id] = $pr;    
				}
				
			}
		}
		$this->newInstrumentList = $result;
		return $this;
	}

	public function getMidData()
	{
		$midi = new StdClass();
		$midi->tempo = $this->tempo;
		$midi->timebase = $this->timebase;
		$midi->tempoMsgNum = $this->tempoMsgNum;
		$midi->type = $this->type;
		$midi->throwFlag = $this->throwFlag;
		$midi->tracks = array();

		foreach ($this->tracks as $i => $track) {
			$midi->tracks[$i] = array();
			foreach ($track as $raw) {
				if (stripos($raw, 'copyright') === false) {
					$midi->tracks[$i][] = $raw;
				}
			}
		}
		return $midi;
	}
	
	/**
	 * Replace instrument
	 *
	 * @param string $line
	 * @param array $newInstrumentList
	 * @param integer $ch
	 * @param integer $index
	 * @return string
	 */
	public function replaceInstrument($line, $newInstrumentList, $ch, $index)
	{
		$arr = explode(' ', $line);
		eval("\$".$arr[2].";");
		eval("\$".$arr[3].";");
		$ch = isset($ch) ? $ch : 0;
		$p = isset($p) ? $p : 0;
		
		$time = $arr[0];
		
		$newProgram = $newInstrumentList[$ch][$index];
		
		return "$time PrCh ch=$ch p=$newProgram";
	}
	
	public function replaceInst($line)
	{
		$arr = explode(' ', $line);
		if($arr[1] == 'PrCh')
		{
			eval("\$".$arr[2].";");
			eval("\$".$arr[3].";");
			$ch = isset($ch) ? $ch : 0;
			$p = isset($p) ? $p : 0;
			if(!isset($this->prChIndex[$ch]))
			{
				$this->prChIndex[$ch] = 0;
			}
			$this->prChIndex[$ch]++;
			$index = $this->prChIndex[$ch] - 1;
			$line = $this->replaceInstrument($line, $this->getNewInstrumentList(), $ch, $index);
		}
		return $line;
	}
	
	/**
	 * Get MIDI with new instruments
	 *
	 * @return string
	 */
	public function getMidWithNewInstrument()
	{
		$this->prChIndex = array();
		
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
                
                // update instrument
				$line = $this->replaceInst($line);
				
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
		fwrite($smf, $this->getMidWithNewInstrument());
		fclose($smf);
		if ($chmod !== false) {
			@chmod($midPath, $chmod);
		}
	}

	/**
	 * Get the value of newInstrumentList
	 */ 
	public function getNewInstrumentList()
	{
		return $this->newInstrumentList;
	}

	/**
	 * Set the value of newInstrumentList
	 *
	 * @return  self
	 */ 
	public function setNewInstrumentList($newInstrumentList)
	{
		$this->newInstrumentList = $newInstrumentList;

		return $this;
	}
}
