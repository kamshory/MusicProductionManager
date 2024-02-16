<?php

use Pico\Data\Entity\User;
use Pico\Request\PicoRequest;

require_once "inc/app.php";
require_once "inc/session.php";

$inputPost = new PicoRequest(INPUT_POST);
if($inputPost->getUsername() != null && $inputPost->getPassword() != null)
{
  $username = $inputPost->getUsername();
  $password = $inputPost->getPassword();
  $username = trim($username);
  $password = trim($password);
  $password = hash('sha256', $password);
  $url = 'index.php';
  if(!empty($username) && !empty($password))
  {
    try
    {
      $currentLoggedInUser = new User(null, $database);
      $currentLoggedInUser->findOneByUsernameAndPasswordAndBlockedAndActive($username, $password, false, true);
      if($currentLoggedInUser->hasValueUserId())
      {
        $_SESSION['suser'] = $username;
        $_SESSION['spass'] = $password;
        if($inputPost->getReferer() != null)
        {
          $referer = trim($inputPost->getReferer());
          if(!empty($referer) && stripos($referer, 'login.php'))
          {
            $url = $referer;        
          }
        }
      }
      header("Location: ".$url);
      exit();
    }
    catch(Exception $e)
    {
      // do nothing
    }
  }
  if(!empty($referer) && stripos($referer, 'login.php'))
  {
    $url = $referer;   
  }
  header("Location: ".$url);
  exit();
}


?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modernize Free</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="index.html" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="assets/images/logos/dark-logo.svg" width="180" alt="">
                </a>
                <p class="text-center">Music Creator</p>
                <form action="login.php" method="post">
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Username</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" name="username" aria-describedby="emailHelp">
                  </div>
                  <div class="mb-4">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="password">
                  </div>
                  <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                      <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                      <label class="form-check-label text-dark" for="flexCheckChecked">
                        Remeber this Device
                      </label>
                    </div>
                    <a class="text-primary fw-bold" href="index.html">Forgot Password ?</a>
                  </div>
                  <input type="hidden" name="referer" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Sign In</button>
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">New to Modernize?</p>
                    <a class="text-primary fw-bold ms-2" href="authentication-register.php">Create an account</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>