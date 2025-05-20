<div class="modal  fade"  data-bs-focus="false" id="add_leave_type" tabindex="-1" role="dialog" aria-labelledby="add_leave_typeLabel" aria-hidden="true">
    <div class="modal-dialog" role="leave_type" style="width:500px;">
        <form class="modal-content" id="addLeaveTypeForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Add leave type</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="leave_typeName">Type name</label>
                                <input type="text"  class="form-control validate" data-msg="leave type name is required" id="leave_typeName" name="leave_typeName">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcPaidType">Paid Type</label>
                                <select name="slcPaidType" class="form-control " id="slcPaidType">
                                	<option value="Unpaid">Unpaid </option>
                                	<option value="Paid">Paid </option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                                </select>
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