<?php

require_once "inc/auth-with-login-form.php";

require_once "inc/header.php";
?>
<header class="cd__intro">
   <h1> Summernote Editor Example </h1>
   <p> Summernote Editor </p>
</header>
<!--$%adsense%$-->
<main class="cd__main">
   <!-- Start DEMO HTML (Use the following code into your project)-->
   <div id="summernote"></div>
   <!-- END EDMO HTML (Happy Coding!)-->
</main>

<!-- Summernote CSS -->
<link href="assets/summernote/css/summernote-lite.min.css" rel="stylesheet">
<!-- Style CSS -->
<link rel="stylesheet" href="assets/summernote/css/style.css">
<!-- Summernote JS -->
<script src="assets/summernote/js/summernote-lite.min.js"></script>

<script>
   $(document).ready(function() {
      $('#summernote').summernote({
         placeholder: 'Type here',
         tabsize: 2,
         height: 400
      });
   })
</script>

<?php
require_once "inc/footer.php";
?>