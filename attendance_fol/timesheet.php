<div class="row">
    <div class="col-md-12 col-lg-12">
		<div class="page content">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
		        <h5 class="">Timesheet </h5>
		        <div class="ms-auto d-sm-flex">
		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#download_timesheetUploadFile"  class="btn btn-secondary">Download Timesheet Upload File</button>
		            </div>

		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#timesheet_upload"  class="btn btn-primary">Upload Timesheet</button>
		            </div>


		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#add_timesheet"  class="btn btn-primary">Add Record</button>
		            </div>
		        </div>
		    </div>
		    <hr>
		    
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table id="timesheetDT" class="table table-striped table-bordered" style="width:100%">
							
						</table> 
					</div>
				</div>
			</div>
		</div>
	</div>


</div>

<script type="text/javascript">

	
</script>



<?php 
require('timesheet_add.php');
require('timesheet_edit.php');
?>
