<?php

use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoSortable;
use MagicObject\Exceptions\NoRecordFoundException;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MusicProductionManager\Constants\ParamConstant;
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Utility\SpecificationUtil;

require_once "inc/auth-with-login-form.php";
require_once "inc/header.php";

$inputGet = new InputGet();
if($inputGet->equalsAction(ParamConstant::ACTION_DETAIL) && $inputGet->getArtistId() != null)
{
  $artist = new Artist(null, $database);
  try
  {
  $artist->findOneByArtistId($inputGet->getArtistId());
  ?>
  <table class="table table-responsive table-responsive-two-side table-borderless ">
    <tbody>
      <tr>
        <td>Artist ID</td>
        <td><?php echo $artist->getArtistId();?></td>
      </tr>
      <tr>
        <td>Name</td>
        <td><?php echo $artist->getName();?></td>
      </tr>
      <tr>
        <td>Stage Name</td>
        <td><?php echo $artist->getStageName();?></td>
      </tr>
      <tr>
          <td>Active</td>
          <td><?php echo $artist->booleanToTextByActive('Yes', 'No');?></td>
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
  'stageName'=>'stageName',
  'artistId'=>'artistId', 
  'artist'=>'artistId',
  'gender'=>'gender',
  'active'=>'active'
);
$defaultOrderBy = 'name';
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createArtistSpecification($inputGet);
$sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType());
$pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);

$artistEntity = new Artist(null, $database);
$rowData = $artistEntity->findAll($spesification, $pagable, $sortable, true);

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
<table class="table">
  <thead>
    <tr>
      <th scope="col" width="20"><i class="ti ti-edit"></i></th>
      <th scope="col" width="20"><i class="ti ti-trash"></i></th>
      <th scope="col" width="20">#</th>
      <th scope="col" class="col-sort" data-name="name">Real Name</th>
      <th scope="col" class="col-sort" data-name="stage_name">Stage Name</th>
      <th scope="col" class="col-sort" data-name="gender">Gender</th>
      <th scope="col" class="col-sort" data-name="active">Active</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = $pagination->getOffset();
    foreach($result as $artist)
    {
      $no++;
      $artistId = $artist->getArtistId();
      $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&artist_id=".$artistId;
      $linkdDelete = basename($_SERVER['PHP_SELF'])."?action=delete&artist_id=".$artistId;
      $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&artist_id=".$artistId;
    ?>
    <tr data-id="<?php echo $artistId;?>">
      <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
      <th scope="row"><a href="<?php echo $linkdDelete;?>" class="delete-data"><i class="ti ti-trash"></i></a></th>
      <th scope="row"><?php echo $no;?></th>
      <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-name"><?php echo $artist->getName();?></a></td>
      <td class="text-data text-data-stage_name"><?php echo $artist->getStageName();?></td>
      <td class="text-data text-data-gender"><?php echo $artist->getGender() == 'M' ? 'Man' : 'Woman';?></td>
      <td class="text-data text-data-active"><?php echo $artist->isActive() ? 'Yes' : 'No';?></td>
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
<div class="lazy-dom modal-container modal-add-data" data-url="lib.ajax/artist-add-dialog.php"></div>
<div class="lazy-dom modal-container modal-update-data" data-url="lib.ajax/artist-update-dialog.php"></div>

<script>
  let addArtistModal;
  let updateArtistModal;
  $(document).ready(function(e){

    $(document).on('click', '.add-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let dialogSelector = $('.modal-add-data');
      dialogSelector.load(dialogSelector.attr('data-url'), function(data){
        let addArtistModalElem = document.querySelector('#addArtistDialog');
        addArtistModal = new bootstrap.Modal(addArtistModalElem, {
          keyboard: false
        });
        addArtistModal.show();
      })
    });

    $(document).on('click', '.edit-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let artistId = $(this).closest('tr').attr('data-id') || '';
      let dialogSelector = $('.modal-update-data');
      dialogSelector.load(dialogSelector.attr('data-url')+'?artist_id='+artistId, function(data){
        let updateArtistModalElem = document.querySelector('#updateArtistDialog');
        updateArtistModal = new bootstrap.Modal(updateArtistModalElem, {
          keyboard: false
        });
        updateArtistModal.show();
      })
    });

    

    $(document).on('click', '.save-add-artist', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/artist-add.php',
        data:dataSet, 
        dataType:'html',
        success: function(data)
        {
          addArtistModal.hide();
          window.location.reload();
        }
      })
    });

    $(document).on('click', '.save-edit-artist', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/artist-update.php',
        data:dataSet, 
        dataType:'html',
        success: function(data)
        {
          updateArtistModal.hide();
          let formData = getFormData(dataSet);
          let dataId = formData.artist_id;
          let name = formData.name;
          let stage_name = formData.stage_name;
          let gender = formData.gender;
          let active = $('[name="active"]')[0].checked;
          $('[data-id="'+dataId+'"] .text-data.text-data-name').text(name);
          $('[data-id="'+dataId+'"] .text-data.text-data-stage_name').text(stage_name);
          $('[data-id="'+dataId+'"] .text-data.text-data-gender').text(gender=='M'?'Man':'Woman');
          $('[data-id="'+dataId+'"] .text-data.text-data-active').text(active?'Yes':'No');
        }
      })
    });
  });
</script>

<?php
}
require_once "inc/footer.php";
?>