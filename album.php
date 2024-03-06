<?php

use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoSortable;
use MagicObject\Exceptions\NoRecordFoundException;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MusicProductionManager\Constants\ParamConstant;
use MusicProductionManager\Data\Entity\EntityAlbum;
use MusicProductionManager\Data\Entity\Producer;
use MagicObject\Response\Generated\PicoSelectOption;
use MusicProductionManager\Utility\SpecificationUtil;

require_once "inc/auth-with-login-form.php";
require_once "inc/header.php";

$inputGet = new InputGet();
if($inputGet->equalsAction(ParamConstant::ACTION_DETAIL) && $inputGet->getAlbumId() != null)
{
  $album = new EntityAlbum(null, $database);
  try
  {
  $album->findOneByAlbumId($inputGet->getAlbumId());
  ?>
  
  <table class="table table-responsive">
    <tbody>
      <tr>
        <td>Album ID</td>
        <td><?php echo $album->getAlbumId();?></td>
      </tr>
      <tr>
        <td>Name</td>
        <td><?php echo $album->getName();?></td>
      </tr>
      <tr>
        <td>Description</td>
        <td><?php echo $album->getDescription();?></td>
      </tr>
      <tr>
        <td>Producer</td>
        <td><?php echo $album->hasValueProducer() ? $album->getProducer()->getName() : "";?></td>
      </tr>
      <tr>
        <td>Release Date</td>
        <td><?php echo $album->getReleaseDate();?></td>
      </tr>
      <tr>
        <td>Number of Song</td>
        <td><?php echo $album->getNumberOfSong();?></td>
      </tr>
      <tr>
        <td>Duration</td>
        <td><?php echo $album->getDuration();?></td>
      </tr>
      <tr>
          <td>Active</td>
          <td><?php echo $album->booleanToTextByActive('Yes', 'No');?></td>
        </tr>
    </tbody>
  </table>
  
  <?php
  }
  catch(NoRecordFoundException $e)
  {
    ?>
    <div class="alert alert-warning"><?php echo $e->getMessage();?></div>
    <?php
  }
  catch(Exception $e)
  {
    // do something here
  }
}
else
{
  ?>
  <div class="filter-container">
  <form action="" method="get">
    
  <div class="filter-group">
      <span>Producer</span>
      <select class="form-control" name="producer_id" id="producer_id">
          <option value="">- All -</option>
          <?php echo new PicoSelectOption(new Producer(null, $database), array('value'=>'producerId', 'label'=>'name'), $inputGet->getProducerId(), null, new PicoSortable('name', PicoSortable::ORDER_TYPE_ASC)); ?>
      </select>
  </div>
  <div class="filter-group">
      <span>Name</span>
      <input class="form-control" type="text" name="name" id="name" autocomplete="off" value="<?php echo $inputGet->getName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
  </div>
  
  <input class="btn btn-success" type="submit" value="Show">
  <input class="btn btn-primary add-data" type="button" value="Add">
  
  </form>
</div>
<?php
$orderMap = array(
  'name'=>'name', 
  'albumId'=>'albumId', 
  'album'=>'albumId',
  'sortOrder'=>'sortOrder',
  'numberOfSong'=>'numberOfSong',
  'duration'=>'duration',
  'active'=>'active',
  'ad_draft'=>'ad_draft',
  'producer_id'=>'producer_id'
);
$defaultOrderBy = 'sortOrder';
$defaultOrderType = 'desc';
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createAlbumSpecification($inputGet);
$sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType($defaultOrderType));
$pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);

