<div class="modal  fade"   data-bs-focus="false" id="edit_indicators" tabindex="-1" role="dialog" aria-labelledby="editIndicatorsLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction"style="min-width:700px; width: 700px; max-width: 700px;">
        <form class="modal-content" id="editIndicatorsForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Indicator</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <input type="hidden" id="edit_indicator_id" name="id">
                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="edit_slcDepartment">Department</label>
                                <select type="text"  class="form-control validate edit_slcDepartment" data-msg="Please select department" name="slcDepartment" id="edit_slcDepartment">
                                    <option value="All"> All</option>
                                    <?php select_active('branches');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="edit_slcDesignation">Designation</label>
                                <select type="text"  class="form-control validate edit_slcDesignation" data-msg="Please select designation" name="slcDesignation" id="edit_slcDesignation">
                                    <option value="All"> All</option>
                                    <?php select_active('designations');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>


                    <p class="bold smt-15">Behavioural Competencies</p>
                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <span class="sml-15">Business Process</span>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="edit_business_pro_5" name="business_pro" value="5">
                                <label for="edit_business_pro_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_business_pro_4" name="business_pro" value="4">
                                <label for="edit_business_pro_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_business_pro_3" name="business_pro" value="3">
                                <label for="edit_business_pro_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_business_pro_2" name="business_pro" value="2">
                                <label for="edit_business_pro_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_business_pro_1" name="business_pro" value="1">
                                <label for="edit_business_pro_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col  col-xs-12 col-md-12 col-lg-6">
                            <span class="sml-15">Oral Communication</span>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="edit_oral_com_5" name="oral_com" value="5">
                                <label for="edit_oral_com_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_oral_com_4" name="oral_com" value="4">
                                <label for="edit_oral_com_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_oral_com_3" name="oral_com" value="3">
                                <label for="edit_oral_com_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_oral_com_2" name="oral_com" value="2">
                                <label for="edit_oral_com_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_oral_com_1" name="oral_com" value="1">
                                <label for="edit_oral_com_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>
                    </div>

                    <p class="bold smt-15">Organizational Competencies</p>
                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <span class="sml-15">Leadership</span>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="edit_leadership_5" name="leadership" value="5">
                                <label for="edit_leadership_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_leadership_4" name="leadership" value="4">
                                <label for="edit_leadership_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_leadership_3" name="leadership" value="3">
                                <label for="edit_leadership_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_leadership_2" name="leadership" value="2">
                                <label for="edit_leadership_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_leadership_1" name="leadership" value="1">
                                <label for="edit_leadership_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <span class="sml-15">Project Management</span>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="edit_project_mgt_5" name="project_mgt" value="5">
                                <label for="edit_project_mgt_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_project_mgt_4" name="project_mgt" value="4">
                                <label for="edit_project_mgt_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_project_mgt_3" name="project_mgt" value="3">
                                <label for="edit_project_mgt_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_project_mgt_2" name="project_mgt" value="2">
                                <label for="edit_project_mgt_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_project_mgt_1" name="project_mgt" value="1">
                                <label for="edit_project_mgt_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>
                    </div>

                    <p class="bold smt-15">Technical Competencies</p>
                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <span class="sml-15">Allocating Resources</span>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="star-rating animated-stars">
                                <input type="radio" id="edit_res_allocating_5" name="res_allocating" value="5">
                                <label for="edit_res_allocating_5" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_res_allocating_4" name="res_allocating" value="4">
                                <label for="edit_res_allocating_4" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_res_allocating_3" name="res_allocating" value="3">
                                <label for="edit_res_allocating_3" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_res_allocating_2" name="res_allocating" value="2">
                                <label for="edit_res_allocating_2" class="bi bi-star-fill"></label>
                                <input type="radio" id="edit_res_allocating_1" name="res_allocating" value="1">
                                <label for="edit_res_allocating_1" class="bi bi-star-fill"></label>
                            </div>
                        </div>
                    </div>

                  
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Update</button>
            </div>
        </form>
    </div>
</div>
