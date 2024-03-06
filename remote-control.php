<?php
use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoSort;
use MagicObject\Database\PicoSortable;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Request\PicoRequest;
use MagicObject\Response\Generated\PicoSelectOption;
use MusicProductionManager\Constants\ParamConstant;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\Genre;

use MusicProductionManager\Utility\SpecificationUtil;

require_once "inc/auth-with-login-form.php";
require_once "inc/header.php";

$inputGet = new InputGet();
if($inputGet->equalsAction(ParamConstant::ACTION_DETAIL) && $inputGet->getSongId() != null)
{
  try
  {
    $song = new EntitySong(null, $database);
    $song->findOneBySongId($inputGet->getSongId());
    ?>
    <table class="table table-responsive">
      <tbody>
        <tr>
          <td>Song ID</td>
          <td><?php echo $song->getSongId();?></td>
        </tr>
        <tr>
          <td>Title</td>
          <td><?php echo $song->getName();?></td>
        </tr>
        <tr>
          <td>Duration</td>
          <td><?php echo $song->getDuration();?></td>
        </tr>
        <tr>
          <td>Genre</td>
          <td><?php echo $song->hasValueGenre() ? $song->getGenre()->getName() : '';?></td>
        </tr>
        <tr>
          <td>Album</td>
          <td><?php echo $song->hasValueAlbum() ? $song->getAlbum()->getName() : '';?></td>
        </tr>
        <tr>
          <td>Vocal</td>
          <td><?php echo $song->hasValueVocalist() ? $song->getVocalist()->getName() : "";?></td>
        </tr>
        <tr>
          <td>Composer</td>
          <td><?php echo $song->hasValueComposer() ? $song->getComposer()->getName() : '';?></td>
        </tr>
        <tr>
          <td>Arranger</td>
          <td><?php echo $song->hasValueArranger() ? $song->getArranger()->getName() : '';?></td>
        </tr>
        <tr>
          <td>File Size</td>
          <td><?php echo $song->getFileSize();?></td>
        </tr>
        <tr>
          <td>Created</td>
          <td><?php echo $song->getTimeCreate();?></td>
        </tr>
        <tr>
          <td>Last Update</td>
          <td><?php echo $song->getTimeEdit();?></td>
        </tr>
        <tr>
          <td>Active</td>
          <td><?php echo $song->booleanToTextByActive('Yes', 'No');?></td>
        </tr>
      </tbody>
    </table>
    
    <?php
  }
  catch(Exception $e)
  {
    ?>
    <div class="alert alert-warning"><?php echo $e->getMessage();?></div>
    <?php
  }
}
else
{
    ?>
    <div class="filter-container">
    <form action="" method="get">
    <div class="filter-group">
        <span>Genre</span>
        <select class="form-control" name="genre_id" id="genre_id">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Genre(null, $database), array('value'=>'genreId', 'label'=>'name'), $inputGet->getGenreId()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Album</span>
        <select class="form-control" name="album_id" id="album_id">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Album(null, $database), array('value'=>'albumId', 'label'=>'name'), $inputGet->getAlbumId(), null, new PicoSortable('sortOrder', PicoSortable::ORDER_TYPE_DESC)); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Artist Vocal</span>
        <select class="form-control" name="artist_vocal_id" id="artist_vocal_id">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Artist(null, $database), array('value'=>'artistId', 'label'=>'name'), $inputGet->getArtistVocalistId()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Title</span>
        <input class="form-control" type="text" name="title" id="title" autocomplete="off" value="<?php echo $inputGet->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>

    <input class="btn btn-success" type="submit" value="Show">
    
    </form>
