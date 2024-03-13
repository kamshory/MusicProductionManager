<?php

use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoSortable;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MagicObject\Request\PicoRequest;
use MusicProductionManager\Constants\ParamConstant;
use MusicProductionManager\Data\Entity\EntityUser;
use MusicProductionManager\Data\Entity\User;
use MusicProductionManager\Utility\ServerUtil;
use MusicProductionManager\Utility\SpecificationUtil;
use MusicProductionManager\Utility\UserUtil;

require_once "inc/auth-with-login-form.php";

$inputGet = new InputGet();
$inputPost = new InputPost();
if($inputGet->equalsAction(ParamConstant::ACTION_EDIT) && $inputPost->getSave() == 'save')
{
    $inputPost->filterName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
    $inputPost->filterUsername(PicoFilterConstant::FILTER_SANITIZE_ALPHANUMERICPUNC);
    $inputPost->filterEmail(PicoFilterConstant::FILTER_SANITIZE_EMAIL);

    $userId = $currentLoggedInUser->getUserId();
    $user = new User(null, $database);
    $user->setUserId($userId);
    $password = $inputPost->getPassword();
    if(!empty($password))
    {
        $hashPassword = hash('sha256', $inputPost->getPassword());
        $user->setPassword($hashPassword);
    }
    $user->setName($inputPost->getName());
    $user->setBirthDay($inputPost->getBirthDay());
    $user->setGender($inputPost->getGender());

    $username = $inputPost->getUsername();
    if(!empty($username) && !UserUtil::isDuplicatedUsername($database, $userId, $username))
    {
        $user->setUsername($username);
    }

    $email = $inputPost->getEmail();
    if(!empty($email) && !UserUtil::isDuplicatedEmail($database, $userId, $email))
    {
        $user->setEmail($email);
    }

    $user->setTimeEdit(date('Y-m-d H:i:s'));
    $user->setAdminEdit($userId);
    $user->setIpEdit(ServerUtil::getRemoteAddress());

    $user->update();

    if(!isset($inputGet))
    {
      $inputGet = new InputGet();
    }
    UserUtil::logUserActivity($database, $currentLoggedInUser->getUserId(), "Update user ".$user->getUserId(), $inputGet, $inputPost);

    header('Location: '.basename(($_SERVER['PHP_SELF'])));
}

if($inputGet->equalsAction(ParamConstant::ACTION_EDIT))
{
    require_once "inc/header.php";
    $user = new User(null, $database);
    try
    {
    $user->findOneByUserId($currentLoggedInUser->getUserId());
    ?>
    <form action="" method="post">
    <table class="table table-responsive">
    <tbody>
      <tr>
        <td>Name</td>
        <td><input type="text" class="form-control" name="name" id="name" value="<?php echo $user->getName();?>" autocomplete="off"></td>
      </tr>
      <tr>
        <td>Gender</td>
        <td><select class="form-control" name="gender" id="gender">
        <option value="M"<?php echo $user->createSelectedGender('M');?>>Man</option>
        <option value="W"<?php echo $user->createSelectedGender('W');?>>Woman</option>
        </select></td>
      </tr>
      <tr>
        <td>Birth Day</td>
        <td><input type="date" class="form-control" name="birth_day" id="birth_day" value="<?php echo $user->getBirthDay();?>" autocomplete="off"></td>
      </tr>
      <tr>
        <td>Email</td>
        <td><input type="email" class="form-control" name="email" id="email" value="<?php echo $user->getEmail();?>" autocomplete="off"></td>
      </tr>
      <tr>
        <td>Username</td>
        <td><input type="text" class="form-control" name="username" id="username" value="<?php echo $user->getUsername();?>" autocomplete="off"></td>
      </tr>
      <tr>
        <td>Password</td>
        <td><input type="password" class="form-control" name="password" id="password" value="" autocomplete="off"></td>
      </tr>
    </tbody>
  </table>
  <input type="hidden" name="save" value="save">
  <button type="submit" class="btn btn-success">Update</button>
  <button type="button" class="btn btn-primary" onclick="window.location='profile.php'">Cancel</button>
    </form>
    <?php
    }
    catch(Exception $e)
    {
      // do nothing
    }
    require_once "inc/footer.php";
}
else if($inputGet->equalsAction(ParamConstant::ACTION_DETAIL))
{
    require_once "inc/header.php";
    $user = new User(null, $database);
    try
    {
    $user->findOneByUserId($currentLoggedInUser->getUserId());
    ?>
    <table class="table table-responsive">
    <tbody>
    <tr>
        <td>User ID</td>
        <td><?php echo $user->getUserId();?></td>
      </tr>
      <tr>
        <td>Username</td>
        <td><?php echo $user->getUsername();?></td>
      </tr>
      <tr>
        <td>Name</td>
        <td><?php echo $user->getName();?></td>
      </tr>
      <tr>
        <td>Gender</td>
        <td><?php echo $user->getGender() == 'M' ? 'Man' : 'Woman';?></td>
      </tr>
      <tr>
        <td>Birth Day</td>
        <td><?php echo $user->getBirthDay();?></td>
      </tr>
      <tr>
        <td>Email</td>
        <td><?php echo $user->getEmail();?></td>
      </tr>
    </tbody>
  </table>
  <button type="button" class="btn btn-primary" onclick="window.location='profile.php?action=edit'">Edit</button>
    <?php
    }
    catch(Exception $e)
    {
        // do nothing
    }
    require_once "inc/footer.php";
}
else
{
    require_once "inc/header.php";
  ?>
  <div class="filter-container">
  <form action="" method="get">
  <div class="filter-group">
      <span>Name</span>
      <input class="form-control" type="text" name="name" id="name" autocomplete="off" value="<?php echo $inputGet->getName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
  </div>
  
  <div class="filter-group">
      <span>Username</span>
      <input class="form-control" type="text" name="username" id="username" autocomplete="off" value="<?php echo $inputGet->getUsername(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);?>">
  </div>
  
  <div class="filter-group">
      <span>Email</span>
      <input class="form-control" type="email" name="email" id="email" autocomplete="off" value="<?php echo $inputGet->getEmail(PicoFilterConstant::FILTER_SANITIZE_EMAIL);?>">
  </div>
  
  <input class="btn btn-success" type="submit" value="Show">
  <input class="btn btn-primary add-data" type="button" value="Add">
  
  </form>
</div>
<?php
$orderMap = array(
  'name'=>'name', 
  'userId'=>'userId', 
  'user'=>'userId',
  'username'=>'username',
  'email'=>'email'
);
$defaultOrderBy = 'name';
$pagination = new PicoPagination($cfg->getResultPerPage());

$spesification = SpecificationUtil::createUserSpecification($inputGet);
$sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType());
$pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);

