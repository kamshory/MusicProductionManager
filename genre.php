<?php
use Pico\Data\Entity\Genre;
use Pico\Database\PicoPagable;
use Pico\Database\PicoPage;
use Pico\Database\PicoSortable;
use Pico\Pagination\PicoPagination;
use Pico\Request\PicoFilterConstant;
use Pico\Request\PicoRequest;
use Pico\Utility\SpecificationUtil;

require_once "inc/auth-with-login-form.php";
require_once "inc/header.php";

$inputGet = new PicoRequest(INPUT_GET);


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
  'genreId'=>'genreId', 
  'genre'=>'genreId',
  'sortOrder'=>'sortOrder',
  'active'=>'active'
);
$defaultOrderBy = 'sortOrder';
$defaultOrderType = 'asc';
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createGenreSpecification($inputGet);
$sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType($defaultOrderType));
$pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);

$genreEntity = new Genre(null, $database);
$rowData = $genreEntity->findAll($spesification, $pagable, $sortable, true);

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
      <th scope="col" width="20">#</th>
      <th scope="col" class="col-sort" data-name="name">Name</th>
      <th scope="col" class="col-sort" data-name="sort_order">Order</th>
      <th scope="col" class="col-sort" data-name="active">Active</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = $pagination->getOffset();
    foreach($result as $genre)
    {
      $no++;
      $genreId = $genre->getGenreId();
      $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&genre_id=".$genreId;
      $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&genre_id=".$genreId;
      $linkDelete = basename($_SERVER['PHP_SELF'])."?action=delete&genre_id=".$genreId;
    ?>
    <tr data-id="<?php echo $genreId;?>">
      <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
      <th scope="row"><a href="<?php echo $linkDelete;?>" class="delete-data"><i class="ti ti-trash"></i></a></th>
      <th scope="row"><?php echo $no;?></th>
      <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-name"><?php echo $genre->getName();?></a></td>
      <td class="text-data text-data-sort-order"><?php echo $genre->getSortOrder();?></td>
      <td class="text-data text-data-active"><?php echo $genre->isActive() ? 'Yes' : 'No';?></td>
    </tr>
    <?php
    }
    ?>
    
  </tbody>
</table>
<?php
}
?>

<div class="lazy-dom modal-container modal-add-data" data-url="lib.ajax/genre-add-dialog.php"></div>
<div class="lazy-dom modal-container modal-update-data" data-url="lib.ajax/genre-update-dialog.php"></div>

<script>
  let addGenreModal;
  let updateGenreModal;
  $(document).ready(function(e){
    $(document).on('click', '.add-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let dialogSelector = $('.modal-add-data');
      dialogSelector.load(dialogSelector.attr('data-url'), function(data){
        let addGenreModalElem = document.querySelector('#addGenreDialog');
        addGenreModal = new bootstrap.Modal(addGenreModalElem, {
          keyboard: false
        });
        addGenreModal.show();
      })
    });

    $(document).on('click', '.edit-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let genreId = $(this).closest('tr').attr('data-id') || '';
      let dialogSelector = $('.modal-update-data');
      dialogSelector.load(dialogSelector.attr('data-url')+'?genre_id='+genreId, function(data){
        let updateGenreModalElem = document.querySelector('#updateGenreDialog');
        updateGenreModal = new bootstrap.Modal(updateGenreModalElem, {
          keyboard: false
        });
        updateGenreModal.show();
      })
    });

    $(document).on('click', '.save-add-genre', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/genre-add.php',
        data:dataSet, 
        dataType:'html',
        success: function(data)
        {
          addGenreModal.hide();
          window.location.reload();
        }
      })
    });

    $(document).on('click', '.save-edit-genre', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/genre-update.php',
        data:dataSet, 
        dataType:'html',
        success: function(data)
        {
          updateGenreModal.hide();
          let formData = getFormData(dataSet);
          let dataId = formData.genre_id;
          let sortOrder = formData.sort_order;
          let name = formData.name;
          let active = $('[name="active"]')[0].checked;
          $('[data-id="'+dataId+'"] .text-data.text-data-name').text(name);
          $('[data-id="'+dataId+'"] .text-data.text-data-sort-order').text(sortOrder);
          $('[data-id="'+dataId+'"] .text-data.text-data-active').text(active?'Yes':'No');
        }
      })
    });
  });
</script>

<?php
require_once "inc/footer.php";
?>