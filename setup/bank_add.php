<div class="modal  fade"  data-bs-focus="false" id="add_bank" tabindex="-1" role="dialog" aria-labelledby="add_bankLabel" aria-hidden="true">
    <div class="modal-dialog" role="bank" style="width:500px;">
        <form class="modal-content" id="addBankForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Add bank account</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="bankName">Bank Name</label>
                                <input type="text"  class="form-control validate" data-msg="Bank name is required" id="bankName" name="bankName">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="account">Account number</label>
                                <input type="text"  class="form-control validate" data-msg="Account number is required" id="account" name="account">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="balance">Begin balance</label>
                                <input type="text" value="0" class="form-control " id="balance" name="balance">
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