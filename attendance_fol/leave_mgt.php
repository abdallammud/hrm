<div class="row">
    <div class="col-md-12 col-lg-8">
		<div class="page content">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
		        <h5 class="">Employee Leave Management</h5>
		        <div class="ms-auto d-sm-flex">
		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#add_employeeLeave"  class="btn btn-primary">Add Record</button>
		            </div>
		        </div>
		    </div>
		    <hr>
		    
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table id="empLeaveDT" class="table table-striped table-bordered" style="width:100%">
							
						</table> 
					</div>
				</div>
			</div>
		</div>
	</div>

    <div class="col-md-12 col-lg-4">
		<div class="page content">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
		        <h5 class="">Leave Types</h5>
		        <div class="ms-auto d-sm-flex">
		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#add_leave_type"  class="btn btn-primary">Add Type</button>
		            </div>
		        </div>
		    </div>
		    <hr>
		    
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table id="leaveTypesDT" class="table table-striped table-bordered" style="width:100%">
							
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
require('leave_type_add.php');
require('leave_type_edit.php');


require('emp_leave_add.php');
require('emp_leave_edit.php');
?>
