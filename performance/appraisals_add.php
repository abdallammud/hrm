<div class="modal show fade" style="display: block;"   data-bs-focus="false" id="add_appraisals" tabindex="-1" role="dialog" aria-labelledby="addAppraisalsLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction"style="min-width:700px; width: 700px; max-width: 700px;">
        <form class="modal-content" id="addAppraisalsForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Create New Appraisal</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-5">
                            <div class="form-group">
                                <label class="label required" for="slcDepartment">Department</label>
                                <select type="text"  class="form-control validate slcDepartment" data-msg="Please select department" name="slcDepartment" id="slcDepartment">
                                    <option value="All"> All</option>
                                    <?php select_active('branches');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label class="label required" for="slcDesignation">Designation</label>
                                <select type="text"  class="form-control validate slcDesignation" data-msg="Please select designation" name="slcDesignation" id="slcDesignation">
                                    <option value="All"> All</option>
                                    <?php select_active('designations');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="txtMonth">Month</label>
                                <input type="month" class="form-control validate" value="<?php echo date('Y-m'); ?>" data-msg="Please enter month" name="txtMonth" id="txtMonth">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group attenForDiv">
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

                                            $text = $full_name;

                                            if($phone_number) {
                                                $text .= ", ".$phone_number;
                                            }

                                            echo '<option value="'.$employee_id.'">'.$text.'</option>';
                                        }
                                    } 

                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="">&nbsp;</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class=" bold">Indicators</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class=" bold">Appraisals</span>
                        </div>
                    </div>
                    <p class="bold smt-15">Behavioural Competencies</p>
                    <div class="row">
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="sml-15">Business Process</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="indicator_business_pro_5" name="indicator_business_pro" value="5">
                                <label for="indicator_business_pro_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_business_pro_4" name="indicator_business_pro" value="4">
                                <label for="indicator_business_pro_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_business_pro_3" name="indicator_business_pro" value="3">
                                <label for="indicator_business_pro_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_business_pro_2" name="indicator_business_pro" value="2">
                                <label for="indicator_business_pro_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_business_pro_1" name="indicator_business_pro" value="1">
                                <label for="indicator_business_pro_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>

                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="appraisal_business_pro_5" name="appraisal_business_pro" value="5">
                                <label for="appraisal_business_pro_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_business_pro_4" name="appraisal_business_pro" value="4">
                                <label for="appraisal_business_pro_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_business_pro_3" name="appraisal_business_pro" value="3">
                                <label for="appraisal_business_pro_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_business_pro_2" name="appraisal_business_pro" value="2">
                                <label for="appraisal_business_pro_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_business_pro_1" name="appraisal_business_pro" value="1">
                                <label for="appraisal_business_pro_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="sml-15">Oral Communication</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="indicator_oral_com_5" name="indicator_oral_com" value="5">
                                <label for="indicator_oral_com_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_oral_com_4" name="indicator_oral_com" value="4">
                                <label for="indicator_oral_com_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_oral_com_3" name="indicator_oral_com" value="3">
                                <label for="indicator_oral_com_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_oral_com_2" name="indicator_oral_com" value="2">
                                <label for="indicator_oral_com_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_oral_com_1" name="indicator_oral_com" value="1">
                                <label for="indicator_oral_com_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>

                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="appraisal_oral_com_5" name="appraisal_oral_com" value="5">
                                <label for="appraisal_oral_com_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_oral_com_4" name="appraisal_oral_com" value="4">
                                <label for="appraisal_oral_com_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_oral_com_3" name="appraisal_oral_com" value="3">
                                <label for="appraisal_oral_com_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_oral_com_2" name="appraisal_oral_com" value="2">
                                <label for="appraisal_oral_com_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_oral_com_1" name="appraisal_oral_com" value="1">
                                <label for="appraisal_oral_com_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>
                    </div>

                    <p class="bold smt-15">Organizational Competencies</p>
                    <div class="row">
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="sml-15">Leadership</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="indicator_leadership_5" name="indicator_leadership" value="5">
                                <label for="indicator_leadership_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_leadership_4" name="indicator_leadership" value="4">
                                <label for="indicator_leadership_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_leadership_3" name="indicator_leadership" value="3">
                                <label for="indicator_leadership_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_leadership_2" name="indicator_leadership" value="2">
                                <label for="indicator_leadership_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_leadership_1" name="indicator_leadership" value="1">
                                <label for="indicator_leadership_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>

                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="appraisal_leadership_5" name="appraisal_leadership" value="5">
                                <label for="appraisal_leadership_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_leadership_4" name="appraisal_leadership" value="4">
                                <label for="appraisal_leadership_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_leadership_3" name="appraisal_leadership" value="3">
                                <label for="appraisal_leadership_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_leadership_2" name="appraisal_leadership" value="2">
                                <label for="appraisal_leadership_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_leadership_1" name="appraisal_leadership" value="1">
                                <label for="appraisal_leadership_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="sml-15">Project Management</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="indicator_project_mgt_5" name="indicator_project_mgt" value="5">
                                <label for="indicator_project_mgt_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_project_mgt_4" name="indicator_project_mgt" value="4">
                                <label for="indicator_project_mgt_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_project_mgt_3" name="indicator_project_mgt" value="3">
                                <label for="indicator_project_mgt_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_project_mgt_2" name="indicator_project_mgt" value="2">
                                <label for="indicator_project_mgt_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_project_mgt_1" name="indicator_project_mgt" value="1">
                                <label for="indicator_project_mgt_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>

                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="appraisal_project_mgt_5" name="appraisal_project_mgt" value="5">
                                <label for="appraisal_project_mgt_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_project_mgt_4" name="appraisal_project_mgt" value="4">
                                <label for="appraisal_project_mgt_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_project_mgt_3" name="appraisal_project_mgt" value="3">
                                <label for="appraisal_project_mgt_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_project_mgt_2" name="appraisal_project_mgt" value="2">
                                <label for="appraisal_project_mgt_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_project_mgt_1" name="appraisal_project_mgt" value="1">
                                <label for="appraisal_project_mgt_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>

                       
                    </div>

                    <p class="bold smt-15">Technical Competencies</p>
                    <div class="row">
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="sml-15">Allocating Resources</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="indicator_res_allocating_5" name="indicator_res_allocating" value="5">
                                <label for="indicator_res_allocating_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_res_allocating_4" name="indicator_res_allocating" value="4">
                                <label for="indicator_res_allocating_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_res_allocating_3" name="indicator_res_allocating" value="3">
                                <label for="indicator_res_allocating_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_res_allocating_2" name="indicator_res_allocating" value="2">
                                <label for="indicator_res_allocating_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="indicator_res_allocating_1" name="indicator_res_allocating" value="1">
                                <label for="indicator_res_allocating_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>

                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="appraisal_res_allocating_5" name="appraisal_res_allocating" value="5">
                                <label for="appraisal_res_allocating_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_res_allocating_4" name="appraisal_res_allocating" value="4">
                                <label for="appraisal_res_allocating_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_res_allocating_3" name="appraisal_res_allocating" value="3">
                                <label for="appraisal_res_allocating_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_res_allocating_2" name="appraisal_res_allocating" value="2">
                                <label for="appraisal_res_allocating_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="appraisal_res_allocating_1" name="appraisal_res_allocating" value="1">
                                <label for="appraisal_res_allocating_1" class="bi bi-star-fill"></label>
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

