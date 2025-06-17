<div class="modal fade" data-bs-focus="false" id="add_expense" tabindex="-1" role="dialog" aria-labelledby="add_expenseLabel" aria-hidden="true">
    <div class="modal-dialog" role="expense" style="width:500px;">
        <form class="modal-content"  id="addExpenseForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Add Expense</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcFinancialAccount">Financial Account</label>
                                <select class="form-control validate" data-msg="Financial account is required" id="slcFinancialAccount" name="slcFinancialAccount">
                                    <option value="">Select Financial Account</option>
                                    <!-- Get financial accounts with the type 'expense' -->
                                    <?php 
                                    $financialAccounts = get_data('financial_accounts', ['type' => 'Expense', 'status' => 'Active']);
                                    ?>
                                    <?php foreach ($financialAccounts as $account): ?>
                                        <option value="<?= $account['id'] ?>"><?= $account['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcBank">Paid From Bank</label>
                                <select class="form-control validate" data-msg="Bank is required" id="slcBank" name="slcBank">
                                    <option value="">Select Bank Account</option>
                                    <?php 
                                    $banks = get_data('bank_accounts', ['status' => 'Active']);
                                    ?>
                                    <?php foreach ($banks as $bank): ?>
                                        <option value="<?= $bank['id'] ?>"><?= $bank['bank_name'] ?>, <?= $bank['account'] ?> (Balance: <?= formatMoney($bank['balance']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-6">
                            <div class="form-group">
                                <label class="label required" for="amount">Amount</label>
                                <input type="text" step="0.01" class="form-control validate" data-msg="Amount is required" onkeypress="return isNumberKey(event)" id="amount" name="amount">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-6">
                            <div class="form-group">
                                <label class="label" for="refNumber">Reference Number</label>
                                <input type="text" class="form-control" id="refNumber" name="refNumber">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="paidTo">Paid To</label>
                                <input type="text" class="form-control validate" data-msg="Paid to is required" id="paidTo" name="paidTo">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="paidDate">Paid Date</label>
                                <input type="date" class="form-control validate" data-msg="Paid date is required" id="paidDate" name="paidDate" value="<?= date('Y-m-d') ?>">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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
