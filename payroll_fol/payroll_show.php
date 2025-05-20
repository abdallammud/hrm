<?php 
$payroll_id = $_GET['payroll_id'] ?? 0;
$payrollInfo = get_data('payroll', ['id' => $payroll_id]);

if($payrollInfo) {
	$payrollInfo = $payrollInfo[0];
} else {
	$payrollInfo['month'] = '';
	$payrollInfo['ref'] = '';
	$payrollInfo['ref_name'] = '';
	$payrollInfo['added_date'] = '';
	$payrollInfo['status'] = '';

}

$payrollInfo['month'] = explode(",", $payrollInfo['month']);

?>
<div class="row">
    <div class="col-md-12 col-lg-12">
		<div class="page content">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
		        <h5 class="">Payroll details </h5>
		        <div class="ms-auto d-sm-flex">
		        	<div class="btn-group smr-10">
		                <!-- <a id="download_payroll"  class="btn btn-success"> Download Excel</a> -->
		            </div>

		            <div class="btn-group smr-10">
		                <a href="<?=baseUri();?>/payroll"  class="btn btn-secondary"> Back</a>
		            </div> 
		            <div class="ms-auto d-none d-md-block">
		                <div class="btn-group">
		                    <button type="button" class="btn btn-outline-primary">Actions</button>
		                    <button type="button" class="btn btn-outline-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle Dropdown</span>
		                    </button>
		                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
		                        <a class="dropdown-item cursor  edit-table_customize" data-table="showpayrollDT"> Edit table columns</a>
		                        <a class="dropdown-item cursor  edit-table_customize" data-table="payroll_pdf"> Edit pdf columns</a>
		                        <a class="dropdown-item cursor " href="<?=baseUri();?>/pdf.php?print=payroll&id=<?=$payroll_id;?>" target="_blank"> Download PDF</a>
		                        <a id="download_payroll"  class="dropdown-item cursor"> Download Excel</a>
		                    </div>
		                </div>
		            </div> 
		        </div>
		    </div>

		    <hr>

			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-ms-12 col-md-6 col-lg-3">
							<div class="form-group">
                                <label class="label required" for="">Month</label>
                                <select id="payrollMonth" type="text" readonly class="form-control cursor ">
                                	<?php 

                                	if(isset($payrollInfo['month']) && is_array($payrollInfo['month'])) {

                                		foreach ($payrollInfo['month'] as $month ) {
                                			echo '<option value="'.date('Y-m', strtotime($month)).'"> '.date('F Y', strtotime($month)).'</option>';
                                		}
                                	}


                                	 ?>
                                	
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
						</div>
						<div class="col-ms-12 col-md-6 col-lg-3">
							<div class="form-group">
                                <label class="label required" for="payrollMonth">Reference</label>
                                <input type="text" readonly class="form-control cursor " value="<?=$payrollInfo['ref'];?>,  <?=$payrollInfo['ref_name'];?>">
                                <span class="form-error text-danger">This is error</span>
                            </div>
						</div>
						<div class="col-ms-12 col-md-6 col-lg-2">
							<div class="form-group">
                                <label class="label required" for="payrollMonth">Status</label>
                                <input type="text" readonly class="form-control cursor " value="<?=$payrollInfo['status'];?>">
                                <span class="form-error text-danger">This is error</span>
                            </div>
						</div>
						<div class="col-ms-12 col-md-6 col-lg-2">
							<div class="form-group">
                                <label class="label required" for="">Date added</label>
                                <input type="text" readonly class="form-control cursor " value="<?=date('F d, Y', strtotime($payrollInfo['added_date']));?>">
                                <span class="form-error text-danger">This is error</span>
                            </div>
						</div>
						<div class="col-ms-12 col-md-6 col-lg-2">
							<div class="form-group">
                                <label class="label required" for="payrollMonth">&nbsp; </label>
	                            <div class="ms-auto d-sm-flex">
						            <div class="btn-group " style="width:100%">
						            	<?php if($payrollInfo['status'] == 'Request') { ?>
						                	<button  type="button" data-recid="<?=$payroll_id;?>" class="btn btn-primary approve_payrollBtn">Approve payroll</button>
						                <?php } else if($payrollInfo['status'] == 'Approved') {  ?>
						                	<button  type="button" data-recid="<?=$payroll_id;?>" class="btn btn-primary pay_payrollBtn">Pay payroll</button>
						                <?php } else { ?>
						                	<button  type="button" class="btn btn-success ">Paid</button>
						                <?php } ?>
						            </div>
						        </div>
                            </div>
						</div>
					</div>
					<div class="table-responsive">
						<table id="showpayrollDT" class="table table-striped table-bordered" style="width:100%">
							
						</table> 
					</div>
				</div>
			</div>
		</div>
	</div>


</div>
<?php 
$columns = get_columns('showpayrollDT', 'show_columns');
require('./customize_table.php');
?>
<script type="text/javascript">
	var payroll_id = '<?=$payroll_id;?>';
	var	tableColumns = <?=json_encode($columns);?>;
	console.log(tableColumns)
	addEventListener("DOMContentLoaded", (event) => {
		var month = $('#payrollMonth').val();
		if(month) {
			load_showPayroll(payroll_id, month);
			$('#download_payroll').attr('href', `${base_url}/download_payroll.php?id=${payroll_id}&month=${month}`)
		}
		$('#payrollMonth').on('change', () => {
			var month = $('#payrollMonth').val();
			if(month) {
				load_showPayroll(payroll_id, month);
				$('#download_payroll').attr('href', `${base_url}/download_payroll.php?id=${payroll_id}&month=${month}`)
			}
		})
	});

	
</script>

<style type="text/css">
	.dropdown.bootstrap-select.my-select {
		display: block;
		width: 100% !important;
	}
</style>

<?php 
require('payslip_show.php');
require('pay_payroll.php');
?>
