<?php

namespace Midi;

use stdClass;

class MidiLyric extends Midi
{
	/**
	 * Array contains lyric
	 *
	 * @var array
	 */
	protected $lyric = array();

	/**
	 * Get lyric
	 *
	 * @return stdClass
	 */
	public function getLyric()
	{
		$midi = $this;
		$lyric = new stdClass();
		$lyric->tempo = $this->tempo;
		$lyric->lyric = new stdClass();
		$lyric->time = new stdClass();
		$lyric->lyric->tracks = array();
		$lyric->time->tracks = array();

		foreach ($midi->tracks as $i => $track) {
			$lyric->lyric->tracks[$i] = array();
			$k = 0;
			foreach ($track as $raw) {
				$arr = explode(' ', $raw, 3);
				$type = $arr[1];
				$data = $arr[2];
				if ($type == 'Meta' && stripos($data, 'Lyric ') === 0) {
					$lyric->lyric->tracks[$i][$k] = $raw;
					$k++;
				}
			}
		}
		foreach ($midi->tracks as $i => $track) {
			$lyric->time->tracks[$i] = array();
			$k = 0;
			foreach ($track as $raw) {
				$arr = explode(' ', $raw, 3);
				$time = $arr[0]; // NOSONAR
				$type = $arr[1];
				$data = $arr[2]; // NOSONAR
				if ($type == 'Tempo') {
					$lyric->time->tracks[$i][$k] = $raw;
					$k++;
				}
			}
		}

		$lyric->timebase = $this->getTimebase();
		return $lyric;
	}

	/**
	 * Get song
	 *
	 * @param integer $channel
	 * @return array
	 */
	public function getSong($channel) //NOSONAR
	{
		$midi = $this;
		$lyric = new stdClass();
		$lyric->tempo = $this->tempo;
		$lyric->lyric = new stdClass();
		$lyric->time = new stdClass();
		$lyric->lyric->tracks = array();
		$lyric->time->tracks = array();
		$lyric->note = new stdClass();
		$lyric->note->track = array();

		$f = 1 / $this->getTimebase() / 1000000;

		$currentTempo = 0;

		$song = array();

		foreach ($midi->tracks as $i => $track) {
			$lastNote = array();
			$lyric->lyric->tracks[$i] = array();
			$k = 0;
			$l = 0;
			$t = 0;
			foreach ($track as $raw) {
				$arr = explode(' ', $raw, 3);
				$type = $arr[1];
				$data = $arr[2];

				$dt = (int)$arr[0] - $t;

				if ($type == 'Tempo') {
					$currentTempo = $arr[2] * 1;
				}
				if ($type == 'On') {
					$arr2 = explode(' ', $arr[2]);
					eval("\$" . $arr2[0] . ";");
					eval("\$" . $arr2[1] . ";");
					eval("\$" . $arr2[2] . ";");
					$n = isset($n) ? $n : 0;
					$ch = isset($ch) ? $ch : 0;
					$v = isset($v) ? $v : 0;
					if ($ch == $channel) {
						$tm = (int) $arr[0];
						$song[$tm] = array();

						$lyric->note->tracks[$i][$l] = $raw;
						$lastNote[$n] = $arr[0];
						$time = $dt * $currentTempo * $f;
						$song[$tm]['velocity'] = $v * 1;
						$song[$tm]['time'] = $arr[0] * 1;
						$song[$tm]['start'] = $time;
						$song[$tm]['end'] = $time;
						$song[$tm]['note'] = $n;
						$l++;
					}
				}

				if ($type == 'Meta' && stripos($data, 'Lyric ') === 0) {
					$lyric->lyric->tracks[$i][$k] = $raw;
					$arr3 = explode(' ', $raw);
					preg_match('"([^\\"]+)"', $arr3[3], $result);
					$song[(int) $arr[0]]['lyric'] = $result[0];
					$k++;
				}

				if ($type == 'Off') {
					$arr2 = explode(' ', $arr[2]);
					eval("\$" . $arr2[0] . ";");
					eval("\$" . $arr2[1] . ";");
					eval("\$" . $arr2[2] . ";");
					$n = isset($n) ? $n : 0;
					$ch = isset($ch) ? $ch : 0;
					$v = isset($v) ? $v : 0;
					if ($ch == $channel) {
						$time = $dt * $currentTempo * $f;
						$tm = (int) $lastNote[$n];
						$song[$tm]['end'] = $time;
					}
				}
			}
		}
		$song = $this->fixingSong($song);
		return $song;
	}

	/**
	 * Fixing note without note off
	 *
	 * @param array $song
	 * @return array
	 */
	public function fixingSong($song)
	{
		$keys = array_keys($song);
		$values = array_values($song);
		$count = count($values);
		$count2 = $count - 2;
		foreach ($values as $index => $value) {
			if ($value['start'] == $value['end'] && $index < $count2 && $value['note'] == $values[$index + 1]['note']) {
				$values[$index]['end'] = $values[$index + 1]['start'];
			}
		}
		foreach ($values as $index => $value) {
			$values[$index]['duration'] = $values[$index]['end'] - $values[$index]['start'];
		}
		return array_combine($keys, $values);
	}

