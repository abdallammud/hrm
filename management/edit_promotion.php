<div class="modal fade" data-bs-focus="false" id="edit_promotion" tabindex="-1" role="dialog" aria-labelledby="edit_promotionLabel" aria-hidden="true">
    <div class="modal-dialog" role="promotion" style="width:500px;">
        <form class="modal-content" id="editPromotionForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Promotion</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_promotion_id" name="promotion_id">
                
                <div class="row">
                    <div class="col col-lg-12">
                        <div class="form-group">
                            <label class="label" for="edit_employee_name">Employee</label>
                            <input type="text" class="form-control" id="edit_employee_name" name="employee_name" readonly>
                            <input type="hidden" id="edit_employee_id" name="employee_id">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label" for="edit_old_designation">Current Designation</label>
                            <input type="text" class="form-control" id="edit_old_designation" name="old_designation" readonly>
                            <input type="hidden" id="edit_old_designation" name="old_designation">
                        </div>
                    </div>
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label required" for="edit_new_designation">New Designation</label>
                            <select class="form-control validate" data-msg="New designation is required" id="edit_new_designation" name="new_designation">
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
                            <label class="label" for="edit_current_salary">Current Salary</label>
                            <input type="number" step="0.01" class="form-control" id="edit_current_salary" name="current_salary" readonly>
                        </div>
                    </div>
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label" for="edit_new_salary">New Salary</label>
                            <input type="number" step="0.01" class="form-control" id="edit_new_salary" name="new_salary" placeholder="Enter new salary">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label required" for="edit_promotion_date">Promotion Date</label>
                            <input type="date" class="form-control cursor datepicker validate" data-msg="Promotion date is required" readonly id="edit_promotion_date" name="promotion_date">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label required" for="edit_status">Status</label>
                            <select class="form-control validate" data-msg="Status is required" id="edit_status" name="status">
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
                            <label class="label" for="edit_reason">Reason</label>
                            <textarea class="form-control" id="edit_reason" name="reason" rows="3" placeholder="Enter promotion reason"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor" data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Update</button>
            </div>
        </form>
    </div>
</div>
