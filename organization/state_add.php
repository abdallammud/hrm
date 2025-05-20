<div class="modal  fade"  data-bs-focus="false" id="add_state" tabindex="-1" role="dialog" aria-labelledby="add_stateLabel" aria-hidden="true">
    <div class="modal-dialog" role="State" style="width:500px;">
        <form class="modal-content" id="addStateForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title" id="editBookLabel">Add State</h5>
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
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="stampDuty">Stamp Duty</label>
                                <input type="text"  class="form-control " id="stampDuty" value="0" name="stampDuty">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <label class="label mt-2" >Tax Grid</label>
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
                    <label class="mt-2 cursor add-tax-grid-row">
                    	<i class="fa fa-plus-square "></i>
                    	Add row
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
            </div>
        </form>
    </div>
</div>