<style type="text/css">
    .SumoSelect > .CaptionCont {
        background: var(--bs-body-bg-2);;
    }
    .main-wrapper .main-content .options {
        display: flex;
        align-items: center;
        color: #494949;
        border-radius: 0%; 
        transition: all 0.3s;
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        height: fit-content;
        background: var(--bs-body-bg-2);;
    }

    .main-wrapper .main-content .options li {
        flex-basis: 100%;
    }

    .main-wrapper .main-content .options li.opt {
        border-bottom: var(--bs-border-width) solid var(--bs-border-color);
    }
    .main-wrapper .main-content .options li.opt:hover {
        background: var(--bs-body-bg-2);
        opacity: .7;
    }

    .SumoSelect {
        /* width: 98%; */
        flex-basis: 70%;
        border-radius: 5px;
    }
    .SumoSelect > .CaptionCont {
        border: var(--bs-border-width) solid var(--bs-border-color);
        border-radius: 5px;
    } 

    .SumoSelect > .optWrapper.multiple > .options li.opt.selected span i, .SumoSelect .select-all.selected > span i, .SumoSelect .select-all.partial > span i {
        background-color: #2e80f9;
    }

    .star-rating {
        direction: rtl;
        display: inline-block;
        cursor: pointer;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        color: #ddd;
        font-size: 18px;
        padding: 0 2px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .star-rating label:hover,
    .star-rating label:hover~label,
    .star-rating input:checked~label {
        color: #ffc107;
    }
</style>