
<div class="page content">
	<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <h5 class="">System Roles</h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-10">
                <button type="button" data-bs-toggle="modal" data-bs-target="#addRole"  class="btn btn-primary">Add Role</button>
            </div>
        </div>
    </div>
    <hr>
    <div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table id="rolesDT" class="table table-striped table-bordered" style="width:100%">
					<thead>
                        <tr>
                            <th scope="col" style="width: 10%;">Role </th>
                            <th scope="col">Permissions</th>
                            <th scope="col" style="width: 15%;">Actions</th>
                        </tr>  
                    </thead>
                    <tbody>
                        <?php
                        foreach ($GLOBALS['sys_roles']->read_all() as $role) {
                            $permissions = $GLOBALS['sys_role_permissions']->get_permissions( $role['id']);

                            // var_dump($permissions);
                            ?>
                            <tr>
                                <td><?=ucwords($role['name']);?></td>
                                <td>
                                    <?php
                                    foreach ($permissions as $permission) {
                                        echo "<span  class='btn smt-10 btn-success'>".ucwords(str_replace('_',' ',$permission))."</span> ";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="editRoleModal(<?= $role['id'];?>)" >Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRole(<?= $role['id'];?>)">Delete</button>
                                </td>
                            </tr>
                        <?php } ?>
				</table> 
			</div>
		</div>
	</div>
</div>



<div class="modal fade "  data-bs-focus="false" id="addRole" tabindex="-1" role="dialog" aria-labelledby="addRoleLabel" aria-hidden="true">
    <div class="modal-dialog" role="Category" style="min-width:700px; width: 90vw; max-width: 750px;">
        <form class="modal-content" id="addSystemRoleForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title" id="addSystemRole">Add New Role</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label  required"  for="roleName">Role  Name</label>
                                <input type="text"  data-msg="Please provide role name."  class="form-control validate" id="roleName" name="roleName">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-md-12 col-lg-12 col-xs-12">
                            <h6 style="margin-top: 10px;;">Assign permission to this role</h6>
                        </div>
                        
                         <div class="table-responsive">
                            <table class="table assing_roles table-borderless">
                                <thead style="background-color: #f2f2f2;">
                                    <tr>
                                        <th scope="col">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="selectAll">
                                                <label class="form-check-label" for="selectAll">MODULE</label>
                                            </div>
                                        </th>
                                        <th scope="col">PERMISSIONS</th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($GLOBALS['sys_permissions']->read_all() as $permission) {
                                    $actions = json_decode($permission['actions']);

                                    // var_dump($actions);
                                    $permission['actions'] = $actions;
                                    $permission_name = $permission['module'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input module" type="checkbox" value="" id="<?=strtolower(str_replace(" ", "_",$permission_name));?>">
                                                <label class="form-check-label" for="<?=strtolower(str_replace(" ", "_",$permission_name));?>"><?=ucwords($permission_name);?></label>
                                            </div>     
                                        </td>

                                        <?php

                                        foreach ($actions as $action_name => $action_code) {
                                            ?>
                                            <td>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input action <?=strtolower(str_replace(" ", "_",$permission_name));?>" data-module="<?=strtolower(str_replace(" ", "_",$permission_name));?>" type="checkbox" id="<?=$action_code->code;?>" value="<?=$action_code->code;?>">
                                                    <label class="form-check-label" for="<?=$action_code->code;?>"><?=ucwords($action_name);?></label>
                                                </div>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade "  data-bs-focus="false" id="editRole" tabindex="-1" role="dialog" aria-labelledby="editRoleLabel" aria-hidden="true">
    <div class="modal-dialog" role="Category" style="min-width:700px; width: 90vw; max-width: 750px;">
        <form class="modal-content" id="editSystemRoleForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	<div class="modal-header">
                <h5 class="modal-title" id="editSystemRole">Edit Role</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        simplifyRoles();
    });
</script>
<style>
    table.assing_roles th:first-of-type {
        width: 40%;
    }
    table.assing_roles {
        margin-top: 10px;
    }
    table.assing_roles td {
        margin-right: 15px;
    }
    .form-check {
        display: flex;
        align-items: center;
    }
    table.assing_roles .form-check-input {
        height: 20px;
        width: 20px;
        margin-right: 10px;
        margin-top: 0px;
    }
    table.assing_roles .form-check-label {
        cursor: pointer;
    }
</style>

