<div class="page content header">
	<div class="page-breadcrumb d-sm-flex align-items-center sp-y-10">
        <h5 class="">Approved Payrolls for Payment</h5>
        <div class="ms-auto d-sm-flex">
            
        </div>
    </div>
</div>

<div class="page content">
	<div class="card">
		<div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="input-group input-group" >
                    <input type="month" class="form-control" value="<?php echo date('Y-m'); ?>" id="monthFilter">
                </div>
            </div>
            <div class="col-md-3">
                <!-- Bulk Actions -->
                <div id="bulkActions" class="mb-3" style="display: none;">
                    <button style="padding:10px;" type="button" id="bulkPayBtn" class="btn btn-success btn">
                        <i class="fa fa-dollar-sign"></i> Pay Selected
                    </button>
                    <button style="padding:10px;" type="button" id="bulkRejectBtn" class="btn btn-danger btn">
                        <i class="fa fa-times"></i> Reject Selected
                    </button>
                </div>
            </div>
        </div>
			<div class="table-responsive">
				<table id="approvedPayrollsDT" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="select-all-checkbox"></th>
                            <th>Staff No.</th>
                            <th>Full Name</th>
                            <th>Month</th>
                            <th>Base Salary</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
				</table> 
			</div>
		</div>
	</div>
</div>

<!-- Payment Modal -->
<?php include 'pay_payroll.php';?>

<style>
.table-row.approved {
    background-color: #f8f9fa;
}

.badge-success {
    background-color: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
}


.cursor {
    cursor: pointer;
}

.fa-dollar-sign {
    color: #28a745;
}

.fa-times {
    color: #dc3545;
}

.fa-dollar-sign:hover {
    color: #1e7e34;
}

.fa-times:hover {
    color: #c82333;
}

.required::after {
    content: " *";
    color: red;
}
</style>
































