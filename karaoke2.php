<?php

use MagicObject\Request\InputGet;
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
    $lyric['lyric'] = $song->getLyric();
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
      $sql = "select song.song_id, song.title, song.track_number, album.album_id, album.name as album_name
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
                $arr2[] = '<option value="'.$songItem['song_id'].'"'.$selected.'>'.sprintf("%02d &mdash; ", $songItem['track_number']).$songItem['title'].'</option>';
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

    <script src="karaoke-js.js"></script>
<style> 
    .teleprompter
    {
        position: relative;
        width: calc(100% + 40px);
        height: calc(100vh - 270px);
        min-height: 200px;
        background-color: white;
        overflow: hidden;
        margin: 30px -20px;
        white-space: nowrap;
        text-transform: uppercase;
    }
    @media screen and (min-width: 1200px) {
        .main .teleprompter{
            margin: 0;
            width: 100%;
        }
        
    }
    
    .teleprompter-container{
        position: relative;
        width: 100%;
    }
    .teleprompter-container > div{
        position: absolute;
        text-align: center;
        width: 100%;
        border-top: 1px solid #fafafa;
        padding-top: 5px;
        box-sizing: border-box;
    }
    .marked{
        background-color: #cdff43c4;
        color: #222222;
    }
    
    .box1{
        position: relative;
        margin-top: 5px;
    }
    .box2{
        max-width: 400px;
        width: calc(100% - 0px);
        position: relative;
        height: 40px;
        margin: auto;
        
    }
    .box3{
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        border-bottom: 1px solid #DDDDDD;
        border-left: 1px solid #DDDDDD;
        border-right: 1px solid #DDDDDD;
        width: 100%;
        height: 40px;
        margin: auto;
        position: absolute;
        top: 20px;
        box-sizing: border-box;
        z-index: -1;
    }
    .box4{
        width: 100%;    
        box-sizing: border-box;
        text-align: center;
        padding: 10px 10px 0px 10px;
        z-index: 1;
    }
    .box2::before, .box2::after{
        content: "";
        width: 20px;
        height: 20px;
        top: 0;
        position: absolute;
        border-top: 1px solid #DDDDDD;
    }
    .box2::before{
        left: -19px;
        border-top-right-radius: 10px;
        border-right: 1px solid #DDDDDD;
        
    }
    .box2::after{
        right: -19px;
        border-top-left-radius: 10px;
        border-left: 1px solid #DDDDDD;
        
    }
    audio{
        border-radius: 4px;
        width: 100%;
        box-sizing: border-box;
        height: 40px;
    }
</style>

<div class="control">
    <div class="box1">
        <div class="box2">
            <div class="box3"></div>
            <div class="box4">
                
            <?php
            
            if($inputGet->getSongId() != null)
            {
              try
              {
                $song = new EntitySong(null, $database);
                $song->findOneBySongId($inputGet->getSongId());
                
            ?>
            <audio class="player" src="<?php echo $cfg->getSongBaseUrl()."/".$song->getFileName();?>?hash=<?php echo str_replace(array(' ', '-', ':'), '', $song->getLastUploadTime());?>" controls></audio>
            <script>
            let karaoke = null;
            let data = <?php echo $song;?>;
            
            $(document).ready(function(){
                
            
                if(typeof data.lyric != 'undefined' && data.lyric != '')
                {
                    karaoke = new Karaoke(data, '.teleprompter-container');      
                    animate();
                }
            });
            function animate()
            {
                let pos = document.querySelector('.player').currentTime;
                karaoke.updatePosition(pos*1000);
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
            
        </div>
    </div>
</div>

<div class="teleprompter">
    <div class="teleprompter-container"></div>
</div>

<?php
require_once "inc/footer.php";
?>