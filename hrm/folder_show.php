<?php

if(!isset($folder_id)) {
    $folder_id = isset($_GET['id']) ? $_GET['id'] : '';
}

// Get folder details
$folder_name = "";
if($folder_id) {
    $folder_query = "SELECT name FROM folders WHERE id = '$folder_id'";
    $folder_result = $GLOBALS['conn']->query($folder_query);
    if($folder_result->num_rows > 0) {
        $folder_name = $folder_result->fetch_assoc()['name'];
    }
}
?>


<div class="row">
	<div class="page content">
        <div class="col-md-12">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?=baseUri();?>/documents">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Documents</a></li>
                    <li class="breadcrumb-item active"><?php echo $folder_name; ?></li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Documents</h4>
                        <small class="text-muted">Manage employee documents in <?php echo $folder_name; ?></small>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_document">
                        <i class="fa fa-plus"></i> Add Document
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <input type="hidden" id="folder_id" value="<?php echo $folder_id; ?>">
                        <table class="table table-bordered" id="empDocumentsDT" style="width:100%">
                           
                        </table>
                    </div>
                </div>            
            </div>
        </div>
    </div>
</div>










<?php 

require('emp_doc.php');
?>