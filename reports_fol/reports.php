<div class="row">
    <div class="col-md-12 col-lg-12">
		<div class="page content">
			<form onsubmit="show_report(event)" class="row">
				<div class="col-md-6 col-lg-3">
					<div class="form-group">
                        <label class="label required" for="slcReport">Select report</label>
                        <select type="text"  class="form-control validate slcReport" data-msg="Please select transaction for" name="slcReport" id="slcReport">
                        	<option value="">-Select</option>
                            <option value="allEmployees"> All Employees</option>
                            <option value="attendance"> Attendance</option>
                           	<!--  <option value="timesheet"> Timesheet</option>
                            <option value="transactions"> Transactions</option> -->
                        </select>
                        <span class="form-error text-danger">This is error</span>
                    </div>
				</div>

				<div class="col-md-6 hidden slcMonthFilter col-lg-2">
					<div class="form-group">
                        <label class="label required" for="slcMonth">Month</label>
                       	<input type="month" class="form-control validate" value="<?=date('Y-m');?>" id="slcMonth" name="">
                    </div>
				</div>

				<!-- <div class="col-md-6 col-lg-1">
					<div class="form-group">
                        <label class="label required" for="slcYear">Year</label>
                       	<select type="text" class="form-control validate slcYear" data-msg="Please select transaction for" name="slcYear" id="slcYear">
                            <?php 
                            for ($year = date("Y"); $year >= 2015; $year--) {
							    echo "<option value=\"$year\">$year</option>";
							}
                            ?>
                        </select>
                    </div>
				</div>

				<div class="col-md-6 col-lg-3">
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

				<div class="col-md-6 col-lg-3">
					<div class="form-group">
                        <label class="label required" for="searchDepartment">Department</label>
                        <select class="my-select searchDepartment" name="searchDepartment" id="searchDepartment" data-live-search="true" title="Search and select department">
                            <?php 
                            $query = "SELECT * FROM `branches` WHERE `status` = 'active' ORDER BY `name` ASC LIMIT 10";
		                    $branchSet = $GLOBALS['conn']->query($query);
		                    if($branchSet->num_rows > 0) {
		                    	while($row = $branchSet->fetch_assoc()) {
		                    		$id = $row['id'];
		                    		$name = $row['name'];
		                    		echo '<option value="'.$id.'">'.$name.'</option>';
		                    	}
		                    } 
                            ?>
                        </select>
                    </div>
				</div>

				<div class="col-md-6 col-lg-3">
					<div class="form-group">
                        <label class="label required" for="searchDepartment">Duty Location</label>
                        <select class="my-select searchDepartment" name="searchDepartment" id="searchDepartment" data-live-search="true" title="Search and select Location">
                            <?php 
                            $query = "SELECT * FROM `locations` WHERE `status` = 'active' ORDER BY `name` ASC LIMIT 10";
			                    $locationSet = $GLOBALS['conn']->query($query);
			                    if($locationSet->num_rows > 0) {
			                    	while($row = $locationSet->fetch_assoc()) {
			                    		$id = $row['id'];
			                    		$name = $row['name'];
			                    		echo '<option value="'.$id.'">'.$name.'</option>';
			                    	}
			                    } 
                            ?>
                        </select>
                    </div>
				</div> -->

				<div class="col-md-4 col-lg-2">
					<div class="form-group sflex swrap">
                        <label class="label sflex-basis-100 required" for="">&nbsp;</label>
                        <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Show</button>
                        <span class="form-error text-danger">This is error</span>
                    </div>
				</div>
				
			</form>
		    <hr>
		    
			<div class="card">
				<div class="card-body">
					<div class="sflex sjend">
						<a id="printTag" class="cursor" target="_blank">
							<i class="fa fa-print"></i>
							Print
						</a>
					</div>
					<div class="table-responsive">
						<table id="reportDataTable" class="table table-striped table-bordered" style="width:100%">
							
						</table> 
					</div>
				</div>
			</div>
		</div>
	</div>


</div>

<script type="text/javascript">
	addEventListener("DOMContentLoaded", (event) => {

	});
</script>

<style type="text/css">
	.dropdown.bootstrap-select.my-select {
		display: block;
		width: 100% !important;
	}
</style>

<?php 
// require('payroll_add.php');
?>
