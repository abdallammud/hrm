<?php 
require('./app/auth.php');
require('./app/utilities.php');
if (authenticate()) {
    header("Location: ".baseUri()."/".get_landingMenu($_SESSION['user_id'])." ");
} 
?>
<!doctype html>
<html lang="en" data-bs-theme="">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payroll | Payroll management system</title>
  <!--favicon-->
  <link rel="icon" href="./assets/images/favicon-32x32.png" type="image/png">
  <!-- loader-->
  <link href="./assets/css/font-awesome/css/all.min.css" rel="stylesheet">
	<link href="./assets/css/pace.min.css" rel="stylesheet">
	<script src="./assets/js/pace.min.js"></script>
  <link href="./assets/css/utilities.css" rel="stylesheet">
  <!--plugins-->
  <link href="./assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="./assets/plugins/metismenu/metisMenu.min.css">
  <link rel="stylesheet" type="text/css" href="./assets/plugins/metismenu/mm-vertical.css">
  <link rel="stylesheet" type="text/css" href="./assets/plugins/simplebar/css/simplebar.css">
  <link rel="stylesheet" type="text/css" href="./assets/plugins/pikaday/css/pikaday.css">
  <!--bootstrap css-->
  <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <!--main css-->
  <link href="./assets/css/bootstrap-extended.css" rel="stylesheet">
  <link href="./assets/sass/main.css" rel="stylesheet">
  <link href="./assets/sass/dark-theme.css" rel="stylesheet">
  <link href="./assets/sass/blue-theme.css" rel="stylesheet">
  <link href="./assets/sass/semi-dark.css" rel="stylesheet">
  <link href="./assets/sass/bordered-theme.css" rel="stylesheet">
  <link href="./assets/sass/responsive.css" rel="stylesheet">
  <link href="./assets/css/styles.css" rel="stylesheet">

</head>

<body>