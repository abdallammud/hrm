<?php
require('init.php');

if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		// Save data
		if($_GET['action'] == 'save') {
			$result = [];
			if($_GET['endpoint'] == 'user') {
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();

				    // Prepare data from POST request (escaping input)
				    $full_name 	= escapeStr($_POST['full_name'] ?? "");
				    $phone 	= escapeStr($_POST['phone'] ?? "");
				    $email 	= escapeStr($_POST['email'] ?? "");
				    $username 		= escapeStr($_POST['username'] ?? null);
				    $password 		= escapeStr($_POST['password'] ?? null);
				    $systemRole 	= escapeStr($_POST['systemRole'] ?? null);
				    $permissions 	= $_POST['permissions'];
				    $password   	= password_hash($password, PASSWORD_DEFAULT);

				    $employee_id = $branch_id = 0;
			    	

			    	$data = array(
				        'full_name' => $full_name,
				        'phone'   	=> $phone,
				        'email'     => $email,
				        'emp_id'    => $employee_id,
				        'branch_id'         => $branch_id,
				        'username'  	=> $username,
				        'password'      => $password,
				        'role'     		=> $systemRole,
				        'added_by'      => $_SESSION['user_id'],
				    );

				    $user_id = $userClass->create($data);
				    // exit();

				    if($user_id) {
				    	foreach ($permissions as $permission) {
				    		$permissions_data = array('user_id' => $user_id, 'permission_id' => $permission);
				    		$userPermissionsClass->create($permissions_data);
				    	}
				    }

				    $GLOBALS['conn']->commit();

			        // Return success response
			        $result['msg'] = 'User created successfully';
			        $result['error'] = false;
				    
				} catch (Exception $e) {
				    // If any exception occurs, rollback the transaction
				    $GLOBALS['conn']->rollback();

				    // Return error response
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response
				echo json_encode($result);
			}
			exit();
		} 


		// Update data
		else if($_GET['action'] == 'update') {
			$updated_date = date('Y-m-d H:i:s');
			if($_GET['endpoint'] == 'user') {
				
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();

				    // Prepare data from POST request (escaping input)
				    $full_name 		= escapeStr($_POST['full_name'] ?? "");
				    $phone 		= escapeStr($_POST['phone'] ?? "");
				    $email 		= escapeStr($_POST['email'] ?? "");
				    $user_id 		= escapeStr($_POST['user_id'] ?? null);
				    $employee_id 	= escapeStr($_POST['employee_id'] ?? null);
				    $username 		= escapeStr($_POST['username'] ?? null);
				    $systemRole 	= escapeStr($_POST['systemRole'] ?? null);
				    $slcStatus 		= escapeStr($_POST['slcStatus'] ?? 'Active');
				    $permissions 	= $_POST['permissions'];

			    	
			    	$employee_id = $branch_id = 0;

			    	$data = array(
				        'full_name' => $full_name,
				        'phone'   	=> $phone,
				        'email'     => $email,
				        'emp_id'    => $employee_id,
				        'branch_id'     => $branch_id,
				        'username'  	=> $username,
				        'role'     		=> $systemRole,
				        'status'     	=> $slcStatus,
				        'updated_by'      => $_SESSION['user_id'],
				        'updated_date' => $updated_date,
				    );

				    $updateUser = $userClass->update($user_id, $data);
				    // exit();

				    if($updateUser) {
				    	$sql = "DELETE FROM `user_permissions` WHERE `user_id` = '$user_id'";
				    	mysqli_query($GLOBALS['conn'], $sql);
				    	foreach ($permissions as $permission) {
				    		$permissions_data = array('user_id' => $user_id, 'permission_id' => $permission);
				    		$userPermissionsClass->create($permissions_data);
				    	}
				    }

				    $GLOBALS['conn']->commit();

			        // Return success response
			        $result['msg'] = 'User updated successfully';
			        $result['error'] = false;
				   
				} catch (Exception $e) {
				    // If any exception occurs, rollback the transaction
				    $GLOBALS['conn']->rollback();

				    // Return error response
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

				// Return the result as a JSON response
				echo json_encode($result);
			} 
		}



		// Load data
		else if($_GET['action'] == 'load') {
			$role = '';
			$status = '';
			$length = isset($_POST['length']) ? (int)$_POST['length'] : 20;
			$searchParam = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
			$orderBy = 'name'; // Default sorting
			$order = 'ASC';
			$draw = isset($_POST['draw']) ? (int)$_POST['draw'] : 0;
			$start = isset($_POST['start']) ? (int)$_POST['start'] : 0;

			if (isset($_POST['role'])) $role = $_POST['role'];
			if (isset($_POST['status'])) $status = $_POST['status'];

			if (isset($_POST['order']) && isset($_POST['order'][0])) {
			    $orderColumnMap = ['full_name', 'phone', 'email', 'usernamer'];
			    $orderByIndex = (int)$_POST['order'][0]['column'];
			    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
			    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
			}

			$result = [
			    'status' => 201,
			    'error' => false,
			    'data' => [],
			    'draw' => $draw,
			    'iTotalRecords' => 0,
			    'iTotalDisplayRecords' => 0,
			    'msg' => ''
			];

			if ($_GET['endpoint'] === 'users') {
			    // Base query
			    $query = "SELECT * FROM `users` WHERE `user_id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`full_name` LIKE '%" . escapeStr($searchParam) . "%'  OR `phone` LIKE '%" . escapeStr($searchParam) . "%'  OR `email` LIKE '%" . escapeStr($searchParam) . "%'  OR `username` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $users = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `users` WHERE `user_id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`full_name` LIKE '%" . escapeStr($searchParam) . "%'  OR `phone` LIKE '%" . escapeStr($searchParam) . "%'  OR `email` LIKE '%" . escapeStr($searchParam) . "%'  OR `username` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($users->num_rows > 0) {
			        while ($row = $users->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $users->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} 

			echo json_encode($result);

			exit();

		} 



		// search data
		else if($_GET['action'] == 'search') {
			if ($_GET['endpoint'] === 'employee4UserCreate') {
				$search = $_POST['search'];
				$searchFor = isset($_POST['searchFor']) ? $_POST['searchFor'] : '';

				$data = '';
				$result = [];
				$result['error'] = false;

				$query = "SELECT * FROM `employees` WHERE `status` = 'Active' AND (`full_name` LIKE '%$search%' OR `email` LIKE '%$search%' OR `phone_number` LIKE '%$search%') AND `employee_id` NOT IN (SELECT `emp_id` FROM `users`) ORDER BY `employee_id` DESC LIMIT 10";
				$employees = $GLOBALS['conn']->query($query);
				if($employees->num_rows > 0) {
					while($row = $employees->fetch_assoc()) {
						$employee_id 	= $row['employee_id'];
						$full_name 		= $row['full_name'];
						$email 			= $row['email'];
						$phone_number 	= $row['phone_number'];
						$branch_id 		= $row['branch_id'];
						$department 	= get_data('branches', array('id' => $branch_id))[0]['name'];

						$data .= '<a onclick="handleUser4CreateUser('.$employee_id.', `'.$full_name.'`)" class="d-flex cursor flex-wrap">
                    		<p class="d-flex">
                    			<span class="bold">Full name: </span>
                    			<span class="sml-5">'.$full_name.'</span>
                    		</p>
                    		<p class="d-flex">
                    			<span class="bold">Phone  </span>
                    			<span class="sml-5">'.$phone_number.'</span>
                    		</p>	
                    		<p class="d-flex">
                    			<span class="bold">Department  </span>
                    			<span class="sml-5">'.$department.'</span>
                    		</p>
                    	</a>';
					}
				} else {
					$result['error'] = true;
					$data = '<a  class="d-flex flex-wrap">
                		<p>No records were found.</p>
                	</a>';
				}

				$result['data'] = $data;

				echo json_encode($result); exit();

			} else if ($_GET['endpoint'] === 'branch') {
				json(get_data('branches', array('id' => $_POST['id'])));
			}

			exit();
		}



		

		
	}
}

?>