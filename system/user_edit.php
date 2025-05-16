<div class="modal fade "  data-bs-focus="false" id="editUser" tabindex="-1" role="dialog" aria-labelledby="editUserLabel" aria-hidden="true">
    <div class="modal-dialog" role="Category" style="min-width:400px; width: 50vw; max-width: 400px;">
        <form class="modal-content" id="editUserForm" style="border-radius: 14px 14px 0px 0px; margin-top: 25px;">
        	<div class="modal-header">
                <h5 class="modal-title" id="editUserLabel">Edit user Information</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group relative">
                            <label class="label required" for="full_name4Edit">Full name</label>
                            <input type="hidden" name="userId4Edit" id="userId4Edit">
                            <input type="text"  class="form-control " id="full_name4Edit" name="full_name4Edit">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="label " for="phone4Edit">Phone number</label>
                            <input type="text"  class="form-control " id="phone4Edit" name="phone4Edit" placeholder="">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="label  " for="email4Edit">Email</label>
                            <input type="text"  class="form-control " id="email4Edit" name="email4Edit" placeholder="">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="label " for="sysRole4Edit">Role</label>
                            <select  class="form-control validate" data-msg="Please select user role." id="sysRole4Edit" name="sysRole4Edit" >
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
                            <label class="label " for="username4Edit">Username</label>
                            <input type="text"  class="form-control " id="username4Edit" name="username4Edit" placeholder="">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group">
                            <label class="label required" for="slcStatus">Status</label>
                            <select  class="form-control " id="slcStatus" name="slcStatus">
                                <option value="Active">Active</option>
                                <option value="Suspended">Suspended</option>
                            </select>
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


