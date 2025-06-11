
<div class="modal show fade" style="display: block;" data-bs-focus="false" id="edit_appraisals" tabindex="-1" role="dialog" aria-labelledby="editAppraisalsLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction" style="min-width:700px; width: 700px; max-width: 700px;">
        <form class="modal-content" id="editAppraisalsForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="modal-header">
                <h5 class="modal-title">Edit Performance Record</h5>
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
                                <select type="text" class="form-control validate slcDepartment" data-msg="Please select department" name="slcDepartment" id="slcDepartment">
                                    <option value="">Select Department</option>
                                    <option value="All">All</option>
                                    <?php 
                                    $dept_query = "SELECT * FROM branches WHERE status = 'active' ORDER BY name ASC";
                                    $dept_result = $GLOBALS['conn']->query($dept_query);
                                    while($dept = $dept_result->fetch_assoc()) {
                                        $selected = ($dept['id'] == $performance['department_id']) ? 'selected' : '';
                                        echo '<option value="'.$dept['id'].'" '.$selected.'>'.$dept['name'].'</option>';
                                    }
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-4">
                            <div class="form-group">
                                <label class="label required" for="slcDesignation">Designation</label>
                                <select type="text" class="form-control validate slcDesignation" data-msg="Please select designation" name="slcDesignation" id="slcDesignation">
                                    <option value="">Select Designation</option>
                                    <option value="All">All</option>
                                    <?php 
                                    $desg_query = "SELECT * FROM designations WHERE status = 'active' ORDER BY name ASC";
                                    $desg_result = $GLOBALS['conn']->query($desg_query);
                                    while($desg = $desg_result->fetch_assoc()) {
                                        $selected = ($desg['id'] == $performance['designation_id']) ? 'selected' : '';
                                        echo '<option value="'.$desg['id'].'" '.$selected.'>'.$desg['name'].'</option>';
                                    }
                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="txtMonth">Month</label>
                                <input type="month" class="form-control validate" value="<?php echo $performance['month']; ?>" data-msg="Please enter month" name="txtMonth" id="txtMonth">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="txtRemarks">Remarks</label>
                                <textarea name="txtRemarks" id="txtRemarks" class="form-control"><?php echo $performance['remarks']; ?></textarea>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="">&nbsp;</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="bold">Indicators</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="bold">Appraisals</span>
                        </div>
                    </div>

                    <?php
                    $indicator_ratings = json_decode($performance['indicator_rating'], true);
                    $appraisal_ratings = json_decode($performance['appraisal_rating'], true);
                    ?>

                    <p class="bold smt-15">Behavioural Competencies</p>
                    <div class="row">
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="sml-15">Business Process</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="indicator_business_pro_<?php echo $i; ?>" name="indicator_business_pro" value="<?php echo $i; ?>" <?php echo ($indicator_ratings['indicator_business_pro'] == $i) ? 'checked' : ''; ?>>
                                    <label for="indicator_business_pro_<?php echo $i; ?>" class="bi bi-star-fill"></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="appraisal_business_pro_<?php echo $i; ?>" name="appraisal_business_pro" value="<?php echo $i; ?>" <?php echo ($appraisal_ratings['appraisal_business_pro'] == $i) ? 'checked' : ''; ?>>
                                    <label for="appraisal_business_pro_<?php echo $i; ?>" class="bi bi-star-fill"></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="sml-15">Oral Communication</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="indicator_oral_com_<?php echo $i; ?>" name="indicator_oral_com" value="<?php echo $i; ?>" <?php echo ($indicator_ratings['indicator_oral_com'] == $i) ? 'checked' : ''; ?>>
                                    <label for="indicator_oral_com_<?php echo $i; ?>" class="bi bi-star-fill"></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="appraisal_oral_com_<?php echo $i; ?>" name="appraisal_oral_com" value="<?php echo $i; ?>" <?php echo ($appraisal_ratings['appraisal_oral_com'] == $i) ? 'checked' : ''; ?>>
                                    <label for="appraisal_oral_com_<?php echo $i; ?>" class="bi bi-star-fill"></label>
                                <?php endfor; ?>
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
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="indicator_leadership_<?php echo $i; ?>" name="indicator_leadership" value="<?php echo $i; ?>" <?php echo ($indicator_ratings['indicator_leadership'] == $i) ? 'checked' : ''; ?>>
                                    <label for="indicator_leadership_<?php echo $i; ?>" class="bi bi-star-fill"></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="appraisal_leadership_<?php echo $i; ?>" name="appraisal_leadership" value="<?php echo $i; ?>" <?php echo ($appraisal_ratings['appraisal_leadership'] == $i) ? 'checked' : ''; ?>>
                                    <label for="appraisal_leadership_<?php echo $i; ?>" class="bi bi-star-fill"></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <span class="sml-15">Project Management</span>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="indicator_project_mgt_<?php echo $i; ?>" name="indicator_project_mgt" value="<?php echo $i; ?>" <?php echo ($indicator_ratings['indicator_project_mgt'] == $i) ? 'checked' : ''; ?>>
                                    <label for="indicator_project_mgt_<?php echo $i; ?>" class="bi bi-star-fill"></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="appraisal_project_mgt_<?php echo $i; ?>" name="appraisal_project_mgt" value="<?php echo $i; ?>" <?php echo ($appraisal_ratings['appraisal_project_mgt'] == $i) ? 'checked' : ''; ?>>
                                    <label for="appraisal_project_mgt_<?php echo $i; ?>" class="bi bi-star-fill"></label>
                                <?php endfor; ?>
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
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="indicator_res_allocating_<?php echo $i; ?>" name="indicator_res_allocating" value="<?php echo $i; ?>" <?php echo ($indicator_ratings['indicator_res_allocating'] == $i) ? 'checked' : ''; ?>>
                                    <label for="indicator_res_allocating_<?php echo $i; ?>" class="bi bi-star-fill"></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="col col-xs-4 col-md-4 col-lg-4">
                            <div class="star-rating animated-stars">
                                <?php for($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" id="appraisal_res_allocating_<?php echo $i; ?>" name="appraisal_res_allocating" value="<?php echo $i; ?>" <?php echo ($appraisal_ratings['appraisal_res_allocating'] == $i) ? 'checked' : ''; ?>>
                                    <label for="appraisal_res_allocating_<?php echo $i; ?>" class="bi bi-star-fill"></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor" data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle form submission
    $('#editAppraisalsForm').on('submit', function(e) {
        e.preventDefault();
        
        // Collect all indicator and appraisal ratings
        let indicatorRatings = {};
        let appraisalRatings = {};
        
        // Get all indicator ratings
        $('input[name^="indicator_"]').each(function() {
            if($(this).is(':checked')) {
                let name = $(this).attr('name');
                indicatorRatings[name] = $(this).val();
            }
        });
        
        // Get all appraisal ratings
        $('input[name^="appraisal_"]').each(function() {
            if($(this).is(':checked')) {
                let name = $(this).attr('name');
                appraisalRatings[name] = $(this).val();
            }
        });

        // Calculate overall rating
        let overallRating = calculateOverallRating(indicatorRatings, appraisalRatings);

        // Prepare form data
        let formData = {
            action: 'update_performance',
            id: $('input[name="id"]').val(),
            department_id: $('#slcDepartment').val(),
            designation_id: $('#slcDesignation').val(),
            department: $('#slcDepartment option:selected').text(),
            designation: $('#slcDesignation option:selected').text(),
            indicator_rating: JSON.stringify(indicatorRatings),
            appraisal_rating: JSON.stringify(appraisalRatings),
            overall_rating: overallRating,
            month: $('#txtMonth').val(),
            remarks: $('#txtRemarks').val()
        };

        // Submit form
        $.ajax({
            url: 'performance/performance_controller.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    toastr.success('Performance record updated successfully');
                    window.location.href = '../performance.php';
                } else {
                    toastr.error(response.message || 'Error updating performance record');
                }
            },
            error: function() {
                toastr.error('Error updating performance record');
            }
        });
    });

    // Calculate average rating from ratings object
    function calculateAverageRating(ratings) {
        let sum = 0;
        let count = 0;
        for(let key in ratings) {
            sum += parseInt(ratings[key]);
            count++;
        }
        return count > 0 ? (sum / count).toFixed(1) : 0;
    }

    // Calculate overall rating
    function calculateOverallRating(indicatorRatings, appraisalRatings) {
        let indicatorAvg = calculateAverageRating(indicatorRatings);
        let appraisalAvg = calculateAverageRating(appraisalRatings);
        return ((parseFloat(indicatorAvg) + parseFloat(appraisalAvg)) / 2).toFixed(1);
    }
});
</script>

<style type="text/css">
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
