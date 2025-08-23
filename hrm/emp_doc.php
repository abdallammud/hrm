<?php

if(!isset($folder_id)) {
    $folder_id = isset($_GET['id']) ? $_GET['id'] : '';
}

if(!isset($emp_id)) {
    $emp_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : '';
}

$emp_id = trim($emp_id);
$folder_id = trim($folder_id);

?>

<!-- Add Document Modal -->
<div class="modal fade" id="add_document" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentModalLabel">Add New Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEmpDocumentForm">
                    <input type="hidden" id="folder_id" value="<?php echo $folder_id; ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="docName">Document Name</label>
                                <input type="text" class="form-control validate" data-msg="Document name is required"  id="docName" name="docName" placeholder="Enter document name">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="label required" for="employee">Employee</label>
                                <select class="my-select searchEmployee" name="searchEmployee" id="searchEmployee" data-live-search="true" title="Search and select employee">
                                    <?php 
                                    $query = "SELECT * FROM `employees` WHERE `status` = 'active' ";
                                    if($emp_id) {
                                        $query .= " AND `employee_id` = '$emp_id'";
                                    }
                                    $query .= " ORDER BY `full_name` ASC LIMIT 10";
                                    $empSet = $GLOBALS['conn']->query($query);
                                    if($empSet->num_rows > 0) {
                                        while($row = $empSet->fetch_assoc()) {
                                            $employee_id = $row['employee_id'];
                                            $full_name = $row['full_name'];
                                            $phone_number = $row['phone_number'];

                                            $text = $full_name;
                                            if($phone_number) {
                                                $text .= ', '.$phone_number;
                                            }

                                            // echo $emp_id;

                                            echo '<option value="'.$employee_id.'" '.($emp_id == $employee_id ? 'selected' : '').'>'.$text.'</option>';
                                        }
                                    } 
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group mb-3">
                                <label for="docType">Document Type</label>
                                <select class="form-control validate" data-msg="Document type is required" id="docType" name="docType">
                                    <option value="">Select Document Type</option>
                                    <?php 
                                    select_active('document_types');
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="docFolder">Document Folder</label>
                                <select class="form-control validate" data-msg="Document folder is required" id="docFolder" name="docFolder">
                                    <option value="">Select Document Folder</option>
                                    <?php
                                    if(isset($_GET['id'])) {
                                        select_active('folders', array('value' => 'id', 'text' => 'name'), $current = $_GET['id']);
                                    } else {
                                        select_active('folders', array('value' => 'id', 'text' => 'name'));
                                    }
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="expirationDate">Expiration Date</label>
                                <input type="text" class="form-control cursor datepicker" readonly="" id="expirationDate" value="<?=date('Y-m-d');?>" name="attendDate" aria-label="Use the arrow keys to pick a date">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="docFile">Document File</label>
                                <input type="file" class="form-control" id="docFile" name="docFile">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 sflex sjend">
                        <button type="submit" class="btn btn-primary">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<style type="text/css">
    .dropdown.bootstrap-select.my-select {
        display: block;
        width: 100% !important;
    }
</style>