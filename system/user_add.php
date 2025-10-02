
<div class="modal fade "  data-bs-focus="false" id="addUser" tabindex="-1" role="dialog" aria-labelledby="addUserLabel" aria-hidden="true">
    <div class="modal-dialog" role="Category" style="min-width:400px; width: 50vw; max-width: 400px;">
        <form class="modal-content" id="addUserForm" style="border-radius: 14px 14px 0px 0px; margin-top: 25px;">
        	<div class="modal-header">
                <h5 class="modal-title" id="addUserLabel">Add new user</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group relative">
                            <label class="label required" for="full_name">Full name</label>
                            <input type="text"  class="form-control " id="full_name" name="full_name">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="label " for="phone">Phone number</label>
                            <input type="text"  class="form-control " id="phone" name="phone" placeholder="">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="label  " for="email">Email</label>
                            <input type="text"  class="form-control " id="email" name="email" placeholder="">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="label " for="sysRole">Role</label>
                            <select  class="form-control validate" data-msg="Please select user role." id="sysRole" name="sysRole" >
                                <option value="">- Select</option>
                                <?php
                                $query = "SELECT `id`, `name` FROM `sys_roles`";
                                $roles = $GLOBALS['conn']->query($query);
                                if ($roles->num_rows > 0) {
                                    while ($row = $roles->fetch_assoc()) {
                                        echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
                                    }
                                }   
                                ?>
                            </select>
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="label " for="reportsTo">Reports To</label>
                            <select  class="form-control my-select" data-msg="Please select user role." data-live-search="true" multiple id="reportsTo" name="reportsTo" >
                                <option value="">- Select</option>
                                <?php
                                $query = "SELECT `user_id`, `full_name`, `role` FROM `users`";
                                $users = $GLOBALS['conn']->query($query);
                                if ($users->num_rows > 0) {
                                    while ($row = $users->fetch_assoc()) {
                                        $role = $row['role'];
                                        $roleName = $GLOBALS['userClass']->get_roleName($row['user_id']);
                                        echo '<option value="'.$row['user_id'].'">'.$row['full_name'].' ('.$roleName.')</option>';
                                    }
                                }   
                                ?>
                            </select>
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="label " for="username">Username</label>
                            <input type="text"  class="form-control " id="username" name="username" placeholder="">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="label " for="password">Password</label>
                            <input type="password"  class="form-control " id="password" name="password" placeholder="">
                            <span class="form-error text-danger">This is error</span>
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


<style type="text/css">
    .card-header {
        color: var(--bs-heading-color);
    }

    
</style>
