<div class="modal  fade"  data-bs-focus="false" id="add_transaction" tabindex="-1" role="dialog" aria-labelledby="add_transactionLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction" style="width:500px;">
        <form class="modal-content" id="addTransactionForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Add transactions</h5>
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
                                <select class="my-select searchEmployee" name="searchEmployee" id="searchEmployee" data-live-search="true" title="Search and select empoyee">
                                <?php 
                                $query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                                $empSet = $GLOBALS['conn']->query($query);
                                if($empSet->num_rows > 0) {
                                    while($row = $empSet->fetch_assoc()) {
                                        $employee_id = $row['employee_id'];
                                        $full_name = $row['full_name'];
                                        $phone_number = $row['phone_number'];

                                        echo '<option value="'.$employee_id.'">'.$full_name.', '.$phone_number.'</option>';
                                    }
                                } 

                                ?>
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="transType">Transaction type</label>
                                <select  class="form-control validate" data-msg="Please select transaction type" id="transType" name="transType">
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
                                <label class="label required" for="transSubType">Transaction subtype</label>
                                <!-- <input type="text" name="transSubType" class="form-control " id="transSubType" /> -->
                                <select  class="form-control " id="transSubType" name="transSubType">
                                    <option value="">None</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="label required" for="transAmount">Amount</label>
                                <input type="text" name="transAmount" onkeypress="return isNumberKey(event)" class="form-control " id="transAmount" />
                                    
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="label required" for="transStatus">Status</label>
                                <select  class="form-control validate" data-msg="leave type name is required" id="transStatus" name="transStatus">
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
                                <label class="label required" for="transDate">Date</label>
                                <input type="text" name="transDate" class="form-control datepicker cursor" readonly value="<?php echo date('Y-m-d'); ?>" id="transDate" />
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="txtComments">Comments</label>
                                <input type="text" name="txtComments" class="form-control " id="txtComments" />
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

<!-- Download upload file -->
<div class="modal  fade"   data-bs-focus="false" id="download_transactionUploadFile" tabindex="-1" role="dialog" aria-labelledby="download_transactionUploadFileLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction" style="width:500px;">
        <form class="modal-content" id="downloadTransactionUploadForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Download sample file</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="slcTransFor">Transaction for</label>
                                <select type="text"  class="form-control validate slcTransFor" data-msg="Please select transaction for" name="slcTransFor" id="slcTransFor">
                                    <option value="All"> All</option>
                                    <option value="Department"> Department</option>
                                    <option value="Location"> Duty Location</option>
                                    <option value="Emplployee"> Emplployee</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group attenForDiv">
                                
                            </select>
                            </div>
                        </div>
                    </div>
                   

                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="transDate4Download">Date</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="transDate4Download" value="<?php echo date('Y-m-d'); ?>" name="transDate4Download">
                                <span class="form-error text-danger">This is error</span>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Download File</button>
            </div>
        </form>
    </div>
</div>

<!-- Upload transaction -->
<div class="modal fade"   data-bs-focus="false" id="transaction_upload" tabindex="-1" role="dialog" aria-labelledby="transaction_uploadLabel" aria-hidden="true">
    <div class="modal-dialog" role="transaction_upload" style="width:500px;">
        <form class="modal-content" id="transaction_uploadForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title">Upload transaction</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <label class="cursor col col-xs-12 col-md-12">
                            <input class="form-control py-2" id="transaction_uploadInput"  type="file" name="">
                            <span class="file-selected-name"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Upload</button>
            </div>
        </form>
    </div>
</div>