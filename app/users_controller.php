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
			} if($_GET['endpoint'] == 'role') {
				$result = [];
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);
					$data = array(
				        'name' => $post['name'], 
				    );

					$role_id = $sys_roles->create($data);
					if($role_id) {
				    	foreach ($post['actions'] as $action) {
				    		$actions_data = array('role_id' => $role_id, 'permission' => $action);
				    		$sys_role_permissions->create($actions_data);
				    	}
				    }

				    $GLOBALS['conn']->commit();
			        // Return success response
			        $result['msg'] = 'Role created successfully';
			        $result['error'] = false;

				} catch (Exception $e) {
				    // If any exception occurs, rollback the transaction
				    $GLOBALS['conn']->rollback();

				    // Return error response
				    $result['msg'] = 'Error: Something went wrong';
				    $result['sql_error'] = $e->getMessage(); // Get the error message from the exception
				    $result['error'] = true;
				}

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


		// Get data
		else if($_GET['action'] == 'get') {
			if($_GET['endpoint'] == 'role4Edit') {
				$role = $sys_roles->get($_POST['id']);
				$role_permissions = $sys_role_permissions->get_permissions($_POST['id']);

				$data = '<div>
                    <div class="row">
                        <div class="col col-xs-12">
                            <div class="form-group">
                                <label class="label  required"  for="roleName">Role  Name</label>
                                <input type="text"  data-msg="Please provide role name." value="'.$role['name'].'"  class="form-control validate" id="roleName" name="roleName">
								input type="hidden" name="role_id" id="role_id" value="'.$_POST['id'].'">
                                <span class="form-error text-danger">This is error</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-md-12 col-lg-12 col-xs-12">
                            <h6 style="margin-top: 10px;;">Assign permission to this role</h6>
                        </div>
                        
                         <div class="table-responsive">
                            <table class="table assing_roles table-borderless">
                                <thead style="background-color: #f2f2f2;">
                                    <tr>
                                        <th scope="col">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="" id="selectAll">
                                                <label class="form-check-label" for="selectAll">MODULE</label>
                                            </div>
                                        </th>
                                        <th scope="col">PERMISSIONS</th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>';

								foreach ($GLOBALS['sys_permissions']->read_all() as $permission) {
                                    $actions = json_decode($permission['actions']);

                                    // var_dump($actions);
                                    $permission['actions'] = $actions;
                                    $permission_name = $permission['module'];
									$data .= '<tr>
										<td>
											<div class="form-check">
												<input class="form-check-input module" type="checkbox" value="" id="'.strtolower(str_replace(" ", "_",$permission_name)).'">
												<label class="form-check-label" for="'.strtolower(str_replace(" ", "_",$permission_name)).'">'.ucwords($permission_name).'</label>
											</div>
										</td>';

									foreach ($actions as $action_name => $action_code) {
										$data .= '<td>
											<div class="form-check form-check-inline">
												<input class="form-check-input action '.strtolower(str_replace(" ", "_",$permission_name)).'" data-module="'.strtolower(str_replace(" ", "_",$permission_name)).'" type="checkbox" id="'.$action_code->code.'" value="'.$action_code->code.'">
												<label class="form-check-label" for="'.$action_code->code.'">'.ucwords($action_name).'</label>
											</div>
										</td>';
									}
									$data .= '</tr>';
								}

                                $data .='</tbody>
                            </table>
                        </div>
                    </div>
                </div>';
				$result = array(
					'error' => false,
					'data' => $data
				);
				echo json_encode($result);
				exit();
			} else if ($_GET['endpoint'] === 'branch') {
				json(get_data('branches', array('id' => $_GET['id'])));
			}
		}


		

		
	}
}

?>