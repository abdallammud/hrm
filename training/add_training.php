<div class="modal fade" data-bs-focus="false" id="add_training" tabindex="-1" role="dialog" aria-labelledby="add_trainingLabel" aria-hidden="true">
    <div class="modal-dialog" role="training" style="width:500px;">
        <form class="modal-content"  id="addTrainingForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Add Training</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col col-lg-12">
                        <div class="form-group">
                            <label class="label required" for="searchEmployee">Employee</label>
                            <select multiple class="my-select searchEmployee" name="searchEmployee" id="searchEmployee" data-live-search="true" title="Search and select empoyee">
                                <?php 
                                    $query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                                    $empSet = $GLOBALS['conn']->query($query);
                                    if($empSet->num_rows > 0) {
                                        while($row = $empSet->fetch_assoc()) {
                                            $employee_id = $row['employee_id'];
                                            $full_name = $row['full_name'];
                                            $phone_number = $row['phone_number'];

                                            echo '<option value="'.$employee_id.'">'.$full_name.', '.$phone_number.'</option>';
                                        }
                                    } 

                                    ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label" for="training_type">Training Type</label>
                            <select class="form-control validate" data-msg="Training type is required" id="training_type" name="training_type">
                                <option value="">Select Training Type</option>
                                <?php echo select_active('training_types');?>
                            </select>
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label" for="training_options">Training Options</label>
                            <select class="form-control validate" data-msg="Training options is required" id="training_options" name="training_options">
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
                            <label class="label" for="trainer">Trainer</label>
                            <select class="form-control validate" data-msg="Trainer is required" id="trainer" name="trainer">
                                <option value="">Select Trainer</option>
                                <?php echo select_active('trainers', ['id' => 'id', 'text' => 'full_name']);?>
                            </select>
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                    <div class="col col-lg-4">
                        <div class="form-group">
                            <label class="label" for="cost">Cost</label>
                            <input type="text" onkeypress="return isNumber(event)" class="form-control validate" data-msg="Cost is required" id="cost" name="cost">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>

                <!-- Tow columns for start date and end date -->
                <div class="row">
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label" for="start_date">Start Date</label>
                            <input type="date" class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d'); ?>" id="start_date" name="start_date">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                    <div class="col col-lg-6">
                        <div class="form-group">
                            <label class="label" for="end_date">End Date</label>
                            <input type="date" class="form-control cursor datepicker" readonly value="<?php echo date('Y-m-d'); ?>" id="end_date" name="end_date">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="row">
                    <div class="col col-lg-12">
                        <div class="form-group">
                            <label class="label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor" data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
            </div>
        </form>
    </div>
</div>
