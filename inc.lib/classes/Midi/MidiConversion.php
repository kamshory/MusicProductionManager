<?php

namespace Midi;

class MidiConversion extends Midi
{

	//---------------------------------------------------------------
	// converts midi file of type 1 (multiple tracks) to type 0 (single track)
	//---------------------------------------------------------------
	public function convertToType0()
	{
		$this->type = 0;
		if (count($this->tracks) < 2) return;
		$singleTrack = array();
		foreach ($this->tracks as $track) {
			array_pop($track); // remove Meta TrkEnd
			$singleTrack = array_merge($singleTrack, $track);
		}
		/*
		Old code
		=> usort
		=> ($singleTrack, create_function('$a,$b', '$ta =(int)strtok($a,\' \');$tb=(int)strtok($b,\' \');return $ta==$tb?0:($ta<$tb?-1:1);'));
		*/
		
		if (version_compare(phpversion(), '7.2', '>='))
		{
			usort($singleTrack, function($a, $b){
				$ta = (int) strtok ($a, ' ');
				$tb = (int) strtok ($b, ' ');
				if($ta == $tb)
				{
					return 0;
				}
				else if($ta < $tb)
				{
					return -1;
				}
				else
				{
					return 1;
				}
			});
		}
		else
		{
			usort($singleTrack, create_function('$a,$b', '$ta =(int)strtok($a,\' \');$tb=(int)strtok($b,\' \');return $ta==$tb?0:($ta<$tb?-1:1);'));
		}
		
		$endTime = strtok($singleTrack[count($singleTrack) - 1], " ");
		$singleTrack[] = "$endTime Meta TrkEnd";
		$this->tracks = array($singleTrack);
	}
}
