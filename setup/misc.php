<?php $active = ''; ?>
<div class="row">
	<div class="page content">
		<div class="col-sm-12 col-md-12 col-lg-12">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<?php if(check_session('manage_designations')) { $active = 'designations'; ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php if($active == 'designations') echo 'active';?>" id="designations-tab" data-bs-toggle="tab" data-bs-target="#designations" type="button" role="tab" aria-controls="designations" aria-selected="true">Designations</button>
					</li>
				<?php } ?>
				<?php if(check_session('manage_projects')) { if(!$active) $active = 'projects'; ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php if($active == 'projects') echo 'active';?>" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab" aria-controls="projects" aria-selected="false">Projects</button>
					</li>
				<?php } ?>
				<?php if(check_session('manage_contract_types')) { if(!$active) $active = 'contractTypes';  ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php if($active == 'contractTypes') echo 'active';?>" id="contractTypes-tab" data-bs-toggle="tab" data-bs-target="#contractTypes" type="button" role="tab" aria-controls="contractTypes" aria-selected="false">Contract Types</button>
					</li>	
				<?php } ?>
				<?php if(check_session('manage_budget_codes')) { if(!$active) $active = 'budgetCodes';  ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php if($active == 'budgetCodes') echo 'active';?>" id="budgetCodes-tab" data-bs-toggle="tab" data-bs-target="#budgetCodes" type="button" role="tab" aria-controls="budgetCodes" aria-selected="false">Budget Codes</button>
					</li>
				<?php } ?>
				<?php if(check_session('manage_bank_accounts')) { if(!$active) $active = 'banks';  ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php if($active == 'banks') echo 'active';?>" id="banks-tab" data-bs-toggle="tab" data-bs-target="#banks" type="button" role="tab" aria-controls="banks" aria-selected="false">Banks</button>
					</li>
				<?php } ?>
				<?php if(check_session('manage_transaction_subtypes')) { if(!$active) $active = 'transactionSubtypes';  ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php if($active == 'transactionSubtypes') echo 'active';?>" id="transactionSubtypes-tab" data-bs-toggle="tab" data-bs-target="#transactionSubtypes" type="button" role="tab" aria-controls="transactionSubtypes" aria-selected="false">Transaction Subtypes</button>
					</li>
				<?php } if(check_session('manage_goal_types')) { if(!$active) $active = 'goalTypes';  ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php if($active == 'goalTypes') echo 'active';?>" id="goalTypes-tab" data-bs-toggle="tab" data-bs-target="#goalTypes" type="button" role="tab" aria-controls="goalTypes" aria-selected="false">Goal Types</button>
					</li>
				<?php } if(check_session('manage_award_types')) { if(!$active) $active = 'awardTypes';  ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php if($active == 'awardTypes') echo 'active';?>" id="awardTypes-tab" data-bs-toggle="tab" data-bs-target="#awardTypes" type="button" role="tab" aria-controls="awardTypes" aria-selected="false">Award Types</button>
					</li>
				<?php } if(check_session('manage_financial_accounts')) { if(!$active) $active = 'financialAccounts';  ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php if($active == 'financialAccounts') echo 'active';?>" id="financialAccounts-tab" data-bs-toggle="tab" data-bs-target="#financialAccounts" type="button" role="tab" aria-controls="financialAccounts" aria-selected="false">Financial Accounts</button>
					</li>
				<?php } ?>
				<?php if(check_session('manage_training_options') || check_session('manage_training_types')) { if(!$active) $active = 'training';  ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?php if($active == 'training') echo 'active';?>" id="training-tab" data-bs-toggle="tab" data-bs-target="#training" type="button" role="tab" aria-controls="training" aria-selected="false">Training</button>
					</li>
				<?php } ?>
				
				
			</ul>
			<div class="tab-content" id="myTabContent">
				<?php if(check_session('manage_designations')) { ?>
					<div class="tab-pane fade  <?php if($active == 'designations') echo 'active show';?>" id="designations" role="tabpanel" aria-labelledby="designations-tab">
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
				<?php } ?>
				<?php if(check_session('manage_projects')) { ?>
					<div class="tab-pane fade <?php if($active == 'projects') echo 'active show';?> " id="projects" role="tabpanel" aria-labelledby="projects-tab">
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
				<?php } ?>
				<?php if(check_session('manage_contract_types')) { ?>
					<div class="tab-pane fade <?php if($active == 'contractTypes') echo 'active show';?> " id="contractTypes" role="tabpanel" aria-labelledby="contractTypes-tab">
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
				<?php } ?>
				<?php if(check_session('manage_budget_codes')) { ?>
					<div class="tab-pane fade <?php if($active == 'budgetCodes') echo 'active show';?> " id="budgetCodes" role="tabpanel" aria-labelledby="budgetCodes-tab">
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
				<?php } ?>
				<?php if(check_session('manage_bank_accounts')) { ?>
					<div class="tab-pane fade <?php if($active == 'banks') echo 'active show';?> " id="banks" role="tabpanel" aria-labelledby="banks-tab">
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
				<?php } ?>
				<?php if(check_session('manage_transaction_subtypes')) { ?>
					<div class="tab-pane fade <?php if($active == 'transactionSubtypes') echo 'active show';?> " id="transactionSubtypes" role="tabpanel" aria-labelledby="transactionSubtypes-tab">
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
				<?php } if(check_session('manage_award_types')) { ?>
					<div class="tab-pane fade <?php if($active == 'awardTypes') echo 'active show';?> " id="awardTypes" role="tabpanel" aria-labelledby="awardTypes-tab">
						<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
							<h5 class="">Award Types</h5>
							<div class="ms-auto d-sm-flex">
								<div class="btn-group smr-10">
									<button type="button" data-bs-toggle="modal" data-bs-target="#add_awardType"  class="btn btn-primary">Add Award Type</button>
								</div>
							</div>
						</div>
						<hr>
						
						<div class="card">
							<div class="card-body">
								<div class="table-responsive">
									<table id="awardTypesDT" class="table table-striped table-bordered" style="width:100%">
										
									</table> 
								</div>
							</div>
						</div>
					</div>
				<?php } if(check_session('manage_goal_types')) { ?>
					<div class="tab-pane fade <?php if($active == 'goalTypes') echo 'active show';?> " id="goalTypes" role="tabpanel" aria-labelledby="goalTypes-tab">
						<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
							<h5 class="">Goal types</h5>
							<div class="ms-auto d-sm-flex">
								<div class="btn-group smr-10">
									<button type="button" data-bs-toggle="modal" data-bs-target="#add_goalType"  class="btn btn-primary">Add goal type</button>
								</div>
							</div>
						</div>
						<hr>
						
						<div class="card">
							<div class="card-body">
								<div class="table-responsive">
									<table id="goalTypesDT" class="table table-striped table-bordered" style="width:100%">
										
									</table> 
								</div>
							</div>
						</div>
					</div>
				<?php } if(check_session('manage_financial_accounts')) { ?>
					<div class="tab-pane fade <?php if($active == 'financialAccounts') echo 'active show';?> " id="financialAccounts" role="tabpanel" aria-labelledby="financialAccounts-tab">
						<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
							<h5 class="">Financial Accounts</h5>
							<div class="ms-auto d-sm-flex">
								<div class="btn-group smr-10">
									<button type="button" data-bs-toggle="modal" data-bs-target="#add_financialAccount"  class="btn btn-primary">Add Financial Account</button>
								</div>
							</div>
						</div>
						<hr>
						
						<div class="card">
							<div class="card-body">
								<div class="table-responsive">
									<table id="financialAccountsDT" class="table table-striped table-bordered" style="width:100%">
										
									</table> 
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if(check_session('manage_training_options') || check_session('manage_training_types')) { ?>
					<div class="tab-pane fade <?php if($active == 'training') echo 'active show';?> " id="training" role="tabpanel" aria-labelledby="training-tab">
						<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
							<h5 class="">Training</h5>
						</div>
						<hr>
						
						<div class="row">
							<?php if(check_session('manage_training_options')) { ?>
							<div class="col-md-6">
								<div class="card">
									<div class="card-header d-flex justify-content-between align-items-center">
										<h6 class="mb-0">Training Options</h6>
										<button type="button" data-bs-toggle="modal" data-bs-target="#add_trainingOption" class="btn btn-primary btn-sm">Add Training Option</button>
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table id="trainingOptionsDT" class="table table-striped table-bordered" style="width:100%">
												
											</table> 
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
							
							<?php if(check_session('manage_training_types')) { ?>
							<div class="col-md-6">
								<div class="card">
									<div class="card-header d-flex justify-content-between align-items-center">
										<h6 class="mb-0">Training Types</h6>
										<button type="button" data-bs-toggle="modal" data-bs-target="#add_trainingType" class="btn btn-primary btn-sm">Add Training Type</button>
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table id="trainingTypesDT" class="table table-striped table-bordered" style="width:100%">
												
											</table> 
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
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
