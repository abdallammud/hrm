<div class="modal fade" data-bs-focus="false" id="edit_trainer" tabindex="-1" role="dialog" aria-labelledby="edit_trainerLabel" aria-hidden="true">
    <div class="modal-dialog" role="trainer" style="width:500px;">
        <form class="modal-content" id="editTrainerForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Trainer</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <input type="hidden" id="edit_trainer_id" name="edit_trainer_id">
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_trainer_full_name">Full Name</label>
                                <input type="text" class="form-control validate" data-msg="Full name is required" id="edit_trainer_full_name" name="edit_trainer_full_name">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="edit_trainer_phone">Phone</label>
                                <input type="text" class="form-control" id="edit_trainer_phone" name="edit_trainer_phone">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_trainer_email">Email</label>
                                <input type="email" class="form-control validate" data-msg="Email is required" id="edit_trainer_email" name="edit_trainer_email">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="edit_trainer_status">Status</label>
                                <select class="form-control" id="edit_trainer_status" name="edit_trainer_status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Suspended</option>
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
