<div class="modal fade" data-bs-focus="false" id="edit_goal_tracking" tabindex="-1" role="dialog" aria-labelledby="editGoalTrackingLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction" style="min-width:700px; width: 700px; max-width: 700px;">
        <form class="modal-content" id="editGoalTrackingForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Goal Tracking</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="slcDepartment">Department</label>
                                <select type="text" class="form-control validate slcDepartment" data-msg="Please select department" name="slcDepartment" id="slcDepartment">
                                    <option value="">Select Department</option>
                                    <?php select_active('branches');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="slcGoalType">Goal Type</label>
                                <select type="text" class="form-control validate slcGoalType" data-msg="Please select goal type" name="slcGoalType" id="slcGoalType">
                                    <option value="">Select Goal Type</option>
                                    <?php select_active('goal_types');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="startDate">Start Date</label>
                                <input type="text" readonly class="form-control cursor datepicker validate startDate" data-msg="Please select start date" name="slcStartDate" id="slcStartDate">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="endDate">End Date</label>
                                <input type="text" readonly class="form-control cursor datepicker validate endDate" data-msg="Please select end date" name="slcEndDate" id="slcEndDate">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="subject">Subject</label>
                                <input type="text" class="form-control validate subject" data-msg="Please enter subject" name="subject" id="subject">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="target">Target Achievement</label>
                                <input type="text" class="form-control validate target" data-msg="Please enter target achievement" name="target" id="target">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="description">Description</label>
                                <textarea class="form-control validate description" data-msg="Please enter description" name="description" id="description"></textarea>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="progressRange">Progress</label>
                                <input type="range" class="form-range" id="progressRange" name="progressRange" min="0" max="100" value="0" oninput="updateProgressValue(this.value)">
                                <div><span id="progressValue">0</span> %</div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" id="id">
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
    function updateProgressValue(value) {
        document.getElementById("progressValue").textContent = value;
    }

    // Optional: Initialize value on page load
    document.addEventListener("DOMContentLoaded", function () {
        const slider = document.getElementById("progressRange");
        updateProgressValue(slider.value);
    });
</script>

<style>
    input[type=range].form-range {
        height: 8px;
        background-color: #d3d3d3;
        border-radius: 10px;
        appearance: none;
        width: 100%;
        outline: none;
        padding: 0;
    }

    input[type=range].form-range::-webkit-slider-thumb {
        appearance: none;
        width: 18px;
        height: 18px;
        background: #007bff;
        border-radius: 50%;
        cursor: pointer;
        margin-top: -5px;
    }

    input[type=range].form-range::-moz-range-thumb {
        width: 18px;
        height: 18px;
        background: #007bff;
        border-radius: 50%;
        cursor: pointer;
    }

    input[type=range].form-range::-webkit-slider-runnable-track {
        height: 8px;
        background: linear-gradient(to right, #007bff 0%, #007bff var(--val, 0%), #d3d3d3 var(--val, 0%), #d3d3d3 100%);
        border-radius: 10px;
    }

    input[type=range].form-range:active::-webkit-slider-runnable-track {
        background: linear-gradient(to right, #0056b3 0%, #0056b3 var(--val, 0%), #d3d3d3 var(--val, 0%), #d3d3d3 100%);
    }

    input[type=range].form-range:focus {
        outline: none;
    }

    /* Dynamic fill using JS */
</style>

<script>
    function updateProgressValue(value) {
        const progressValueEl = document.getElementById("progressValue");
        const rangeEl = document.getElementById("progressRange");

        progressValueEl.textContent = value;
        rangeEl.style.setProperty('--val', value + '%');
    }

    document.addEventListener("DOMContentLoaded", function () {
        const slider = document.getElementById("progressRange");
        updateProgressValue(slider.value);
    });
</script>