</div>
<?php
$orderMap = array(
    'title'=>'title', 
    'score'=>'score',
    'albumId'=>'albumId', 
    'album'=>'albumId', 
    'trackNumber'=>'trackNumber',
    'genreId'=>'genreId', 
    'genre'=>'genreId',
    'producerId'=>'producerId',
    'artistVocalId'=>'artistVocalId',
    'artistVocalist'=>'artistVocalId',
    'artistComposer'=>'artistComposer',
    'artistArranger'=>'artistArranger',
    'duration'=>'duration',
    'lsubtitleComplete'=>'lsubtitleComplete',
    'vocal'=>'vocal',
    'active'=>'active'
);
$defaultOrderBy = 'albumId';
$defaultOrderType = 'desc';
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createSongSpecification($inputGet);

if($pagination->getOrderBy() == '')
{
  $sortable = new PicoSortable();
  $sort1 = new PicoSort('albumId', PicoSortable::ORDER_TYPE_DESC);
  $sortable->addSortable($sort1);
  $sort2 = new PicoSort('trackNumber', PicoSortable::ORDER_TYPE_ASC);
  $sortable->addSortable($sort2);
}
else
{
  $sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType($defaultOrderType));
}

$pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);

$songEntity = new EntitySong(null, $database);
$rowData = $songEntity->findAll($spesification, $pagable, $sortable, true);

$result = $rowData->getResult();

?>

<script>
    let intvl = null;
    let audio = new Audio();
    function push(songId)
    {

    }
    function start(songId, btnObj)
    {
        window.open('teleprompter.php?song_id='+songId+'&delay='+0);
        let url = btnObj.parentNode.getAttribute('data-url');
        audio = new Audio();
        audio.src = url;
        audio.play();
        
        if(intvl != null)
        {
            clearInterval(intvl);
        }
        let start = 0;
        intvl = setInterval(function(){
            if(audio.currentTime != null && audio.currentTime >= 0)
            {
                start = (new Date()).getTime() - audio.currentTime;
                clearInterval(intvl);
                window.localStorage.setItem('offset_'+songId, start);         
            }
        }, 30);
    }

    function setPos(audio)
    {
        let pos = audio.currentTime * 1000;
        let start = (new Date()).getTime() - pos;
        let avg = getAvg(start, 10);
        document.title = avg;
    }
    let arrSum = [];
    function getAvg(val, max)
    {
        arrSum.push(val);
        if(arrSum.length > max)
        {
            arrSum.shift();
        }
        if(arrSum.length == 0)
        {
            return 0;
        }
        return arrSum.reduce((a, b) => a + b, 0) / arrSum.length;
    }

    $(document).ready(function(e){
        let pg = new Pagination('.pagination', '.page-selector', 'data-page-number', 'page');
        pg.init();
        $(document).on('change', '.filter-container form select', function(e2){
            $(this).closest('form').submit();
        });
    });
</script>

<?php
if(!empty($result))
{
?>
<div class="pagination">
    <div class="pagination-number">
    <?php
    foreach($rowData->getPagination() as $pg)
    {
        ?><span class="page-selector<?php echo $pg['selected'] ? ' page-selected':'';?>" data-page-number="<?php echo $pg['page'];?>"><a href="#"><?php echo $pg['page'];?></a></span><?php
    }
    ?>
    </div>
</div>

        <?php
        $no = $pagination->getOffset();
        foreach($result as $song)
        {
        $no++;
        $songId = $song->getSongId();
        $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&song_id=".$songId;
        $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&song_id=".$songId;
        $linkDelete = basename($_SERVER['PHP_SELF'])."?action=delete&song_id=".$songId;
        ?>
            <div class="song-wrapper" data-url="<?php echo $cfg->getSongBaseUrl()."/".$song->getFileName();?>?hash=<?php echo str_replace(array(' ', '-', ':'), '', $song->getLastUploadTime());?>">
            <button class="btn btn-success" onclick="push('<?php echo $songId;?>')">Push</button>
            <button class="btn btn-primary" onclick="start('<?php echo $songId;?>', this)">Start</button>          
            <?php echo $song->getName();?>
            </div>
        <?php
        }
        ?>

    <div class="pagination">
    <div class="pagination-number">
    <?php
    foreach($rowData->getPagination() as $pg)
    {
        ?><span class="page-selector<?php echo $pg['selected'] ? ' page-selected':'';?>" data-page-number="<?php echo $pg['page'];?>"><a href="#"><?php echo $pg['page'];?></a></span><?php
    }
    ?>
    </div>
</div>

<?php
}
?>

