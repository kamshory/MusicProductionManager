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
use MusicProductionManager\Data\Dto\SongFile;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\EntitySongComment;
use MusicProductionManager\Data\Entity\Genre;
use MusicProductionManager\Utility\SongFileUtil;
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
        <span>Vocalist</span>
        <select class="form-control" name="vocalist" id="vocalist">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Artist(null, $database), array('value'=>'artistId', 'label'=>'name'), $inputGet->getVocalist()); ?>
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
        <span>Title</span>
        <input class="form-control" type="text" name="title" id="title" autocomplete="off" value="<?php echo $inputGet->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>
    <div class="filter-group">
        <span>Subtitle</span>
        <input class="form-control" type="text" name="subtitle" id="subtitle" autocomplete="off" value="<?php echo $inputGet->getSubtitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>

    <div class="filter-group">
        <span>Subtitle</span>
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
  'genre'=>'genre.sortOrder',
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
$defaultOrderType = PicoSortable::ORDER_TYPE_DESC;
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

<link rel="stylesheet" href="assets/rateyo/rateyo.min.css">
<script src="assets/rateyo/rateyo.min.js"></script>
<script>
  $(document).ready(function() {
    $('.song-rating').each(function(e) {
      let rate = parseFloat($(this).attr('data-rate'));
      $(this).rateYo({
        rating: rate,
        starWidth: "14px"
      });
    });

    $('.song-rating').rateYo().on('rateyo.set', function(e, data) {
      setRateEvent(e, data);
    });

  });
  
  function setRateEvent(e, data)
  {
    let songId = $(e.currentTarget).attr('data-song-id');
      $.ajax({
        type: 'POST',
        url: 'lib.ajax/song-set-rating.php',
        dataType: 'json',
        data: {
          song_id: songId,
          rating: data.rating
        },
        success: function(response) {
          updateRate(response);
        }
      });
  }
  function updateRate(response)
  {
    let selector = '.song-rating[data-song-id="'+response.song_id+'"]';
    let newRate = $('<div />');
    $(selector).replaceWith(newRate);
    newRate.addClass("song-rating");
    newRate.addClass("half-star-ratings");
    newRate.attr("data-rateyo-half-star", "true");
    newRate.attr('data-song-id', response.song_id);
    newRate.attr('data-rate', response.rating);
  
    $(selector).rateYo({
      rating: parseFloat(response.rating),
      starWidth: "14px"
    });
    $(selector).rateYo().on('rateyo.set', function(e, data) {
      setRateEvent(e, data);
    });
  }
</script>

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



<div class="row">
<?php

foreach ($result as $song) {
    $songFile = new SongFile($song);
    $buttonMp3 = SongFileUtil::createDownloadButton($songFile, 'mp3', 'MP3', 'read-file.php', '_blank');
    $buttonMidi = SongFileUtil::createDownloadButton($songFile, 'midi', 'MID', 'read-file.php', '_blank');
    $buttonXml = SongFileUtil::createDownloadButton($songFile, 'xml', 'XML', 'read-file.php', '_blank');
    $buttonPdf = SongFileUtil::createDownloadButton($songFile, 'pdf', 'PDF', 'read-file.php', '_blank');
  ?>

  <div class="custom-card-container col-sm-12 col-xl-6">
    <div class="card overflow-hidden rounded-2">
      <div class="card-body pt-3 p-4">

        <div class="d-flex align-items-center justify-content-between">
          <h6 class="fw-semibold fs-4 col-4"><?php echo $song->getName(); ?></h6>
          <div class="song-tools list-unstyled d-flex align-items-center col-8 mb-0 me-1 justify-content-end text-end">
            <div class="d-inline-block">
              <a href="karaoke.php?song_id=<?php echo $song->getSongId(); ?>&action=open"><span class="ti ti-music"></span></a> &nbsp;
              <a href="karaoke.php?song_id=<?php echo $song->getSongId(); ?>&action=open"><span class="ti ti-microphone"></span></a> &nbsp;
              <a href="comment.php?song_id=<?php echo $song->getSongId(); ?>&action=edit"><span class="ti ti-message"></span></a> &nbsp;
              <div class="d-inline-block">
                <div class="song-rating half-star-ratings" data-rateyo-half-star="true" data-rate="<?php echo $song->getRating() * 1; ?>" data-song-id="<?php echo $song->getSongId(); ?>"></div>
              </div>
          </div>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Title</div>
          <div class="col-8"><?php echo $song->getTitle(); ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Album</div>
          <div class="col-8"><?php echo $song->hasValueAlbum() ? $song->getAlbum()->getName() : ''; ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Producer</div>
          <div class="col-8"><?php echo $song->hasValueProducer() ? $song->getProducer()->getName() : ''; ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Genre</div>
          <div class="col-8"><?php echo $song->hasValueGenre() ? $song->getGenre()->getName() : ''; ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Composer</div>
          <div class="col-8"><?php echo $song->hasValueComposer() ? $song->getComposer()->getName() : ''; ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Arranger</div>
          <div class="col-8"><?php echo $song->hasValueArranger() ? $song->getArranger()->getName() : ''; ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Vocalist</div>
          <div class="col-8"><?php echo $song->hasValueVocalist() ? $song->getVocalist()->getName() : ''; ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Track</div>
          <div class="col-8"><?php echo $song->getTrackNumber(); ?><?php echo $song->hasValueAlbum() ? "/" . $song->getAlbum()->getNumberOfSong() : ''; ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Duration</div>
          <div class="col-8"><?php echo (new Dms())->ddToDms($song->getDuration() / 3600)->printDms(true, true); ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">BPM</div>
          <div class="col-8"><?php echo $song->getBpm(); ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Time Signature</div>
          <div class="col-8"><?php echo $song->getTimeSignature(); ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="col-4">Last Update</div>
          <div class="col-8"><?php echo date('M j<\s\u\p>S</\s\u\p> Y H:i:s', strtotime($song->getTimeEdit())); ?></div>
        </div>
        <div class="d-flex align-items-center justify-content-end text-end pt-4">
          <div class="list-unstyled align-items-center mb-0 me-1 d-inline">
            <a href="subtitle.php?action=edit&song_id=<?php echo $song->getSongId(); ?>" class="btn btn-sm btn-tn btn-success"><span class="ti ti-edit"></span> EDIT</a>
            <a href="javascript;" onclick="uploadFile('<?php echo $song->getSongId(); ?>'); return false" class="btn btn-sm btn-tn btn-success"><span class="ti ti-upload"></span> UPLOAD</a>
            <?php echo $buttonMp3; ?>
            <?php echo $buttonMidi; ?>
            <?php echo $buttonXml; ?>
            <?php echo $buttonPdf; ?>

          </div>
        </div>
      </div>
    </div>
  </div>

  <?php
  }
  ?>
  
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