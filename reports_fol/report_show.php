<?php 
$report = isset($_GET['report']) ? $_GET['report'] : '';
if($report == 'employees') $reportTitle = 'All Employees Reports';
else if($report == 'absence') $reportTitle = 'Absence and Leave Reports';
else if($report == 'attendance') $reportTitle = 'Timesheet and Attendance Reports';
else if($report == 'componsation') $reportTitle = 'Componsation and Benfits Reports';
else if($report == 'deductions') $reportTitle = 'Deductions Reports';
?>
<div class="row">
    <div class="col-md-12 col-lg-12">
		<div class="page content reportShow">
            <input type="hidden" id="report" value="<?=$report;?>">
			<div class="d-flex align-items-center text-primary ">
				<h5><?=$reportTitle;?></h5>
			</div>
            <hr>
            <?php 
            if($report == 'employees') {
            ?>
            <div class="row filter">
                <div class="col-md-12 col-lg-2">
                    <div class="form-group">
                        <label for="slcGender">Gender</label>
                        <select id="slcGender" class="form-control">
                            <option value="">All</option>
                            <option value="Male">Male</option>
                            <option value="Female   ">Female</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 col-lg-2">
                    <div class="form-group">
                        <label for="slcState">State</label>
                        <select  name="state" class="form-control " data-msg="Please select state" id="slcState">
                            <option value="">All </option>
                            <?php 
                            // var_dump(select_all('states'));
                            select_all('states');
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-12 col-lg-3">
                    <div class="form-group">
                        <label for="slcDepartment">Department</label>
                        <select  name="branch" class="form-control " data-msg="Please select branch" id="slcDepartment">
                            <option value="">All </option>
                            <?php 
                            // var_dump(select_all('branchs'));
                            select_all('branches');
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-12 col-lg-2">
                    <div class="form-group">
                        <label for="slcLocation">Duty locations</label>
                        <select id="slcLocation" class="form-control">
                            <option value="">All</option>
                            <?php select_all('locations'); ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-12 col-lg-2">
                    <div class="form-group">
                        <label for="slcGender">Salary range from</label>
                        <input type="text" class="form-control" id="salary_range_start" name="salary_range_start">
                    </div>
                </div>
                <div class="col-md-12 col-lg-1">
                    <div class="form-group">
                        <label for="slcGender">&nbsp</label>
                        <input type="text" class="form-control" id="salary_range_end" name="salary_range_end">
                    </div>
                </div>

                <input type="hidden" id="slcAge" name="slcAge">
                <!-- <div class="col-md-12 col-lg-1">
                    <div class="form-group">
                        <label for="slcAge">Age</label>
                        <select id="slcAge" class="form-control">
                            <option value="">All</option>
                            <option value="18-24">18-24</option>
                            <option value="25-34">25-34</option>
                            <option value="35-44">35-44</option>
                            <option value="45-54">45-54</option>
                            <option value="55-64">55-64</option>
                            <option value="65+">65+</option>
                        </select>
                    </div>
                </div> -->
                
            </div>
            <?php } ?>

            
            <div class="row filter">
                <?php 
                if($report == 'other') { ?>
                <div class="col-md-12 col-lg-2">
                    <div class="form-group">
                        <label for="date_range_start">Date from</label>
                        <input type="date" class="form-control datepicker cursor" id="date_range_start" value="<?php echo date('Y-m-d'); ?>" readonly name="date_range_start">
                    </div>
                </div>
                <div class="col-md-12 col-lg-2">
                    <div class="form-group">
                        <label for="date_range_end">Date to</label>
                        <input type="date" class="form-control datepicker cursor" id="date_range_end" value="<?php echo date('Y-m-d'); ?>" readonly name="date_range_end">
                    </div>
                </div>
                <?php } ?>
                <?php 
                if($report == 'componsation' || $report == 'absence' || $report == 'deductions') { ?>
                <div class="col-md-12 col-lg-2">
                    <div class="form-group">
                        <label for="month">Month</label>
                        <input type="month" class="form-control monthPicker cursor" id="month" value="<?php echo date('Y-m'); ?>" readonly name="month">
                    </div>
                </div>
                <?php } ?>
            </div>

            <div class="card smt-10">
				<div class="card-body">
					<div class="sflex sjend">
                        <a onclick="display_report(true)" class="cursor smr-20" target="_blank">
							<i class="fa fa-refresh"></i>
							Set and filter
						</a>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        display_report();
    })
</script>