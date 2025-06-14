<div class="page content header">
	<div class="page-breadcrumb d-sm-flex align-items-center sp-y-10">
        <h5 class="">Employees</h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-20">
                <a href="<?=baseUri();?>/employees/add"  class="btn btn-primary sflex scenter-items"><span class="fa fa-plus"></span> Add </a>
            </div>
            <div class="btn-group smr-25 ">
                <button type="button" data-bs-toggle="modal" data-bs-target="#upload_employees"  class=" btn btn-outline-secondary">
                    <span class="fa fa-upload"></span>
                    Bulk 
                </button>
	        </div>
           
            <div class="ms-auto d-none d-md-block">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary">
                        <span class="fa fa-cog"></span>
                        Settings
                    </button>
                    <button type="button" class="btn btn-outline-secondary split-bg-primary dropdown-toggle actions dropdown-toggle-split" data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                        <a class="dropdown-item cursor  edit-table_customize" data-table="employeesDT"> Edit table columns</a>
                        <!-- <a class="dropdown-item cursor " href="<?=baseUri();?>/pdf.php?print=employees" target="_blank"> Download PDF</a> -->
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>