<div class="modal fade" data-bs-focus="false" id="edit_expense" tabindex="-1" role="dialog" aria-labelledby="edit_expenseLabel" aria-hidden="true">
    <div class="modal-dialog" role="expense" style="width:500px;">
        <form class="modal-content" id="editExpenseForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Expense</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <input type="hidden" id="edit_id" name="edit_id">
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_slcFinancialAccount">Financial Account</label>
                                <select class="form-control validate" data-msg="Financial account is required" id="edit_slcFinancialAccount" name="edit_slcFinancialAccount">
                                    <option value="">Select Financial Account</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_slcBank">Paid From Bank</label>
                                <select class="form-control validate" data-msg="Bank is required" id="edit_slcBank" name="edit_slcBank">
                                    <option value="">Select Bank Account</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_amount">Amount</label>
                                <input type="number" step="0.01" class="form-control validate" data-msg="Amount is required" id="edit_amount" name="edit_amount">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_paidTo">Paid To</label>
                                <input type="text" class="form-control validate" data-msg="Paid to is required" id="edit_paidTo" name="edit_paidTo">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_paidDate">Paid Date</label>
                                <input type="date" class="form-control validate" data-msg="Paid date is required" id="edit_paidDate" name="edit_paidDate">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="edit_refNumber">Reference Number</label>
                                <input type="text" class="form-control" id="edit_refNumber" name="edit_refNumber">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="edit_description">Description</label>
                                <textarea class="form-control" id="edit_description" name="edit_description" rows="3"></textarea>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="edit_slcStatus">Status</label>
                                <select class="form-control" id="edit_slcStatus" name="edit_slcStatus">
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
                <button type="button" class="btn btn-secondary cursor" data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Update</button>
            </div>
        </form>
    </div>
</div>
