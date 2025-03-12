<div class="row">
	<div class="page content">
		<div class="col-sm-12 col-md-12 col-lg-12">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="designations-tab" data-bs-toggle="tab" data-bs-target="#designations" type="button" role="tab" aria-controls="designations" aria-selected="true">Designations</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab" aria-controls="projects" aria-selected="false">Projects</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="contractTypes-tab" data-bs-toggle="tab" data-bs-target="#contractTypes" type="button" role="tab" aria-controls="contractTypes" aria-selected="false">Contract Types</button>
				</li>

				<li class="nav-item" role="presentation">
					<button class="nav-link" id="budgetCodes-tab" data-bs-toggle="tab" data-bs-target="#budgetCodes" type="button" role="tab" aria-controls="budgetCodes" aria-selected="false">Budget Codes</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="banks-tab" data-bs-toggle="tab" data-bs-target="#banks" type="button" role="tab" aria-controls="banks" aria-selected="false">Banks</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="transactionSubtypes-tab" data-bs-toggle="tab" data-bs-target="#transactionSubtypes" type="button" role="tab" aria-controls="transactionSubtypes" aria-selected="false">Transaction Subtypes</button>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="designations" role="tabpanel" aria-labelledby="home-tab">
					<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
						<h5 class="">Designations</h5>
						<div class="ms-auto d-sm-flex">
							<div class="btn-group smr-10">
								<button type="button" data-bs-toggle="modal" data-bs-target="#add_designation"  class="btn btn-primary">Add Designation</button>
							</div>
						</div>
					</div>
					<hr>
					
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="designationsDT" class="table table-striped table-bordered" style="width:100%">
									
								</table> 
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade " id="projects" role="tabpanel" aria-labelledby="home-tab">
					<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
						<h5 class="">Projects</h5>
						<div class="ms-auto d-sm-flex">
							<div class="btn-group smr-10">
								<button type="button" data-bs-toggle="modal" data-bs-target="#add_project"  class="btn btn-primary">Add Project</button>
							</div>
						</div>
					</div>
					<hr>
					
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="projectsDT" class="table table-striped table-bordered" style="width:100%">
									
								</table> 
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade " id="contractTypes" role="tabpanel" aria-labelledby="home-tab">
					<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
						<h5 class="">Contract Types</h5>
						<div class="ms-auto d-sm-flex">
							<div class="btn-group smr-10">
								<button type="button" data-bs-toggle="modal" data-bs-target="#add_contractType"  class="btn btn-primary">Add Contract Type</button>
							</div>
						</div>
					</div>
					<hr>
					
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="contractTypesDT" class="table table-striped table-bordered" style="width:100%">
									
								</table> 
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade " id="budgetCodes" role="tabpanel" aria-labelledby="home-tab">
					<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
						<h5 class="">Budget codes</h5>
						<div class="ms-auto d-sm-flex">
							<div class="btn-group smr-10">
								<button type="button" data-bs-toggle="modal" data-bs-target="#add_budgetCode"  class="btn btn-primary">Add Budget Code</button>
							</div>
						</div>
					</div>
					<hr>
					
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="budgetCodesDT" class="table table-striped table-bordered" style="width:100%">
									
								</table> 
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade " id="banks" role="tabpanel" aria-labelledby="home-tab">
					<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
						<h5 class="">Banks</h5>
						<div class="ms-auto d-sm-flex">
							<div class="btn-group smr-10">
								<button type="button" data-bs-toggle="modal" data-bs-target="#add_bank"  class="btn btn-primary">Add Bank</button>
							</div>
						</div>
					</div>
					<hr>
					
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="allBanksDT" class="table table-striped table-bordered" style="width:100%">
									
								</table> 
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade " id="transactionSubtypes" role="tabpanel" aria-labelledby="home-tab">
					<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
						<h5 class="">Transaction subtypes</h5>
						<div class="ms-auto d-sm-flex">
							<div class="btn-group smr-10">
								<button type="button" data-bs-toggle="modal" data-bs-target="#add_subtype"  class="btn btn-primary">Add Subtype</button>
							</div>
						</div>
					</div>
					<hr>
					
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="subTypesDT" class="table table-striped table-bordered" style="width:100%">
									
								</table> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

</script>

<?php 
require('misc_add.php');
require('misc_edit.php');
?>
