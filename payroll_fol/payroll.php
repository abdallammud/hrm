
<div class="page content header">
    <div class="page-breadcrumb d-sm-flex align-items-center ">
        <h5 class="spy-10">Payroll</h5>
        <div class="ms-auto d-sm-flex">
        	<div class="btn-group smr-10">
	            <button type="button" data-bs-toggle="modal" data-bs-target="#generate_payroll"  class="btn btn-primary">
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
				<table id="payrollDT" class="table table-striped table-bordered" style="width:100%">
					
				</table> 
			</div>
		</div>
	</div>
</div>
	
<script type="text/javascript">

	
</script>

<style type="text/css">
	.dropdown.bootstrap-select.my-select {
		display: block;
		width: 100% !important;
	}
</style>

<?php 
require('payroll_add.php');
?>
