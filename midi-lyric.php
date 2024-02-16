<?php

use Midi\MidiLyric;
use Pico\Data\Entity\Song;
use Pico\Request\PicoRequest;

require_once "inc/auth-with-login-form.php";
function importLyricMidi($original)
{
	$match = preg_split('/\d+:\d+:\d+,\d+\s-->\s\d+:\d+:\d+,\d+./i', $original);
	$str = implode("\r\n", $match);
	$str = str_replace("\n", "\r\n", $str);
	$str = str_replace("\r\r\n", "\r\n", $str);
	$str = str_replace("\r", "\r\n", $str);
	$str = str_replace("\r\n\n", "\r\n", $str);
	$str = str_replace("\r\n\r\n", "\r\n", $str);
	$str = str_replace("\r\n\r\n", "\r\n", $str);
	$str = trim($str);
	$str = str_replace("\r\n", "\\\r\n", $str);
	$str = str_replace(" ", "_ ", $str);
	return $str;
}
error_reporting(E_ALL);
$inputGet = new PicoRequest(INPUT_GET);

if($inputGet->equalsAction('save-raw'))
{
	$inputPost = new PicoRequest(INPUT_POST);
	if($inputPost->getRaw() != null)
	{
		$raw = $inputPost->getRaw();
		$songUpdate = new Song(array('songId'=>$inputPost->getSongId(), 'lyricMidi'=>$raw), $database);
		$songUpdate->update();
	}
	exit();
}

if (isset($song)) {
	$midi = new MidiLyric();

	$midi->importMid($song->getFilePathMidi());

	$list = $midi->getLyric();

	$lyricMidi = $song->getLyricMidi();
	if(empty($lyricMidi))
	{
		$lyricMidi = importLyricMidi($song->getLyric());
	}

?>

<script>
	
</script>

	<div class="main-content">
		<link rel="stylesheet" type="text/css" href="assets/css/midi-player.css" />
		<link rel="stylesheet" type="text/css" href="assets/css/lyric-editor.css" />

		<h3 style="font-size: 18px; padding-bottom:2px;"><?php echo $song->getTitle(); ?></h3>

		<script type="text/javascript">
			var midi_data = <?php echo json_encode($midi->getMidData());?>;
		</script>
		<script type="text/javascript" src="assets/js/lyric-editor.js?rnd=<?php echo mt_rand(1, 9999999);?>"></script>
		<script type="text/javascript" src="assets/midijs/midi.js"></script>

		<div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="generate-dialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="generateDialogLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="addAlbumDialogLabel">Generate Lyric From Vocal</h5>
						<button type="button" class="btn-primary btn-close close-player" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="ajax-content-loader-case-option">
							<table class="from-table-two-cols" width="100%" cellspacing="0" cellpadding="0">
								<tbody>
									<tr>
										<td>Select Channel</td>
										<td><select name="channel" class="form-control">
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
												<option value="7">7</option>
												<option value="8">8</option>
												<option value="9">9</option>
												<option value="10">10</option>
												<option value="11">11</option>
												<option value="12">12</option>
												<option value="13">13</option>
												<option value="14">14</option>
												<option value="15">15</option>
												<option value="16">16</option>
											</select></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="save-genre" onclick="generateLyricFromVocal(); ">Generate</button>
						<button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<div class="planet-midi-player" data-is-stoped="true" data-midi-url="files/<?php echo basename($song->getFilePathMidi()); ?>">
			<div class="mp-wrapper">
				<div class="mp-div waveform">
					<canvas id="canvas" style="width:256px; height:64px" width="256" height="64"></canvas>
				</div>
				<div class="mp-div midi-indicator">
					<div class="midi-display">
						<div class="midi-channel" data-channel="1">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="2">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="3">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="4">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="5">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="6">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="7">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="8">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="9">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="10">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="11">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="12">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="13">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="14">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="15">
							<div></div>
						</div>
						<div class="midi-channel" data-channel="16">
							<div></div>
						</div>
					</div>
				</div>

				<div class="mp-div mp-control">
					<div class="mp-control-1">
						<button class="mp-prev"><i class="fa fa-backward" aria-hidden="true"></i></button>
						<button class="mp-play"><i class="fa fa-pause" aria-hidden="true"></i><i class="fa fa-play" aria-hidden="true"></i></button>
						<button class="mp-stop"><i class="fa fa-stop" aria-hidden="true"></i></button>
						<button class="mp-next"><i class="fa fa-forward" aria-hidden="true"></i></button>
					</div>
				</div>

				<div class="mp-div mp-progress">
					<div class="mp-timer">
						<div class="mp-duration">

						</div>
						<div class="mp-elapsed">

						</div>
					</div>
					<div class="mp-progress-bar">
						<div class="mp-progress-bar-container">
							<div class="mp-progress-bar-inner">
							</div>
						</div>
					</div>
					<div class="mp-status-bar">
					</div>
				</div>
			</div>
		</div>
		
		

		<div class="flex-row">
			<div class="flex-column lyric-preview-container">
				<div class="raw-area">
					<div><textarea name="rawdata" id="rawdata" class="rawdata"><?php echo htmlspecialchars($lyricMidi); ?></textarea>
					</div>
					<div class="button-area">
						<input type="button" id="generate" value="Generate" class="btn btn-primary">
						<input type="button" id="replace-lyric" value="Replace Lyric" class="btn btn-success">
						<input type="button" id="save-raw" value="Save Raw" class="btn btn-success">
						<input type="button" id="update-lyric" value="Update Lyric" class="btn btn-success">
						<input type="hidden" name="song_id" id="song_id" value="<?php echo $song->getSongId();?>">
					</div>
				</div>
				<div class="lyric-preview"></div>
			</div>
			<div class="flex-column lyric-editor">
				<table class="table timetable" width="100%" border="0">
					<thead>
						<tr>
							<td width="72">Tr/Ch</td>
							<td width="80">R Time</td>
							<td width="150">A Time</td>
							<td>Text</td>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>

		</div>
	<?php


}
?>