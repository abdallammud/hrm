<div class="page content">
	<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <h5 class=""><?=$GLOBALS['branch_keyword']['plu'];?></h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-10">
                <button type="button" data-bs-toggle="modal" data-bs-target="#add_branch"  class="btn btn-primary">Add <?=$GLOBALS['branch_keyword']['sing'];?></button>
            </div>
            
        </div>
    </div>
    <hr>
    <div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table id="branchesDT" class="table table-striped table-bordered" style="width:100%">
					
				</table> 
			</div>
		</div>
	</div>

				
</div>


<?php 
require('branch_add.php');
require('branch_edit.php');
?>
