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
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Data\Entity\EntitySongDraft;
use MusicProductionManager\Data\Entity\EntitySongDraftComment;
use MusicProductionManager\Utility\SpecificationUtil;
use MusicProductionManager\Utility\UserUtil;

require_once "inc/auth-with-login-form.php";
require_once "inc/header.php";

$inputGet = new InputGet();
if($inputGet->equalsAction(ParamConstant::ACTION_DETAIL) && $inputGet->getSongDraftId() != null)
{
  try
  {
    $songDraft = new EntitySongDraft(null, $database);
    $songDraft->findOneBySongDraftId($inputGet->getSongDraftId());
    ?>
    <table class="table table-responsive">
      <tbody>
        <tr>
          <td>Song Draft ID</td>
          <td><?php echo $songDraft->getSongDraftId();?></td>
        </tr>
        <tr>
          <td>Name</td>
          <td><?php echo $songDraft->getName();?></td>
        </tr>
        <tr>
          <td>Title</td>
          <td><?php echo $songDraft->getTitle();?></td>
        </tr>
        <tr>
          <td>Rating</td>
          <td><?php echo $songDraft->getRating();?></td>
        </tr>
        <tr>
          <td>Duration</td>
          <td><?php echo $songDraft->getDuration();?></td>
        </tr>
        <tr>
          <td>File Size</td>
          <td><?php echo $songDraft->getFileSize();?></td>
        </tr>
        <tr>
          <td>Created</td>
          <td><?php echo $songDraft->getTimeCreate();?></td>
        </tr>
        <tr>
          <td>Last Update</td>
          <td><?php echo $songDraft->getTimeEdit();?></td>
        </tr>
        <tr>
          <td>Active</td>
          <td><?php echo $songDraft->booleanToTextByActive('Yes', 'No');?></td>
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
    <link rel="stylesheet" href="assets/summernote/css/style.css">
    <!-- Summernote JS -->
    <script src="assets/summernote/js/summernote-lite.min.js"></script>
 

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
        $('.summernote').summernote({
            placeholder: 'Type here',
            tabsize: 2,
            height: 120
        });
      })
    </script>
    
    <?php
    
    $songComment = new EntitySongDraftComment(null, $database);
    try
    {
      $result = $songComment->findDescBySongDraftId($inputGet->getSongDraftId());
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
        <span>Artist</span>
        <select class="form-control" name="artist_id" id="artist_id">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Artist(null, $database), array('value'=>'artistId', 'label'=>'name'), $inputGet->getArtistId()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>From</span>
        <input class="form-control" type="date" name="from" id="from" autocomplete="off" value="<?php echo $inputGet->getFrom(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>
    <div class="filter-group">
        <span>To</span>
        <input class="form-control" type="date" name="to" id="to" autocomplete="off" value="<?php echo $inputGet->getTo(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>
    <div class="filter-group">
        <span>Title</span>
        <input class="form-control" type="text" name="title" id="title" autocomplete="off" value="<?php echo $inputGet->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>
    <div class="filter-group">
        <span>Lyric</span>
        <input class="form-control" type="text" name="lyric" id="lyric" autocomplete="off" value="<?php echo $inputGet->getLyric(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
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
    'time_create'=>'time_create',
    'time_edit'=>'time_edit',
    'admin_create'=>'admin_create'
);
$defaultOrderBy = 'timeCreate';
$defaultOrderType = 'desc';
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createSongDraftSpecification($inputGet, array("parent_id"=>null));

if($pagination->getOrderBy() == '')
{
  $sortable = new PicoSortable();
  $sort1 = new PicoSort('timeCreate', PicoSortable::ORDER_TYPE_DESC);
  $sortable->addSortable($sort1);
}
else
{
  $sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType($defaultOrderType));
}

$pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);
$songDraftEntity = new EntitySongDraft(null, $database);
$rowData = $songDraftEntity->findAll($spesification, $pagable, $sortable, true);
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
        <th scope="col" width="20"><i class="ti ti-player-play"></i></th>
        <th scope="col" width="20"><i class="ti ti-download"></i></th>
        <th scope="col" width="20">#</th>
        <th scope="col" class="col-sort" data-name="name">Name</th>
        <th scope="col" class="col-sort" data-name="title">Title</th>
        <th scope="col" class="col-sort" data-name="rating">Rating</th>
        <th scope="col" class="col-sort" data-name="artist_id">Artist</th>
        <th scope="col" class="col-sort" data-name="time_create">Created</th>
        <th scope="col" class="col-sort" data-name="duration">Duration</th>
        <th scope="col" class="col-sort" data-name="active">Active</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = $pagination->getOffset();
        foreach($result as $songDraft)
        {
        $no++;
        $songDraftId = $songDraft->getSongDraftId();
        $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&song_draft_id=".$songDraftId;
        $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&song_draft_id=".$songDraftId;
        $linkDelete = basename($_SERVER['PHP_SELF'])."?action=delete&song_draft_id=".$songDraftId;
        $linkDownload = "read-file.php?type=draft&song_draft_id=".$songDraftId;
        ?>
        <tr data-id="<?php echo $songDraftId;?>">
        <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
        <th scope="row"><a href="#" class="play-data" data-song-draft-id="<?php echo $songDraftId;?>" data-url="<?php echo $cfg->getSongDraftBaseUrl()."/".$songDraft->getSongDraftId()."/".basename($songDraft->getFilePath());?>?hash=<?php echo str_replace(array(' ', '-', ':'), '', $songDraft->getLastUploadTime());?>"><i class="ti ti-player-play"></i></a></th>
        <th scope="row"><a href="<?php echo $linkDownload;?>"><i class="ti ti-download"></i></a></th>
        <th class="text-right" scope="row"><?php echo $no;?></th>
        <td class="text-nowrap"><a href="<?php echo $linkDetail;?>" class="text-data text-data-name"><?php echo $songDraft->getName();?></a></td>
        <td class="text-nowrap"><a href="<?php echo $linkDetail;?>" class="text-data text-data-title"><?php echo $songDraft->getTitle();?></a></td>
        <td class="text-data text-data-time-create text-nowrap"><?php echo number_format($songDraft->getRating(), 1);?></td>
        <td class="text-data text-data-time-artist text-nowrap"><?php echo $songDraft->hasValueArtist() ? $songDraft->getArtist()->getName() : "";?></td>
        <td class="text-data text-data-time-create text-nowrap"><?php echo $songDraft->getTimeCreate();?></td>
        <td class="text-data text-data-duration text-nowrap"><?php echo (new Dms())->ddToDms($songDraft->getDuration() / 3600)->printDms(true, true); ?></td>
        <td class="text-data text-data-active text-nowrap"><?php echo $songDraft->isActive() ? 'Yes' : 'No';?></td>
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
<link rel="stylesheet" href="assets/rateyo/rateyo.css">
<script src="assets/rateyo/rateyo.js"></script>

<script>
  let playerModal;
  
  
  $(document).ready(function(e){
    let playerModalSelector = document.querySelector('#songPlayer');
    playerModal = new bootstrap.Modal(playerModalSelector, {
      keyboard: false
    });
    
    $('a.play-data').on('click', function(e2){
      e2.preventDefault();
      let song_draft_id = $(this).attr('data-song-draft-id');
      $('#songPlayer').find('audio').attr('src', $(this).attr('data-url'));
      $('#songPlayer').find('.song-rating').attr('data-song-draft-id', song_draft_id);
      $.ajax({
        type:'GET',
        url:'lib.ajax/song-draft-get-rating.php',
        data:{song_draft_id:song_draft_id},
        success:function(response)
        {
          updateRate(response);
        }
      });

      playerModal.show();




    });
    $('.close-player').on('click', function(e2){
      e2.preventDefault();
      $('#songPlayer').find('audio')[0].pause();
      playerModal.hide();
    });
  });
  
</script>
<script>
  $(document).ready(function() {
    $('.song-rating').each(function(e) {
      let rate = parseFloat($(this).attr('data-rate'));
      $(this).rateYo({
        rating: rate,
        starWidth: "16px"
      });
    });

    $('.song-rating').rateYo().on('rateyo.set', function(e, data) {
      setRateEvent(e, data);
    });

  });
  
  function setRateEvent(e, data)
  {
    let songDraftId = $(e.currentTarget).attr('data-song-draft-id');
      $.ajax({
        type: 'POST',
        url: 'lib.ajax/song-draft-set-rating.php',
        dataType: 'json',
        data: {
          song_draft_id: songDraftId,
          rating: data.rating
        },
        success: function(response) {
          updateRate(response);
        }
      });
  }
  function updateRate(response)
  {
    let selector = '.song-rating[data-song-draft-id="'+response.song_draft_id+'"]';
    let newRate = $('<div />');
    $(selector).replaceWith(newRate);
    newRate.addClass("song-rating");
    newRate.addClass("half-star-ratings");
    newRate.attr("data-rateyo-half-star", "true");
    newRate.attr('data-song-draft-id', response.song_draft_id);
    newRate.attr('data-rate', response.rating);
  
    $(selector).rateYo({
      rating: parseFloat(response.rating),
      starWidth: "16px"
    });
    $(selector).rateYo().on('rateyo.set', function(e, data) {
      setRateEvent(e, data);
    });
  }
</script>
<div style="background-color: rgba(0, 0, 0, 0.11);" class="modal fade" id="songPlayer" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="songPlayerLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="addAlbumDialogLabel">Play Song</h5>
              <button type="button" class="btn-primary btn-close close-player" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div class="audio-player">
              <audio style="width: 100%; height: 40px;" controls></audio>
              </div>
              <div class="song-rating half-star-ratings" data-rateyo-half-star="true" data-rate="0" data-song-draft-id=""></div>
          </div>
          
          <div class="modal-footer">
              <button type="button" class="btn btn-success close-player">Close</button>
          </div>
      </div>
  </div>
</div>

<div class="lazy-dom modal-container modal-update-data" data-url="lib.ajax/song-draft-update-dialog.php"></div>

<script>
  let updateSongModal;
  
  $(document).ready(function(e){
    
    $(document).on('click', '.edit-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      
      let songDraftId = $(this).closest('tr').attr('data-id') || '';
      let dialogSelector = $('.modal-update-data');
      dialogSelector.load(dialogSelector.attr('data-url')+'?song_draft_id='+songDraftId, function(data){
        
        let updateSongModalElem = document.querySelector('#updateSongDraftDialog');
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

    $(document).on('click', '.save-update-song-draft', function(){
      if($('.song-draft-dialog audio').length > 0)
      {
        $('.song-draft-dialog audio').each(function(){
          $(this)[0].pause();
        });
      }
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/song-draft-update.php',
        data:dataSet, 
        dataType:'json',
        success: function(data)
        {
          updateSongModal.hide();
          let formData = getFormData(dataSet);
          let dataId = data.song_draft_id;
          $('[data-id="'+dataId+'"] .text-data.text-data-name').text(data.name);
          $('[data-id="'+dataId+'"] .text-data.text-data-title').text(data.title);
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