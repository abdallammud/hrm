<div class="modal  fade"  data-bs-focus="false" id="edit_state" tabindex="-1" role="dialog" aria-labelledby="edit_stateLabel" aria-hidden="true">
    <div class="modal-dialog" role="State" style="width:500px;">
        <form class="modal-content" id="editStateForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title" >Edit State</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="stateName">State Name</label>
                                <input type="text"  class="form-control validate" data-msg="State name is required" id="stateName" name="stateName">
                                <input type="hidden" id="state_id" name="">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="stateCountry">Country</label>
                                <select name="stateCountry" class="form-control" id="stateCountry">
                                	<option value="">- Select </option>
                                	<?php 
									$countries = $GLOBALS['countryClass']->read_all();

									foreach ($countries as $country) {
										echo '<option value="'.$country['country_id'].'"';
										if($country['is_default'] == 'Yes') echo 'selected="selected"';
										echo '>'.$country['country_name'].'</option>';
									}

                                	?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-6">
                            <div class="form-group">
                                <label class="label required" for="stampDuty">Stamp duty</label>
                                <input type="text"  class="form-control validate" data-msg="State name is required" id="stampDuty" name="stampDuty">
                                <input type="hidden" id="state_id" name="">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-6">
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
                    <label class="label mt-2" >Tax Grid</label>
                    <div class="tax-gridRows">
	                    <div class="row tax-grid-row" style="margin-top: 2px;">
	                        <div class="col-sm-4">
	                        	<label class="label required">Min amount</label>
	                        	<input type="text" onkeypress="return isNumberKey(event)" class="form-control min-amount col-sm-4 col-lg-4">
	                        </div>
	                        <div class="col-sm-4">
	                        	<label class="label required">Max amount</label>
	                        	<input type="text" onkeypress="return isNumberKey(event)" class="form-control max-amount col-sm-4 col-lg-4">
	                        </div>
	                        <div class="col-sm-3">
	                        	<label class="label required">Rate</label>
	                        	<input type="text" onkeypress="return isNumberKey(event)" class="form-control rate col-sm-4 col-lg-4">
	                        	
	                        </div>
	                        <div class="col-sm-1">
	                        	<label class="label required">&nbsp;</label>
	                        	<i class="fa fa-trash-alt remove-tax-grid-row cursor mt-2"></i>
	                        </div>
	                        
	                    </div>
                    </div>
                    <label class="mt-2 cursor add-tax-grid-row">
                    	<i class="fa fa-plus-square "></i>
                    	Add row
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>