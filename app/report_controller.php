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
			$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
			$state = isset($_POST['state']) ? $_POST['state'] : '';
			$department = isset($_POST['department']) ? $_POST['department'] : '';
			$location = isset($_POST['location']) ? $_POST['location'] : '';
			$salary = isset($_POST['salary']) ? $_POST['salary'] : '';
			$salary_up = isset($_POST['salary_up']) ? $_POST['salary_up'] : '';
			$age = isset($_POST['age']) ? $_POST['age'] : '';

			if (isset($_POST['order']) && isset($_POST['order'][0])) {
			    $orderColumnMap = ['staff_no', 'full_name', 'phone_number', 'email', 'branch', 'location'];
			    // var_dump($_POST['order']);
			    $orderByIndex = (int)$_POST['order'][0]['column'];
			    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
			    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
			}
		    // Base query
		    $query = "SELECT * FROM `employees` WHERE `employee_id` IS NOT NULL AND `status` = 'Active'";

			if($gender) $query .= " AND `gender` = '$gender'";
			if($state) $query .= " AND `state_id` = '$state'";
			if($department) $query .= " AND `branch_id` = '$department'";
			if($location) $query .= " AND `location_id` = '$location'";
			if($salary) $query .= " AND `salary` >= '$salary'";
			if($salary_up) $query .= " AND `salary` <= '$salary_up'";

			// Age is grouped like 18-24 and so on change the code to handle this
			if ($age) {
				$maxAge = 65;
				if (strpos($age, '-') !== false) {
					// Age is a range (e.g., "18-24")
					list($minAge, $maxAge) = explode('-', $age);
					$query .= " AND `age` >= '$minAge' AND `age` <= '$maxAge'";
				} else {
					// Age is a single value
					$query .= " AND `age` >= '$maxAge'";
				}
			}
		    // Add search functionality
		    if ($searchParam) {
		        $query .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone_number` LIKE '%" . escapeStr($searchParam) . "%' OR `email` LIKE '%" . escapeStr($searchParam) . "%'  OR `branch` LIKE '%" . escapeStr($searchParam) . "%' OR `location_name` LIKE '%" . escapeStr($searchParam) . "%' )";
		    }

		    // Add ordering
		    $query .= " ORDER BY `staff_no` $order LIMIT $start, $length";

		    // Execute query
		    $employees = $GLOBALS['conn']->query($query);

		    // Count total records for pagination
		    $countQuery = "SELECT COUNT(*) as total FROM `employees` WHERE `employee_id` IS NOT NULL AND `status` = 'Active'";
		    if ($searchParam) {
		        $countQuery .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%' OR `phone_number` LIKE '%" . escapeStr($searchParam) . "%' OR `email` LIKE '%" . escapeStr($searchParam) . "%'  OR `branch` LIKE '%" . escapeStr($searchParam) . "%' OR `location_name` LIKE '%" . escapeStr($searchParam) . "%' )";
		    }

			if($gender) $countQuery .= " AND `gender` = '$gender'";
			if($state) $countQuery .= " AND `state_id` = '$state'";
			if($department) $countQuery .= " AND `branch_id` = '$department'";
			if($location) $countQuery .= " AND `location_id` = '$location'";
			if($salary) $countQuery .= " AND `salary` >= '$salary'";
			if($salary_up) $countQuery .= " AND `salary` <= '$salary_up'";

		    // Execute count query
		    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			if($totalRecordsResult) {
				$record = $totalRecordsResult->fetch_assoc();
				$totalRecords = isset($record['total']) ? $record['total'] : 0;
			}

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
		} else if($report == 'absence') {
			$month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');
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
		    $query .= " GROUP BY emp_id, full_name ORDER BY `staff_no` $order LIMIT $start, $length";

		    // Execute query
		    $employees = $GLOBALS['conn']->query($query);

			// Count total records for pagination
		    $countQuery = "SELECT COUNT(*) as total FROM `atten_details` WHERE `atten_date` LIKE '$month%'";
		    if ($searchParam) {
		        $countQuery .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%'  )";
		    }

		    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			if($totalRecordsResult) {
				$record = $totalRecordsResult->fetch_assoc();
				$totalRecords = isset($record['total']) ? $record['total'] : 0;
			}

		    if ($employees->num_rows > 0) {
		        while ($row = $employees->fetch_assoc()) {
		        	$emp_id = $row['emp_id'];
		        	$paid_leave_count = $row['paid_leave_count'];
		        	$unpaid_leave_count = $row['unpaid_leave_count'];
		        	$not_hired_count = $row['not_hired_count'];
		        	$holiday_count = $row['holiday_count'];
		        	$no_show_count = $row['no_show_count'];
		        	$required_days = $worked_days = 0;
		        	$employeeInfo = get_data('employees', ['employee_id' => $emp_id]);
		        	if($employeeInfo) {
		        		$employeeInfo = $employeeInfo[0];
		        		$work_days = $employeeInfo['work_days'];
		        		$required_days = getWorkdaysInMonth($month, $work_days = 0);
		        		$required_days -= $not_hired_count + $holiday_count;
		        		$worked_days = $required_days - $paid_leave_count - $unpaid_leave_count - $no_show_count;
		        	}

					$total_absence = $paid_leave_count + $unpaid_leave_count + $no_show_count;
		        	$row['required_days'] =$required_days;
		        	$row['worked_days'] =$worked_days;
					$row['total_absence'] =$total_absence;
		            $result['data'][] = $row;
		        }
		        $result['iTotalRecords'] = $totalRecords;
		        $result['iTotalDisplayRecords'] = $totalRecords;
		        $result['msg'] = $employees->num_rows . " records were found.";
		    } else {
		        $result['msg'] = "No records found";
		    }
		} else if($report == 'componsation') {
			$month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');
			$month = date('Y-m', strtotime($month));

			if (isset($_POST['order']) && isset($_POST['order'][0])) {
			    $orderColumnMap = ['staff_no', 'full_name', 'present_count', 'paid_leave_count', 'unpaid_leave_count', 'not_hired_count', 'holiday_count'];
			    // var_dump($_POST['order']);
			    $orderByIndex = (int)$_POST['order'][0]['column'];
			    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
			    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
			}
		    // Base query
		    $query = "SELECT  `id`, `payroll_id`, `emp_id`, `staff_no`, `full_name`, `email`, `contract_type`, `job_title`, `month`, `required_days`, `days_worked`, `unpaid_days`, `unpaid_hours`, `bank_name`, `bank_number`, `pay_date`, `paid_by`,  `status`, `base_salary`, `allowance`, `bonus`, `commission`, (`allowance` + `bonus` + `commission`) AS earnings, (`loan` + `advance` + `deductions`) AS `total_deductions`, `tax`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `month` LIKE '$month'";

			// Add search functionality
		    if ($searchParam) {
		        $query .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%'  )";
		    }

			// Add ordering
		    $query .= " GROUP BY emp_id, full_name ORDER BY `staff_no` $order LIMIT $start, $length";

		    // Execute query
		    $employees = $GLOBALS['conn']->query($query);

		    // Count total records for pagination
		    $countQuery = "SELECT COUNT(*) as total FROM `payroll_details` WHERE `month` LIKE '$month'";
		    if ($searchParam) {
		        $countQuery .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%'  )";
		    }

		    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			if($totalRecordsResult) {
				$record = $totalRecordsResult->fetch_assoc();
				$totalRecords = isset($record['total']) ? $record['total'] : 0;
			}

		    if ($employees->num_rows > 0) {
		        while ($row = $employees->fetch_assoc()) {
		        	$emp_id = $row['emp_id'];
		        	$base_salary = $row['base_salary'];
		        	$allowance = $row['allowance'];
		        	$bonus = $row['bonus'];
		        	$commission = $row['commission'];
		        	$earnings = $row['earnings'];
					$net_salary = $row['net_salary'];
		        	

		            $result['data'][] = $row;
		        }
		        $result['iTotalRecords'] = $totalRecords;
		        $result['iTotalDisplayRecords'] = $totalRecords;
		        $result['msg'] = $employees->num_rows . " records were found.";
		    } else {
		        $result['msg'] = "No records found";
		    }
		} else if($report == 'deductions') {
			$month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');
			$month = date('Y-m', strtotime($month));

			if (isset($_POST['order']) && isset($_POST['order'][0])) {
			    $orderColumnMap = ['staff_no', 'full_name', 'earnings', 'total_deductions', 'net_salary'];
			    // var_dump($_POST['order']);
			    $orderByIndex = (int)$_POST['order'][0]['column'];
			    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
			    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
			}
		    // Base query
		    $query = "SELECT `staff_no`, `full_name`, (`allowance` + `bonus` + `commission`) AS earnings, (`loan` + `advance` + `deductions`) AS total_deductions, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `month` LIKE '$month' AND (`loan` + `advance` + `deductions`) > 0";

		    // Add search functionality
		    if ($searchParam) {
		        $query .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%'  )";
		    }

			// Add ordering
		    $query .= " ORDER BY `staff_no` $order LIMIT $start, $length";

		    // Execute query
		    $employees = $GLOBALS['conn']->query($query);

		    // Count total records for pagination
		    $countQuery = "SELECT COUNT(*) as total FROM `payroll_details` WHERE `month` LIKE '$month'";
		    if ($searchParam) {
		        $countQuery .= " AND (`staff_no` LIKE '%" . escapeStr($searchParam) . "%' OR `full_name` LIKE '%" . escapeStr($searchParam) . "%'  )";
		    }

		    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			if($totalRecordsResult) {
				$record = $totalRecordsResult->fetch_assoc();
				$totalRecords = isset($record['total']) ? $record['total'] : 0;
			}

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
		} else if($report == 'payroll') {
			$month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');
			$month = date('Y-m', strtotime($month));

			// Filters
			$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
			$state = isset($_POST['state']) ? $_POST['state'] : '';
			$department = isset($_POST['department']) ? $_POST['department'] : '';
			$location = isset($_POST['location']) ? $_POST['location'] : '';
			$salary = isset($_POST['salary']) ? $_POST['salary'] : '';
			$salary_up = isset($_POST['salary_up']) ? $_POST['salary_up'] : '';
			$age = isset($_POST['age']) ? $_POST['age'] : '';

			// Ordering
			$orderBy = 'pd.staff_no';
			$order = 'ASC';
			if (isset($_POST['order']) && isset($_POST['order'][0])) {
				$orderColumnMap = ['pd.staff_no', 'pd.full_name', 'earnings', 'total_deductions', 'net_salary'];
				$orderByIndex = (int)$_POST['order'][0]['column'];
				$orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				$order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
			}

			// Base query with JOIN to employees
			$query = "SELECT 
						pd.staff_no, 
						pd.full_name, 
						pd.base_salary,
						(pd.allowance + pd.bonus + pd.commission) AS earnings,
						(pd.loan + pd.advance + pd.deductions) AS total_deductions,
						(pd.base_salary + (pd.allowance + pd.bonus + pd.commission) - 
						(pd.loan + pd.advance + pd.deductions) - pd.tax) AS net_salary,
						e.gender, e.state, e.branch AS department, e.location_name AS location, e.salary AS base_salary_emp, e.date_of_birth
					FROM payroll_details pd
					INNER JOIN employees e ON pd.emp_id = e.employee_id
					WHERE pd.month = '$month' AND e.status = 'Active'";

			// Filters
			if ($gender) $query .= " AND e.gender = '$gender'";
			if ($state) $query .= " AND e.state_id = '$state'";
			if ($department) $query .= " AND e.branch_id = '$department'";
			if ($location) $query .= " AND e.location_id = '$location'";
			if ($salary) $query .= " AND e.salary >= '$salary'";
			if ($salary_up) $query .= " AND e.salary <= '$salary_up'";

			// Handle age range
			if ($age) {
				if (strpos($age, '-') !== false) {
					list($minAge, $maxAge) = explode('-', $age);
					$query .= " AND TIMESTAMPDIFF(YEAR, e.date_of_birth, CURDATE()) BETWEEN '$minAge' AND '$maxAge'";
				} else {
					$query .= " AND TIMESTAMPDIFF(YEAR, e.date_of_birth, CURDATE()) >= '$age'";
				}
			}

			// Search
			if ($searchParam) {
				$search = escapeStr($searchParam);
				$query .= " AND (pd.staff_no LIKE '%$search%' 
								OR pd.full_name LIKE '%$search%' 
								OR e.branch LIKE '%$search%' 
								OR e.location_name LIKE '%$search%' 
								OR e.state LIKE '%$search%')";
			}

			// Order & limit
			$query .= " ORDER BY $orderBy $order LIMIT $start, $length";

			// Execute
			$payroll = $GLOBALS['conn']->query($query);

			// Count query for pagination
			$countQuery = "SELECT COUNT(*) AS total 
						FROM payroll_details pd 
						INNER JOIN employees e ON pd.emp_id = e.employee_id
						WHERE pd.month = '$month' AND e.status = 'Active'";

			if ($gender) $countQuery .= " AND e.gender = '$gender'";
			if ($state) $countQuery .= " AND e.state_id = '$state'";
			if ($department) $countQuery .= " AND e.branch_id = '$department'";
			if ($location) $countQuery .= " AND e.location_id = '$location'";
			if ($salary) $countQuery .= " AND e.salary >= '$salary'";
			if ($salary_up) $countQuery .= " AND e.salary <= '$salary_up'";
			if ($searchParam) {
				$countQuery .= " AND (pd.staff_no LIKE '%$search%' OR pd.full_name LIKE '%$search%' 
								OR e.branch LIKE '%$search%' OR e.location_name LIKE '%$search%' OR e.state LIKE '%$search%')";
			}

			$totalResult = $GLOBALS['conn']->query($countQuery);
			$totalRecords = 0;
			if ($totalResult) {
				$record = $totalResult->fetch_assoc();
				$totalRecords = $record['total'] ?? 0;
			}

			// Format response
			if ($payroll && $payroll->num_rows > 0) {
				while ($row = $payroll->fetch_assoc()) {
					$result['data'][] = $row;
				}
				$result['iTotalRecords'] = $totalRecords;
				$result['iTotalDisplayRecords'] = $totalRecords;
				$result['msg'] = $payroll->num_rows . " records were found.";
			} else {
				$result['msg'] = "No records found";
			}
		} else if($report == 'taxation') {
			$month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');
			$month = date('Y-m', strtotime($month));

			// Filters
			$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
			$state = isset($_POST['state']) ? $_POST['state'] : '';
			$department = isset($_POST['department']) ? $_POST['department'] : '';
			$location = isset($_POST['location']) ? $_POST['location'] : '';
			$salary = isset($_POST['salary']) ? $_POST['salary'] : '';
			$salary_up = isset($_POST['salary_up']) ? $_POST['salary_up'] : '';
			$age = isset($_POST['age']) ? $_POST['age'] : '';

			$orderBy = 'pd.staff_no';
			$order = 'ASC';
			if (isset($_POST['order']) && isset($_POST['order'][0])) {
				$orderColumnMap = ['pd.staff_no', 'pd.full_name', 'earnings', 'total_deductions', 'tax', 'net_salary'];
				$orderByIndex = (int)$_POST['order'][0]['column'];
				$orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				$order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
			}

			// Base query with join
			$query = "SELECT 
						pd.staff_no, 
						pd.full_name, 
						pd.base_salary,
						(pd.allowance + pd.bonus + pd.commission) AS earnings,
						(pd.loan + pd.advance + pd.deductions) AS total_deductions,
						pd.tax,
						(pd.base_salary + (pd.allowance + pd.bonus + pd.commission) - 
						(pd.loan + pd.advance + pd.deductions) - pd.tax) AS net_salary,
						e.gender, e.state, e.branch AS department, e.location_name AS location, e.salary AS base_salary_emp, e.date_of_birth
					FROM payroll_details pd
					INNER JOIN employees e ON pd.emp_id = e.employee_id
					WHERE pd.month = '$month' AND e.status = 'Active'";

			// Filters
			if ($gender) $query .= " AND e.gender = '$gender'";
			if ($state) $query .= " AND e.state_id = '$state'";
			if ($department) $query .= " AND e.branch_id = '$department'";
			if ($location) $query .= " AND e.location_id = '$location'";
			if ($salary) $query .= " AND e.salary >= '$salary'";
			if ($salary_up) $query .= " AND e.salary <= '$salary_up'";
			if ($age) {
				if (strpos($age, '-') !== false) {
					list($minAge, $maxAge) = explode('-', $age);
					$query .= " AND TIMESTAMPDIFF(YEAR, e.date_of_birth, CURDATE()) BETWEEN '$minAge' AND '$maxAge'";
				} else {
					$query .= " AND TIMESTAMPDIFF(YEAR, e.date_of_birth, CURDATE()) >= '$age'";
				}
			}

			// Search
			if ($searchParam) {
				$search = escapeStr($searchParam);
				$query .= " AND (pd.staff_no LIKE '%$search%' 
								OR pd.full_name LIKE '%$search%' 
								OR e.branch LIKE '%$search%' 
								OR e.location_name LIKE '%$search%' 
								OR e.state LIKE '%$search%')";
			}

			// Order & limit
			$query .= " ORDER BY $orderBy $order LIMIT $start, $length";

			// Execute
			$payroll = $GLOBALS['conn']->query($query);

			// Count
			$countQuery = "SELECT COUNT(*) AS total 
						FROM payroll_details pd 
						INNER JOIN employees e ON pd.emp_id = e.employee_id
						WHERE pd.month = '$month' AND e.status = 'Active'";

			if ($gender) $countQuery .= " AND e.gender = '$gender'";
			if ($state) $countQuery .= " AND e.state_id = '$state'";
			if ($department) $countQuery .= " AND e.branch_id = '$department'";
			if ($location) $countQuery .= " AND e.location_id = '$location'";
			if ($salary) $countQuery .= " AND e.salary >= '$salary'";
			if ($salary_up) $countQuery .= " AND e.salary <= '$salary_up'";
			if ($searchParam) {
				$countQuery .= " AND (pd.staff_no LIKE '%$search%' OR pd.full_name LIKE '%$search%' 
								OR e.branch LIKE '%$search%' OR e.location_name LIKE '%$search%' OR e.state LIKE '%$search%')";
			}

			$totalResult = $GLOBALS['conn']->query($countQuery);
			$totalRecords = 0;
			if ($totalResult) {
				$record = $totalResult->fetch_assoc();
				$totalRecords = $record['total'] ?? 0;
			}

			// Response
			if ($payroll && $payroll->num_rows > 0) {
				while ($row = $payroll->fetch_assoc()) {
					$result['data'][] = $row;
				}
				$result['iTotalRecords'] = $totalRecords;
				$result['iTotalDisplayRecords'] = $totalRecords;
				$result['msg'] = $payroll->num_rows . " records were found.";
			} else {
				$result['msg'] = "No records found";
			}
		}

		echo json_encode($result);
	}
}

?>