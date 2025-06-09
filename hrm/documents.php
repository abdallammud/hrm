<div class="page content header">
	<div class="page-breadcrumb d-sm-flex align-items-center sp-y-10">
        <h5 class="spy-10">Documents</h5>
    </div>
</div>

<div class="page content">
	<div class="col-sm-12 col-md-12 col-lg-12">
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="true">Documents</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="docTypes-tab" data-bs-toggle="tab" data-bs-target="#docTypes" type="button" role="tab" aria-controls="docTypes" aria-selected="false">Document Types</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="allDocs-tab" data-bs-toggle="tab" data-bs-target="#allDocs" type="button" role="tab" aria-controls="allDocs" aria-selected="false">All Documents</button>
			</li>
			
		</ul>
		<div class="tab-content" id="myTabContent">
			<div class="tab-pane fade show active" id="documents" role="tabpanel" aria-labelledby="home-tab">
				<div class="smt-10">
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
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade " id="allDocs" role="tabpanel" aria-labelledby="home-tab">
				<div class="page-breadcrumb mt-3 d-sm-flex align-items-center mb-3">
					<h5 class="">All Documents</h5>
					<div class="ms-auto d-sm-flex">
						<div class="btn-group smr-10">
							<button type="button" data-bs-toggle="modal" data-bs-target="#add_document" class="btn btn-primary">Add Document</button>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="empDocumentsDT" class="table table-striped table-bordered" style="width:100%">
							</table>
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
require('emp_doc.php');
require('docs_add.php');
require('docs_edit.php');
?>
