<div class="page content header">
	<div class="page-breadcrumb d-sm-flex align-items-center">
		<h5 class="">Appraisals </h5>
		<div class="ms-auto d-sm-flex">
			<div class="btn-group smr-10">
				<button type="button" data-bs-toggle="modal" data-bs-target="#add_appraisals"  class="btn btn-primary">
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
				<table id="appraisalsDT" class="table table-striped table-bordered" style="width:100%">
					<thead>
						<tr>
							<th>Employee ID</th>
							<th>Employee Name</th>
							<th>Target Rating</th>
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
	#appraisalsDT td:nth-of-type(6) {
		width: 100px;
	}
</style>

<?php 
require('appraisals_add.php');
require('appraisals_edit.php');
?>
