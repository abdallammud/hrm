<div class="modal  fade"   data-bs-focus="false" id="add_goal_tracking" tabindex="-1" role="dialog" aria-labelledby="addGoalTrackingLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction"style="min-width:700px; width: 700px; max-width: 700px;">
        <form class="modal-content" id="addGoalTrackingForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Create New Goal Tracking</h5>
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
                                <select type="text"  class="form-control validate slcDepartment" data-msg="Please select department" name="slcDepartment" id="slcDepartment">
                                    <option value=""> Select Department</option>
                                    <?php select_active('branches');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="slcGoalType">Goal Type</label>
                                <select type="text"  class="form-control validate slcGoalType" data-msg="Please select goal type" name="slcGoalType" id="slcGoalType">
                                    <option value=""> Select Goal Type</option>
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
                                <input type="text" readonly value="<?php echo date('Y-m-d'); ?>" class="form-control cursor datepicker validate startDate" data-msg="Please select start date" name="slcStartDate" id="slcStartDate">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                <label class="label required" for="endDate">End Date</label>
                                <input type="text" readonly value="<?php echo date('Y-m-d'); ?>" class="form-control cursor datepicker validate endDate" data-msg="Please select end date" name="slcEndDate" id="slcEndDate">
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
                                <input type="text" class="form-control  target"  name="target" id="target">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                       
                    </div>

                    <div class="row">
                        <div class="col col-xs-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label class="label required" for="description">Description</label>
                                <textarea class="form-control description" name="description" id="description"></textarea>
                                <span class="form-error text-danger">This is error</span>
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

