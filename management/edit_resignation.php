<div class="modal fade" data-bs-focus="false" id="edit_resignation" tabindex="-1" role="dialog" aria-labelledby="edit_resignationLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_resignationLabel">Edit Resignation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editResignationForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="edit_resignation_id" name="resignation_id">
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="edit_resignation_employee_id">Employee</label>
                                <select class="form-control my-select " data-live-search="true" data-msg="Employee is required" id="edit_resignation_employee_id" name="employee_id">
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
                                <input type="hidden" id="edit_resignation_employee_name">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="edit_resignation_date">Resignation Date</label>
                                <input type="date" class="form-control datepicker " data-msg="Resignation date is required" id="edit_resignation_date" name="resignation_date">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="edit_last_working_day">Last Working Day</label>
                                <input type="date" class="form-control datepicker " data-msg="Last working day is required" id="edit_last_working_day" name="last_working_day">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label" for="edit_resignation_reason">Reason</label>
                                <textarea class="form-control" id="edit_resignation_reason" name="reason" rows="3" placeholder="Enter resignation reason"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label" for="edit_resignation_status">Status</label>
                                <select class="form-control" id="edit_resignation_status" name="status">
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
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>