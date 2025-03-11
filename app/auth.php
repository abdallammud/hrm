<?php 
require('Model.php');
require('UserClass.php');
session_start();
$userClass = $GLOBALS['userClass'];
if(isset($_GET['action'])) {
	if($_GET['action'] == 'login') {
		$result['msg'] = 'Correct action';
		$result['status'] = 201;

		$username = $_POST['username'];
	    $password = $_POST['password'];

	    $getUser = "SELECT * FROM `users` WHERE (`username` = '$username' OR `email` LIKE '$username')";
	    $userSet = $GLOBALS['conn']->query($getUser);
	    if($userSet->num_rows < 1) {
	        $result['error'] = true;
	        $result['errType']  = 'username';
	        $result['msg'] = ' Username is not found.';
	        echo json_encode($result); 
	        exit();
	    }

	    while($row = $userSet->fetch_assoc()) {
	        $user_id    = $row['user_id'];
	        $passDB     = $row['password'];
	        $status     = $row['status'];
	        $role     	= $row['role'];
	        $full_name  = $row['full_name'];

	        if (!password_verify($password, $passDB)) {
	            $result['error'] = true;
	            $result['errType']  = 'password';
	            $result['msg'] = ' Incorrect password.';
	            echo json_encode($result); 
	            exit();
	        }

	        if(strtolower($status) != 'active') {
	            $result['error'] = true;
	            $result['errType']  = 'username';
	            $result['msg'] = ' Inactive user. Please contact system adminstrator.';
	            echo json_encode($result); 
	            exit();
	        }
	    }

	    $land = get_landingMenu($user_id);






	    if(set_sessions($user_id)) {
	        setLoginInfo($user_id);
	    } else {
	        $result['msg']    = ' Couln\'t set sessions.';
	        $result['error'] = true;
	        $result['errType']  = 'sessions';
	        echo json_encode($result); exit();
	    }

	    $result['msg'] = "Succefully logged in.";
	    $result['error'] = false;
	    $result['land'] = $land;
	    echo json_encode($result); exit(); 

	} 
}

function get_landingMenu($user_id) {
    $menu = null;
    $permissions = $GLOBALS['userClass']->getPermissions($user_id);
    // var_dump($permissions);
    $routes = [
        'view_dashboard' => './dashboard',
        'manage_company_info' => './org',
        'manage_departments' => './departments',
        'manage_duty_locations' => './locations',
        'manage_states' => './locations',
        'manage_company_banks' => './banks',
        'view_employees' => './employees',
        'manage_designations' => './designations',
        'view_payroll' => './payroll',
        'manage_bonus_allowances' => './bonus',
        'view_attendance' => './attendance',
        'manage_timesheets' => './timesheet',
        'manage_leaves' => './leave',
        'view_payment_history' => './payments',
        'manage_users' => './users',
        'view_reports' => './reports',
        'manage_settings' => './settings'
    ];
    
    foreach ($permissions as $permission) {
        if (isset($routes[$permission])) {
            $menu = $routes[$permission];
            break;
        }
    }

    return $menu;
}

function set_sessions($user_id) {
	$user = $GLOBALS['userClass']->get($user_id);
	// var_dump($user);
	$_SESSION['full_name'] 	= $user['full_name'];
	$_SESSION['emp_id'] 	= $user['emp_id'];
	$_SESSION['phone'] 		= $user['phone'];
	$_SESSION['email'] 		= $user['email'];
	$_SESSION['username'] 	= $user['username'];
	$_SESSION['myUser'] 	= $user['username'];
	$_SESSION['role'] 		= $user['role'];
	$_SESSION['branch_id'] 		= $user['branch_id'];
	$_SESSION['user_id'] 		= $user['user_id'];

	$emp_id = $user['emp_id'];
	$avatar = 'male_avatar.png';
	$employeeInfo = $GLOBALS['userClass']->get_emp($emp_id); 
	if(count($employeeInfo) > 0) {
		if(!$employeeInfo['avatar']) {
			if(strtolower($employeeInfo['gender']) == 'female')  {
				$employeeInfo['avatar'] = 'female_avatar.png';
			} else {
				$employeeInfo['avatar'] = 'male_avatar.png';
			}
		}

		$avatar = $employeeInfo['avatar'];
	}

	$_SESSION['avatar'] = $avatar;

	$sysPermissions = $GLOBALS['permissionsClass']->read_all();
	$userPermissions = $GLOBALS['userClass']->getPermissions($user_id);

	foreach ($sysPermissions as $sysPermission) {
		if(in_array($sysPermission['name'], $userPermissions)) {
			$_SESSION[$sysPermission['name']] = 'on';
		} else {
			$_SESSION[$sysPermission['name']] = 'off';
		}
	}
	return true;
}

function authenticate() {
	if(!isset($_SESSION['myUser']) || !$_SESSION['myUser']) {
        return false;
    }

    $username = $_SESSION['myUser'];

    $getUser = "SELECT * FROM `users` WHERE (`username` = '$username' OR `email` LIKE '$username') AND `status` NOT IN ('deleted')";
    $userSet = $GLOBALS['conn']->query($getUser);
    $status = '';
    while($row = $userSet->fetch_assoc()) {
        $user_id    = $row['user_id'];
        $passDB     = $row['password'];
        $status     = $row['status'];
        $role     	= $row['role'];
        $full_name  = $row['full_name'];
    }

    if(strtolower($status) != 'active') {
    	$_SESSION['isLogged'] = false;
    	return false;
    }

    set_sessions($user_id); 

    return true;
}

function setLoginInfo($userID, $logout = false) {
    $this_time = date('Y-m-d h:i:s');
    $is_logged = 'yes';
    $column = 'this_time';
    if($logout) { $is_logged = 'no'; $column = 'last_logged';}
    $stmt = $GLOBALS['conn']->prepare("UPDATE `users` SET `is_logged` = ?, `$column` = ? WHERE `user_id` = ?");
    $stmt->bind_param("sss", $is_logged, $this_time, $userID);
    if(!$stmt->execute()) {
        echo $stmt->error;
    }
}

function check_session($authKey) {
    if (is_array($authKey)) {
        foreach ($authKey as $key) {
            if (isset($_SESSION[$key]) && $_SESSION[$key] === 'on') {
                return true;
            }
        }
        return false;
    }

    return isset($_SESSION[$authKey]) && $_SESSION[$authKey] === 'on';
}

function check_auth($authKey, $msg = "You are not authorized to perform this action.") {
	if(!check_session($authKey)) {
		$result = [];
		$result['error'] = true;
		$result['msg'] = $msg;
		echo json_encode($result);
		exit();
	}
	return true;
}



?>