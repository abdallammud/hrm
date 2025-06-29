<div class="page content header">
	<div class="page-breadcrumb d-sm-flex align-items-center">
		<h5 class="">Attendance </h5>
		<div class="ms-auto d-sm-flex">
			<div class="btn-group smr-10">
				<button type="button" data-bs-toggle="modal" data-bs-target="#add_attendance"  class="btn btn-primary">
					<i class="bi bi-plus"></i>
					Add
				</button>
			</div>
			<div class="btn-group smr-10">
				<button type="button" data-bs-toggle="modal" data-bs-target="#download_attendanceUploadFile"  class="btn btn-outline-secondary">Attendance Template</button>
			</div>

			<div class="btn-group smr-10">
				<button type="button" data-bs-toggle="modal" data-bs-target="#attendance_upload"  class="btn btn-outline-secondary">
					<i class="bi bi-upload"></i>
					Upload
				</button>
			</div>


			

			<div class="btn-group smr-10">
				<a type="button" href="<?=baseUri();?>/attendance/add"  class="btn btn-outline-secondary">
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
				<table id="attendanceDT" class="table table-striped table-bordered" style="width:100%">
					
				</table> 
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
