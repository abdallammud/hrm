<header class="top-header">
	<nav class="navbar navbar-expand align-items-center gap-4">
		<div class="btn-toggle">
			<a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
		</div>
		<div class="search-bar flex-grow-1">
			
		</div>
		<ul class="navbar-nav gap-1 nav-right-links align-items-center">
			<li class="nav-item d-lg-none mobile-search-btn">
				<a class="nav-link" href="javascript:;">
					<i class="material-icons-outlined">search</i>
				</a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="avascript:;" data-bs-toggle="dropdown">
					<!-- <span class="material-symbols-outlined">light_mode</span> -->
					<i class="material-icons-outlined">light_mode</i>
				</a>
				<ul class="dropdown-menu dropdown-menu-end">
					<li>
						<a data-color="light" class="dropdown-item toggle-system-color d-flex align-items-center py-2" href="javascript:;">
							<i class="material-icons-outlined">light_mode</i>
							<span class="ms-2">Light mode</span>
						</a>
					</li>
					<li>
						<a data-color="dark" class="dropdown-item toggle-system-color d-flex align-items-center py-2" href="javascript:;">
							<i class="material-icons-outlined">dark_mode</i>
							<span class="ms-2">Dark Mode</span>
						</a>
					</li>
					<li>
						<a data-color="blue-theme" class="dropdown-item toggle-system-color d-flex align-items-center py-2" href="javascript:;">
							<i style="color:#181f4a;" class="material-icons-outlined">contrast</i>
							<span class="ms-2">Dark blue</span>
						</a>
					</li>
					
				</ul>
			</li>
			
			<li class="nav-item dropdown">
				<a href="javascrpt:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
					<img src="<?=baseUri();?>/assets/images/avatars/<?=$_SESSION['avatar'];?>" class="rounded-circle p-1 border" width="45" height="45" alt="">
				</a>
				<div class="dropdown-menu dropdown-user dropdown-menu-end shadow">
				<a class="dropdown-item  gap-2 py-2" href="javascript:;">
					<div class="text-center">
						<img src="<?=baseUri();?>/assets/images/avatars/<?=$_SESSION['avatar'];?>" class="rounded-circle p-1 shadow mb-3" width="90" height="90" alt="">
						<h5 class="user-name mb-0 fw-bold"><?=$_SESSION['full_name'];?></h5>
					</div>
				</a>
				<hr class="dropdown-divider">
				<a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?=baseUri();?>/employees/show/<?=$_SESSION['emp_id'];?>">
					<i class="material-icons-outlined">person_outline</i>
					Profile
				</a>
				<a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?=baseUri();?>/settings/">
					<i class="material-icons-outlined">local_bar</i>
					Setting
				</a>
				<a class="dropdown-item d-flex align-items-center gap-2 py-2"  href="<?=baseUri();?>/dashboard/">
					<i class="material-icons-outlined">dashboard</i>
					Dashboard
				</a>
				
				<hr class="dropdown-divider">
				<a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?=baseUri();?>/logout">
					<i class="material-icons-outlined">power_settings_new</i>
					Logout
				</a>
				</div>
			</li>
		</ul>
	</nav>
</header>