<div class="modal fade" data-bs-focus="false" id="add_promotion" tabindex="-1" role="dialog" aria-labelledby="add_promotionLabel" aria-hidden="true">
    <div class="modal-dialog" role="promotion" style="width:500px;">
        <form class="modal-content" id="addPromotionForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Add Promotion</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col col-lg-12">
                        <div class="form-group">
                            <label class="label required" for="searchEmployee">Employee</label>
                            <select class="my-select searchEmployee" name="searchEmployee" id="searchEmployee" data-live-search="true" title="Search and select employee">
                                <?php 
                                    $query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                                    $empSet = $GLOBALS['conn']->query($query);
                                    if($empSet->num_rows > 0) {
                                        while($row = $empSet->fetch_assoc()) {
                                            $employee_id = $row['employee_id'];
                                            $full_name = $row['full_name'];
                                            $phone_number = $row['phone_number'];
                                            $designation = $row['designation'];
                                            $salary = $row['salary'];

                                            echo '<option value="'.$employee_id.'" data-current-designation="'.$designation.'" data-current-salary="'.$salary.'">'.$full_name.', '.$phone_number.'</option>';
                                        }
                                    } 
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label" for="old_designation">Current Designation</label>
                            <input type="text" class="form-control" data-msg="Current designation is required" id="old_designation" name="old_designation" readonly>
                            <input type="hidden" id="old_designation_id" name="old_designation_id">
                        </div>
                    </div>
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label required" for="new_designation_id">New Designation</label>
                            <select class="form-control validate" data-msg="New designation is required" id="new_designation_id" name="new_designation_id">
                                <option value="">Select New Designation</option>
                                <?php echo select_active('designations', ['value' => 'name']);?>
                            </select>
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label" for="current_salary">Current Salary</label>
                            <input type="number" step="0.01" class="form-control" id="current_salary" name="current_salary" readonly>
                        </div>
                    </div>
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label" for="new_salary">New Salary</label>
                            <input type="number" step="0.01" class="form-control validate" data-msg="New salary is required" id="new_salary" name="new_salary" placeholder="Enter new salary">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label required" for="promotion_date">Promotion Date</label>
                            <input type="date" class="form-control cursor datepicker validate" data-msg="Promotion date is required" readonly value="<?php echo date('Y-m-d'); ?>" id="promotion_date" name="promotion_date">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label" for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-lg-12">
                        <div class="form-group">
                            <label class="label" for="reason">Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Enter promotion reason"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor" data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
            </div>
        </form>
    </div>
</div>
