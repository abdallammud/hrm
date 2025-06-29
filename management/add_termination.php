<div class="modal fade" data-bs-focus="false" id="add_termination" tabindex="-1" role="dialog" aria-labelledby="add_terminationLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_terminationLabel">Add Termination</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTerminationForm" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="add_termination_employee_id">Employee</label>
                                <select class="form-control my-select " data-live-search="true" data-msg="Employee is required" id="add_termination_employee_id" name="employee_id">
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
                                <label class="label required" for="add_termination_date">Termination Date</label>
                                <input type="date" class="form-control datepicker " data-msg="Termination date is required" value="<?php echo date('Y-m-d'); ?>" id="add_termination_date" name="termination_date">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label" for="add_termination_type">Termination Type</label>
                                <select class="form-control validate" data-msg="Termination type is required" id="add_termination_type" name="termination_type">
                                    <option value="">Select Type</option>
                                    <option value="Voluntary">Voluntary</option>
                                    <option value="Involuntary">Involuntary</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="add_termination_reason">Reason</label>
                                <textarea class="form-control validate" data-msg="Reason is required" id="add_termination_reason" name="reason" rows="3" placeholder="Enter termination reason"></textarea>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label" for="add_termination_status">Status</label>
                                <select class="form-control" id="add_termination_status" name="status">
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
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