<?php

use MagicObject\Request\InputGet;
use Midi\MidiLyric;
use MusicProductionManager\Data\Dto\Karaoke;
use MusicProductionManager\Data\Dto\SongDto;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\Song;

require_once "inc/auth-with-login-form.php";

$song = new Song(null, $database);

$inputGet = new InputGet();
$delayStr = $inputGet->getDelay();
if($delayStr == null || empty($delayStr))
{
    $delay = 0;
}
else
{
    $delay = intval($delayStr);
}
$lyric = array('lyric' => '', 'start'=>0, 'duration'=>0, 'song_id'=>'');
if($inputGet->getSongId() != null)
{
    $song->findOneBySongId($inputGet->getSongId());
    $lyric['subtitle'] = $song->getSubtitle();
    $lyric['duration'] = $song->getDuration() * 1000;
    $lyric['start'] = (time() * 1000) + $delay;
    $lyric['song_id'] = $song->getSongId();
}


require_once "inc/header.php";
?>

<div class="filter-container">
  <form action="" method="get">
  <div class="filter-group">
      <?php
      $inputGet = new InputGet();
      $sql = "select song.song_id, song.name, song.title, song.track_number, album.album_id, album.name as album_name
      from song 
      inner join(album) on(album.album_id = song.album_id)
      where song.active = true and album.active = true and album.as_draft = false
      order by album.sort_order asc, song.track_number asc
      ";
      $album_list = array();
      try
      {
      $res = $database->executeQuery($sql);
      $rows = $res->fetchAll(PDO::FETCH_ASSOC);
      foreach($rows as $row)
      {
        if(!isset($album_list[$row['album_id']]))
        {
            $album_list[$row['album_id']] = array();
        }
        $album_list[$row['album_id']][] = $row;
      }
      $arr1 = array();
      foreach($album_list as $albumItem)
      {
        $arr2 = array();
        if(!empty($albumItem))
        {
            $arr2[] = '<optgroup label="'.$albumItem[0]['album_name'].'">';
            foreach($albumItem as $songItem)
            {
                if($inputGet->getSongId() != null && $songItem['song_id'] == $inputGet->getSongId())
                {
                    $selected = " selected";
                }
                else
                {
                    $selected = "";
                }
                $arr2[] = '<option value="'.$songItem['song_id'].'"'.$selected.'>'.sprintf("%02d &mdash; %s", $songItem['track_number'], $songItem['name']).'</option>';
            }
            $arr2[] = '</optgroup>';
        }
        $arr1[] = implode("\r\n", $arr2);
      }
      
      }
      catch(Exception $e)
      {
        echo $e->getMessage();   
      }
      ?>
      <select class="form-control" name="song_id" id="song_id">
        <?php
        echo implode("\r\n", $arr1);
        
        ?>
      </select>
      
  </div>
  
  <input class="btn btn-primary open" type="submit" name="open" value="Open">

  </form>
</div>

<link rel="stylesheet" href="assets/css/karaoke.css">
<link rel="stylesheet" href="assets/css/piano.css">
<script src="assets/js/karaoke.js"></script>
<script src="assets/js/piano.js"></script>

<div class="control">
            <?php
            
            if($inputGet->getSongId() != null)
            {
              try
              {
                $song = new EntitySong(null, $database);
                $song->findOneBySongId($inputGet->getSongId());
                $songDto = Karaoke::valueOf($song);
                
            ?>
            <audio class="player" src="<?php echo $cfg->getSongBaseUrl()."/".$song->getSongId()."/".basename($song->getFilePath());?>?hash=<?php echo str_replace(array(' ', '-', ':'), '', $song->getLastUploadTime());?>" controls></audio>
            <script src="assets/soundfont/soundfont-player.js"></script>
            <script src="assets/soundfont/soundfont-midi-player.js"></script>
            <script>
            let piano = null;
            let karaoke = null;
            let data = <?php echo $songDto;?>;
            let hasMidiSong = <?php echo $song->hasValueVocalGuide() ? 'true' : 'false';?>;
            let midiSong = <?php echo $song->hasValueVocalGuide() ? $song->getVocalGuide() : '[]';?>;
            <?php
            /**
            Old code
            $midi = new MidiLyric();
            if(file_exists($song->getFilePathMidi()))
            {
                try
                {
                    $midi->importMid($song->getFilePathMidi());
                    echo "midiSong = ".json_encode(array_values($midi->getSong($song->getMidiVocalChannel()))).";\r\n";
                    echo "hasMidiSong = true;\r\n";
                }
                catch(Exception $e)
                {
                    // do nothing
                }
            }
            */
            ?>
            let midiPlayer = new SoundfontMidiPlayer();
            let ac = new AudioContext();
            let instrumentName = 'clavinet';
            let active = true;
            midiPlayer.setAudioContext(ac);
            midiPlayer.loadInstrument(instrumentName);
            midiPlayer.loadNote(midiSong);
            $(document).ready(function(){      
                if(hasMidiSong)
                {
                    piano = new Piano(document.querySelector('.piano'));
                    piano.setSong(midiSong);
                }  
                if(typeof data.subtitle != 'undefined' && data.subtitle != '')
                {
                    karaoke = new Karaoke(data, '.teleprompter-container');      
                    animate();
                }              
            });
            function animate()
            {
                let pos = document.querySelector('.player').currentTime;
                if(hasMidiSong)
                {
                    piano.setTime(pos);
                    piano.draw();
                }
                karaoke.updatePosition(pos * 1000);
                midiPlayer.play(instrumentName, pos, active);
                requestAnimationFrame(animate);
            }
            </script>
            <?php
              }
              catch(Exception $e)
              {
                echo $e->getMessage();
              }
            }
            ?>
            
            
</div>
<div class="piano-container">
    <div class="piano"></div>
</div>

<div class="teleprompter">
    <div class="teleprompter-container"></div>
</div>

<?php
require_once "inc/footer.php";
?>