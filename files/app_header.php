<?php 
require('./app/init.php');
if (!authenticate()) {
    header("Location: ".baseUri()."/login ");
    exit; // Important to exit to prevent further execution
}
?>
<!doctype html>
<html lang="en" data-bs-theme="">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payroll | Payroll management system</title>
  <!--favicon-->
  <link rel="icon" href="<?=baseUri();?>/assets/images/favicon-32x32.png" type="image/png">
  <!-- loader-->
  <link href="<?=baseUri();?>/assets/css/font-awesome/css/all.min.css" rel="stylesheet">
	<link href="<?=baseUri();?>/assets/css/pace.min.css" rel="stylesheet">
	<script src="<?=baseUri();?>/assets/js/pace.min.js"></script>
  <link href="<?=baseUri();?>/assets/css/utilities.css" rel="stylesheet">
  <!--plugins-->
  <link href="<?=baseUri();?>/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="<?=baseUri();?>/assets/plugins/metismenu/metisMenu.min.css">
  <link rel="stylesheet" type="text/css" href="<?=baseUri();?>/assets/plugins/metismenu/mm-vertical.css">
  <link rel="stylesheet" type="text/css" href="<?=baseUri();?>/assets/plugins/simplebar/css/simplebar.css">
  <link rel="stylesheet" type="text/css" href="<?=baseUri();?>/assets/plugins/pikaday/css/pikaday.css">
  <!--bootstrap css-->
  <link href="<?=baseUri();?>/assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <link href="<?=baseUri();?>/assets/css/sumo_select.css" rel="stylesheet">
  <!--main css-->
  <link href="<?=baseUri();?>/assets/css/bootstrap-extended.css" rel="stylesheet">
  <link href="<?=baseUri();?>/assets/sass/main.css" rel="stylesheet">
  <link href="<?=baseUri();?>/assets/sass/dark-theme.css" rel="stylesheet">
  <link href="<?=baseUri();?>/assets/sass/blue-theme.css" rel="stylesheet">
  <link href="<?=baseUri();?>/assets/sass/semi-dark.css" rel="stylesheet">
  <link href="<?=baseUri();?>/assets/sass/bordered-theme.css" rel="stylesheet">
  <link href="<?=baseUri();?>/assets/sass/responsive.css" rel="stylesheet">
  <link href="<?=baseUri();?>/assets/css/styles.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css">
  <script type="text/javascript">
    let settings = JSON.parse(localStorage.getItem('settings'));
    // console.log(settings)
    if (settings) {
        document.documentElement.removeAttribute('data-bs-theme');
        document.documentElement.setAttribute('data-bs-theme', settings.theme);
    }
  </script>
</head>



<body>