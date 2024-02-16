<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
  <title>Music Creator</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/favicon.png" />
  
  <link rel="stylesheet" href="assets/css/styles.min.css" />
  <link rel="stylesheet" href="lib/style.css">
  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/sidebarmenu.js"></script>
  <script src="assets/js/app.min.js"></script>
  <script src="lib/pagination.js"></script>
  
  <script>
    $(document).ready(function(e){
      
      
      const url = new URL(document.location.toString());
      const searchParams = url.searchParams;
      let params = {};
      searchParams.forEach((value, key) => {
        params[key] = value;
      });

      let orderby = searchParams.get('orderby');
      let ordertype = searchParams.get('ordertype'); 
      
      $('table thead .col-sort').each(function(e1){
        let th = $(this);
        let text = th.text().trim();
        let col = th.attr('data-name');
        let linkUrl = createSortUrl(url, col, params);
        let a = $('<a />');
        a.text(text);
        a.attr('href', linkUrl);
        th.empty().append(a);
      });
    });
    
    function createSortUrl(url, col, params1)
    {
      let params = JSON.parse(JSON.stringify(params1));
      let path = url.pathname; // '/search'
      
      
      if(typeof params['orderby'] != 'undefined' && col == params['orderby'])
      {
        params['orderby'] = col;
        if(params['ordertype'] == 'asc')
        {
          params['ordertype'] = 'desc';
        }
        else
        {
          params['ordertype'] = 'asc';
        }
      }
      else
      {
        params['orderby'] = col;
        params['ordertype'] = 'asc';
      }
      let p = [];
      for (const key in params) {
        if (params.hasOwnProperty(key)) {   
          p.push(encodeURIComponent(key)+'='+encodeURIComponent(params[key]));
        }
      }
      return path + '?'+p.join('&');
    }
  </script>
  
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <?php require_once "inc/sidebar.php";?>
    
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <?php
        require_once "header-inner.php";
      ?>
      <div class="main-page">