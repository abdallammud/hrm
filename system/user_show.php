<?php 
$user_id = $_GET['user_id'];
$user = $GLOBALS['userClass']->read($user_id);
// var_dump($user);

if(!$user['avatar']) {
	$user['avatar'] = 'male_avatar.png';
}

$role_id = $user['role'];
$role = isset($GLOBALS['sys_roles']->get($role_id)['name']) ? $GLOBALS['sys_roles']->get($role_id)['name'] : '';

?>
<div class="page content">
	<div class="tab-content" id="userPage">
		<div class="tab-pane fade show active" id="empInfo" role="tabpanel" aria-labelledby="home-tab">
			<div class="page-breadcrumb d-sm-flex align-items-center mb-3">
				<h5 class="">User profile </h5>
				<div class="ms-auto d-sm-flex">
					<div class="btn-group smr-10">
						<a href="<?=baseUri();?>/user"  class="btn btn-secondary">Go Back</a>
					</div>            
				</div>
			</div>
            <hr>
			<div class="row">
				<!-- <div class="col-lg-4 col-md-12">
					<div class="card">
						<div class="card-header bold">
							Profile picture
						</div>
						<div class="card-body">
							<div class="sflex swrap emp-profile sjcenter">
								<img style="border-color: <?=$border_color;?>;" class="profile-img sflex-basis-100" src="<?=baseUri();?>/assets/images/avatars/<?=$user['avatar'];?>">
								<label class="profile-img-edit">
									<input type="hidden" id="user_id" value="<?=$user_id;?>" name="">
									<input type="file" id="profile-img" class="hidden" name="">
									<i class="fa fa-pencil"></i>
								</label>
							</div>
                        </div>  
                    </div>
                </div> -->
                <div class="col-lg-12 col-md-12">
					<div class="card">
						<div class="card-header bold">
							Profile Information
						</div>
						<div class="card-body">
							
							<div class="sflex smt-10 swrap">
								<span class="sflex-basis-100 sflex swrap  ">Full name</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$user['full_name'];?> </p>
								
							</div>

							<div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap  ">Email</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$user['email'];?> </p>
								
							</div>

                            <div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap">Phone</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$user['phone'];?> </p>
								
							</div>

                            <div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap">Role</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$role;?> </p>
								
							</div>

                            <div class="sflex smt- swrap">
								<span class="sflex-basis-100 sflex swrap">Username</span>
								<p class="sflex-basis-100 sflex swrap  bold"><?=$user['username'];?>  </p>
								
							</div>
                            
                            <?php if(check_session('edit_users') || $_SESSION['user_id'] == $user_id) { ?>
                                <div class="sflex smt- swrap">
                                    <span class="sflex-basis-100 sflex swrap">Password</span>
                                    <p onclick="return changePasswordModal(<?=$user['user_id'];?>)" class="sflex-basis-100 sflex scenter-items cursor   bold">
                                        <i class="fa fa-pencil smr-10"></i>
                                        Change password 
                                    </p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>



















<div class="modal fade " data-bs-focus="false" id="changePassword" tabindex="-1" role="dialog" aria-labelledby="changePasswordLabel" aria-hidden="true">
    <div class="modal-dialog" role="Category" style="min-width:400px; width: 50vw; max-width: 400px;">
        <form class="modal-content" " method="post" id="changePasswordForm" style="border-radius: 14px 14px 0px 0px; margin-top: 25px;">
        	<div class="modal-header">
                <h5 class="modal-title" id="changePasswordLabel">Change Password</h5>
                <button type="button" class="close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group relative">
                            <label class="label required" for="newPassword">New Password</label>
                            <input type="hidden" name="user_id" id="user_id">
                            <input type="password"  class="form-control " id="newPassword" name="full_name">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-xs-12 col-md-12 col-lg-12">
                        <div class="form-group relative">
                            <label class="label required" for="confirmNewPassword">Repeat New Password</label>
                            <input type="password"  class="form-control " id="confirmNewPassword" name="full_name">
                            <span class="form-error text-danger">This is error</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary cursor " data-bs-dismiss="modal" aria-label="Close" style="min-width: 100px;">Cancel</button>
                <button type="submit" class="btn btn-primary cursor" style="min-width: 100px;">Apply</button>
            </div>
        </form>
    </div>
</div>