<?php
use MagicObject\Database\PicoPageable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoSortable;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Response\Generated\PicoSelectOption;
use MusicProductionManager\Constants\ParamConstant;
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Data\Entity\EntityReference;
use MusicProductionManager\Data\Entity\Genre;

use MusicProductionManager\Utility\SpecificationUtil;


require_once "inc/auth-with-login-form.php";
require_once "inc/header.php";

$inputGet = new InputGet();
if($inputGet->equalsAction(ParamConstant::ACTION_DETAIL) && $inputGet->getReferenceId() != null)
{
  $reference = new EntityReference(null, $database);
  try
  {
    $reference->findOneByReferenceId($inputGet->getReferenceId());
    ?>
    <table class="table table-responsive table-responsive-two-side">
      <tbody>
        <tr>
          <td>Reference ID</td>
          <td><?php echo $reference->getReferenceId();?></td>
        </tr>
        <tr>
          <td>Title</td>
          <td><?php echo $reference->getTitle();?></td>
        </tr>
        <tr>
          <td>Genre</td>
          <td><?php echo $reference->issetGenre() ? $reference->getGenre()->getName() : '';?></td>
        </tr>
        <tr>
          <td>Artist</td>
          <td><?php echo $reference->issetArtist() ? $reference->getArtist()->getName() : '';?></td>
        </tr>
        <tr>
          <td>Album</td>
          <td><?php echo $reference->getAlbum();?></td>
        </tr>
        <tr>
          <td>Year</td>
          <td><?php echo $reference->getYear();?></td>
        </tr>
        <tr>
          <td>URL</td>
          <td><?php echo $reference->getUrl();?></td>
        </tr>
        <tr>
          <td>Description</td>
          <td><?php echo $reference->getDescription();?></td>
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
        <span>Title</span>
        <input class="form-control" type="text" name="title" id="title" autocomplete="off" value="<?php echo $inputGet->getTitle(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>
    <div class="filter-group">
        <span>Artist</span>
        <select class="form-control" name="artist_vocal_id" id="artist_vocal_id">
            <option value="">- All -</option>
            <?php echo new PicoSelectOption(new Artist(null, $database), array('value'=>'artistId', 'label'=>'name'), $inputGet->getArtistId()); ?>
        </select>
    </div>
    <div class="filter-group">
        <span>Album</span>
        <input class="form-control" type="text" name="album" id="album" autocomplete="off" value="<?php echo $inputGet->getAlbum(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>
    <div class="filter-group">
        <span>Year</span>
        <input class="form-control" type="number" name="year" id="year" autocomplete="off" value="<?php echo $inputGet->getYear(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
    </div>

    <div class="filter-group">
        <span>Complete</span>
        <select class="form-control" name="subtitle_complete" id="subtitle_complete">
            <option value="">- All -</option>
            <option value="1"<?php echo $inputGet->createSelectedLyricComplete(true);?>>Yes</option>
            <option value="0"<?php echo $inputGet->createSelectedLyricComplete(false);?>>No</option>
        </select>
    </div>

    <input class="btn btn-success" type="submit" value="Show">
    <input class="btn btn-primary add-data" type="button" value="Add">
    
    </form>
</div>
<?php
$orderMap = array(
    'title'=>'title', 
    'album'=>'albumId', 
    'genreId'=>'genreId', 
    'genre'=>'genreId',
    'artistId'=>'artistId'
);
$defaultOrderBy = 'albumId';
$defaultOrderType = 'desc';
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createReferenceSpecification($inputGet);
$sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType($defaultOrderType));
$pageable = new PicoPageable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);

