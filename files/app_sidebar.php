<?php 
$menu 	= $_GET['menu'];
$action = $_GET['action'] ?? null;
$tab 	= $_GET['tab'] ?? null;


$menus = get_menu_config();

$sidebar = '';
foreach ($menus as $sideMenu) {
	$active = '';
	if(isset($sideMenu['sub'])) {
		$showSub = '';
		if($menu == $sideMenu['menu']) {
			$active = 'mm-active';
			$showSub = 'mm-show';
		}
		if(check_session($sideMenu['auth'])) {
			$sidebar .= '
				<li class="'.$active.'">
					<a href="javascript:;" class="has-arrow">
						<div class="parent-icon">
							<i class="material-icons-outlined">'.$sideMenu['icon'].'</i>
						</div>
						<div class="menu-title">'.$sideMenu['name'].'</div>
					</a>
					<ul class="'.$showSub.'">';
			foreach ($sideMenu['sub'] as $sub) {
				if(check_session($sub['auth'])) {
					$activeSub = '';
					if($tab == $sub['route']) $activeSub = 'active mm-active';
					$sidebar .= '
						<li class="'.$activeSub.'">
							<a href="'.baseUri().'/'.strtolower($sub['route']).'">
								<i class="material-icons-outlined">arrow_right</i>
								'.$sub['name'].'
							</a>
						</li>';
				}
			}

			$sidebar .= '</ul></li>';
		}
	} else {
		if($menu == $sideMenu['menu']) $active = 'mm-active';
		if(check_session($sideMenu['auth'])) {
			$sidebar .= '<li class="'.$active.'">
				<a href="'.baseUri().'/'.$sideMenu['route'].'">
					<div class="parent-icon">
						<i class="material-icons-outlined">'.$sideMenu['icon'].'</i>
					</div>
					<div class="menu-title">'.$sideMenu['name'].'</div>
				</a>
			</li>';
		}
	}
}

?>

<aside class="sidebar-wrapper" data-simplebar="true">
	<div class="sidebar-header">
		<div class="logo-icon" style="width:100%;">
			<img style="width:100%; height: 50px;" src="<?=baseUri();?>/assets/images/<?=get_logo_name_from_url();?>" class="logo-img" alt="">
		</div>
		<div class="logo-name flex-grow-1">
			<!-- <h5 class="mb-0">Asheeri</h5> -->
		</div>
		<div class="sidebar-close">
			<span class="material-icons-outlined">close</span>
		</div>
	</div>
	<div class="sidebar-nav">
		<!--navigation-->
		<ul class="metismenu" id="sidenav">	
			<?=$sidebar;?>
		</ul>
		<!--end navigation-->
	</div>
</aside>