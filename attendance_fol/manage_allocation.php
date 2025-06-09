<?php
$employee_id1 = $supervisor_id = '';
$month = date("Y-m");
$foundData = '';
if(isset($_GET['id'])) {
    $get_record = "SELECT * FROM `res_allocation` WHERE `id` = '".$_GET['id']."'";
    $allocation = $GLOBALS['conn']->query($get_record);
    while($row = $allocation->fetch_assoc()) {
        $employee_id1 = $row['emp_id'];
        $supervisor_id = $row['sup_id'];
        $month = $row['month'];
        $foundData = $row['allocation'];
    }
}


?>
<div class="row">
    <div class="col-md-12 col-lg-12">
		<div class="page content">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
		        <h5 class="">Add new timesheet allocation record </h5>
		        <div class="ms-auto d-sm-flex">
					<div class="btn-group smr-10">
		                <a type="button" href="<?=baseUri();?>/allocation"  class="btn btn-secondary">Go back</a>
		            </div>
		        </div>
		    </div>
		    <hr>
		    
			<div class="card">
				<form id="add_allocationForm" class="card-body">
                    <div class="row">
                        <div class="col col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group ">
                                <label class="label required" for="searchEmployee">Employee</label>
                                <input type="hidden" name="" class="prevMonth" id="prevMonth" value="<?=$month;?>">
                                <select class="my-select searchEmployee" name="searchEmployee" id="searchEmployee" data-live-search="true" title="Search and select empoyee">
                                    <?php 
                                    $query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                                    $empSet = $GLOBALS['conn']->query($query);
                                    if($empSet->num_rows > 0) {
                                        while($row = $empSet->fetch_assoc()) {
                                            $employee_id = $row['employee_id'];
                                            $full_name = $row['full_name'];
                                            $phone_number = $row['phone_number'];

                                            $selected = '';
                                            if($employee_id == $employee_id1) $selected = 'selected=""';

                                            echo '<option '.$selected.' value="'.$employee_id.'">'.$full_name.', '.$phone_number.'</option>';
                                        }
                                    } 

                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col col-sm-12 col-md-4 col-lg-4">
                            <div class="form-group ">
                                <label class="label required" for="searchSupervisor">Supervisor</label>
                                <select class="my-select searchSupervisor" name="searchSupervisor" id="searchSupervisor" data-live-search="true" title="Search and select empoyee">
                                    <?php 
                                    $query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                                    $empSet = $GLOBALS['conn']->query($query);
                                    if($empSet->num_rows > 0) {
                                        while($row = $empSet->fetch_assoc()) {
                                            $employee_id = $row['employee_id'];
                                            $full_name = $row['full_name'];
                                            $phone_number = $row['phone_number'];

                                            $selected = '';
                                            if($employee_id == $supervisor_id) $selected = 'selected=""';

                                            echo '<option '.$selected.' value="'.$employee_id.'">'.$full_name.', '.$phone_number.'</option>';
                                        }
                                    } 

                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col col-sm-12 col-md-3 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="rsaMonth">Timesheet month</label>
                                <input type="month"  class="form-control cursor rsaMonth " id="rsaMonth" value="<?php echo $month; ?>" name="rsaMonth">
                            </div>
                        </div>
                        
                    </div>
                    <hr>
                    
                    <div class="row employeeData4Allocation">
                        <h6 class="text-center">Select employee </h6>
                    </div>
                    
                    <hr>
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
        simplifyResourceAllocation();
        let employee_id = '<?=$employee_id1;?>'
        let month = '<?=$month;?>'

        if(employee_id) {
            $('.searchEmployee').trigger('change')
            get_prevAllocation(employee_id, month);
        }
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
    th:nth-last-of-type(4) {
        width: 10%;;
    }
    th:nth-last-of-type(1) {
        width: 22%;;
    }
    .time_in {
        margin-right: 10px;
    }
    
</style>
