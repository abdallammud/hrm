<?php
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

function formatRelativeDate($date) {
	$time = strtotime($date);
	$today = strtotime(date("Y-m-d"));
	$yesterday = strtotime("-1 day", $today);

	if ($time >= $today) {
		return "Today " . date("H:i", $time);
	} elseif ($time >= $yesterday) {
		return "Yesterday " . date("H:i", $time);
	} else {
		return date("M d, Y H:i", $time);
	}
}

function getPriorityIcon($priority) {
	switch ($priority) {
		case 'critical':
			return ['icon' => '⚠️', 'color' => 'red'];
		case 'high':
			return ['icon' => '🔴', 'color' => 'darkred'];
		case 'medium':
			return ['icon' => '🟠', 'color' => 'orange'];
		case 'low':
		default:
			return ['icon' => '🟢', 'color' => 'green'];
	}
}

$notificationsCount = 0;
if ($user_id > 0) {
	$sql = "SELECT id, subject, details, message, priority, added_date, link
			FROM notifications 
			WHERE recipient_id = ? AND is_read = 0
			ORDER BY added_date DESC";

	if ($stmt = $GLOBALS['conn']->prepare($sql)) {
		$stmt->bind_param("i", $user_id);
		$stmt->execute();
		$result = $stmt->get_result();

		$notificationsCount = $result->num_rows;
	}
}
?>
<header class="top-header">
	<nav class="navbar navbar-expand align-items-center gap-4">
		<div class="btn-toggle">
			<a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
		</div>
		<div class="search-bar flex-grow-1">
			
		</div>
		<ul class="navbar-nav gap-1 nav-right-links align-items-center">
			<!-- <li class="nav-item d-lg-none mobile-search-btn">
				<a class="nav-link" href="javascript:;">
					<i class="material-icons-outlined">search</i>
				</a>
			</li> -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative " data-bs-auto-close="outside" data-bs-toggle="dropdown" href="javascript:;" aria-expanded="true"><i class="material-icons-outlined">notifications</i>
                <span class="badge-notify"><?php echo $notificationsCount; ?></span>
                </a>
                <div class="dropdown-menu dropdown-notify dropdown-menu-end shadow " data-bs-popper="static">
                    <div class="px-3 py-1 d-flex align-items-center justify-content-between border-bottom">
                        <h5 class="notiy-title mb-0">Notifications</h5>
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle dropdown-toggle-nocaret option" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="material-icons-outlined">
                            more_vert
                            </span>
                            </button>
                            <div class="dropdown-menu dropdown-option dropdown-menu-end shadow">
                                <!-- <div>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i class="material-icons-outlined fs-6">inventory_2</i>Archive All</a>
                                </div> -->
                                <div onclick="markAsRead('all')">
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i class="material-icons-outlined fs-6">done_all</i>Mark all as read</a>
                                </div>
                                <!-- <div>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i class="material-icons-outlined fs-6">mic_off</i>Disable Notifications</a>
                                </div>
                                <div>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" ><i class="material-icons-outlined fs-6">grade</i>Whats new ?</a>
                                </div> -->
                                <!-- <div>
                                    <hr class="dropdown-divider">
                                </div>
                                <div>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i class="material-icons-outlined fs-6">leaderboard</i>Reports</a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <div class="notify-list ps">
                       
						<?php

						if ($notificationsCount > 0) {
							echo '<div class="notification-wrapper">';
							while ($row = $result->fetch_assoc()) {
								$priority = getPriorityIcon($row['priority']);
								$dateLabel = formatRelativeDate($row['added_date']);
								?>

								<div data-id="<?php echo $row['id']; ?>">
									<a class="dropdown-item border-bottom py-2" href="javascript:;">
										<div class="d-flex align-items-center gap-3">
											<div class="">
												<i class="bi bi-bell" style="color:<?php echo $priority['color']; ?>; font-size: 28px;">
													
												</i>
											</div>
											<div class="">
												<h5 class="notify-title"><?php echo htmlspecialchars($row['subject']); ?></h5>
												<p class="mb-0 notify-desc"><?php echo htmlspecialchars($row['details']); ?></p>
												<p class="mb-0 notify-time"><?php echo $dateLabel; ?></p>
											</div>
											<div onclick="markAsRead(<?php echo $row['id']; ?>)" class="notify-close position-absolute end-0 me-3">
												<i class="material-icons-outlined fs-6">close</i>
											</div>
										</div>
									</a>
								</div>
								
								

								<?php
							}
							echo '</div>';
						} else {
							echo "<p style='color:#666;'>No new notifications 🎉</p>";
						}
							
						?>
                    
                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                        </div>
                        <div class="ps__rail-y" style="top: 0px; right: 0px;">
                            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                        </div>
                    </div>
                </div>
            </li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="avascript:;" data-bs-toggle="dropdown">
					<!-- <span class="material-symbols-outlined">light_mode</span> -->
					<i class="bi bi-brightness-high"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-end">
					<li>
						<a data-color="light" class="dropdown-item toggle-system-color d-flex align-items-center py-2" href="javascript:;">
							<i class="bi bi-brightness-high"></i>
							<span class="ms-2">Light mode</span>
						</a>
					</li>
					<li>
						<a data-color="dark" class="dropdown-item toggle-system-color d-flex align-items-center py-2" href="javascript:;">
							<i class="bi bi-moon"></i>
							<span class="ms-2">Dark Mode</span>
						</a>
					</li>
					<li>
						<a data-color="blue-theme" class="dropdown-item toggle-system-color d-flex align-items-center py-2" href="javascript:;">
							<i class="bi bi-brightness-low-fill"></i>
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