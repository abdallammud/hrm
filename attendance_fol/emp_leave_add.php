<div class="modal  fade"  data-bs-focus="false" id="add_employeeLeave" tabindex="-1" role="dialog" aria-labelledby="add_employeeLeaveLabel" aria-hidden="true">
    <div class="modal-dialog" role="employeeLeave" style="width:500px;">
        <form class="modal-content" id="addEmpLeaveForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Add employee leave request</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="searchEmployee">Employee</label>
                                <select class="my-select searchEmployee" name="searchEmployee" id="searchEmployee" data-live-search="true" title="Search and select empoyee">
                                <?php 
                                $query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                                $empSet = $GLOBALS['conn']->query($query);
                                if($empSet->num_rows > 0) {
                                	while($row = $empSet->fetch_assoc()) {
                                		$employee_id = $row['employee_id'];
                                		$full_name = $row['full_name'];
                                		$phone_number = $row['phone_number'];

                                		echo '<option value="'.$employee_id.'">'.$full_name.', '.$phone_number.'</option>';
                                	}
                                } 

                                ?>
					        </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcLeaveType">Leave type</label>
                                <select type="text"  class="form-control validate" data-msg="Please select leave type" name="slcLeaveType" id="slcLeaveType">
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
                                <label class="label required" for="dateFrom">From</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="dateFrom" value="<?php echo date('Y-m-d'); ?>" name="dateFrom">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-6">
                            <div class="form-group">
                                <label class="label required" for="dateTo">To</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="dateTo" value="<?php echo date('Y-m-d'); ?>" name="dateTo">
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