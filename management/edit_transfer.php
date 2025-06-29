<div class="modal fade" data-bs-focus="false" id="edit_transfer" tabindex="-1" role="dialog" aria-labelledby="edit_transferLabel" aria-hidden="true">
    <div class="modal-dialog" role="edit_transfer" style="width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_transferLabel">Edit Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTransferForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="edit_transfer_id" name="transfer_id">
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
                                <label class="label" for="edit_old_department">Current Department</label>
                                <input type="text" class="form-control" id="edit_old_department" name="old_department" readonly>
                                <input type="hidden" id="edit_old_department_id" name="old_department_id">
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="edit_new_department_id">New Department</label>
                                <select class="form-control validate" data-msg="New department is required" id="edit_new_department_id" name="new_department_id">
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
                                <label class="label required" for="edit_transfer_date">Transfer Date</label>
                                <input type="date" class="form-control validate" data-msg="Transfer date is required" id="edit_transfer_date" name="transfer_date">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="edit_status">Status</label>
                                <select class="form-control validate" data-msg="Status is required" id="edit_status" name="status">
                                    <option value="">Select Status</option>
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
                                <textarea class="form-control" id="edit_reason" name="reason" rows="3" placeholder="Enter transfer reason"></textarea>
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
