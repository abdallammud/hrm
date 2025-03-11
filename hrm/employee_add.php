<div class="page content">
	<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <h5 class="">Add New Employee</h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-10">
                <a href="<?=baseUri();?>/employees"  class="btn btn-secondary">Go Back</a>
            </div>            
        </div>
    </div>
    <hr>
    <div class="card">
		<div class="card-body">
			<form class="modal-content" id="addEmployeeForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	
            <div class="modal-body">
                <div id="">
                	<p class="bold smt-10">Employee Information</p>
                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-5">
                            <div class="form-group">
                                <label class="label required" for="full-name">Employee Name</label>
                                <input type="text"  class="form-control validate" data-msg="Employee full name is required" id="full-name" name="full-name">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="phone">Phone Number</label>
                                <!-- <input type="text"  class="form-control validate" data-msg="Phone number is required" id="phone" name="phone"> -->
                                <input type="text"  class="form-control " id="phone" name="phone">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="label required" for="email">Email</label>
                                <!-- <input type="email"  class="form-control validate" id="email" name="email" data-msg="Email is rquired"> -->
                                <input type="email"  class="form-control " id="email" name="email">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label" for="staffNo">Staff Number</label>
                                <input type="text" value="<?=sys_setting('staff_prefix');?>"  class="form-control " id="staffNo" name="staffNo">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label" for="nationalID">ID Number</label>
                                <input type="text"   class="form-control " id="nationalID" name="nationalID">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="gender">Gender </label>
                                <select  class="form-control validate" id="gender" name="gender" data-msg="Please select gender">
                                	<option value="">- Select</option>
                                	<option value="Male">Male</option>
                                	<option value="Female">Female</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="dob">Date Of Birth</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="dob" value="<?php echo date('Y-m-d', strtotime("-18 years")); ?>" name="dob">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label " for="address">Address</label>
                                <input type="text"  class="form-control " id="address" name="address">
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
									select_active('states');
                                	?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label " for="city">City</label>
                                <input type="text"  class="form-control " id="city" name="city">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label " for="bankName">Bank Account</label>
                                <!-- <input type="text"  class="form-control " id="bankName" name="bankName" placeholder="Bank name"> -->
                                <select  name="bankName" class="form-control "  id="bankName">
                                    <option value="">- Select </option>
                                    <?php 
                                    select_active('banks', ['value' => 'name']);
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                               <label class="label ">Account Number</label>
                                <input type="text"  class="form-control " id="accountNo" name="accountNo" >
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                       
                    </div>

                    <p class="bold smt-20" style="margin-bottom: 0px;">Contract Information</p>
                    <div class="row">
                    	<div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="position">Position</label>
                                <input type="text"  class="form-control validate" data-msg="Please provide employee position/job title" id="position" name="position">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="dep"><?=$GLOBALS['branch_keyword']['sing'];?></label>
                                <select  name="dep" class="form-control validate" data-msg="Please select <?=$GLOBALS['branch_keyword']['sing'];?>" id="dep">
                                	<option value="">- Select <?=$GLOBALS['branch_keyword']['sing'];?></option>
                                	<?php 
                                	select_active('branches');
                                	?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="dutyStation">Duty Station/Health facility</label>
                                <select  name="dutyStation" class="form-control validate" id="dutyStation" data-msg="Please select duty station">
                                    <option value="">- Select </option>
                                    <option value="All">All</option>
                                    <?php 
                                    select_active('locations');
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label " for="project">Project</label>
                                <input type="hidden" id="designation" value="" name="">
                                <select  name="project" class="form-control " id="project">
                                    <option value="">- Select</option>
                                    <?php 
                                    select_active('projects');
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
                                    select_active('designations', ['value' => 'name']);
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="contractType">Contract Type</label>
                                <select  name="contractType" class="form-control validate" data-msg="Please select contract type" id="contractType">
                                	<option value="">- Select </option>
                                	<?=select_active('contract_types', ['value' => 'name']);?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="mohContract">MoH Contract</label>
                                <select  name="mohContract" class="form-control" id="mohContract">
                                    <option value="No">No </option>
                                    <option value="Yes">Yes </option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="grade">Grade + Step</label>
                                 <input type="text"  name="grade" class="form-control" id="grade" />
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="salary">Base Salary</label>
                                <input type="text" class="form-control " id="salary" onkeypress="return isNumberKey(event)" name="salary">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="budgetCode">Budget code</label>
                                <select  name="budgetCode" class="form-control "  id="budgetCode">
                                    <option value="">- Select </option>
                                    <?=select_active('budget_codes', ['value' => 'name']);?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="taxExempt">Tax exempt</label>
                                <select  name="taxExempt" class="form-control" id="taxExempt">
                                    <option value="No">No </option>
                                    <option value="Yes">Yes </option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="hireDate">Hire Date</label>
                                <input type="text"  class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d'); ?>" id="hireDate" name="hireDate">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="currentContract">Current contract start</label>
                                <input type="text"  class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d'); ?>" id="currentContract" name="currentContract">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="contractEnd">Current contract end</label>
                                <input type="text"  class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d'); ?>" id="contractEnd" name="contractEnd">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="seniority">Seniority</label>
                                 <input type="text"  name="seniority" class="form-control" id="seniority" />
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="workDays">Working days/week</label>
                                 <input type="text" onkeypress="return isNumberKeyWihtLimit(event, 7)" value="<?=sys_setting('working_days');?>"  name="workDays" class="form-control" id="workDays" />
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label " for="workHours">Working hours/day</label>
                                 <input type="text" value="<?=sys_setting('working_hours');?>"  name="workHours" onkeypress="return isNumberKeyWihtLimit(event, 24)" class="form-control" id="workHours" />
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        
                        
                    </div>

                    

                    <p class="bold smt-20" style="margin-bottom: 0px;">Education Information</p>
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
                			<button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
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
