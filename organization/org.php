<div class="page content">
	<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <h5 class="">Organization</h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-10">
                <button type="button" data-bs-toggle="modal" data-bs-target="#add_org"  class="btn btn-primary">Add Organization</button>
            </div>
            <div class="ms-auto d-none d-md-block">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary">Menu</button>
                    <button type="button" class="btn btn-outline-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                    	<a class="dropdown-item" href="<?=baseUri();?>/org">Organization</a>
                        <a class="dropdown-item" href="<?=baseUri();?>/<?=strtolower($GLOBALS['branch_keyword']['plu']);?>"><?=$GLOBALS['branch_keyword']['plu'];?></a>
                        <a class="dropdown-item" href="<?=baseUri();?>/chart"> Chart</a>
                        
                    </div>
                </div>
            </div>
            <!-- <div style="position: absolute; z-index: 999999; right: 10px; width: 50%;" class="alert alert-danger border-0 bg-grd-danger alert-dismissible fade show">
				<div class="d-flex align-items-center">
					<div class="font-35 text-white"><span class="material-icons-outlined fs-2">report_gmailerrorred</span>
					</div>
					<div class="ms-3">
						<h6 class="mb-0 text-white">Danger Alerts</h6>
						<div class="text-white">A simple danger alert—check it out!</div>
					</div>
				</div>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div> -->
        </div>
    </div>
    <hr>
    <div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table id="companyDT" class="table table-striped table-bordered" style="width:100%">
					<!-- <thead>
						<tr>
							<th>Organization Name</th>
							<th>Phone numbers</th>
							<th>Emails</th>
							<th>Address</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Hawlkar Tech solutions</td>
							<td>0610000000 | 0614444444 | 0615555555</td>
							<td>Info@hawlkar.com | Sales@hawlkar.com | hello@hawlkar.com</td>
							<td>Bondhere Mogadishu Somaia</td>
						</tr>
					</tbody>-->
				</table> 
			</div>
		</div>
	</div>

				
</div>


<?php 
require('org_add.php');
require('org_edit.php');
?>
