<div class="modal fade" data-bs-focus="false" id="add_transfer" tabindex="-1" role="dialog" aria-labelledby="add_transferLabel" aria-hidden="true">
    <div class="modal-dialog" role="add_transfer" style="width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_transferLabel">Add Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTransferForm" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="searchEmployee">Employee</label>
                                <select class="form-control my-select validate" data-live-search="true" data-msg="Employee is required" id="searchEmployee" name="employee_id">
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
                                <label class="label" for="old_department">Current Department</label>
                                <input type="text" class="form-control" data-msg="Current department is required" id="old_department" name="old_department" readonly>
                                <input type="hidden" id="old_department_id" name="old_department_id">
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="new_department_id">New Department</label>
                                <select class="form-control validate" data-msg="New department is required" id="new_department_id" data-msg="New department is required" name="new_department_id">
                                    <option value="">Select New Department</option>
                                    <?php echo select_active('branches');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="transfer_date">Transfer Date</label>
                                <input type="date" class="form-control datepicker" value="<?php echo date('Y-m-d'); ?>" readonly id="transfer_date" name="transfer_date">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="status">Status</label>
                                <select class="form-control " data-msg="Status is required" id="status" name="status">
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label" for="reason">Reason</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Enter transfer reason"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