<script>
  let playerModal;
  
  
  $(document).ready(function(e){
    let playerModalSelector = document.querySelector('#songPlayer');
    playerModal = new bootstrap.Modal(playerModalSelector, {
      keyboard: false
    });
    
    $('a.play-data').on('click', function(e2){
      e2.preventDefault();
      $('#songPlayer').find('audio').attr('src', $(this).attr('data-url'));
      playerModal.show();
    });
    $('.close-player').on('click', function(e2){
      e2.preventDefault();
      $('#songPlayer').find('audio')[0].pause();
      playerModal.hide();
    });
  });
</script>

<div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="songPlayer" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="songPlayerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="addAlbumDialogLabel">Play Song</h5>
              <button type="button" class="btn-primary btn-close close-player" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <audio style="width: 100%; height: 40px;" controls></audio>
          </div>
          
          <div class="modal-footer">
              <button type="button" class="btn btn-success close-player">Close</button>
          </div>
      </div>
  </div>
</div>

<div class="lazy-dom modal-container modal-update-data" data-url="lib.ajax/song-update-dialog.php"></div>

<script>
  let updateSongModal;
  
  $(document).ready(function(e){
    
    $(document).on('click', '.edit-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      
      let songId = $(this).closest('tr').attr('data-id') || '';
      let dialogSelector = $('.modal-update-data');
      dialogSelector.load(dialogSelector.attr('data-url')+'?song_id='+songId, function(data){
        
        let updateSongModalElem = document.querySelector('#updateSongDialog');
        updateSongModal = new bootstrap.Modal(updateSongModalElem, {
          keyboard: false
        });
        updateSongModal.show();
        downloadForm('.lazy-dom-container', function(){
          if(!allDownloaded)
          {
              initModal2();
              console.log('loaded')
              allDownloaded = true;
          }
          loadForm();
      });
      })
    });

    $(document).on('click', '.save-update-song', function(){
      if($('.song-dialog audio').length > 0)
      {
        $('.song-dialog audio').each(function(){
          $(this)[0].pause();
        });
      }
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/song-update.php',
        data:dataSet, 
        dataType:'json',
        success: function(data)
        {
          updateSongModal.hide();
          let formData = getFormData(dataSet);
          let dataId = data.song_id;
          let title = data.title;
          let active = data.active;
          $('[data-id="'+dataId+'"] .text-data.text-data-title').text(data.title);
          $('[data-id="'+dataId+'"] .text-data.text-data-rating').text(data.score);
          $('[data-id="'+dataId+'"] .text-data.text-data-track-number').text(data.track_number);
          $('[data-id="'+dataId+'"] .text-data.text-data-artist-vocal-name').text(data.artist_vocal_name);
          $('[data-id="'+dataId+'"] .text-data.text-data-artist-composer-name').text(data.artist_composer_name);
          $('[data-id="'+dataId+'"] .text-data.text-data-artist-arranger-name').text(data.artist_arranger_name);
          $('[data-id="'+dataId+'"] .text-data.text-data-album-name').text(data.album_name);
          $('[data-id="'+dataId+'"] .text-data.text-data-genre-name').text(data.genre_name);
          $('[data-id="'+dataId+'"] .text-data.text-data-duration').text(data.duration);
          $('[data-id="'+dataId+'"] .text-data.text-data-vocal').text(data.vocal === true || data.vocal == 1 || data.vocal == "1" ?'Yes':'No');
          $('[data-id="'+dataId+'"] .text-data.text-data-active').text(data.active === true || data.active == 1 || data.active == "1" ?'Yes':'No');
        }
      })
    });
  });
</script>
<?php
}
require_once "inc/footer.php";
?>