$userEntity = new EntityUser(null, $database);
$rowData = $userEntity->findAll($spesification, $pagable, $sortable, true);

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
      <th scope="col" width="20">#</th>
      <th scope="col">Username</th>
      <th scope="col">Name</th>
      <th scope="col">Admin</th>
      <th scope="col">Artist</th>
      <th scope="col">Gender</th>
      <th scope="col">Active</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = $pagination->getOffset();
    foreach($result as $user)
    {
      $no++;
      $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&user_id=".$user->getUserId();
      $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&user_id=".$user->getUserId();
    ?>
    <tr data-id="<?php echo $user->getUserId();?>">
      <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
      <th scope="row"><?php echo $no;?></th>
      <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-username"><?php echo $user->getUsername();?></a></td>
      <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-name"><?php echo $user->getName();?></a></td>
      <td class="text-data text-data-admin"><?php echo $user->getAdmin() ? 'Yes' : 'No';?></td>
      <td class="text-data text-data-associated-artist"><?php 
      if($user->hasValueArtist())
      {
        echo $user->getArtist()->getName();
      }
      ?></td>
      <td class="text-data text-data-gender"><?php echo $user->getGender() == 'M' ? 'Man' : 'Woman';?></td>
      <td class="text-data text-data-active"><?php echo $user->getActive() ? 'Yes' : 'No';?></td>
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
<div class="lazy-dom modal-container modal-add-data" data-url="lib.ajax/user-add-dialog.php"></div>
<div class="lazy-dom modal-container modal-update-data" data-url="lib.ajax/user-update-dialog.php"></div>

<script>
  let addUserModal;
  let updateUserModal;
  $(document).ready(function(e){

    $(document).on('click', '.add-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let dialogSelector = $('.modal-add-data');
      dialogSelector.load(dialogSelector.attr('data-url'), function(data){
        let addUserModalElem = document.querySelector('#addUserDialog');
        addUserModal = new bootstrap.Modal(addUserModalElem, {
          keyboard: false
        });
        addUserModal.show();
      })
    });

    $(document).on('click', '.edit-data', function(e2){
      e2.preventDefault();
      e2.stopPropagation();
      let userId = $(this).closest('tr').attr('data-id') || '';
      let dialogSelector = $('.modal-update-data');
      dialogSelector.load(dialogSelector.attr('data-url')+'?user_id='+userId, function(data){
        let updateUserModalElem = document.querySelector('#updateUserDialog');
        updateUserModal = new bootstrap.Modal(updateUserModalElem, {
          keyboard: false
        });
        updateUserModal.show();
      })
    });

    

    $(document).on('click', '.save-add-user', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/user-add.php',
        data:dataSet, 
        dataType:'html',
        success: function(data)
        {
          addUserModal.hide();
          window.location.reload();
        }
      })
    });

    $(document).on('click', '.save-update-user', function(){
      let dataSet = $(this).closest('form').serializeArray();
      $.ajax({
        type:'POST',
        url:'lib.ajax/user-update.php',
        data:dataSet, 
        dataType:'html',
        success: function(data)
        {
          updateUserModal.hide();
          let formData = getFormData(dataSet);
          let dataId = formData.user_id;
          let name = formData.name;
          let username = formData.username;
          let gender = formData.gender;
          let active = $('[name="active"]')[0].checked;
          let admin = $('[name="admin"]')[0].checked;
          let artist = $('[name="associated_artist"] option:selected').text();
          $('[data-id="'+dataId+'"] .text-data.text-data-name').text(name);
          $('[data-id="'+dataId+'"] .text-data.text-data-username').text(username);
          $('[data-id="'+dataId+'"] .text-data.text-data-gender').text(gender=='M'?'Man':'Woman');
          $('[data-id="'+dataId+'"] .text-data.text-data-admin').text(admin?'Yes':'No');
          $('[data-id="'+dataId+'"] .text-data.text-data-associated-artist').text(artist);
          $('[data-id="'+dataId+'"] .text-data.text-data-active').text(active?'Yes':'No');
        }
      })
    });
  });
</script>

<?php
require_once "inc/footer.php";
}

?>