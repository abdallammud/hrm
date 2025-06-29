<div class="modal fade" data-bs-focus="false" id="edit_training" tabindex="-1" role="dialog" aria-labelledby="edit_trainingLabel" aria-hidden="true">
    <div class="modal-dialog" role="training" style="width:500px;">
        <form class="modal-content" id="editTrainingForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Training</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <input type="hidden" id="edit_training_id" name="edit_training_id">
                    
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label" for="edit_employee_info">Employee</label>
                                <input type="text" class="form-control" id="edit_employee_info" name="edit_employee_info" readonly>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label" for="edit_type_id">Training Type</label>
                                <select class="form-control validate" data-msg="Training type is required" id="edit_type_id" name="edit_type_id">
                                    <option value="">Select Training Type</option>
                                    <?php echo select_active('training_types');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label" for="edit_option_id">Training Options</label>
                                <select class="form-control validate" data-msg="Training options is required" id="edit_option_id" name="edit_option_id">
                                    <option value="">Select Training Options</option>
                                    <?php echo select_active('training_options');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col col-lg-8">
                            <div class="form-group">
                                <label class="label" for="edit_trainer_id">Trainer</label>
                                <select class="form-control validate" data-msg="Trainer is required" id="edit_trainer_id" name="edit_trainer_id">
                                    <option value="">Select Trainer</option>
                                    <?php echo select_active('trainers', ['id' => 'id', 'text' => 'full_name']);?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-lg-4">
                            <div class="form-group">
                                <label class="label" for="edit_cost">Cost</label>
                                <input type="text" onkeypress="return isNumber(event)" class="form-control" id="edit_cost" name="edit_cost">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <!-- Two columns for start date and end date -->
                    <div class="row">
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label" for="edit_start_date">Start Date</label>
                                <input type="date" class="form-control cursor datepicker" readonly id="edit_start_date" name="edit_start_date">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-lg-6">
                            <div class="form-group">
                                <label class="label" for="edit_end_date">End Date</label>
                                <input type="date" class="form-control cursor datepicker" readonly id="edit_end_date" name="edit_end_date">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label" for="edit_description">Description</label>
                                <textarea class="form-control" id="edit_description" name="edit_description"></textarea>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col col-lg-12">
                            <div class="form-group">
                                <label class="label" for="edit_training_status">Status</label>
                                <select class="form-control" id="edit_training_status" name="edit_training_status">
                                    <option value="Active">Active</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
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