$referenceEntity = new EntityReference(null, $database);
$rowData = $referenceEntity->findAll($spesification, $pageable, $sortable, true);

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
        <th scope="col" width="20"><i class="ti ti-trash"></i></th>
        <th scope="col" width="20">#</th>
        <th scope="col">Title</th>
        <th scope="col">Album</th>
        <th scope="col">Genre</th>
        <th scope="col">Artist</th>
        <th scope="col">Year</th>
        <th scope="col">Active</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = $pagination->getOffset();
        foreach($result as $reference)
        {
        $no++;
        $referenceId = $reference->getReferenceId();
        $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&reference_id=".$referenceId;
        $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&reference_id=".$referenceId;
        $linkDelete = basename($_SERVER['PHP_SELF'])."?action=delete&reference_id=".$referenceId;
        ?>
        <tr data-id="<?php echo $referenceId;?>">
        <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
        <th scope="row"><a href="<?php echo $linkDelete;?>" class="delete-data"><i class="ti ti-trash"></i></a></th>
        <th class="text-right" scope="row"><?php echo $no;?></th>
        <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-title"><?php echo $reference->getTitle();?></a></td>
        <td class="text-data text-data-album"><?php echo $reference->getAlbum();?></td>
        <td class="text-data text-data-genre-name"><?php echo $reference->issetGenre() ? $reference->getGenre()->getName() : "";?></td>
        <td class="text-data text-data-artist-name"><?php echo $reference->issetArtist() ? $reference->getArtist()->getName() : "";?></td>
        <td class="text-data text-data-duration"><?php echo $reference->getYear();?></td>
        <td class="text-data text-data-active"><?php echo $reference->isActive() ? 'Yes' : 'No';?></td>
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

<div class="lazy-dom modal-container modal-update-data" data-url="lib.ajax/reference-update-dialog.php"></div>
<div class="lazy-dom modal-container modal-add-data" data-url="lib.ajax/reference-add-dialog.php"></div>

<script>
  let addReferenceModal;
  let updateReferenceModal;

  $(document).ready(function(e){
    $(document).on('click', '.add-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let dialogSelector = $('.modal-add-data');
      dialogSelector.load(dialogSelector.attr('data-url'), function(data){
        let addReferenceModalElem = document.querySelector('#addReferenceDialog');
        addReferenceModal = new bootstrap.Modal(addReferenceModalElem, {
          keyboard: false
        });
        addReferenceModal.show();
      })
    });

    $(document).on('click', '.save-add-reference', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/reference-add.php',
        data:dataSet, 
        dataType:'html',
        success: function(data)
        {
          addReferenceModal.hide();
          window.location.reload();
        }
      })
    });  
    
    $(document).on('click', '.edit-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      
      let referenceId = $(this).closest('tr').attr('data-id') || '';
      let dialogSelector = $('.modal-update-data');
      dialogSelector.load(dialogSelector.attr('data-url')+'?reference_id='+referenceId, function(data){
        
        let updateReferenceModalElem = document.querySelector('#updateReferenceDialog');
        updateReferenceModal = new bootstrap.Modal(updateReferenceModalElem, {
          keyboard: false
        });
        updateReferenceModal.show();
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

    $(document).on('click', '.save-update-reference', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/reference-update.php',
        data:dataSet, 
        dataType:'json',
        success: function(data)
        {
          updateReferenceModal.hide();
          let formData = getFormData(dataSet);
          let dataId = data.reference_id;
          let title = data.title;
          let active = data.active;
          $('[data-id="'+dataId+'"] .text-data.text-data-title').text(data.title);
          $('[data-id="'+dataId+'"] .text-data.text-data-artist-name').text(data.artist_vocal_name);
          $('[data-id="'+dataId+'"] .text-data.text-data-album').text(data.album);
          $('[data-id="'+dataId+'"] .text-data.text-data-genre-name').text(data.genre_name);
          $('[data-id="'+dataId+'"] .text-data.text-data-active').text(data.active === true || data.active == 1 || data.active == "1" ?'Yes':'No');
          $('[data-id="'+dataId+'"] .text-data.text-data-duration').text(data.duration);

        }
      })
    });
  });
</script>
<?php
}
require_once "inc/footer.php";
?>