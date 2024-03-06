<?php

use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoPredicate;
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
use MusicProductionManager\Data\Entity\EntitySongComment;
use MusicProductionManager\Data\Entity\Genre;

use MusicProductionManager\Utility\SpecificationUtil;

require_once "inc/auth-with-login-form.php";

$inputGet = new InputGet();
if($inputGet->equalsAction('download') && $inputGet->getSongId() != null)
{
  try
  {
    $song = new EntitySong(null, $database);
    $song->findOneBySongId($inputGet->getSongId());
    if(file_exists($song->getFilePathMidi()))
    {
      $filename = $song->getName().".mid";
      header("Content-disposition: attachment; filename=\"$filename\"");
      header("Content-type: audio/midi");
      readfile($song->getFilePathMidi());
    }
  }
  catch(Exception $e)
  {
    // do nothing
  }
  exit();
}
else
{
require_once "inc/header.php";

if($inputGet->equalsAction('edit-lyric') && $inputGet->getSongId() != null)
{
  try
  {
    $song = new EntitySong(null, $database);
    $song->findOneBySongId($inputGet->getSongId());
    require_once "midi-lyric.php";
  }
  catch(Exception $e)
  {
    // do nothing
  }
}
else if($inputGet->equalsAction('edit-instrument') && $inputGet->getSongId() != null)
{
  try
  {
    $song = new EntitySong(null, $database);
    $song->findOneBySongId($inputGet->getSongId());
    require_once "midi-instrument.php";
  }
  catch(Exception $e)
  {
    // do nothing
  }
}
else if($inputGet->equalsAction('rescale') && $inputGet->getSongId() != null)
{
  try
  {
    $song = new EntitySong(null, $database);
    $song->findOneBySongId($inputGet->getSongId());
    require_once "midi-rescale.php";
  }
  catch(Exception $e)
  {
    // do nothing
  }
}

else if($inputGet->equalsAction('transpose') && $inputGet->getSongId() != null)
{
  try
  {
    $song = new EntitySong(null, $database);
    $song->findOneBySongId($inputGet->getSongId());
    require_once "midi-transpose.php";
  }
  catch(Exception $e)
  {
    // do nothing
  }
}

else if($inputGet->equalsAction(ParamConstant::ACTION_DETAIL) && $inputGet->getSongId() != null)
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
    
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.js" integrity="sha512-VqW3FWLsKVphZNAVsUKfA5UJ9oxVamFqNtHs46UxI7UA/gQ6GGaZ37GYdotPJ27Y/C8dvOQEKjWbfiNOkkhVAA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css" integrity="sha512-ngQ4IGzHQ3s/Hh8kMyG4FC74wzitukRMIcTOoKT3EyzFZCILOPF0twiXOQn75eDINUfKBYmzYn2AA8DkAk8veQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />



    <form action="">
      <h4>Comment</h4>
    <div><textarea name="summernote" class="summernote" rows="4"></textarea></div>
    <div class="button-area">
      <input type="submit" class="btn btn-primary" name="save" value="Save">
      <input type="button" class="btn btn-secondary" name="cancel" value="Cancel">
    </div>
    </form>
    <script>
      $(document).ready(function() {
        $('.summernote').summernote({});
      });
    </script>
    
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
        <div class="comment-creator"><?php echo $comment->hasValueCreator() ? $comment->getCreator()->getName() : "";?> <?php echo date("j F Y H:i:s", strtotime($comment->hasValueTimeCreate()));?></div>
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
        <input class="form-control" type="text" name="name" id="name" autocomplete="off" value="<?php echo $inputGet->getName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
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
        <span>Lyric</span>
        <select class="form-control" name="lyric_complete" id="lyric_complete">
            <option value="">- All -</option>
            <option value="1"<?php echo $inputGet->createSelectedLyricComplete("1");?>>Yes</option>
            <option value="0"<?php echo $inputGet->createSelectedLyricComplete("0");?>>No</option>
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
    'album'=>'albumId', 
    'trackNumber'=>'trackNumber',
    'genreId'=>'genreId', 
    'genre'=>'genreId',
    'artistVocalId'=>'artistVocalId',
    'artistVocalist'=>'artistVocalId',
    'artistComposerId'=>'artistComposerId',
    'artistComposer'=>'artistComposerId',
    'duration'=>'duration',
    'lyricComplete'=>'lyricComplete',
    'vocal'=>'vocal',
    'active'=>'active'
);
$defaultOrderBy = 'albumId';
$defaultOrderType = 'desc';
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createSongSpecification($inputGet);

