<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Payroll Payment</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    <input type="hidden" id="payroll_id" name="payroll_id">
                    <input type="hidden" id="payroll_detIds" name="payroll_detIds">
                    <input type="hidden" id="payroll_detId" name="payroll_detId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="slcBank" class="required">Bank Account</label>
                                <select class="form-control" id="slcBank" name="slcBank" required>
                                    <option value="">Select bank account</option>
                                </select>
                                <!-- <small class="form-text text-muted">Select the bank account to pay from</small> -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payDate" class="required">Payment Date</label>
                                <input type="date" class="form-control" id="payDate" name="payDate" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert smt-10 alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Note:</strong> The payment amount will be automatically calculated based on the selected payroll entries. The bank balance will be updated accordingly.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-dollar-sign"></i> Process Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>