<div class="row">
    <div class="col-md-12 col-lg-8">
		<div class="page content">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
		        <h5 class="">Duty Locations</h5>
		        <div class="ms-auto d-sm-flex">
		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#add_location"  class="btn btn-primary">Add Location</button>
		            </div>
		        </div>
		    </div>
		    <hr>
		    
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table id="locationsDT" class="table table-striped table-bordered" style="width:100%">
							
						</table> 
					</div>
				</div>
			</div>
		</div>
	</div>

    <div class="col-md-12 col-lg-4">
		<div class="page content">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
		        <h5 class="">States</h5>
		        <div class="ms-auto d-sm-flex">
		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#add_state"  class="btn btn-primary">Add State</button>
		            </div>
		        </div>
		    </div>
		    <hr>
		    
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table id="statesDT" class="table table-striped table-bordered" style="width:100%">
							
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
require('state_add.php');
require('state_show.php');
require('state_edit.php');

require('location_add.php');
// require('location_show.php');
require('location_edit.php');
?>
