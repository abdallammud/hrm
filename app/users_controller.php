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

					check_auth('create_users'); 

				    // Prepare data from POST request (escaping input)
				    $full_name 	= escapeStr($_POST['full_name'] ?? "");
				    $phone 	= escapeStr($_POST['phone'] ?? "");
				    $email 	= escapeStr($_POST['email'] ?? "");
				    $username 		= escapeStr($_POST['username'] ?? null);
				    $password 		= escapeStr($_POST['password'] ?? null);
				    $sysRole 	= escapeStr($_POST['sysRole'] ?? null);
				    $password   	= password_hash($password, PASSWORD_DEFAULT);
					$reportsTo = $_POST['reportsTo'] ?? [];

				    $employee_id = $branch_id = 0;

			    	$data = array(
				        'full_name' => $full_name,
				        'phone'   	=> $phone,
				        'email'     => $email,
				        'emp_id'    => $employee_id,
				        'branch_id'         => $branch_id,
				        'username'  	=> $username,
				        'password'      => $password,
				        'role'     		=> $sysRole,
						'reports_to' => json_encode($reportsTo),
				        'added_by'      => $_SESSION['user_id'],
				    );

				    $user_id = $userClass->create($data);

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
						'reports_to' => json_encode($post['reportsTo']),
				    );

					check_auth('create_roles'); 

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

					check_auth('edit_users'); 

				    // Prepare data from POST request (escaping input)
				    $full_name 		= escapeStr($_POST['full_name'] ?? "");
				    $phone 			= escapeStr($_POST['phone'] ?? "");
				    $email 			= escapeStr($_POST['email'] ?? "");
				    $user_id 		= escapeStr($_POST['user_id'] ?? null);
				    $employee_id 	= escapeStr($_POST['employee_id'] ?? null);
				    $username 		= escapeStr($_POST['username'] ?? null);
				    $sysRole 		= escapeStr($_POST['sysRole'] ?? null);
				    $slcStatus 		= escapeStr($_POST['slcStatus'] ?? 'Active');
					$reportsTo 		= $_POST['reportsTo'] ?? [];

					$employee_id = $branch_id = 0;

			    	$data = array(
				        'full_name' => $full_name,
				        'phone'   	=> $phone,
				        'email'     => $email,
				        'emp_id'    => $employee_id,
				        'branch_id'     => $branch_id,
				        'username'  	=> $username,
				        'role'     		=> $sysRole,
				        'status'     	=> $slcStatus,
				        'updated_by'      => $_SESSION['user_id'],
				        'updated_date' => $updated_date,
				        'reports_to' => json_encode($reportsTo)
				    );

				    $updateUser = $userClass->update($user_id, $data);
				    // exit();

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
			} else if($_GET['endpoint'] == 'role') {
				$result = [];
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);
					$reportsTo = $_POST['reportsTo'] ?? [];
					$data = array(
				        'name' => $post['name'], 
						'reports_to' => json_encode($reportsTo),
				    );

					check_auth('edit_roles');

					$role_id = $sys_roles->update($post['id'], $data);
					if($role_id) {
				    	$sql = "DELETE FROM `sys_role_permissions` WHERE `role_id` = '".$post['id']."'";
				    	mysqli_query($GLOBALS['conn'], $sql);
				    	foreach ($post['actions'] as $action) {
				    		$actions_data = array('role_id' => $post['id'], 'permission' => $action);
				    		$sys_role_permissions->create($actions_data);
				    	}
				    }

				    $GLOBALS['conn']->commit();
			        // Return success response
			        $result['msg'] = 'Role updated successfully';
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
				exit();
			} else if($_GET['endpoint'] == 'user_password') {
				$result = [];
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();
					$post = escapePostData($_POST);
					$password 	= $post['newPassword'];
					$user_id 	= $post['user_id'];
					$password 	= password_hash($password, PASSWORD_DEFAULT);
					$data = array(
				        'password' => $password, 
				    );

					
					check_auth('edit_users');

					$data = array(
				        'password' => $password,
				    );

				    $updateUser = $userClass->update($user_id, $data);
				    // exit();

				    $GLOBALS['conn']->commit();

			        // Return success response
			        $result['msg'] = 'User password changed successfully';
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
				exit();
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
						$role_id = $row['role'];
						$role = isset($sys_roles->get($role_id)['name']) ? $sys_roles->get($role_id)['name'] : '';
			            $row['role'] = $role;
						$row['reports_to'] = json_decode($row['reports_to']);
						if(is_array($row['reports_to'])) {
							foreach ($row['reports_to'] as $reports_to) {
								$reports_to = isset($userClass->get($reports_to)['full_name']) ? $userClass->get($reports_to)['full_name'] : '';
								$row['reports_to'] = $reports_to;
							}
						} else {
							$row['reports_to'] = '';
						}
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
				
				$reports_to = json_decode($role['reports_to']);
				$reports_html = '';
				foreach ($GLOBALS['sys_roles']->read_all() as $role2) {
					$reports_html .= '<option value="' . $role2['id'] . '" '
						.(is_array($reports_to) && in_array($role2['id'], $reports_to) ? 'selected' : '').'>'
						. ucwords($role2['name']) . '</option>';
				}
			
				$data = '
				<div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="label required" for="roleName4Edit">Role Name</label>
								<input type="text" data-msg="Please provide role name." value="'.$role['name'].'" class="form-control validate" id="roleName4Edit" name="roleName4Edit">
								<input type="hidden" name="role_id" id="role_id" value="'.$_POST['id'].'">
								<span class="form-error text-danger">This is error</span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="label required" for="reportsTo">Reports to</label>
								<select data-live-search="true" name="reportsTo" id="reportsTo" title="Select reports to" multiple class="form-control my-select reports_to">
									<option value="">Select</option>
									'.$reports_html.'
								</select>
								<span class="form-error text-danger">This is error</span>
							</div>
						</div>
					</div>
			
					<h6 class="mt-3">Assign permission to this role</h6>
					<hr>
			
					<div class="permissions-list">
				';
			
				foreach ($GLOBALS['sys_permissions']->read_all() as $permission) {
					$actions = json_decode($permission['actions']);
					$disabled_features = get_setting('disabled_features');
					$disabled_features = json_decode($disabled_features['value']);
			
					if (in_array($permission['module'], $disabled_features)) {
						continue;
					}
			
					$permission_name = $permission['module'];
					$module_id = strtolower(str_replace(" ", "_", $permission_name));
			
					$data .= '
					<div class="permission-module mb-3">
						<div class="form-check mb-2">
							<input class="form-check-input module" type="checkbox" id="'.$module_id.'">
							<label class="form-check-label fw-bold" for="'.$module_id.'">'.ucwords($permission_name).'</label>
						</div>
			
						<div class="d-flex flex-wrap gap-3 ms-3">';
					
					foreach ($actions as $action_name => $action_code) {
						$data .= '
							<div class="form-check me-3">
								<input class="form-check-input action '.$module_id.'" 
									data-module="'.$module_id.'" 
									type="checkbox" 
									id="'.$action_code->code.'4Edit"
									value="'.$action_code->code.'" 
									'.(in_array($action_code->code, $role_permissions) ? 'checked' : '').'>
								<label class="form-check-label" for="'.$action_code->code.'4Edit">'.ucwords($action_name).'</label>
							</div>';
					}
			
					$data .= '
						</div>
					</div>';
				}
			
				$data .= '
					</div>
				</div>';
			
				$result = array(
					'error' => false,
					'data' => $data
				);
				echo json_encode($result);
				exit();
			}else if ($_GET['endpoint'] === 'user') {
				$user = get_data('users', array('user_id' => $_POST['id']));
				$user[0]['reports_to'] = json_decode($user[0]['reports_to']);
				json($user);
			}
		}


		// Delete data
		else if($_GET['action'] == 'delete') {
			if($_GET['endpoint'] == 'role') {
				$result = [];
				try {
				    // Begin a transaction
				    $GLOBALS['conn']->begin_transaction();

					$role_id = $_POST['id'];
					check_auth('delete_roles');
					
					// Check if the role is being used by any user
					$sql = "SELECT * FROM `users` WHERE `role` = '$role_id'";	
					$found = $GLOBALS['conn']->query($sql);
					if($found->num_rows > 0) {
						$result = array(
							'error' => true,
							'msg' => 'This role is being used by a user. Please remove the user from this role before deleting it.'
						);
						echo json_encode($result);
						exit();
					}
					// Delete the role	
					$sys_roles->delete($role_id);
					$del_permission = "DELETE FROM `sys_role_permissions` WHERE `role_id` = '$role_id'";
					if(!mysqli_query($GLOBALS["conn"], $del_permission)) {
						// throw new Exception('Error: ' . mysqli_error($GLOBALS["conn"]));
						$result = array(
							'error' => true,
							'msg' => 'Error: ' . mysqli_error($GLOBALS["conn"])
						);
						echo json_encode($result);
						exit();
					}

				    $GLOBALS['conn']->commit();
			        // Return success response
			        $result['msg'] = 'Role deleted successfully';
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
				exit();
			} else if($_GET['endpoint'] == 'user') {
				$result = [];
				try {
					// Begin a transaction
					$GLOBALS['conn']->begin_transaction();
					$user_id = $_POST['id'];
					check_auth('delete_users');
					$userClass->delete($user_id);
					$GLOBALS['conn']->commit();
					$result['msg'] = 'User deleted successfully';
					$result['error'] = false;
				} catch (Exception $e) {
					// If any exception occurs, rollback the transaction
					$GLOBALS['conn']->rollback();
					$result['msg'] = 'Error: Something went wrong';
					$result['sql_error'] = $e->getMessage(); // Get the error message from the exception
					$result['error'] = true;
				}
			}

			// Delet user requires more logic will implement later
		}

		

		
	}
}

?>