$filter1 = new PicoPredicate();
$filter1->notEquals("filePathMidi", null);
$spesification->addAnd($filter1);

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
<table class="table">
    <thead>
        <tr>
        <th scope="col" width="20"><i class="ti ti-edit"></i></th>
        <th scope="col" width="20"><i class="ti ti-edit"></i></th>
        <th scope="col" width="20"><i class="ti ti-edit"></i></th>
        <th scope="col" width="20"><i class="ti ti-download"></i></th>
        <th scope="col" width="20"><i class="ti ti-player-play"></i></th>
        <th scope="col" width="20">#</th>
        <th scope="col" class="col-sort" data-name="name">Name</th>
        <th scope="col" class="col-sort" data-name="title">Title</th>
        <th scope="col" class="col-sort" data-name="rating">Rating</th>
        <th scope="col" class="col-sort" data-name="album_id">Album</th>
        <th scope="col" class="col-sort" data-name="track_number">Track</th>
        <th scope="col" class="col-sort" data-name="genre_id">Genre</th>
        <th scope="col" class="col-sort" data-name="artist_vocalist">Vocalist</th>
        <th scope="col" class="col-sort" data-name="artist_composer">Composer</th>
        <th scope="col" class="col-sort" data-name="duration">Duration</th>
        <th scope="col" class="col-sort" data-name="vocal">Vocal</th>
        <th scope="col" class="col-sort" data-name="lyric_complete">Lyric</th>
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
        $linkEditInstrument = basename($_SERVER['PHP_SELF'])."?action=edit-instrument&song_id=".$songId;
        $linkEditLyric = basename($_SERVER['PHP_SELF'])."?action=edit-lyric&song_id=".$songId;
        $linkRescale = basename($_SERVER['PHP_SELF'])."?action=rescale&song_id=".$songId;
        $linkDownload = basename($_SERVER['PHP_SELF'])."?action=download&song_id=".$songId;
        $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&song_id=".$songId;
        $linkDelete = basename($_SERVER['PHP_SELF'])."?action=delete&song_id=".$songId;
        ?>
        <tr data-id="<?php echo $songId;?>">
        <th scope="row"><a href="<?php echo $linkEditInstrument;?>"><i class="ti ti-edit"></i></a></th>
        <th scope="row"><a href="<?php echo $linkEditLyric;?>"><i class="ti ti-edit"></i></a></th>
        <th scope="row"><a href="<?php echo $linkRescale;?>"><i class="ti ti-edit"></i></a></th>
        <th scope="row"><a href="<?php echo $linkDownload;?>"><i class="ti ti-download"></i></a></th>
        <th scope="row"><a href="#" class="play-data" data-url="<?php echo $cfg->getSongBaseUrl()."/".$song->getFileName();?>?hash=<?php echo str_replace(array(' ', '-', ':'), '', $song->getLastUploadTime());?>"><i class="ti ti-player-play"></i></a></th>
        <th class="text-right" scope="row"><?php echo $no;?></th>
        <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-name"><?php echo $song->getName();?></a></td>
        <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-title"><?php echo $song->getTitle();?></a></td>
        <td class="text-data text-data-rating"><?php echo $song->hasValueRating() ? $song->getRating() : "";?></td>
        <td class="text-data text-data-album-name"><?php echo $song->hasValueAlbum() ? $song->getAlbum()->getName() : "";?></td>
        <td class="text-data text-data-track-number"><?php echo $song->hasValueTrackNumber() ? $song->getTrackNumber() : "";?></td>
        <td class="text-data text-data-genre-name"><?php echo $song->hasValueGenre() ? $song->getGenre()->getName() : "";?></td>
        <td class="text-data text-data-artist-vocal-name"><?php echo $song->hasValueVocalist() ? $song->getVocalist()->getName() : "";?></td>
        <td class="text-data text-data-artist-composer-name"><?php echo $song->hasValueComposer() ? $song->getComposer()->getName() : "";?></td>
        <td class="text-data text-data-duration"><?php echo $song->getDuration();?></td>
        <td class="text-data text-data-vocal"><?php echo $song->isVocal() ? 'Yes' : 'No';?></td>
        <td class="text-data text-data-lyric-complete"><?php echo $song->isLyricComplete() ? 'Yes' : 'No';?></td>
        <td class="text-data text-data-active"><?php echo $song->isActive() ? 'Yes' : 'No';?></td>
        </tr>
        <?php
        }
        ?>
        
    </tbody>
    </table>


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
}
?>