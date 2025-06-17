<div class="modal fade" data-bs-focus="false" id="add_trainer" tabindex="-1" role="dialog" aria-labelledby="add_trainerLabel" aria-hidden="true">
    <div class="modal-dialog" role="trainer" style="width:500px;">
        <form class="modal-content"  id="addTrainerForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Add Trainer</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="trainer_full_name">Full Name</label>
                                <input type="text" class="form-control validate" data-msg="Full name is required" id="trainer_full_name" name="trainer_full_name">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="trainer_phone">Phone</label>
                                <input type="text" class="form-control" id="trainer_phone" name="trainer_phone">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="trainer_email">Email</label>
                                <input type="email" class="form-control validate" data-msg="Email is required" id="trainer_email" name="trainer_email">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="trainer_status">Status</label>
                                <select class="form-control" id="trainer_status" name="trainer_status">
                                    <option value="Active" selected>Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
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
