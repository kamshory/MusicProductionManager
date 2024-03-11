<?php

use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Request\InputPost;
use MusicProductionManager\Constants\ParamConstant;
use MusicProductionManager\Data\Entity\User;


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
        $_SESSION['spass'] = $hashPassword;
    }
    $user->setName($inputPost->getName());
    $user->setBirthDay($inputPost->getBirthDay());
    $user->setGender($inputPost->getGender());

    $username = $inputPost->getUsername();
    if(!empty($username) && !UserUtil::isDuplicatedUsername($database, $userId, $username))
    {
        $user->setUsername($username);
        $_SESSION['suser'] = $username;
    }

    $email = $inputPost->getEmail();
    if(!empty($email) && !UserUtil::isDuplicatedEmail($database, $userId, $email))
    {
        $user->setEmail($email);
    }

    $user->setTimeEdit(date('Y-m-d H:i:s'));
    $user->setAdminEdit($userId);
    $user->setIpEdit($_SERVER['REMOTE_ADDR']);

    $user->update();
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
      // do something here
    }
    require_once "inc/footer.php";
}
else
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
      // do something here
    }
    require_once "inc/footer.php";
}