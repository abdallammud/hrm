<div class="row">
	<div class="page content">
		<div class="col-sm-12 col-md-12 col-lg-12">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="true">Documents</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="docTypes-tab" data-bs-toggle="tab" data-bs-target="#docTypes" type="button" role="tab" aria-controls="docTypes" aria-selected="false">Document Types</button>
				</li>
				
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="documents" role="tabpanel" aria-labelledby="home-tab">
					<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
						<h5 class="">Documents</h5>
						<div class="ms-auto d-sm-flex">
							<div class="btn-group smr-10">
								
							</div>
						</div>
					</div>
					<hr>
					
					<div class="">
						<div class="">
							<input type="text" class="form-control" id="searchFolder" placeholder="Search folder">
							<div class="row" id="folders">
                                <!-- Documents loaded here -->
							</div>
						</div>
					</div>
				</div>










				<div class="tab-pane fade " id="docTypes" role="tabpanel" aria-labelledby="home-tab">
					<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
						<h5 class="">Document Types</h5>
						<div class="ms-auto d-sm-flex">
							<div class="btn-group smr-10">
								<button type="button" data-bs-toggle="modal" data-bs-target="#add_docType" class="btn btn-primary">Add Document Type</button>
							</div>
						</div>
					</div>
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="docTypesDT" class="table table-striped table-bordered" style="width:100%">
									<thead>
										<tr>
											<th>Name</th>
											<th>Description</th>
											<th>Created At</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody></tbody>
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
require('docs_add.php');
require('docs_edit.php');
?>
