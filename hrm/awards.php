<div class="page content header">
    <div class="page-breadcrumb d-sm-flex align-items-center ">
        <h5 class="spy-10">Awards</h5>
        <div class="ms-auto d-sm-flex">
        	<div class="btn-group smr-10">
	            <button type="button" data-bs-toggle="modal" data-bs-target="#add_award"  class="btn btn-primary">
                    <i class="bi bi-plus"></i>
                    Add
                </button>
	        </div>
        </div>
    </div>
</div>
<div class="page content">
    <div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table id="awardsDT" class="table table-striped table-bordered" style="width:100%">
				</table> 
			</div>
		</div>
	</div>
</div>


<!-- Add award modal -->
<div class="modal fade " data-bs-focus="false" id="add_award" tabindex="-1" role="dialog" aria-labelledby="add_awardLabel" aria-hidden="true">
    <div class="modal-dialog" role="award" style="width:500px;">
        <form class="modal-content" id="addAwardForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title">Add new award</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group attenForDiv">
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

                                            $text = $full_name;

                                            if($phone_number) {
                                                $text .= ", ".$phone_number;
                                            }

                                            echo '<option value="'.$employee_id.'">'.$text.'</option>';
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
                                <label class="label required" for="awardType">Award Type</label>
                                <select type="text"  class="form-control validate" data-msg="Please select award type" name="awardType" id="awardType">
                                    <option value=""> - Select</option>
                                    <?php select_active('award_types');?>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col col-xs-4">
                            <div class="form-group">
                                <label class="label required" for="awardDate">Date</label>
                                <input type="text"  class="form-control cursor datepicker" readonly id="awardDate" value="<?php echo date('Y-m-d'); ?>" name="awardDate">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-8">
                            <div class="form-group">
                                <label class="label required" for="gift">Gift</label>
                                <input type="text"  class="form-control validate" data-msg="Please enter gift title " name="gift" id="gift" placeholder="Enter gift">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label required" for="description">Description</label>
                                <textarea type="text"  class="form-control " name="description" id="description"></textarea>
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