<?php

use MagicObject\Request\InputGet;
use MusicProductionManager\Data\Dto\Karaoke;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\Song;
use MusicProductionManager\Data\Entity\UserProfile;

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

$userProfile = new UserProfile(null, $database);
try
{
    $userProfile->findOneByUserIdAndProfileName($currentLoggedInUser->getUserId(), "vocal-guide-intrument");
}
catch(Exception $e)
{
    // do nothing
}
$vocalGuideInstrument = $userProfile->issetProfileValue() ? $userProfile->getProfileValue() : $cfg->getDefaultVocalGuideInstrument();
?>
<script>
    let baseSoundFontUrl = "assets/soundfont/";
    let instrumentName = '<?php echo $vocalGuideInstrument;?>';
</script>
<div class="filter-container">
<link rel="stylesheet" href="assets/css/karaoke.css">
<link rel="stylesheet" href="assets/css/piano.css">
<script src="assets/js/karaoke.min.js"></script>
<script src="assets/js/piano.min.js"></script>
<script src="assets/soundfont/soundfont-player.min.js"></script>
<script src="assets/soundfont/soundfont-midi-player.min.js"></script>

  <form action="" method="get">
  
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
    <style>
    .song-control{
        padding: 0px 0px 10px 0px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .song-select{
        width: 145px !important;
        margin-right: 2px;
    }
    .btn.solo{
        width:90px;
    }
    .teleprompter {
        height: calc(100vh - 420px);
        width: calc(100% + 40px);
        min-height: 180px;
    }
    </style>
    <div class="song-control">
      <select class="form-control song-select" name="song_id" id="song_id">
        <?php
        echo implode("\r\n", $arr1);
        
        ?>
      </select>
  <input class="btn btn-primary open" type="submit" name="open" value="Open">
  <input class="btn btn-success solo" type="button" value="Solo Off">
  </div>
  </form>
</div>




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
            <script>
            let piano = null;
            let karaoke = null;
            let data = <?php echo $songDto;?>;
            let hasMidiSong = <?php echo $song->issetVocalGuide() ? 'true' : 'false';?>;
            let midiSong = <?php echo $song->issetVocalGuide() ? $song->getVocalGuide() : '[]';?>;
            let playInterval = setInterval('', 1000000);
            let timeInterval = 2;
            let audioContent = null;
            let active = false;
            let trimmer = 0.05;
       
            function toggleSolo()
            {
                if(active)
                {
                    document.querySelector('.solo').value = 'Solo Off';
                    active = false;
                    document.querySelector('.player').muted = false;
                }
                else
                {
                    document.querySelector('.solo').value = 'Solo On';
                    active = true;
                    document.querySelector('.player').muted = true;
                }
            }
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

                let midiPlayer = new SoundfontMidiPlayer();
                audioContent = new AudioContext();
                /*
                let source = audioContent.createBufferSource();
                var gainNode = audioContent.createGain()
                gainNode.gain.value = 1;
                gainNode.connect(audioContent.destination)
                source.connect(gainNode)
                */

                
                midiPlayer.setAudioContext(audioContent);
                midiPlayer.loadInstrument(baseSoundFontUrl, instrumentName);
                midiPlayer.loadNote(midiSong);
                document.querySelector('.solo').addEventListener('click', function(){
                    toggleSolo();
                })
                document.querySelector('.player').addEventListener('play', function(){
                    clearInterval(playInterval);
                    playInterval = setInterval(function(){
                        let pos = document.querySelector('.player').currentTime + trimmer;
                        
                        midiPlayer.play(instrumentName, pos, active);
                    })
                }); 
                document.querySelector('.player').addEventListener('pause', function(){
                    clearInterval(playInterval);
                });   
                document.querySelector('.player').addEventListener('ended', function(){
                    clearInterval(playInterval);
                });       
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