$albumEntity = new EntityAlbum(null, $database);
$rowData = $albumEntity->findAll($spesification, $pagable, $sortable, true);

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
      <th scope="col" width="20"><i class="ti ti-trash"></i></th>
      <th scope="col" width="20"><i class="ti ti-download"></i></th>
      <th scope="col" width="20">#</th>
      <th scope="col" class="col-sort" data-name="name">Name</th>
      <th scope="col" class="col-sort" data-name="duration">Duration</th>
      <th scope="col" class="col-sort" data-name="number_of_song">Song</th>
      <th scope="col" class="col-sort" data-name="producer_id">Producer</th>
      <th scope="col" class="col-sort" data-name="sort_order">Order</th>
      <th scope="col" class="col-sort" data-name="active">Active</th>
      <th scope="col" class="col-sort" data-name="ad_draft">Draft</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = $pagination->getOffset();
    foreach($result as $album)
    {
      $no++;
      $albumId = $album->getAlbumId();
      $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&album_id=".$albumId;
      $linkdDelete = basename($_SERVER['PHP_SELF'])."?action=delete&album_id=".$albumId;
      $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&album_id=".$albumId;
      $linkDownload = "read-file.php?type=all&album_id=".$albumId;
    ?>
      <tr data-id="<?php echo $albumId;?>">
      <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
      <th scope="row"><a href="<?php echo $linkdDelete;?>" class="delete-data"><i class="ti ti-trash"></i></a></th>
      <th scope="row"><a href="<?php echo $linkDownload;?>"><i class="ti ti-download"></i></a></th>
      <th scope="row"><?php echo $no;?></th>
      <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-name"><?php echo $album->getName();?></a></td>
      <td class="text-data text-data-duration"><?php echo sprintf("%.3f", $album->getDuration());?></td>
      <td class="text-data text-data-number-of-song"><?php echo $album->getNumberOfSong();?></td>
      <td class="text-data text-data-producer"><?php echo $album->hasValueProducer() ? $album->getProducer()->getName() : "";?></td>
      <td class="text-data text-data-sort-order"><?php echo $album->getSortOrder();?></td>
      <td class="text-data text-data-active"><?php echo $album->isActive() ? 'Yes' : 'No';?></td>
      <td class="text-data text-data-ad-draft"><?php echo $album->isAsDraft() ? 'Yes' : 'No';?></td>
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

<div class="lazy-dom modal-container modal-add-data" data-url="lib.ajax/album-add-dialog.php"></div>
<div class="lazy-dom modal-container modal-update-data" data-url="lib.ajax/album-update-dialog.php"></div>

<script>
  let addAlbumModal;
  let updateAlbumModal;
  
  $(document).ready(function(e){
    
    $(document).on('click', '.add-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let dialogSelector = $('.modal-add-data');
      dialogSelector.load(dialogSelector.attr('data-url'), function(data){
        let addAlbumModalElem = document.querySelector('#addAlbumDialog');
        addAlbumModal = new bootstrap.Modal(addAlbumModalElem, {
          keyboard: false
        });
        addAlbumModal.show();
      })
    });
    
    $(document).on('click', '.edit-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let albumId = $(this).closest('tr').attr('data-id') || '';
      let dialogSelector = $('.modal-update-data');
      dialogSelector.load(dialogSelector.attr('data-url')+'?album_id='+albumId, function(data){
        let updateAlbumModalElem = document.querySelector('#updateAlbumDialog');
        updateAlbumModal = new bootstrap.Modal(updateAlbumModalElem, {
          keyboard: false
        });
        updateAlbumModal.show();
        downloadForm('.lazy-dom-container', function(){
          if(!allDownloaded)
          {
              initModal2();
              console.log('loaded')
              allDownloaded = true;
          }
          loadForm();
      });
      });
    });
    
    $(document).on('click', '.save-add-album', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/album-add.php',
        data:dataSet, 
        dataType:'html',
        success: function(data)
        {
          addAlbumModal.hide();
          window.location.reload();
        }
      })
    });

    $(document).on('click', '.save-edit-album', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/album-update.php',
        data:dataSet, 
        dataType:'json',
        success: function(data)
        {
          updateAlbumModal.hide();
          let formData = getFormData(dataSet);
          let dataId = data.album_id;
          let name = data.name;
          let duration = data.duration.toFixed(3);
          let numberOfSong = data.number_of_song;
          let active = data.active == 1 || data.active == '1';
          let draft = data.as_draft == 1 || data.as_draft == '1';
          $('[data-id="'+dataId+'"] .text-data.text-data-name').text(name);
          $('[data-id="'+dataId+'"] .text-data.text-data-sort-order').text(data.sort_order);
          $('[data-id="'+dataId+'"] .text-data.text-data-duration').text(duration);
          $('[data-id="'+dataId+'"] .text-data.text-data-number-of-song').text(numberOfSong);
          $('[data-id="'+dataId+'"] .text-data.text-data-active').text(active?'Yes':'No');
          $('[data-id="'+dataId+'"] .text-data.text-data-as-draft').text(draft?'Yes':'No');
        }
      })
    });
  });
  
  
  
</script>

<?php
}

require_once "inc/footer.php";
?>