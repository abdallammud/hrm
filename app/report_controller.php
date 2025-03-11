<?php
require('init.php');

if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		if($_GET['action'] == 'search') {
			if ($_GET['endpoint'] === 'employee4Select') {
				$searchFor = isset($_POST['searchFor']) ? $_POST['searchFor'] : '';
				$search = isset($_POST['search']) ? $_POST['search'] : '';

				$options = '';
				$response = [];
				$response['error'] = true;
				if($search) {
					$query = "SELECT * FROM `employees` WHERE `status` = 'active' AND (`full_name` LIKE '$search%' OR `phone_number` LIKE '$search%' OR `email` LIKE '$search%') ORDER BY `full_name` ASC LIMIT 10";
                    $empSet = $GLOBALS['conn']->query($query);
                    if($empSet->num_rows > 0) {
                    	while($row = $empSet->fetch_assoc()) {
                    		$employee_id = $row['employee_id'];
                    		$full_name = $row['full_name'];
                    		$phone_number = $row['phone_number'];

                    		$options .=  '<option value="'.$employee_id.'">'.$full_name.', '.$phone_number.'</option>';
                    		$response['error'] = false;
                    	}
                    } 
				} else {
					$query = "SELECT * FROM `employees` WHERE `status` = 'active' ORDER BY `full_name` ASC LIMIT 10";
                    $empSet = $GLOBALS['conn']->query($query);
                    if($empSet->num_rows > 0) {
                    	while($row = $empSet->fetch_assoc()) {
                    		$employee_id = $row['employee_id'];
                    		$full_name = $row['full_name'];
                    		$phone_number = $row['phone_number'];

                    		$options .=  '<option value="'.$employee_id.'">'.$full_name.', '.$phone_number.'</option>';
                    	}
                    } 
				}

				$response['options'] = $options;
				echo json_encode($response); exit();
			} else if ($_GET['endpoint'] === 'department4Select') {
				$searchFor = isset($_POST['searchFor']) ? $_POST['searchFor'] : '';
				$search = isset($_POST['search']) ? $_POST['search'] : '';

				$options = '';
				$response = [];
				$response['error'] = true;
				if($search) {
					$query = "SELECT * FROM `branches` WHERE `status` = 'active' AND (`name` LIKE '$search%' ) ORDER BY `name` ASC LIMIT 10";
                    $branchSet = $GLOBALS['conn']->query($query);
                    if($branchSet->num_rows > 0) {
                    	while($row = $branchSet->fetch_assoc()) {
                    		$id = $row['id'];
                    		$name = $row['name'];
                    		$options .=  '<option value="'.$id.'">'.$name.'</option>';
                    		$response['error'] = false;
                    	}
                    } 
				} else {
					$query = "SELECT * FROM `branches` WHERE `status` = 'active' ORDER BY `name` ASC LIMIT 10";
                    $branchSet = $GLOBALS['conn']->query($query);
                    if($branchSet->num_rows > 0) {
                    	while($row = $branchSet->fetch_assoc()) {
                    		$id = $row['id'];
                    		$name = $row['name'];
                    		$options .=  '<option value="'.$id.'">'.$name.'</option>';
                    	}
                    } 
				}

				$response['options'] = $options;
				echo json_encode($response); exit();
			} else if ($_GET['endpoint'] === 'location4Select') {
				$searchFor = isset($_POST['searchFor']) ? $_POST['searchFor'] : '';
				$search = isset($_POST['search']) ? $_POST['search'] : '';

				$options = '';
				$response = [];
				$response['error'] = true;
				if($search) {
					$query = "SELECT * FROM `locations` WHERE `status` = 'active' AND (`name` LIKE '$search%' ) ORDER BY `name` ASC LIMIT 10";
                    $locationSet = $GLOBALS['conn']->query($query);
                    if($locationSet->num_rows > 0) {
                    	while($row = $locationSet->fetch_assoc()) {
                    		$id = $row['id'];
                    		$name = $row['name'];
                    		$options .=  '<option value="'.$id.'">'.$name.'</option>';
                    		$response['error'] = false;
                    	}
                    } 
				} else {
					$query = "SELECT * FROM `locations` WHERE `status` = 'active' ORDER BY `name` ASC LIMIT 10";
                    $locationSet = $GLOBALS['conn']->query($query);
                    if($locationSet->num_rows > 0) {
                    	while($row = $locationSet->fetch_assoc()) {
                    		$id = $row['id'];
                    		$name = $row['name'];
                    		$options .=  '<option value="'.$id.'">'.$name.'</option>';
                    	}
                    } 
				}

				$response['options'] = $options;
				echo json_encode($response); exit();
			} 

			exit();
		}
	} else if($_GET['action'] == 'report') {
		$report = $_POST['report'];
		$role = '';
		$status = '';
		$length = isset($_POST['length']) ? (int)$_POST['length'] : 20;
		$searchParam = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
		$orderBy = ''; // Default sorting
		$order = 'ASC';
		$draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
		$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;

		if (isset($_POST['role'])) $role = $_POST['role'];
		if (isset($_POST['status'])) $status = $_POST['status'];

		$result = [
		    'status' => 201,
		    'error' => false,
		    'data' => [],
		    'draw' => $draw,
		    'iTotalRecords' => 0,
		    'iTotalDisplayRecords' => 0,
		    'msg' => ''
		];


		if($report == 'allEmployees') {
			if (isset($_POST['order']) && isset($_POST['order'][0])) {
			    $orderColumnMap = ['staff_no', 'full_name', 'phone_number', 'email', 'branch', 'location'];
			    // var_dump($_POST['order']);
			    $orderByIndex = (int)$_POST['order'][0]['column'];
			    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
			    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
			}
		    // Base query
		    $query = "SELECT * FROM `employees` WHERE `employee_id` IS NOT NULL AND `status` = 'Active'";

		    // Add search functionality
		    if ($searchParam) {
		        $query .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone_number` LIKE '%" . escapeStr($searchParam) . "%' OR `email` LIKE '%" . escapeStr($searchParam) . "%'  OR `branch` LIKE '%" . escapeStr($searchParam) . "%' OR `location` LIKE '%" . escapeStr($searchParam) . "%' )";
		    }

		    // Add ordering
		    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

		    // Execute query
		    $employees = $GLOBALS['conn']->query($query);

		    // Count total records for pagination
		    $countQuery = "SELECT COUNT(*) as total FROM `employees` WHERE `employee_id` IS NOT NULL AND `status` = 'Active'";
		    if ($searchParam) {
		        $countQuery .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone_number` LIKE '%" . escapeStr($searchParam) . "%' OR `email` LIKE '%" . escapeStr($searchParam) . "%'  OR `branch` LIKE '%" . escapeStr($searchParam) . "%' OR `location` LIKE '%" . escapeStr($searchParam) . "%' )";
		    }

		    // Execute count query
		    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
		    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

		    if ($employees->num_rows > 0) {
		        while ($row = $employees->fetch_assoc()) {
		            $result['data'][] = $row;
		        }
		        $result['iTotalRecords'] = $totalRecords;
		        $result['iTotalDisplayRecords'] = $totalRecords;
		        $result['msg'] = $employees->num_rows . " records were found.";
		    } else {
		        $result['msg'] = "No records found";
		    }
		} else if($report == 'attendance') {
			$month = $_POST['month'];
			$month = date('Y-m', strtotime($month));
			if (isset($_POST['order']) && isset($_POST['order'][0])) {
			    $orderColumnMap = ['staff_no', 'full_name', 'present_count', 'paid_leave_count', 'unpaid_leave_count', 'not_hired_count', 'holiday_count'];
			    // var_dump($_POST['order']);
			    $orderByIndex = (int)$_POST['order'][0]['column'];
			    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
			    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
			}
		    // Base query
		    $query = "SELECT emp_id, staff_no, full_name, SUM(CASE WHEN status = 'P' THEN 1 ELSE 0 END) AS present_count, SUM(CASE WHEN status = 'PL' THEN 1 ELSE 0 END) AS paid_leave_count, SUM(CASE WHEN status = 'UPL' THEN 1 ELSE 0 END) AS unpaid_leave_count, SUM(CASE WHEN status = 'NH' THEN 1 ELSE 0 END) AS not_hired_count, SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) AS holiday_count, SUM(CASE WHEN status = 'N' THEN 1 ELSE 0 END) AS no_show_count FROM  atten_details  WHERE `atten_date` LIKE '$month%' ";

		    // Add search functionality
		    if ($searchParam) {
		        $query .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' )";
		    }

		    // Add ordering
		    $query .= " GROUP BY emp_id, full_name ORDER BY `$orderBy` $order LIMIT $start, $length";

		    // Execute query
		    $employees = $GLOBALS['conn']->query($query);

		    // Count total records for pagination
		    $countQuery = "SELECT COUNT(*) as total FROM `employees` WHERE `employee_id` IS NOT NULL AND `status` = 'Active'";
		    if ($searchParam) {
		        $countQuery .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' )";
		    }

		    // Execute count query
		    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
		    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

		    if ($employees->num_rows > 0) {
		        while ($row = $employees->fetch_assoc()) {
		        	$emp_id = $row['emp_id'];
		        	$paid_leave_count = $row['paid_leave_count'];
		        	$unpaid_leave_count = $row['unpaid_leave_count'];
		        	$not_hired_count = $row['not_hired_count'];
		        	$holiday_count = $row['holiday_count'];
		        	$required_days = $worked_days = 0;
		        	$employeeInfo = get_data('employees', ['employee_id' => $emp_id]);
		        	if($employeeInfo) {
		        		$employeeInfo = $employeeInfo[0];
		        		$work_days = $employeeInfo['work_days'];
		        		$required_days = getWorkdaysInMonth($month, $work_days = 0);
		        		$required_days -= $not_hired_count + $holiday_count;
		        		$worked_days = $required_days - $paid_leave_count - $unpaid_leave_count;
		        	}
		        	$row['required_days'] =$required_days;
		        	$row['worked_days'] =$worked_days;
		            $result['data'][] = $row;
		        }
		        $result['iTotalRecords'] = $totalRecords;
		        $result['iTotalDisplayRecords'] = $totalRecords;
		        $result['msg'] = $employees->num_rows . " records were found.";
		    } else {
		        $result['msg'] = "No records found";
		    }
		}

		echo json_encode($result);
	}
}

?>