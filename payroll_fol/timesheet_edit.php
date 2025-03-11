<div class="modal  fade"  data-bs-focus="false" id="edit_transaction" tabindex="-1" role="dialog" aria-labelledby="edit_transactionLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction" style="width:500px;">
        <form class="modal-content" id="editTransactionForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Edit transactions</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group ">
                                <label class="label required" for="searchEmployee">Employee</label>
                                <input type="hidden" id="transaction_id" name="">
                                <input type="text" readonly id="employee_name" class="form-control validate" id="transaction_id" name="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="transType4Edit">Transaction type</label>
                                <select  class="form-control validate" data-msg="Please select transaction type" id="transType4Edit" name="transType4Edit">
                                    <option value="">- Select</option>
                                    <option value="Allowance">Allowance</option>
                                    <option value="Bonus">Bonus</option>
                                    <!-- <option value="Commission">Commission</option>
                                    <option value="Advance">Advance</option> -->
                                    <option value="Deduction">Deduction</option>
                                    <!-- <option value="Loan">Loan</option> -->
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="transSubType4Edit">Transaction subtype</label>
                                <!-- <input type="text" name="transSubType4Edit" class="form-control " id="transSubType4Edit" /> -->
                                <select  class="form-control " id="transSubType4Edit" name="transSubType4Edit">
                                    <option value="">None</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="label required" for="transAmount4Edit">Amount</label>
                                <input type="text" name="transAmount4Edit" onkeypress="return isNumberKey(event)" class="form-control " id="transAmount4Edit" />
                                    
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="label required" for="transStatus4Edit">Status</label>
                                <select  class="form-control validate" data-msg="leave type name is required" id="transStatus4Edit" name="transStatus4Edit">
                                    <option value="Request">Request</option>
                                    <?php if(check_session('approve_employee_transactions')) { ?>
                                        <option value="Approved">Approved</option>
                                    <?php } ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>

                        <div class="col col-xs-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="label required" for="transDate4Edit">Date</label>
                                <input type="text" name="transDate4Edit" class="form-control datepicker cursor" readonly value="<?php echo date('Y-m-d'); ?>" id="transDate4Edit" />
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="txtComments4Edit">Comments</label>
                                <input type="text" name="txtComments4Edit" class="form-control " id="txtComments4Edit" />
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