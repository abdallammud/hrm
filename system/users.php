
<div class="page content">
	<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <h5 class="">System Users</h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-10">
                <!-- <a href="<?=baseUri();?>/user/add"  class="btn btn-primary">Add User</a> -->
                <button type="button" data-bs-toggle="modal" data-bs-target="#addUser"  class="btn btn-primary">Add User</button>
            </div>
        </div>
    </div>
    <hr>
    <div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table id="usersDT" class="table table-striped table-bordered" style="width:100%">
					
				</table> 
			</div>
		</div>
	</div>
</div>


<?php require_once 'user_add.php'; ?>
<?php require_once 'user_edit.php'; ?>