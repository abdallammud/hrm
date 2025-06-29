<div class="page content header">
	<div class="page-breadcrumb d-sm-flex align-items-center sp-y-10">
        <h5 class="">Warning </h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-20">
                <a data-bs-toggle="modal" data-bs-target="#add_warning"  class="btn btn-primary sflex scenter-items"><span class="fa fa-plus"></span> Add </a>
            </div>
        </div>
    </div>
</div>

<div class="page content">
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table id="warningsDT" class="table table-striped table-bordered" style="width:100%">
					
				</table> 
			</div>
		</div>
	</div>
</div>







<!-- Add Warning Modal -->
<div class="modal fade" data-bs-focus="false" id="add_warning" tabindex="-1" role="dialog" aria-labelledby="add_warningLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_warningLabel">Add New Warning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addWarningForm" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="searchEmployee">Employee</label>
                                <select class="form-control my-select " data-live-search="true" data-msg="Employee is required" id="searchEmployee" name="employee_id">
                                    <option value="">Select Employee</option>
                                    <?php 
                                        $query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                                        $result = $GLOBALS['conn']->query($query);
                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                $employee_id = $row['employee_id'];
                                                $full_name = $row['full_name'];
                                                $phone_number = $row['phone_number'];
                                                $branch_id = $row['branch_id'];
                                                $branch_name = $row['branch'];

                                                echo '<option value="'.$employee_id.'" data-current-branch="'.$branch_id.'" data-current-branch-name="'.$branch_name.'">'.$full_name.', '.$phone_number.'</option>';
                                            }
                                        } 
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="add_warning_date">Warning Date</label>
                                <input type="date" class="form-control cursor datepicker" readonly id="add_warning_date" value="<?php echo date('Y-m-d'); ?>" name="warning_date">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="add_warning_issued_by">Issued By</label>
                                <select class="form-control " data-msg="Issued by is required" id="add_warning_issued_by" name="issued_by">
                                    <option value="">Select Issuer</option>
                                    <?php 
                                        $query = "SELECT * FROM `users` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                                        $result = $GLOBALS['conn']->query($query);
                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                $user_id = $row['user_id'];
                                                $full_name = $row['full_name'];
                                               
                                                echo '<option value="'.$user_id.'">'.$full_name.'</option>';
                                            }
                                        } 
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="add_warning_reason">Reason</label>
                                <textarea class="form-control" data-msg="Reason is required" id="add_warning_reason" name="reason" rows="3" placeholder="Enter warning reason"></textarea>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label" for="add_warning_severity">Severity</label>
                                <select class="form-control" id="add_warning_severity" name="severity">
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Warning</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Warning Modal -->
<div class="modal fade" data-bs-focus="false" id="edit_warning" tabindex="-1" role="dialog" aria-labelledby="edit_warningLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_warningLabel">Edit Warning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editWarningForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="edit_warning_id" name="warning_id">
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label" for="edit_warning_employee_name">Employee</label>
                                <input type="text" class="form-control" id="edit_warning_employee_name" name="employee_name" readonly>
                                <input type="hidden" id="edit_warning_employee_id" name="employee_id">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="edit_warning_date">Warning Date</label>
                                <input type="date" class="form-control validate" data-msg="Warning date is required" id="edit_warning_date" name="warning_date">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="edit_warning_issued_by_name">Issued By</label>
                                <input type="text" class="form-control" id="edit_warning_issued_by_name" name="issued_by_name" readonly>
                                <input type="hidden" id="edit_warning_issued_by" name="issued_by">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="edit_warning_reason">Reason</label>
                                <textarea class="form-control validate" data-msg="Reason is required" id="edit_warning_reason" name="reason" rows="3" placeholder="Enter warning reason"></textarea>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label" for="edit_warning_severity">Severity</label>
                                <select class="form-control" id="edit_warning_severity" name="severity">
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>