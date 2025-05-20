<div class="modal fade"  data-bs-focus="false" id="pay_payroll" tabindex="-1" role="dialog" aria-labelledby="pay_payrollLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction" style="width:500px;">
        <form class="modal-content" id="payPayrollForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Pay  payroll</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcBank">Select bank account</label>
                                <input type="hidden" id="payroll_id" name="">
                                <input type="hidden" id="payroll_detId" name="">
                                <select  class="form-control validate" data-msg="Please select transaction type" id="slcBank" name="slcBank">
                                    <option value="">- Select</option>
                                    <?php 
                                    $banks = $GLOBALS['conn']->query("SELECT * FROM `bank_accounts` WHERE `status` = 'active'");
                                    if($banks) {
                                    	while($row = $banks->fetch_assoc()) {
                                    		echo '<option value="'.$row['id'].'">'.$row['bank_name'].', '.$row['account'].'</option>';
                                    	}
                                    }

                                    ?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="payDate">Pay date</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="payDate" value="<?php echo date('Y-m-d'); ?>" name="payDate">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Pay</button>
            </div>
        </form>
    </div>
</div>