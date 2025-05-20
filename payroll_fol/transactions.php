<div class="row">
    <div class="col-md-12 col-lg-12">
		<div class="page content">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
		        <h5 class="">Allowances, Bonuses, Deductions and Loans </h5>
		        <div class="ms-auto d-sm-flex">
		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#download_transactionUploadFile"  class="btn btn-secondary">Download Transactions Upload File</button>
		            </div>

		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#transaction_upload"  class="btn btn-primary">Upload Transactions</button>
		            </div>


		            <div class="btn-group smr-10">
		                <button type="button" data-bs-toggle="modal" data-bs-target="#add_transaction"  class="btn btn-primary">Add Transaction</button>
		            </div>
		        </div>
		    </div>
		    <hr>
		    
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table id="transactionsDT" class="table table-striped table-bordered" style="width:100%">
							
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
	.dropdown.bootstrap-select.my-select {
		display: block;
		width: 100% !important;
	}
</style>

<?php 
require('transaction_add.php');
require('timesheet_edit.php');
?>
