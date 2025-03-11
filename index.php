<?php require('./files/app_header.php');?>
<?php require('./asset_config.php');?>
    <!--start header-->
    <?php require('./files/app_topbar.php');?>
    <!--end top header-->


    <!--start sidebar-->
    <?php require('./files/app_sidebar.php');?>
    <!--end sidebar-->

    <!--start main wrapper-->
    <main class="main-wrapper">
        <div class="main-content">
            <?php load_files(); ?>
            <!-- Main content loader function here -->
        </div>
    </main>
    <!--end main wrapper-->

<?php require('./files/app_footer.php');?>