<div class="modal  fade"  data-bs-focus="false" id="edit_leaveType" tabindex="-1" role="dialog" aria-labelledby="edit_leaveTypeLabel" aria-hidden="true">
    <div class="modal-dialog" role="leaveType" style="width:500px;">
        <form class="modal-content" id="editLeaveTypeForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit leave type </h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="leave_typeName4Edit">Type name</label>
                                <input type="hidden" id="leave_typeID" name="">
                                <input type="text"  class="form-control validate" data-msg="leave type name is required" id="leave_typeName4Edit" name="leave_typeName4Edit">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcPaidType4Edit">Paid Type</label>
                                <select name="slcPaidType4Edit" class="form-control " id="slcPaidType4Edit">
                                	<option value="Unpaid">Unpaid </option>
                                	<option value="Paid">Paid </option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcStatus">Status</label>
                                <select  class="form-control " id="slcStatus" name="slcStatus">
                                    <option value="Active">Active</option>
                                    <option value="Suspended">Suspended</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>