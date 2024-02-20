<?php

use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoSortable;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\PicoRequest;
use MusicProductionManager\Data\Entity\UserType;
use MusicProductionManager\Utility\SpecificationUtil;

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
  'userTypeId'=>'userTypeId', 
  'userType'=>'userTypeId',
  'sortOrder'=>'sortOrder',
  'admin'=>'admin',
  'active'=>'active'
);
$defaultOrderBy = 'sortOrder';
$defaultOrderType = 'asc';
$pagination = new PicoPagination($cfg->getResultPerPage());
$spesification = SpecificationUtil::createUserTypeSpecification($inputGet);
$sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType($defaultOrderType));
$pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);

$userTypeEntity = new UserType(null, $database);
$rowData = $userTypeEntity->findAll($spesification, $pagable, $sortable, true);

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
      <th scope="col" class="col-sort" data-name="admin">Admin</th>
      <th scope="col" class="col-sort" data-name="sort_order">Order</th>
      <th scope="col" class="col-sort" data-name="active">Active</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = $pagination->getOffset();
    foreach($result as $userType)
    {
      $no++;
      $userTypeId = $userType->getUserTypeId();
      $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&user_type_id=".$userTypeId;
      $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&user_type_id=".$userTypeId;
      $linkDelete = basename($_SERVER['PHP_SELF'])."?action=delete&user_type_id=".$userTypeId;
    ?>
    <tr data-id="<?php echo $userTypeId;?>">
      <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
      <th scope="row"><a href="<?php echo $linkDelete;?>" class="delete-data"><i class="ti ti-trash"></i></a></th>
      <th scope="row"><?php echo $no;?></th>
      <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-name"><?php echo $userType->getName();?></a></td>
      <td class="text-data text-data-admin"><?php echo $userType->isAdmin() ? 'Yes' : 'No';?></td>
      <td class="text-data text-data-sort-order"><?php echo $userType->getSortOrder();?></td>
      <td class="text-data text-data-active"><?php echo $userType->isActive() ? 'Yes' : 'No';?></td>
    </tr>
    <?php
    }
    ?>
    
  </tbody>
</table>


<?php
}
?>
<div class="lazy-dom modal-container modal-add-data" data-url="lib.ajax/user-type-add-dialog.php"></div>
<div class="lazy-dom modal-container modal-update-data" data-url="lib.ajax/user-type-update-dialog.php"></div>
<script>
  let addUserTypeModal;
  let updateUserTypeModal;
  $(document).ready(function(e){
    $(document).on('click', '.add-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let dialogSelector = $('.modal-add-data');
      dialogSelector.load(dialogSelector.attr('data-url'), function(data){
        let addUserTypeModalElem = document.querySelector('#addUserTypeDialog');
        addUserTypeModal = new bootstrap.Modal(addUserTypeModalElem, {
          keyboard: false
        });
        addUserTypeModal.show();
      })
    });

    $(document).on('click', '.edit-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let userTypeId = $(this).closest('tr').attr('data-id') || '';
      let dialogSelector = $('.modal-update-data');
      dialogSelector.load(dialogSelector.attr('data-url')+'?user_type_id='+userTypeId, function(data){
        let updateUserTypeModalElem = document.querySelector('#updateUserTypeDialog');
        updateUserTypeModal = new bootstrap.Modal(updateUserTypeModalElem, {
          keyboard: false
        });
        updateUserTypeModal.show();
      })
    });

    $(document).on('click', '.save-add-user-type', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/user-type-add.php',
        data:dataSet, 
        dataType:'html',
        success: function(data)
        {
          addUserTypeModal.hide();
          window.location.reload();
        }
      })
    });

    $(document).on('click', '.save-edit-user-type', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/user-type-update.php',
        data:dataSet, 
        dataType:'html',
        success: function(data)
        {
          updateUserTypeModal.hide();
          let formData = getFormData(dataSet);
          let dataId = formData.user_type_id;
          let sortOrder = formData.sort_order;
          let name = formData.name;
          let active = $('[name="active"]')[0].checked;
          let admin = $('[name="admin"]')[0].checked;
          $('[data-id="'+dataId+'"] .text-data.text-data-name').text(name);
          $('[data-id="'+dataId+'"] .text-data.text-data-sort-order').text(sortOrder);
          $('[data-id="'+dataId+'"] .text-data.text-data-active').text(active?'Yes':'No');
          $('[data-id="'+dataId+'"] .text-data.text-data-admin').text(admin?'Yes':'No');
        }
      })
    });
  });
</script>

<?php
require_once "inc/footer.php";
?>