<div class="row">
    <div class="col-md-12 col-lg-12">
		<div class="page content">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
		        <h5 class="">Attendance </h5>
		        <div class="ms-auto d-sm-flex">
		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#download_attendanceUploadFile"  class="btn btn-secondary">Download Attendance Upload File</button>
		            </div>

		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#attendance_upload"  class="btn btn-primary">Upload Attendance</button>
		            </div>


		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#add_attendance"  class="btn btn-primary">Add Record</button>
		            </div>
		        </div>
		    </div>
		    <hr>
		    
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table id="attendanceDT" class="table table-striped table-bordered" style="width:100%">
							
						</table> 
					</div>
				</div>
			</div>
		</div>
	</div>


</div>

<script type="text/javascript">

	
</script>

<style type="text/css">
	#statesDT td:nth-of-type(1) {
		width: 70%;
	}
</style>

<?php 
require('atten_add.php');
require('atten_edit.php');
?>
