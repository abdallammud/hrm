<div class="modal  fade"  data-bs-focus="false" id="edit_employeeLeave" tabindex="-1" role="dialog" aria-labelledby="edit_employeeLeaveLabel" aria-hidden="true">
    <div class="modal-dialog" role="employeeLeave" style="width:500px;">
        <form class="modal-content" id="editEmpLeaveForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Edit employee leave request</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="searchEmployee4Edit">Employee</label>
                               	<input type="text"  class="form-control cursor" readonly id="searchEmployee4Edit" name="searchEmployee4Edit">
                               	<input type="hidden" id="emp_leaveID" name="">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcLeaveType4Edit">Leave type</label>
                                <select type="text"  class="form-control validate" data-msg="Please select leave type" name="slcLeaveType4Edit" id="slcLeaveType4Edit">
                                <option value=""> - Select</option>
                                <?php select_active('leave_types'); ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col col-xs-6">
                            <div class="form-group">
                                <label class="label required" for="dateFrom4Edit">From</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="dateFrom4Edit" value="<?php echo date('Y-m-d'); ?>" name="dateFrom4Edit">
                                <span class="form-error text-danger">This is error</span>
                                </select>
                            </div>
                        </div>
                        <div class="col col-xs-6">
                            <div class="form-group">
                                <label class="label required" for="dateTo4Edit">To</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="dateTo4Edit" value="<?php echo date('Y-m-d'); ?>" name="dateTo4Edit">
                                <span class="form-error text-danger">This is error</span>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcStatus">Status</label>
                                <select  class="form-control " id="slcStatus" name="slcStatus">
                                    <option value="Request">Request</option>
                                    <?php if(check_session('approve_leaves')) { ?>
                                    	<option value="Approved">Approve</option>
                                    <?php } ?>
                                    <option value="Cancelled">Cancel</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
            </div>
        </form>
    </div>
</div>

<style type="text/css">
	.dropdown.bootstrap-select.my-select {
		display: block;
		width: 100% !important;
	}
</style>