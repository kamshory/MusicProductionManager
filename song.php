<?php
use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoSort;
use MagicObject\Database\PicoSortable;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Response\Generated\PicoSelectOption;
use MagicObject\Util\Dms;
use MusicProductionManager\Constants\ParamConstant;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\EntitySongComment;
use MusicProductionManager\Data\Entity\Genre;
use MusicProductionManager\Data\Entity\Producer;
use MusicProductionManager\Utility\SpecificationUtil;
use MusicProductionManager\Utility\UserUtil;

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
    <table class="table table-responsive table-responsive-two-side">
      <tbody>
        <tr>
          <td>Song ID</td>
          <td><?php echo $song->getSongId();?></td>
        </tr>
        <tr>
          <td>Name</td>
          <td><?php echo $song->getName();?></td>
        </tr>
        <tr>
          <td>Title</td>
          <td><?php echo $song->getTitle();?></td>
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
    <style>
      .comment-wrapper{
        padding: 10px 0;
        margin-bottom: 4px;
        border-bottom: 1px solid #DDDDDD;
      }
      .comment-content{
        padding: 5px 0;
      }
      .summernote{
        width: 100%;
        height: 120px;
      }
      .button-area{
        padding: 5px 0;
      }
    </style>
    
    <!-- Summernote CSS -->
    <link href="assets/summernote/css/summernote-lite.min.css" rel="stylesheet">
    <!-- Style CSS -->
    <link rel="stylesheet" href="assets/summernote/css/style.min.css">
    <!-- Summernote JS -->
    <script src="assets/summernote/js/summernote-lite.min.js"></script>

    <script>
      $(document).ready(function() {
        $('.summernote').summernote({
            placeholder: 'Type here',
            tabsize: 2,
            height: 120
        });
      })
    </script>


    <form action="">
      <h4>Comment</h4>
    <div><textarea name="summernote" class="summernote" rows="4"></textarea></div>
    <div class="button-area">
      <input type="submit" class="btn btn-primary" name="save" value="Save">
      <input type="button" class="btn btn-secondary" name="cancel" value="Cancel">
    </div>
    </form>

    
    <?php
    
    $songComment = new EntitySongComment(null, $database);
    try
    {
      $result = $songComment->findDescBySongId($inputGet->getSongId());
      $comments = $result->getResult();
      foreach($comments as $comment)
      {
        ?>
        <div class="comment-wrapper">
        <div class="comment-creator"><?php echo $comment->hasValueCreator() ? $comment->getCreator()->getName() : "";?> <?php echo date("j F Y H:i:s", strtotime($comment->getTimeCreate()));?></div>
        <div class="comment-content"><?php echo $comment->getComment();?></div>
        <div class="comment-controller"><a class="comment-edit" href="javascript:">Edit</a> &nbsp; <a class="comment-delete" href="javascript:">Delete</a></div>
        </div>
        <?php
      }
    }
    catch(Exception $e)
    {
      echo $e->getMessage();
    }
    
    ?>
    
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
  $allowChangeVocalist = UserUtil::isAllowSelectVocalist($currentLoggedInUser);
  $allowChangeComposer = UserUtil::isAllowSelectComposer($currentLoggedInUser);
  $allowChangeArranger = UserUtil::isAllowSelectArranger($currentLoggedInUser);
  
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
        <span>Producer</span>
        <select class="form-control" name="producer_id" id="producer_id">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Producer(null, $database), array('value'=>'producerId', 'label'=>'name'), $inputGet->getProducerId()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Composer</span>
        <select class="form-control" name="composer" id="composer">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Artist(null, $database), array('value'=>'artistId', 'label'=>'name'), $inputGet->getComposer()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Arranger</span>
        <select class="form-control" name="arranger" id="arranger">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Artist(null, $database), array('value'=>'artistId', 'label'=>'name'), $inputGet->getArranger()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Vocalist</span>
        <select class="form-control" name="vocalist" id="vocalist">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Artist(null, $database), array('value'=>'artistId', 'label'=>'name'), $inputGet->getVocalist()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Title</span>
        <input class="form-control" type="text" name="title" id="title" autocomplete="off" value="<?php echo $inputGet->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>
    <div class="filter-group">
        <span>Subtitle</span>
        <input class="form-control" type="text" name="subtitle" id="subtitle" autocomplete="off" value="<?php echo $inputGet->getSubtitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>
    
    <div class="filter-group">
        <span>Subtitle Complete</span>
        <select class="form-control" name="subtitle_complete" id="subtitle_complete">
            <option value="">- All -</option>
            <option value="1"<?php echo $inputGet->createSelectedSubtitleComplete("1");?>>Yes</option>
            <option value="0"<?php echo $inputGet->createSelectedSubtitleComplete("0");?>>No</option>
        </select>
    </div>

    <div class="filter-group">
        <span>Vocal</span>
        <select class="form-control" name="vocal" id="vocal">
            <option value="">- All -</option>
            <option value="1"<?php echo $inputGet->createSelectedVocal("1");?>>Yes</option>
            <option value="0"<?php echo $inputGet->createSelectedVocal("0");?>>No</option>
        </select>
    </div>

    <div class="filter-group">
        <span>Active</span>
        <select class="form-control" name="active" id="active">
            <option value="">- All -</option>
            <option value="1"<?php echo $inputGet->createSelectedActive("1");?>>Yes</option>
            <option value="0"<?php echo $inputGet->createSelectedActive("0");?>>No</option>
        </select>
    </div>

    <input class="btn btn-success" type="submit" value="Show">
    
    </form>
