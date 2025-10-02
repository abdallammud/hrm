<?php
// add_employee.php - included in layout
?>
<div class="page content">
    <div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <h5>Add New Employee</h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-10">
                <a href="<?= baseUri(); ?>/employees" class="btn btn-secondary">Go Back</a>
            </div>
        </div>
    </div>
    <hr>
    <div class="card">
        <form id="addEmployeeForm" class="card-body" method="post" action="<?= baseUri(); ?>/employees/store">
            <div class="modal-content" style="border-radius: 14px 14px 0 0; margin-top: -15px;">
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="employeeTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="emp-info-tab" data-bs-toggle="tab" data-bs-target="#emp-info" type="button" role="tab" aria-controls="emp-info" aria-selected="true">Employee Info</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="organization-tab" data-bs-toggle="tab" data-bs-target="#organization" type="button" role="tab" aria-controls="organization" aria-selected="false">Organization</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contract-info-tab" data-bs-toggle="tab" data-bs-target="#contract-info" type="button" role="tab" aria-controls="contract-info" aria-selected="false">Contract Info</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="education-tab" data-bs-toggle="tab" data-bs-target="#education" type="button" role="tab" aria-controls="education" aria-selected="false">Education</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="employeeTabContent">
                        <div class="tab-pane fade show active" id="emp-info" role="tabpanel" aria-labelledby="emp-info-tab">
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-5">
                                    <div class="form-group">
                                        <label class="label" for="full-name">Employee Name</label>
                                        <input type="text" class="form-control validate" data-msg="Employee full name is " id="full-name" name="full-name">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="phone">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label class="label" for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="staffNo">Staff Number</label>
                                        <input type="text" value="<?=sys_setting('staff_prefix');?>" class="form-control" id="staffNo" name="staffNo">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="nationalID">ID Number</label>
                                        <input type="text" class="form-control" id="nationalID" name="nationalID">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="gender">Gender</label>
                                        <select name="gender" class="form-control" data-msg="Please select gender" id="gender">
                                            <option value="">- Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="dob">Date Of Birth</label>
                                        <input type="text" class="form-control cursor datepicker" readonly id="dob" value="<?php echo date('Y-m-d', strtotime("-18 years")); ?>" name="dob">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="address">Address</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="state">State</label>
                                        <select name="state" class="form-control" data-msg="Please select state" id="state">
                                            <option value="">- Select </option>
                                            <?php
                                            select_active('states');
                                            ?>
                                        </select>
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="city">City</label>
                                        <input type="text" class="form-control" id="city" name="city">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="bankName">Bank Account</label>
                                        <select name="bankName" class="form-control" id="bankName">
                                            <option value="">- Select </option>
                                            <?php
                                            select_active('banks', ['value' => 'name']);
                                            ?>
                                        </select>
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label">Account Number</label>
                                        <input type="text" class="form-control" id="accountNo" name="accountNo">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="organization" role="tabpanel" aria-labelledby="organization-tab">
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label class="label" for="dep"><?=$GLOBALS['branch_keyword']['sing'];?></label>
                                        <select name="dep" class="form-control" data-msg="Please select <?=$GLOBALS['branch_keyword']['sing'];?>" id="dep">
                                            <option value="">- Select <?=$GLOBALS['branch_keyword']['sing'];?></option>
                                            <?php select_active('branches'); ?>
                                        </select>
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label class="label" for="dutyStation">Duty Station/Health facility</label>
                                        <select name="dutyStation" class="form-control" id="dutyStation" data-msg="Please select duty station">
                                            <option value="">- Select </option>
                                            <option value="All">All</option>
                                            <?php select_active('locations'); ?>
                                        </select>
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label class="label" for="project">Project</label>
                                        <input type="hidden" id="designation" value="" name="">
                                        <select class="my-select project" name="project" multiple id="project" data-live-search="true" title="Select projects">
                                            <?php select_active('projects'); ?>
                                        </select>
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label class="label" for="budgetCode">Budget code</label>
                                        <select class="my-select budgetCode" name="budgetCode" multiple id="budgetCode" data-live-search="true" title="Select budget codes">
                                            <?=select_active('budget_codes');?>
                                        </select>
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label class="label" for="position">Position</label>
                                        <input type="text" class="form-control" data-msg="Please provide employee position/job title" id="position" name="position">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="contract-info" role="tabpanel" aria-labelledby="contract-info-tab">
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="contractType">Contract Type</label>
                                        <select name="contractType" class="form-control" data-msg="Please select contract type" id="contractType">
                                            <option value="">- Select </option>
                                            <?=select_active('contract_types', ['value' => 'name']);?>
                                        </select>
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="mohContract">MoH Contract</label>
                                        <select name="mohContract" class="form-control" id="mohContract">
                                            <option value="No">No </option>
                                            <option value="Yes">Yes </option>
                                        </select>
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="grade">Grade + Step</label>
                                        <input type="text" name="grade" class="form-control" id="grade" />
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="salary">Base Salary</label>
                                        <input type="text" class="form-control" id="salary" onkeypress="return isNumberKey(event)" name="salary">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="taxExempt">Tax exempt</label>
                                        <select name="taxExempt" class="form-control" id="taxExempt">
                                            <option value="No">No </option>
                                            <option value="Yes">Yes </option>
                                        </select>
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="hireDate">Hire Date</label>
                                        <input type="text" class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d'); ?>" id="hireDate" name="hireDate">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="currentContract">Current contract start</label>
                                        <input type="text" class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d'); ?>" id="currentContract" name="currentContract">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="contractEnd">Current contract end</label>
                                        <input type="text" class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d'); ?>" id="contractEnd" name="contractEnd">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="seniority">Seniority</label>
                                        <input type="text" name="seniority" class="form-control" id="seniority" />
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="workDays">Working days/week</label>
                                        <input type="text" onkeypress="return isNumberKeyWihtLimit(event, 7)" value="<?=sys_setting('working_days');?>" name="workDays" class="form-control" id="workDays" />
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="workHours">Working hours/day</label>
                                        <input type="text" value="<?=sys_setting('working_hours');?>" name="workHours" onkeypress="return isNumberKeyWihtLimit(event, 24)" class="form-control" id="workHours" />
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education-tab">
                            <p class="bold smt-20" style="margin-bottom: 0;">Education Information</p>
                            <div class="row education-row">
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="form-group">
                                        <label class="label" for="degree">Degree</label>
                                        <input type="text" class="form-control degree" id="degree" name="degree">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-3">
                                    <div class="form-group">
                                        <label class="label" for="institution">Institution</label>
                                        <input type="text" class="form-control institution" id="institution" name="institution">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-2">
                                    <div class="form-group">
                                        <label class="label" onkeypress="return isNumberKey(event)" for="startYear">Started</label>
                                        <input type="text" class="form-control startYear" id="startYear" name="startYear">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-2">
                                    <div class="form-group">
                                        <label class="label" for="endYear">Graduated</label>
                                        <input type="text" onkeypress="return isNumberKey(event)" class="form-control endYear" id="endYear" name="endYear">
                                        <span class="form-error text-danger">This is error</span>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-1">
                                    <div class="form-group">
                                        <label class="label">&nbsp;</label>
                                        <button type="button" class="btn form-control add-educationRow btn-info cursor" style="color: #fff;">
                                            <i class="fa fa-plus-square"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12 d-flex justify-content-end">
                        <a href="<?= baseUri(); ?>/employees" class="btn btn-secondary smr-10" style="min-width:100px;">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="min-width:100px;">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    label.required:after {
        content: "*";
        color: red;
        margin-left: 3px;
    }

    .dropdown.bootstrap-select{
        width: 100% !important;
    }

    .education-row.added .col-12 {
        margin-bottom:10px;
    }
</style>

<?php
// require('org_edit.php');
?>
