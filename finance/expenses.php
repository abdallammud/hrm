<div class="page content header">
	<div class="page-breadcrumb d-sm-flex align-items-center sp-y-10">
        <h5 class="">Other Expenses</h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-20">
                <a data-bs-toggle="modal" data-bs-target="#add_expense"  class="btn btn-primary sflex scenter-items"><span class="fa fa-plus"></span> Add </a>
            </div>
        </div>
    </div>
</div>

<div class="page content">
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table id="expensesDT" class="table table-striped table-bordered" style="width:100%">
					
				</table> 
			</div>
		</div>
	</div>
</div>

<!-- Add bank account -->
<?php include 'add_expense.php';?>
<?php include 'edit_expense.php';?>
