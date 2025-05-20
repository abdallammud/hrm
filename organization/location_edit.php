<div class="modal  fade"  data-bs-focus="false" id="edit_location" tabindex="-1" role="dialog" aria-labelledby="edit_locationLabel" aria-hidden="true">
    <div class="modal-dialog" role="location" style="width:500px;">
        <form class="modal-content" id="editLocationForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Edit location info</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="locationName4Edit">location Name</label>
                                <input type="hidden" id="location_id" name="">
                                <input type="text"  class="form-control validate" data-msg="Location name is required" id="locationName4Edit" name="locationName">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="city4Edit">City</label>
                                <input type="text"  class="form-control validate" data-msg="City is required" id="city4Edit" name="city">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="state4Edit">State</label>
                                <select name="state" class="form-control validate" data-msg="Please select state" id="state4Edit">
                                	<option value="">- Select </option>
                                	<?php 
									$states = $GLOBALS['statesClass']->read_all();

									foreach ($states as $state) {
										echo '<option value="'.$state['id'].'"';
										echo '>'.$state['name'].'</option>';
									}

                                	?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-xs-12">
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
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>