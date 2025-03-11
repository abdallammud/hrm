<?php require('./files/login_header.php');?>
<?php require('./asset_config.php');?>
	<div class="mx-3 mx-lg-0">
		<div class="card my-5 col-xl-3 col-xxl-3 col-lg-4 col-md-6 col-sm-12 mt-9 mx-auto rounded-4 overflow-hidden p-4" style="margin-top: 120px !important;">
			<div class="row g-4">
				<div class="col-lg-12 d-flex">
					<div class="card-body">
						<img src="assets/images/<?=get_logo_name_from_url();?>" class="mb-4" width="145" alt="">
						<h4 class="fw-bold">Get Started Now</h4>
						<p class="mb-0">Enter your credentials to login your account</p>

						<div class="form-body mt-4">
							<form class="row g-3" id="userLoginForm">
								<div class="col-12 div">
									<label for="inputEmailAddress" class="form-label">Email or Username</label>
									<input type="text" class="form-control" id="inputEmailAddress" >
									<span class="form-error text-danger">This is error</span>
								</div>
								<div class="col-12 div">
									<label for="inputChoosePassword" class="form-label">Password</label>
									<div class="input-group" id="show_hide_password">
										<input type="password" class="form-control border-end-0" id="inputChoosePassword" 
										>
										<a href="javascript:;" class="input-group-text bg-transparent"><i
										class="bi bi-eye-slash-fill"></i></a>
									</div>
									<span class="form-error text-danger">This is error</span>
								</div>

								<div class="col-md-6 mt-4">
									<!-- <div class="form-check form-switch">
										<input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
										<label class="form-check-label" for="flexSwitchCheckChecked">Remember Me</label>
									</div> -->
								</div>
								<div class="col-md-6 text-end mt-2">
									<a href="#">Forgot Password ?</a>
								</div>
								<div class="col-12 mt-2">
									<div class="d-grid">
										<button type="submit" class="btn btn-primary">Login</button>
									</div>
								</div>
						
							</form>
						</div>
					</div>
				</div>
				

			</div><!--end row-->
		</div>
	</div>

<script src="assets/js/jquery.min.js"></script>

  <script>
    $(document).ready(function () {
      $("#show_hide_password a").on('click', function (event) {
        event.preventDefault();
        if ($('#show_hide_password input').attr("type") == "text") {
          $('#show_hide_password input').attr('type', 'password');
          $('#show_hide_password i').addClass("bi-eye-slash-fill");
          $('#show_hide_password i').removeClass("bi-eye-fill");
        } else if ($('#show_hide_password input').attr("type") == "password") {
          $('#show_hide_password input').attr('type', 'text');
          $('#show_hide_password i').removeClass("bi-eye-slash-fill");
          $('#show_hide_password i').addClass("bi-eye-fill");
        }
      });
    });
  </script>

<?php require('./files/login_footer.php');?>