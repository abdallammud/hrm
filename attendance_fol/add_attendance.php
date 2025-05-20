<div class="row">
    <div class="col-md-12 col-lg-12">
		<div class="page content">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
		        <h5 class="">Add new attendance record </h5>
		        <div class="ms-auto d-sm-flex">
					<div class="btn-group smr-10">
		                <a type="button" href="<?=baseUri();?>/attendance"  class="btn btn-secondary">Go back</a>
		            </div>
		        </div>
		    </div>
		    <hr>
		    
			<div class="card">
				<form id="add_bulkAttendanceForm" class="card-body">
                    <div class="row">
                        <div class="col col-sm-12 col-md-3 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="slcDepartment4Atten">Department</label>
                                <select type="text"  class="form-control validate slcDepartment4Atten" name="slcDepartment4Atten" id="slcDepartment4Atten">
                                	<option value="">- Select </option>
                                    <?php 
                                    select_active('branches');
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col col-sm-12 col-md-3 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="slcLocation4Atten">Duty Location</label>
                                <select type="text"  class="form-control validate slcLocation4Atten" name="slcLocation4Atten" id="slcLocation4Atten">
                                    <option value="">- Select </option>
                                    <?php 
                                    select_active('locations');
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col col-sm-12 col-md-2 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="attendDate">Date</label>
                                <input type="text"  class="form-control cursor attendDate datepicker" readonly id="attendDate" value="<?php echo date('Y-m-d'); ?>" name="attendDate">
                            </div>
                        </div>
                        
                    </div>
                    <hr>
					<div class="table-responsive">
						<table id="attendanceTable" class="table table-striped table-bordered" style="width:100%">
							<thead>
                                <tr>
                                    <th scope="col">Employee ID</th>
                                    <th scope="col">Employee</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">
                                        <div class="form-check">
                                            <input class="form-check-input cursor selectAll" type="checkbox" value="" id="selectAll">
                                            <label class="form-check-label cursor" for="selectAll">Attendance</label>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC ";
                                $empSet = $GLOBALS['conn']->query($query);
                                if($empSet->num_rows > 0) {
                                    while($row = $empSet->fetch_assoc()) {
                                        $employee_id = $row['employee_id'];
                                        $full_name = $row['full_name'];
                                        $phone_number = $row['phone_number'];
                                        $branch = $row['branch'];
                                ?>
                                <tr>
                                    <td>#<?=$employee_id;?></td>
                                    <td><?=$full_name;?></td>
                                    <td><?=$branch;?></td>
                                    <td>
                                        <div class=" sflex scenter-items">
                                            <input type="hidden" name="employee_id" class="employee_id" value="<?=$employee_id;?>">
                                            <input class="form-check-input smr-10 cursor isPresent" type="checkbox" value="Yes">
                                            <!-- <label class="form-check-label" for="selectAll">Attendance</label> -->
                                            <select type="text"  class="form-control validate slcStatus4Atten" name="slcStatus4Atten">
                                                <option value=""> - Select status</option>
                                                <option value="P">Present</option>
                                                <option value="S">Sick</option>
                                                <option value="PL">Paid Leave</option>
                                                <option value="UL">Unpaid Leave</option>
                                                <option value="H">Holiday</option>
                                                <option value="NH">Not hired day</option>
                                                <option value="N">Absent</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <?php }} ?>
                            </tbody>
						</table> 
					</div>

                    <div class="row">
                        <div class="col-lg-10">

                        </div>
                        <div class="col-lg-2 sflex sjend">
                            <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
                        </div>
                    </div>
				</form>
			</div>
		</div>
	</div>


</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        simplifyAddAttendanceTable();
    });
</script>

<style type="text/css">
	#statesDT td:nth-of-type(1) {
		width: 70%;
	}

    .form-check-input {
        width: 20px;
        height: 20px;
        margin-right: 10px;;
    }
</style>

<?php 
require('atten_add.php');
require('atten_edit.php');
?>
