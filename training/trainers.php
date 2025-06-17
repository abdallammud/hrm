<div class="page content header">
	<div class="page-breadcrumb d-sm-flex align-items-center sp-y-10">
        <h5 class="">Trainers</h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-20">
                <a data-bs-toggle="modal" data-bs-target="#add_trainer"  class="btn btn-primary sflex scenter-items"><span class="fa fa-plus"></span> Add </a>
            </div>
        </div>
    </div>
</div>

<div class="page content">
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table id="trainersDT" class="table table-striped table-bordered" style="width:100%">
					
				</table> 
			</div>
		</div>
	</div>
</div>

<!-- Add trainer -->
<?php include 'add_trainer.php';?>
<?php include 'edit_trainer.php';?>
