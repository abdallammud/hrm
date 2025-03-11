<div class="modal  fade" data-bs-focus="false" id="edit_timesheet" tabindex="-1" role="dialog" aria-labelledby="edit_timesheetLabel" aria-hidden="true">
    <div class="modal-dialog" role="timesheet" style="min-width:1000px; width: 90vw; max-width: 1200px;">
        <form class="modal-content" id="editTimesheetForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Edit timesheet record</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: scroll;">
                <div>
                	<div class="row">
                        <div class="col-xs-4 col-md-4 col-ms-12">
                            <div class="form-group">
                                <label class="label required" for="tsDate4Edit">Date</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="tsDate4Edit" value="<?php echo date('Y-m-d'); ?>" name="tsDate4Edit">
                                <input type="hidden" id="ts_id" name="">
                                <span class="form-error text-danger">This is error</span>
                                </select>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-12 col-ms-12">
                            <span class="bold">Employees</span>
                            <table id="timesheetEmployee" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Staff No.</th>
                                        <th>Full name</th>
                                        <th>Time in</th>
                                        <th>Time out</th>
                                        <th class="cursor info-tooltip">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="employeesData">
                                    <tr>
                                        <td>Staff No.</td>
                                        <td>Ahmed Osman Ali Husein</td>
                                        <td style="width:150px;">
                                        	<input type="time" class="form-control" style="-width: 100%;" name="">
                                        </td>
                                        <td style="width:150px;">
                                        	<input type="time" class="form-control" style="-width:  100%;" name="">
                                        </td>
                                        <td class="cursor info-tooltip">
                                            <div class="sflex scenter-items">
                                            	<select class="form-control smr-10" id="slcTsStatus">
				                                	<option value="P">Present</option>
				                                	<option value="S">Sick</option>
				                                    <option value="PL">Paid Leave</option>
				                                    <option value="UL">Unpaid Leave</option>
				                                	<option class="H">Holiday</option>
				                                	<option value="NH">Not hired day</option>
				                                	<option value="N">Absent</option>
                                            	</select>
								                <input type="radio" class="btn-check removeEmp statusBTN" name="statusBTN' . $emp_id . '" id="removeEmp' . $emp_id . '" value="removeEmp" autocomplete="off">
								                <label title="Remove employee" class="btn removeEmp swidth-40 statusBTNLabel btn-outline-danger" for="removeEmp' . $emp_id . '"><span class="fa fa-trash"></span></label>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
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