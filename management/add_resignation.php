<div class="modal fade" data-bs-focus="false" id="add_resignation" tabindex="-1" role="dialog" aria-labelledby="add_resignationLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_resignationLabel">Add Resignation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addResignationForm" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="add_resignation_employee_id">Employee</label>
                                <select class="form-control my-select " data-live-search="true" data-msg="Employee is required" id="add_resignation_employee_id" name="employee_id">
                                    <option value="">Select Employee</option>
                                    <?php 
                                        $query = "SELECT employee_id, full_name, phone_number FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC";
                                        $result = $GLOBALS['conn']->query($query);
                                        if ($result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                echo '<option value="'.$row['employee_id'].'">'.$row['full_name'].', '.$row['phone_number'].'</option>';
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
                                <label class="label required" for="add_resignation_date">Resignation Date</label>
                                <input type="date" class="form-control datepicker " data-msg="Resignation date is required" value="<?php echo date('Y-m-d'); ?>" id="add_resignation_date" name="resignation_date">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="add_last_working_day">Last Working Day</label>
                                <input type="date" class="form-control datepicker " data-msg="Last working day is required" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>" id="add_last_working_day" name="last_working_day">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label" for="add_resignation_reason">Reason</label>
                                <textarea class="form-control" id="add_resignation_reason" name="reason" rows="3" placeholder="Enter resignation reason"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label" for="add_resignation_status">Status</label>
                                <select class="form-control" id="add_resignation_status" name="status">
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
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