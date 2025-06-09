<div class="page content header">
	<div class="page-breadcrumb d-sm-flex align-items-center">
		<h5 class="">Indicators </h5>
		<div class="ms-auto d-sm-flex">

			<div class="btn-group smr-10">
				<button type="button" data-bs-toggle="modal" data-bs-target="#add_indicators"  class="btn btn-primary">
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
				<table id="indicatorsDT" class="table table-striped table-bordered" style="width:100%">
					<thead>
						<tr>
							<th>Department</th>
							<th>Designation</th>
							<th>Overall Rating</th>
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
	#indicatorsDT td:nth-of-type(5) {
		width: 100px;
	}
</style>

<?php 
require('indicators_add.php');
require('indicators_edit.php');
?>
