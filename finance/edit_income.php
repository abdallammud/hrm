<?php
// Edit Income Modal - mirrors edit_expense.php
?>
<div class="modal fade" data-bs-focus="false" id="edit_income" tabindex="-1" role="dialog" aria-labelledby="edit_incomeLabel" aria-hidden="true">
    <div class="modal-dialog" role="income" style="width:500px;">
        <form class="modal-content" id="editIncomeForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Income</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <input type="hidden" id="edit_income_id" name="edit_income_id">
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_slcFinancialAccountIncome">Financial Account</label>
                                <select class="form-control validate" data-msg="Financial account is required" id="edit_slcFinancialAccountIncome" name="edit_slcFinancialAccountIncome">
                                    <option value="">Select Financial Account</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_slcBankIncome">Received In Bank</label>
                                <select class="form-control validate" data-msg="Bank is required" id="edit_slcBankIncome" name="edit_slcBankIncome">
                                    <option value="">Select Bank Account</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_amountIncome">Amount</label>
                                <input type="number" step="0.01" class="form-control validate" data-msg="Amount is required" id="edit_amountIncome" name="edit_amountIncome">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_receivedFrom">Received From</label>
                                <input type="text" class="form-control validate" data-msg="Received from is required" id="edit_receivedFrom" name="edit_receivedFrom">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="edit_receivedDate">Received Date</label>
                                <input type="date" class="form-control validate" data-msg="Received date is required" id="edit_receivedDate" name="edit_receivedDate">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="edit_refNumberIncome">Reference Number</label>
                                <input type="text" class="form-control" id="edit_refNumberIncome" name="edit_refNumberIncome">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="edit_descriptionIncome">Description</label>
                                <textarea class="form-control" id="edit_descriptionIncome" name="edit_descriptionIncome" rows="3"></textarea>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="edit_slcStatusIncome">Status</label>
                                <select class="form-control" id="edit_slcStatusIncome" name="edit_slcStatusIncome">
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
