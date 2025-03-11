<div class="modal   fade"  data-bs-focus="false" id="add_timesheet" tabindex="-1" role="dialog" aria-labelledby="add_timesheetLabel" aria-hidden="true">
    <div class="modal-dialog" role="timesheet" style="width:500px;">
        <form class="modal-content" id="addTimesheetForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Add new timesheet record</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group ">
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
                        <div class="col col-xs-4">
                            <div class="form-group">
                                <label class="label required" for="tsDate">Date</label>
                                <input type="text"  class="form-control datepicker cursor " readonly id="tsDate" value="<?php echo date('Y-m-d');?>"  name="tsDate">
                                <span class="form-error text-danger">This is error</span>
                                </select>
                            </div>
                        </div>
                        <div class="col col-xs-4">
                            <div class="form-group">
                                <label class="label required" for="timeIn">Time In</label>
                                <input type="time"  class="form-control cursor " id="timeIn" value="<?php echo date('H:i:s', strtotime(return_setting('time_in')));?>"  name="timeIn">
                                <span class="form-error text-danger">This is error</span>
                                </select>
                            </div>
                        </div>
                        <div class="col col-xs-4">
                            <div class="form-group">
                                <label class="label required" for="timeOut">Time out</label>
                                <input type="time"  class="form-control cursor "  id="timeOut" value="<?php echo date('H:i:s', strtotime(return_setting('time_out')));?>"  name="timeOut">
                                <span class="form-error text-danger">This is error</span>
                                </select>
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

<!-- Download file -->
<div class="modal   fade"  data-bs-focus="false" id="download_timesheetUploadFile" tabindex="-1" role="dialog" aria-labelledby="download_timesheetUploadFileLabel" aria-hidden="true">
    <div class="modal-dialog" role="timesheet" style="width:500px;">
        <form class="modal-content" id="downloadTimesheetUploadForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Download sample file</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcAttenFor">timesheet for</label>
                                <select type="text"  class="form-control validate slcAttenFor" data-msg="Please select timesheet for" name="slcAttenFor" id="slcAttenFor">
                                    <!-- <option value="Employee"> Employee</option> -->
                                    <option value="Department"> Department</option>
                                    <option value="Location"> Duty Location</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group attenForDiv">
                                <label class="label required" for="searchDepartment">Employee</label>
                                <select class="my-select searchDepartment" name="searchDepartment" id="searchDepartment" data-live-search="true" title="Search and select empoyee">
                                <?php 
                                $query = "SELECT * FROM `branches` WHERE `status` = 'active' ORDER BY `name` ASC LIMIT 10";
                                $branchSet = $GLOBALS['conn']->query($query);
                                if($branchSet->num_rows > 0) {
                                    while($row = $branchSet->fetch_assoc()) {
                                        $id = $row['id'];
                                        $name = $row['name'];
                                        echo  '<option value="'.$id.'">'.$name.'</option>';
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
                                <label class="label required" for="tsDate">Date</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="tsDate" value="<?php echo date('Y-m-d'); ?>" name="tsDate">
                                <span class="form-error text-danger">This is error</span>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Download File</button>
            </div>
        </form>
    </div>
</div>


<!-- Upload timesheet -->
<div class="modal fade"   data-bs-focus="false" id="timesheet_upload" tabindex="-1" role="dialog" aria-labelledby="timesheet_uploadLabel" aria-hidden="true">
    <div class="modal-dialog" role="timesheet_upload" style="width:500px;">
        <form class="modal-content" id="timesheet_uploadForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Upload timesheet</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <label class="cursor col col-xs-12 col-md-12">
                            <input class="form-control py-2" id="timesheet_uploadInput"  type="file" name="">
                            <span class="file-selected-name"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Upload</button>
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