</div>
<?php
$orderMap = array(
    'name'=>'name', 
    'title'=>'title', 
    'rating'=>'rating',
    'albumId'=>'albumId', 
    'album'=>'album.sortOrder', 
    'trackNumber'=>'trackNumber',
    'genreId'=>'genreId', 
    'genre'=>'album.sortOrder',
    'producer'=>'producer.name',
    'artistVocalId'=>'artistVocalId',
    'artistVocalist'=>'vocalist.name',
    'artistComposer'=>'composer.name',
    'artistArranger'=>'arranger.name',
    'duration'=>'duration',
    'subtitleComplete'=>'subtitleComplete',
    'vocal'=>'vocal',
    'active'=>'active'
);
$defaultOrderBy = 'album.sortOrder';
$defaultOrderType = 'desc';
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createSongSpecification($inputGet);

if($pagination->getOrderBy() == '')
{
  $sortable = new PicoSortable();
  $sort1 = new PicoSort('album.sortOrder', PicoSortable::ORDER_TYPE_DESC);
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
<div class="table-list-container" style="overflow-x:auto">
<table class="table text-nowrap">
    <thead>
        <tr>
        <th scope="col" width="20"><i class="ti ti-edit"></i></th>
        <th scope="col" width="20"><i class="ti ti-player-play"></i></th>
        <th scope="col" width="20"><i class="ti ti-download"></i></th>
        <th scope="col" width="20">#</th>
        <th scope="col" class="col-sort" data-name="name">Name</th>
        <th scope="col" class="col-sort" data-name="title">Title</th>
        <th scope="col" class="col-sort" data-name="rating">Rate</th>
        <th scope="col" class="col-sort" data-name="album">Album</th>
        <th scope="col" class="col-sort" data-name="producer_id">Producer</th>
        <th scope="col" class="col-sort" data-name="track_number">Trk</th>
        <th scope="col" class="col-sort" data-name="genre">Genre</th>
        <th scope="col" class="col-sort" data-name="artist_vocalist">Vocalist</th>
        <th scope="col" class="col-sort" data-name="artist_composer">Composer</th>
        <th scope="col" class="col-sort" data-name="artist_arranger">Arranger</th>
        <th scope="col" class="col-sort" data-name="duration">Duration</th>
        <th scope="col" class="col-sort" data-name="vocal">Vocal</th>
        <th scope="col" class="col-sort" data-name="subtitle_complete">Sub</th>
        <th scope="col" class="col-sort" data-name="active">Active</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = $pagination->getOffset();
        foreach($result as $song)
        {
        $no++;
        $songId = $song->getSongId();
        $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&song_id=".$songId;
        $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&song_id=".$songId;
        $linkDelete = basename($_SERVER['PHP_SELF'])."?action=delete&song_id=".$songId;
        $linkDownload = "read-file.php?type=all&song_id=".$songId;
        ?>
        <tr data-id="<?php echo $songId;?>">
        <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
        <th scope="row"><a href="#" class="play-data" data-url="<?php echo $cfg->getSongBaseUrl()."/".$song->getSongId()."/".basename($song->getFilePath());?>?hash=<?php echo str_replace(array(' ', '-', ':'), '', $song->getLastUploadTime());?>"><i class="ti ti-player-play"></i></a></th>
        <th scope="row"><a href="<?php echo $linkDownload;?>"><i class="ti ti-download"></i></a></th>
        <th class="text-right" scope="row"><?php echo $no;?></th>
        <td class="text-nowrap"><a href="<?php echo $linkDetail;?>" class="text-data text-data-name"><?php echo $song->getName();?></a></td>
        <td class="text-nowrap"><a href="<?php echo $linkDetail;?>" class="text-data text-data-title"><?php echo $song->getTitle();?></a></td>
        <td class="text-data text-data-rating text-nowrap"><?php echo $song->hasValueRating() ? $song->getRating() : "";?></td>
        <td class="text-data text-data-album-name text-nowrap"><?php echo $song->hasValueAlbum() ? $song->getAlbum()->getName() : "";?></td>
        <td class="text-data text-data-producer-name text-nowrap"><?php echo $song->hasValueProducer() ? $song->getProducer()->getName() : "";?></td>
        <td class="text-data text-data-track-number text-nowrap"><?php echo $song->hasValueTrackNumber() ? $song->getTrackNumber() : "";?></td>
        <td class="text-data text-data-genre-name text-nowrap"><?php echo $song->hasValueGenre() ? $song->getGenre()->getName() : "";?></td>
        <td class="text-data text-data-artist-vocal-name text-nowrap"><?php echo $song->hasValueVocalist() ? $song->getVocalist()->getName() : "";?></td>
        <td class="text-data text-data-artist-composer-name text-nowrap"><?php echo $song->hasValueComposer() ? $song->getComposer()->getName() : "";?></td>
        <td class="text-data text-data-artist-arranger-name text-nowrap"><?php echo $song->hasValueArranger() ? $song->getArranger()->getName() : "";?></td>
        <td class="text-data text-data-duration text-nowrap"><?php echo (new Dms())->ddToDms($song->getDuration() / 3600)->printDms(true, true); ?></td>
        <td class="text-data text-data-vocal text-nowrap"><?php echo $song->isVocal() ? 'Yes' : 'No';?></td>
        <td class="text-data text-data-subtitle-complete text-nowrap"><?php echo $song->isSsubtitleComplete() ? 'Yes' : 'No';?></td>
        <td class="text-data text-data-active text-nowrap"><?php echo $song->isActive() ? 'Yes' : 'No';?></td>
        </tr>
        <?php
        }
        ?>
        
    </tbody>
    </table>
</div>

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
          $('[data-id="'+dataId+'"] .text-data.text-data-name').text(data.name);
          $('[data-id="'+dataId+'"] .text-data.text-data-title').text(data.title);
          $('[data-id="'+dataId+'"] .text-data.text-data-rating').text(data.rating);
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