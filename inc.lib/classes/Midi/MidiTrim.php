<?php

namespace Midi;

class MidiTrim extends Midi
{

	/**
	 * Trims song to section from $from to $to (or to the end, if $to is omitted)
	 *
	 * @param integer $from
	 * @param boolean $to
	 * @return void
	 */
	public function trimSong($from = 0, $to = false)
	{
		$tc = count($this->tracks);
		for ($i = 0; $i < $tc; $i++) {
			$this->trimTrack($i, $from, $to);
		}
	}

	/**
	 * Trims track to section from $from to $to (or to the end, if $to is omitted)
	 *
	 * @param integer $tn
	 * @param integer $from
	 * @param boolean $to
	 * @return void
	 */
	public function trimTrack($tn, $from = 0, $to = false)
	{
		$track = $this->tracks[$tn];
		$new = array();
		foreach ($track as $msgStr) {
			$msg = explode(' ', $msgStr);
			$t = (int)$msg[0];
			if ($t == 0) {
				$new[] = $msgStr;
			} else if ($t >= $from && ($t <= $to || $to === false)) {
				$msg[0] = $t - $from;
				$new[] = join(' ', $msg);
			}
		}
		if ($to) {
			$new[] = ($to - $from) . ' Meta TrkEnd'; // bug-fix!
		}
		$this->tracks[$tn] = $new;
	}

	/**
	 * Convert timestamp to second
	 *
	 * @param integer $ts
	 * @return float
	 */
	public function timestamp2seconds($ts)
	{
		return $ts * $this->getTempo() / $this->getTimebase() / 1000000;
	}
	
	/**
	 * Convert second to timestamp
	 *
	 * @param float $sec
	 * @return integer
	 */
	public function seconds2timestamp($sec)
	{
		return (int)($sec * 1000000 * $this->getTimebase() / $this->getTempo());
	}
}
