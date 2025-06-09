<div class="page content header">
    <div class="page-breadcrumb d-sm-flex align-items-center ">
        <h5 class="spy-10">Timesheet</h5>
        <div class="ms-auto d-sm-flex">
			<div class="btn-group smr-10">
				<button type="button" data-bs-toggle="modal" data-bs-target="#download_timesheetUploadFile"  class="btn btn-secondary">Download Timesheet Upload File</button>
			</div>

			<div class="btn-group smr-10">
				<button type="button" data-bs-toggle="modal" data-bs-target="#timesheet_upload"  class="btn btn-primary">
					<i class="bi bi-upload"></i>
					Upload
				</button>
			</div>


			<div class="btn-group smr-10">
				<button type="button" data-bs-toggle="modal" data-bs-target="#add_timesheet"  class="btn btn-primary">
					<i class="bi bi-plus"></i>
					Add
				</button>
			</div>

			<div class="btn-group smr-10">
				<a type="button" href="<?=baseUri();?>/timesheet/add"  class="btn btn-primary">
					<i class="bi bi-plus"></i>
					Bulk add
				</a>
			</div>
		</div>
    </div>
</div>

<div class="page content">
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table id="timesheetDT" class="table table-striped table-bordered" style="width:100%">
					
				</table> 
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
