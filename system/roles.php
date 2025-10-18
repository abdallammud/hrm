<!-- load roles -->
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
                                        echo "<span  class='btn smt-10 btn-outline-secondary'>".ucwords(str_replace('_',' ',$permission))."</span> ";
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


<!-- Add role -->
<div class="modal fade" data-bs-focus="false" id="addRole" tabindex="-1" role="dialog" aria-labelledby="addRoleLabel" aria-hidden="true">
    <div class="modal-dialog" role="Category" style="min-width:700px; width: 90vw; max-width: 750px;">
        <form class="modal-content" id="addSystemRoleForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
            <div class="modal-header">
                <h5 class="modal-title" id="addSystemRole">Add New Role</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label required" for="roleName">Role Name</label>
                            <input type="text" data-msg="Please provide role name." class="form-control validate" id="roleName" name="roleName">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="label required" for="reportsTo">Reports to</label>
                            <select data-live-search="true" name="reportsTo" id="reportsTo" title="Select reports to" multiple class="form-control my-select reports_to">
                                <option value="">Select</option>
                                <?php
                                foreach ($GLOBALS['sys_roles']->read_all() as $role) {
                                    echo '<option value="' . $role['id'] . '">' . ucwords($role['name']) . '</option>';
                                }
                                ?>
                            </select>
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>

                <h6 class="mt-3">Assign permission to this role</h6>
                <!-- Check all -->
                <div class="form-check smt-15 mb-2">
                    <input class="form-check-input module" type="checkbox" id="checkAll">
                    <label class="form-check-label fw-bold" for="checkAll">Check All</label>
                </div>
                <hr>

                <div class="permissions-list">
                    <?php
                    foreach ($GLOBALS['sys_permissions']->read_all() as $permission) {
                        $actions = json_decode($permission['actions']);
                        $disabled_features = json_decode(get_setting('disabled_features')['value']);
                        if (in_array($permission['module'], $disabled_features)) {
                            continue;
                        }
                        $permission_name = $permission['module'];
                        $module_id = strtolower(str_replace(" ", "_", $permission_name));
                        ?>
                        
                        <div class="permission-module mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input module" type="checkbox" id="<?=$module_id;?>">
                                <label class="form-check-label fw-bold" for="<?=$module_id;?>"><?=ucwords($permission_name);?></label>
                            </div>

                            <div class="d-flex flex-wrap gap-3 ms-4">
                                <?php foreach ($actions as $action_name => $action_code) { ?>
                                    <div class="form-check me-3">
                                        <input class="form-check-input role_permission action <?=$module_id;?>" data-module="<?=$module_id;?>" type="checkbox" id="<?=$action_code->code;?>" value="<?=$action_code->code;?>">
                                        <label class="form-check-label" for="<?=$action_code->code;?>"><?=ucwords($action_name);?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor" data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
            </div>
        </form>
    </div>
</div>


<!-- Edit role -->
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
   .form-check-input {
        height: 20px;
        width: 20px;
        margin-right: 10px;
        margin-top: 0px;
    }
    .form-check-label {
        cursor: pointer;
    }
    .permission-module.mb-3 {
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }
</style>

