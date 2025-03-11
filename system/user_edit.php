<?php 
$user = $GLOBALS['userClass']->read($_GET['user_id']);
$userPermissions = $GLOBALS['userClass']->getPermission_ids($_GET['user_id']);
// var_dump($GLOBALS['userClass']->getPermissions(31));
?>
<div class="page content">
	<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
        <h5 class="">Edut User Information</h5>
        <div class="ms-auto d-sm-flex">
            <div class="btn-group smr-10">
                <a href="<?=baseUri();?>/users"  class="btn btn-secondary">Go Back</a>
            </div>            
        </div>
    </div>
    <hr>
    <div class="card">
		<div class="card-body">
			<form class="modal-content" id="editUserForm" style="border-radius: 14px 14px 0px 0px; margin-top: -15px;">
        	
            <div class="modal-body">
                <div id="">
                	<p class="bold smt-10">User Information</p>
                    <div class="row">
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group relative">
                                <label class="label required" for="searchEmployee">Full name</label>
                                <input type="text" value="<?=$user['full_name'];?>"  class="form-control " id="searchEmployee" name="searchEmployee">
                                <input type="hidden" class="employee_id4CreateUser" value="<?=$user['emp_id'];?>" id="employee_id4CreateUser" name="">
                                <input type="hidden" class="user_id4Edit" value="<?=$user['user_id'];?>" id="user_id4Edit" name="">
                                <span class="form-error text-danger">This is error</span>
                                <div class="search_result employee">
                                	
                                </div>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="phone">Phone number</label>
                                <input type="text" value="<?=$user['phone'];?>"  class="form-control " id="phone" name="phone" placeholder="Required">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-3">
                            <div class="form-group">
                                <label class="label required" for="email">Email</label>
                                <input type="text" value="<?=$user['email'];?>" class="form-control " id="email" name="email" placeholder="Required">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="username">Username</label>
                                <input type="hidden" id="systemRole" value="" name="">
                                <input type="text"  class="form-control " value="<?=$user['username'];?>" id="username" name="username" placeholder="Required">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                       <!--  <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="systemRole">System Role</label>
                                <select  class="form-control " id="systemRole" name="systemRole">
                                	<option value="">- Select</option>
                                	<option <?php if(trim($user['role']) == 'Admin') echo 'selected="selected"'; ?>  value="Admin">Admin</option>
                                	<option <?php if(trim($user['role']) == 'User') echo 'selected="selected"'; ?>  value="User">User</option>
                                	<option <?php if(trim($user['role']) == 'Employee') echo 'selected="selected"'; ?>  value="Employee">Employee</option> 
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div> -->
                        <div class="col col-xs-12 col-md-6 col-lg-2">
                            <div class="form-group">
                                <label class="label required" for="slcStatus">Status</label>
                                <select  class="form-control " id="slcStatus" name="slcStatus">
                                	<option <?php if(ucwords($user['status']) == 'Active') echo 'selected="selected"'; ?> value="Active">Active</option>
                                	<option <?php if(ucwords($user['status']) == 'Suspended') echo 'selected="selected"'; ?> value="Suspended">Suspended</option>
                                </select>
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>

                    <p class="bold smt-20" style="margin-bottom: 0px;">User permissions</p>
                    <div class="form-check smt-10 form-switch">
						<input class="form-check-input" type="checkbox" id="checkAll">
						<label class="form-check-label" for="checkAll"> Select all</label>
					</div>
                    <div class="row">
						<?php 
						$query = "SELECT `permission_group`, `id`, `name`, `description` FROM `permissions` ORDER BY `id`";
						$result = $GLOBALS['conn']->query($query);
						$permissions = [];
						if ($result->num_rows > 0) {
						    while ($row = $result->fetch_assoc()) {
						        $permissions[$row['permission_group']][] = $row;
						    }
						}
						?>

						<?php foreach ($permissions as $group => $groupPermissions): ?>
				            <div class="col col-xs-12 col-md-6 col-lg-4">
				                <div class="card  bg-light mb-3">
				                    <div class="card-header bold"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $group))) ?> Permissions</div>
				                    <div class="card-body">
				                        <?php foreach ($groupPermissions as $permission): ?>
				                            <div class="form-check cursor form-switch">
				                                <input class="form-check-input user_permission" type="checkbox" 
				                                       id="permission_<?= $permission['id'] ?>" 
				                                       <?php if(in_array($permission['id'], $userPermissions)) echo 'checked="checked"'; ?>
				                                       value="<?= $permission['id'] ?>">
				                                <label class="form-check-label cursor" for="permission_<?= $permission['id'] ?>">
				                                    <?= htmlspecialchars($permission['description']) ?>
				                                </label>
				                            </div>
				                        <?php endforeach; ?>
				                    </div>
				                </div>
				            </div>
				        <?php endforeach; ?>
					</div>

                    <hr>
                    <div class="row">
                    	<div class="col-sm-12 justify-content-end d-flex">
                    		<a href="<?=baseUri();?>/employees" class="btn smr-10 btn-secondary cursor" style="min-width: 100px;">Cancel</a>
                			<button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Save</button>
                    	</div>
                    </div>
                </div>
            </div>

            
        </form>
		</div>
	</div>

				
</div>


<?php 
// require('org_edit.php');
?>
