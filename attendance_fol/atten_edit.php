<div class="modal  fade" data-bs-focus="false" id="edit_attendance" tabindex="-1" role="dialog" aria-labelledby="edit_attendanceLabel" aria-hidden="true">
    <div class="modal-dialog" role="attendance" style="min-width:1000px; width: 90vw; max-width: 1200px;">
        <form class="modal-content" id="editAttendanceForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Edit attendance record</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: scroll;">
                <div>
                	<div class="row">
                        <div class="col col-xs-4 col-md-4 col-ms-12">
                            <div class="form-group">
                                <label class="label required" for="slcAttenFor4Edit">Attendance for</label>
                                <select type="text"  class="form-control validate slcAttenFor" data-msg="Please select attendance for" name="slcAttenFor4Edit" id="slcAttenFor4Edit">
                                	<option value="Employee"> Employee</option>
                                	<option value="Department"> Department</option>
                                	<option value="Location"> Duty Location</option>
                                </select>
                                <input type="hidden" id="attendance_id" name="">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-4 col-md-4 col-ms-12">
                            <div class="form-group">
                                <label class="label required" for="ref_name">&nbsp;</label>
                                <input type="text"  class="form-control cursor " readonly id="ref_name"  name="ref_name">
                                <span class="form-error text-danger">This is error</span>
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-4 col-md-4 col-ms-12">
                            <div class="form-group">
                                <label class="label required" for="attendDate4Edit">Date</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="attendDate4Edit" value="<?php echo date('Y-m-d'); ?>" name="attendDate4Edit">
                                <span class="form-error text-danger">This is error</span>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    
                   

                    <div class="row">
                        <div class="col-xs-12 col-md-12 col-ms-12">
                            <span class="bold">Employees</span>
                            <table id="attendanceEmployee" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Staff No.</th>
                                        <th>Full name</th>
                                        <th class="cursor info-tooltip">
                                            Status
                                            <i class="fa fa-info-circle"></i>
                                            <div class="tooltip-div sflex swrap">
                                                <div class="sflex swrap sflex-basis-100">
                                                   
                                                    <span class="bold sflex-basis-100 sflex sspace-bw">
                                                        Status letters
                                                        <!-- <span class="text-danger hide-tooltip">x</span> -->
                                                    </span>
                                                    <span class="sflex-basis-100">P = Present</span>
                                                    <span class="sflex-basis-100">PL = Paid Leave</span>
                                                    <span class="sflex-basis-100">S = Sick</span>
                                                    <span class="sflex-basis-100">UL = Unpaid Leave</span>
                                                    <span class="sflex-basis-100">H = Holiday</span>
                                                    <span class="sflex-basis-100">NH = Not hired / Not work day</span>
                                                    <span class="sflex-basis-100">N = Absent</span>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="employeesData">
                                    
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