<div class="page content header">
	<div class="page-breadcrumb d-sm-flex align-items-center">
		<h5 class="">Goal Tracking </h5>
		<div class="ms-auto d-sm-flex">
			<div class="btn-group smr-10">
				<button type="button" data-bs-toggle="modal" data-bs-target="#add_goal_tracking"  class="btn btn-primary">
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
				<table id="goalTrackingDT" class="table table-striped table-bordered" style="width:100%">
					<thead>
						<tr>
							<th>Goal Type</th>
							<th>Subject</th>
							<th>Department</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Progress</th>
							<th>Created Date</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<!-- Data will be loaded dynamically -->
					</tbody>
				</table> 
			</div>
		</div>
	</div>
</div>






<style type="text/css">
	#appraisalsDT td:nth-of-type(6) {
		width: 100px;
	}
</style>

<?php 
require('goal_tracking_add.php');
require('goal_tracking_edit.php');
?>