	/**
	 * Get MIDI data
	 *
	 * @return stdClass
	 */
	public function getMidData()
	{
		$midi = new stdClass();
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
	 * returns absolute time in mili seconds
	 *
	 * @access public
	 * @return int absolute time
	 */
	public function getAbsoluteTime($relativeTime)
	{
		$duration = 0;
		$currentTempo = 0;
		$t = 0;

		$f = 1 / $this->getTimebase() / 1000000;

		foreach ($this->tracks as $trk) {
			$mc = count($trk);
			for ($i = 0; $i < $mc; $i++) {
				$msg = explode(' ', $trk[$i]);

				$tm = (int)@$msg[0];
				if ($tm > $relativeTime) {
					break 2;
				}

				if (@$msg[1] == 'Tempo') {
					$dt = (int)$msg[0] - $t;
					$duration += $dt * $currentTempo * $f;
					$t = (int)$msg[0];
					$currentTempo = (int)$msg[2];
				}
			}
		}

		$dt = $relativeTime - $t;
		$duration += $dt * $currentTempo * $f;
		return $duration * 1000;
	}

	/**
	 * Add lyric
	 *
	 * @param object|stdClass $lyric
	 * @return self
	 */
	public function addLyric($lyric)
	{
		$this->lyric = $lyric;
		return $this;
	}
	
	/**
	 * Save MIDI song as Standard MIDI File
	 *
	 * @param string $midPath
	 * @param integer|bool $chmod
	 * @return self
	 */
	public function saveMidFile($midPath, $chmod = false)
	{
		if (count($this->tracks) < 1) {
			$this->_err('MIDI song has no tracks');
		}
		$SMF = fopen($midPath, "wb"); // SMF
		$data = $this->getMidWithLyric($this->lyric);
		fwrite($SMF, $data);
		fclose($SMF);
		if ($chmod !== false) {
			@chmod($midPath, $chmod);
		}
		return $this;
	}
	
	/**
	 * Get binary MIDI string with lyric
	 *
	 * @param object|stdClass $lyric
	 * @return string
	 */
	public function getMidWithLyric($lyric)
	{
		$tracks = $this->tracks;
		$tracks = $this->updateLyric($tracks, $lyric);
		$tc = count($tracks);
		$type = ($tc > 1) ? 1 : 0;
		$midStr = "MThd\0\0\0\6\0" . chr($type) . $this->_getBytes($tc, 2) . $this->_getBytes($this->timebase, 2);
		for ($i = 0; $i < $tc; $i++) {
			$track = $tracks[$i];
			$mc = count($track);
			$time = 0;
			$midStr .= "MTrk";
			$trackStart = strlen($midStr);

			$last = '';

			for ($j = 0; $j < $mc; $j++) {
				$line = $track[$j];
				$t = $this->_getTime($line);
				$dt = $t - $time;

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
	 * Update lyric
	 *
	 * @param array $tracks
	 * @param object|stdClass $lyric
	 * @return array
	 */
	public function updateLyric($tracks, $lyric)
	{
		$tc = count($tracks);
		for ($i = 0; $i < $tc; $i++) {
			$mc = count($tracks[$i]);
			$copy = array();
			for ($j = 0; $j < $mc; $j++) {
				$line = $tracks[$i][$j];
				$arr = explode(' ', $line, 4);
				if ($arr[2] == 'Lyric') {
					// remove existing lyric if any
				} else {
					$copy[] = $tracks[$i][$j];
				}
			}
			if (isset($lyric->tracks[$i])) {
				// Insert lyric if any
				$tracks[$i] = $this->insertMid($copy, $lyric->tracks[$i]);
			}
		}
		return $tracks;
	}

	/**
	 * Insert MIDI
	 *
	 * @param array $tracks
	 * @param object|stdClass $lyric
	 * @return array
	 */
	public function insertMid($track, $lyric)
	{
		$trackResult = array();
		$mc1 = count($track);
		$mc2 = count($lyric);
		$lt1 = 0;
		$lt2 = 0;
		$lt3 = 0;
		$maxIdx = 0;
		if (!empty($track)) {
			$trackResult[] = $track[0];
		}
		for ($i = 1; $i < $mc1; $i++) {
			$line1 = $track[$i - 1];
			$line2 = $track[$i];
			$arr1 = explode(' ', $line1, 2);
			$arr2 = explode(' ', $line2, 2);

			$lt1 = $arr1[0];
			$lt2 = $arr2[0];

			for ($j = $maxIdx; $j < $mc2; $j++) {
				$line3 = $lyric[$j];
				$arr3 = explode(' ', $line3, 2);
				$lt3 = $arr3[0];
				if ($lt3 >= $lt1 && $lt3 < $lt2) {
					$trackResult[] = $line3;
					$maxIdx = $j + 1;
				}
			}
			$trackResult[] = $line2;
		}
		return $trackResult;
	}
}
