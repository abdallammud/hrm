<?php 
$employee_id = $_GET['employee_id'];
$employee = $GLOBALS['employeeClass']->read($employee_id);
// var_dump($employee);

?>
<div class="page content">
	<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <h5 class="">Edit Employee info </h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-10">
                <a href="<?=baseUri();?>/employees"  class="btn btn-secondary">Go Back</a>
            </div>            
        </div>
    </div>
    <hr>
    <div class="card">
		<div class="card-body">
			<form class="modal-content" id="editEmployeeForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	
            <div class="modal-body">
                <div id="">
                	<p class="bold smt-10">Employee Information</p>
                    <div class="row">
                        <div class="col col-xs-12 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="label required" for="full-name">Employee Name</label>
                                <input type="hidden" id="employee_id" value="<?=$employee['employee_id'];?>" name="">
                                <input type="text"  class="form-control validate" data-msg="Employee full name is required" value="<?=$employee['full_name'];?>" id="full-name" name="full-name">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="phone">Phone Number</label>
                                <!-- <input type="text" value="<?=$employee['phone_number'];?>"  class="form-control validate" data-msg="Phone number is required" id="phone" name="phone"> -->
                                <input type="text" value="<?=$employee['phone_number'];?>"  class="form-control " id="phone" name="phone">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="email">Email</label>
                                <!-- <input type="email" value="<?=$employee['email'];?>"  class="form-control validate" id="email" name="email" data-msg="Email is rquired"> -->
                                <input type="email" value="<?=$employee['email'];?>"  class="form-control " id="email" name="email">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="slcStatus">Status</label>
                                <select  class="form-control " id="slcStatus" name="slcStatus">
                                    <option <?php if(ucwords($employee['status']) == 'Active') echo 'selected="selected"'; ?> value="Active">Active</option>
                                    <option <?php if(ucwords($employee['status']) == 'Suspended') echo 'selected="selected"'; ?> value="Suspended">Suspended</option>
                                    <option <?php if(ucwords($employee['status']) == 'Deleted') echo 'selected="selected"'; ?> value="Deleted">Deleted</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label" for="staffNo">Staff Number</label>
                                <?php if(!$employee['staff_no']) $employee['staff_no'] =  sys_setting('staff_prefix'); ?>
                                <input type="text"  class="form-control " value="<?=$employee['staff_no'];?>" id="staffNo" name="staffNo">
                               
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label" for="nationalID">ID Number</label>
                                <input type="text" value="<?=$employee['national_id'];?>" placeholder="National ID"  class="form-control " id="nationalID" name="nationalID">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="gender">Gender </label>
                                <select  class="form-control validate" id="gender" name="gender" data-msg="Please select gender">
                                	<option value="">- Select</option>
                                	<option <?php if($employee['gender'] == 'Male') echo 'selected="selected"'; ?> value="Male">Male</option>
                                	<option <?php if($employee['gender'] == 'Female') echo 'selected="selected"'; ?> value="Female">Female</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="dob">Date Of Birth</label>
                                <input type="text"   class="form-control cursor datepicker" readonly id="dob" value="<?php echo date('Y-m-d', strtotime($employee['date_of_birth'])); ?>" name="dob">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label " for="address">Address</label>
                                <input value="<?=$employee['address'];?>"  type="text"  class="form-control " id="address" name="address">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="state">State</label>
                                <select  name="state" class="form-control validate" data-msg="Please select state" id="state">
                                	<option value="">- Select </option>
                                	<?php 
									select_active('states', [], $employee['state_id']);
                                	?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label " for="city">City</label>
                                <input type="text" value="<?=$employee['city'];?>"  class="form-control " id="city" name="city">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label " for="bankName">Payment Through</label>
                                <input type="text" value="<?=$employee['payment_bank'];?>"  class="form-control " id="bankName" name="bankName" placeholder="Bank name">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                               <label class="label ">&nbsp;</label>
                                <input type="text"  class="form-control " value="<?=$employee['payment_account'];?>" id="accountNo" name="accountNo" placeholder="Account number">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                       
                    </div>

                    <p class="bold smt-20" style="margin-bottom: 0px;">Contract Information</p>
                    <div class="row">
                    	<div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="position">Position</label>
                                <input type="text" value="<?=$employee['position'];?>"  class="form-control validate" data-msg="Please provide employee position/job title" id="position" name="position">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="dep"><?=$GLOBALS['branch_keyword']['sing'];?></label>
                                <select  name="dep" class="form-control validate" data-msg="Please select <?=$GLOBALS['branch_keyword']['sing'];?>" id="dep">
                                	<option value="">- Select <?=$GLOBALS['branch_keyword']['sing'];?></option>
                                	<?php 
                                	select_active('branches', [], $employee['branch_id']);
                                	?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="dutyStation">Duty Station/Health facility</label>
                                <input type="hidden" id="designation" value="" name="">
                                <select  name="dutyStation" class="form-control validate" id="dutyStation" data-msg="Please select duty station">
                                    <option value="">- Select </option>
                                    <option <?php if($employee['location_id'] == '0') echo 'selected="selected"'; ?> value="All">All</option>
                                    <?php 
                                    select_active('locations', [], $employee['location_id']);
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <!-- <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="designation">Designation</label>
                                <select  name="designation" class="form-control " id="designation">
                                    <option value="">- Select</option>
                                    <?php 
                                    select_active('designations', ['value' => 'name'], $employee['designation']);
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div> -->
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label " for="project">Project</label>
                                <select  name="project" class="form-control " id="project">
                                    <option value="">- Select</option>
                                    <?php 
                                    select_active('projects', [], $employee['project_id']);
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="contractType">Contract Type</label>
                                <select  name="contractType" class="form-control validate" data-msg="Please select contract type" id="contractType">
                                	<option value="">- Select </option>
                                	<?=select_active('contract_types', ['value' => 'name'], $employee['contract_type']);?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="mohContract">MoH Contract</label>
                                <select  name="mohContract" class="form-control" id="mohContract">
                                    <option <?php if($employee['moh_contract'] == 'No') echo 'selected="selected"'; ?> value="No">No </option>
                                    <option <?php if($employee['moh_contract'] == 'Yes') echo 'selected="selected"'; ?> value="Yes">Yes </option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="grade">Grade + Step</label>
                                 <input type="text" value="<?=$employee['grade'];?>" name="grade" class="form-control" id="grade" />
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="salary">Base Salary</label>
                                <input type="text" value="<?=$employee['salary'];?>"  class="form-control " id="salary" onkeypress="return isNumberKey(event)" name="salary">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="budgetCode">Budget code</label>
                                <select  name="budgetCode" class="form-control "  id="budgetCode">
                                    <option value="">- Select </option>
                                    <?=select_active('budget_codes', ['value' => 'name'], $employee['budget_code']);?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="taxExempt">Tax exempt</label>
                                <select  name="taxExempt" class="form-control" id="taxExempt">
                                    <option <?php if($employee['tax_exempt'] == 'No') echo 'selected="selected"'; ?>  value="No">No </option>
                                    <option <?php if($employee['tax_exempt'] == 'Yes') echo 'selected="selected"'; ?>  value="Yes">Yes </option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="hireDate">Hire Date</label>
                                <input type="text"  class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d', strtotime($employee['hire_date'])); ?>" id="hireDate" name="hireDate">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="currentContract">Current contract start</label>
                                <input type="text"  class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d', strtotime($employee['contract_start'])); ?>" id="currentContract" name="currentContract">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="contractEnd">Current contract end</label>
                                <input type="text"  class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d', strtotime($employee['contract_end'])); ?>" id="contractEnd" name="contractEnd">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="seniority">Seniority</label>
                                 <input type="text" value="<?=$employee['seniority'];?>"  name="seniority" class="form-control" id="seniority" />
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="workDays">Working days/week</label>
                                 <input type="text" value="<?=$employee['work_days'];?>"  name="workDays" class="form-control" id="workDays" />
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="workHours">Working hours/day</label>
                                 <input type="text" value="<?=$employee['work_hours'];?>"  name="workHours" class="form-control" id="workHours" />
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        
                        
                    </div>

                    

                    <p class="bold smt-20" style="margin-bottom: 0px;">Education Information</p>
                    <?php  
                    $get_education = "SELECT * FROM `employee_education` WHERE `employee_id` = $employee_id";
                    $educationData = $GLOBALS['conn']->query($get_education);
                    if($educationData->num_rows > 0) {
                    	while($row = $educationData->fetch_assoc()) { ?>
                    		<div class="row education-row">
		                        <div class="col col-xs-12 col-md-6 col-lg-4">
		                            <div class="form-group">
		                                <label class="label " for="degree">Degree</label>
		                                <input type="text" value="<?=$row['degree'];?>"  class="form-control degree" id="degree" name="degree">
		                                <span class="form-error text-danger">This is error</span>
		                            </div>
		                        </div>
		                        <div class="col col-xs-12 col-md-6 col-lg-3">
		                            <div class="form-group">
		                                <label class="label " for="institution">Institution</label>
		                                <input type="text" value="<?=$row['institution'];?>"  class="form-control institution" id="institution" name="institution">
		                                <span class="form-error text-danger">This is error</span>
		                            </div>
		                        </div>
		                        <div class="col col-xs-12 col-md-6 col-lg-2">
		                            <div class="form-group">
		                                <label class="label " onkeypress="return isNumberKey(event)" for="startYear">Started</label>
		                                <input type="text" value="<?=$row['start_year'];?>"  class="form-control startYear" id="startYear" name="startYear">
		                                <span class="form-error text-danger">This is error</span>
		                            </div>
		                        </div>
		                        <div class="col col-xs-12 col-md-6 col-lg-2">
		                            <div class="form-group">
		                                <label class="label " for="endYear">Graduated</label>
		                                <input type="text" value="<?=$row['graduation_year'];?>" onkeypress="return isNumberKey(event)"  class="form-control endYear" id="endYear" name="endYear">
		                                <span class="form-error text-danger">This is error</span>
		                            </div>
		                        </div>
		                        <div class="col col-xs-12 col-md-6 col-lg-1">
		                            <div class="form-group">
		                                <label class="label ">&nbsp;</label>
		                                <button type="button" class="btn form-control remove-educationRow btn-danger cursor" style="display: block;">
						                	<i class="fa fa-trash"></i>
						                </button>
		                            </div>
		                        </div>
		                    </div>
		            <?php  
                    	}
                    }
                    ?>
                    <div class="row education-row">
                        <div class="col col-xs-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="label " for="degree">Degree</label>
                                <input type="text"  class="form-control degree" id="degree" name="degree">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label " for="institution">Institution</label>
                                <input type="text"  class="form-control institution" id="institution" name="institution">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " onkeypress="return isNumberKey(event)" for="startYear">Started</label>
                                <input type="text"  class="form-control startYear" id="startYear" name="startYear">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="endYear">Graduated</label>
                                <input type="text" onkeypress="return isNumberKey(event)"  class="form-control endYear" id="endYear" name="endYear">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-1">
                            <div class="form-group">
                                <label class="label ">&nbsp;</label>
                                <button type="button" class="btn form-control add-educationRow btn-info cursor" style="color: #fff;" >
                                	<i class="fa fa-plus-square"></i>
                                </button>
                                <!-- <button type="button" class="btn form-control remove-educationRow btn-danger cursor" style="display: none;">
                                	<i class="fa fa-trash"></i>
                                </button> -->
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                    	<div class="col-sm-12 justify-content-end d-flex">
                    		<a href="<?=baseUri();?>/employees" class="btn smr-10 btn-secondary cursor" style="min-width: 100px;">Cancel</a>
                			<button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
                    	</div>
                    </div>
                </div>
            </div>

            
        </form>
		</div>
	</div>

				
</div>

<style type="text/css">
    label.required:after {
        content: "*";
        color: red;
        margin-left: 3px;
    }
</style>


<?php 
// require('org_edit.php');
?>
