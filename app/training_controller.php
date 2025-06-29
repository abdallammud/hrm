<?php
require('init.php');

if(isset($_GET['action'])) {
    if(isset($_GET['endpoint'])) {
        // CREATE
        if($_GET['action'] == 'save') {
            if($_GET['endpoint'] == 'trainers') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['full_name']) || empty($post['email'])) {
                        $result['msg'] = 'Full name and email are required';
                        echo json_encode($result); exit();
                    }
                    $exists = get_data('trainers', ['email' => $post['email']]);
                    if($exists) {
                        $result['msg'] = 'Trainer with this email already exists';
                        echo json_encode($result); exit();
                    }
                    check_auth('create_trainers');
                    $data = [
                        'full_name' => $post['full_name'],
                        'phone' => $post['phone'],
                        'email' => $post['email'],
                        'status' => $post['status'] ?? 'Active',
                        'added_by' => $_SESSION['user_id'],
                        'added_date' => date('Y-m-d H:i:s')
                    ];
                    $id = $trainersClass->create($data);
                    if($id) {
                        $result['msg'] = 'Trainer added successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to add trainer';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result); exit();
            } else if($_GET['endpoint'] == 'training') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['type_id']) || empty($post['option_id']) || empty($post['trainer_id'])) {
                        $result['msg'] = 'Training type, training options and trainer are required';
                        echo json_encode($result); exit();
                    }
                    
                    check_auth('create_training');
                    
                    // Get training type details
                    $trainingType = get_data('training_types', ['id' => $post['type_id']])[0];
                    $trainingOption = get_data('training_options', ['id' => $post['option_id']])[0];
                    $trainer = get_data('trainers', ['id' => $post['trainer_id']])[0];
                    
                    $employees = $post['employee_id'];
                    if(!is_array($employees)) {
                        $employees = [$employees];
                    }
                    
                    $successCount = 0;
                    foreach($employees as $emp_id) {
                        $employee = get_data('employees', ['employee_id' => $emp_id])[0];
                        if($employee) {
                            $data = [
                                'type_id' => $post['type_id'],
                                'type_name' => $trainingType['name'] ?? '',
                                'option_id' => $post['option_id'],
                                'option_name' => $trainingOption['name'] ?? '',
                                'trainer_id' => $post['trainer_id'],
                                'trainer_name' => $trainer['full_name'] ?? '',
                                'trainer_phone' => $trainer['phone'] ?? '',
                                'trainer_email' => $trainer['email'] ?? '',
                                'emp_id' => $emp_id,
                                'full_name' => $employee['full_name'],
                                'phone_number' => $employee['phone_number'],
                                'email' => $employee['email'],
                                'staff_no' => $employee['staff_no'],
                                'cost' => $post['cost'] ?? 0,
                                'start_date' => $post['start_date'],
                                'end_date' => $post['end_date'],
                                'description' => $post['description'] ?? '',
                                'status' => 'Active',
                                'added_by' => $_SESSION['user_id'],
                                'added_date' => date('Y-m-d H:i:s'),
                                'updated_by' => $_SESSION['user_id'],
                                'updated_date' => date('Y-m-d H:i:s')
                            ];
                            
                            $id = $trainingListClass->create($data);
                            if($id) {
                                $successCount++;
                            }
                        }
                    }
                    
                    if($successCount > 0) {
                        $result['msg'] = $successCount . ' training record(s) added successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to add training records';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result); exit();
            } else {
                $result['msg'] = 'Invalid endpoint';
                echo json_encode($result); exit();
            }
        }
        // UPDATE
        else if($_GET['action'] == 'update') {
            if($_GET['endpoint'] == 'trainers') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['id']) || empty($post['full_name']) || empty($post['email'])) {
                        $result['msg'] = 'Full name and email are required';
                        echo json_encode($result); exit();
                    }
                    $exists = get_data('trainers', ['email' => $post['email']]);
                    if($exists && $exists[0]['id'] != $post['id']) {
                        $result['msg'] = 'Trainer with this email already exists';
                        echo json_encode($result); exit();
                    }
                    check_auth('edit_trainers');
                    $data = [
                        'full_name' => $post['full_name'],
                        'phone' => $post['phone'],
                        'email' => $post['email'],
                        'status' => $post['status'] ?? 'Active',
                        'updated_by' => $_SESSION['user_id'],
                        'updated_date' => date('Y-m-d H:i:s')
                    ];
                    $updated = $trainersClass->update($post['id'], $data);
                    if($updated) {
                        $result['msg'] = 'Trainer updated successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to update trainer';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result); exit();
            } else if($_GET['endpoint'] == 'training') {
                $result = ['error' => true, 'msg' => ''];
                try {
                    $post = escapePostData($_POST);
                    if(empty($post['type_id']) || empty($post['option_id']) || empty($post['trainer_id'])) {
                        $result['msg'] = 'Training type, training options and trainer are required';
                        echo json_encode($result); exit();
                    }
                    
                    check_auth('edit_training');
                    
                    // Get training type details
                    $trainingType = get_data('training_types', ['id' => $post['type_id']])[0];
                    $trainingOption = get_data('training_options', ['id' => $post['option_id']])[0];
                    $trainer = get_data('trainers', ['id' => $post['trainer_id']])[0];
                    
                    $data = [
                        'type_id' => $post['type_id'],
                        'type_name' => $trainingType['name'] ?? '',
                        'option_id' => $post['option_id'],
                        'option_name' => $trainingOption['name'] ?? '',
                        'trainer_id' => $post['trainer_id'],
                        'trainer_name' => $trainer['full_name'] ?? '',
                        'trainer_phone' => $trainer['phone'] ?? '',
                        'trainer_email' => $trainer['email'] ?? '',
                        'cost' => $post['cost'] ?? 0,
                        'start_date' => $post['start_date'],
                        'end_date' => $post['end_date'],
                        'description' => $post['description'] ?? '',
                        'status' => $post['status'] ?? 'Active',
                        'updated_by' => $_SESSION['user_id'],
                        'updated_date' => date('Y-m-d H:i:s')
                    ];
                    
                    $updated = $trainingListClass->update($post['id'], $data);
                    if($updated) {
                        $result['msg'] = 'Training updated successfully';
                        $result['error'] = false;
                    } else {
                        $result['msg'] = 'Failed to update training';
                    }
                } catch(Exception $e) {
                    $result['msg'] = 'Error: Something went wrong';
                    $result['sql_error'] = $e->getMessage();
                }
                echo json_encode($result); exit();
            } else {
                $result['msg'] = 'Invalid action';
                echo json_encode($result); exit();
            }
        }
        // LOAD (DataTable)
        else if($_GET['action'] == 'load') {
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

            if ($_GET['endpoint'] === 'trainers') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['full_name', 'email', 'phone', 'status'];
				    $orderByIndex = (int)$_POST['order'][0]['column'];
				    $orderBy = $orderColumnMap[$orderByIndex] ?? $orderBy;
				    $order = strtoupper($_POST['order'][0]['dir']) === 'DESC' ? 'DESC' : 'ASC';
				}
			    // Base query
			    $query = "SELECT * FROM `trainers` WHERE `id` IS NOT NULL";

			    // Add search functionality
			    if ($searchParam) {
			        $query .= " AND (`full_name` LIKE '%" . escapeStr($searchParam) . "%'  OR `email` LIKE '%" . escapeStr($searchParam) . "%' OR `phone` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Add ordering
			    $query .= " ORDER BY `$orderBy` $order LIMIT $start, $length";

			    // Execute query
			    $trainers = $GLOBALS['conn']->query($query);

			    // Count total records for pagination
			    $countQuery = "SELECT COUNT(*) as total FROM `trainers` WHERE `id` IS NOT NULL";
			    if ($searchParam) {
			        $query .= " AND (`full_name` LIKE '%" . escapeStr($searchParam) . "%'  OR `email` LIKE '%" . escapeStr($searchParam) . "%' OR `phone` LIKE '%" . escapeStr($searchParam) . "%')";
			    }

			    // Execute count query
			    $totalRecordsResult = $GLOBALS['conn']->query($countQuery);
			    $totalRecords = $totalRecordsResult->fetch_assoc()['total'];

			    if ($trainers->num_rows > 0) {
			        while ($row = $trainers->fetch_assoc()) {
			            $result['data'][] = $row;
			        }
			        $result['iTotalRecords'] = $totalRecords;
			        $result['iTotalDisplayRecords'] = $totalRecords;
			        $result['msg'] = $trainers->num_rows . " records were found.";
			    } else {
			        $result['msg'] = "No records found";
			    }
			} else if ($_GET['endpoint'] === 'training') {
				if (isset($_POST['order']) && isset($_POST['order'][0])) {
				    $orderColumnMap = ['full_name', 'type_name', 'option_name', 'trainer_name', 'cost', 'start_date', 'end_date', 'status'];
				    $orderColumnIndex = $_POST['order'][0]['column'];
				    $orderDirection = $_POST['order'][0]['dir'];
				    
				    if (isset($orderColumnMap[$orderColumnIndex])) {
				        $orderColumn = $orderColumnMap[$orderColumnIndex];
				        $orderBy = "ORDER BY $orderColumn $orderDirection";
				    }
				}

				$searchValue = $_POST['search']['value'] ?? '';
				$searchCondition = '';
				if (!empty($searchValue)) {
				    $searchCondition = "AND (full_name LIKE '%$searchValue%' OR type_name LIKE '%$searchValue%' OR option_name LIKE '%$searchValue%' OR trainer_name LIKE '%$searchValue%' OR staff_no LIKE '%$searchValue%')";
				}

				$totalRecordsQuery = "SELECT COUNT(*) as total FROM training_list WHERE 1=1 $searchCondition";
				$totalRecordsResult = $GLOBALS['conn']->query($totalRecordsQuery);
				$totalRecords = $totalRecordsResult->fetch_assoc()['total'];

				$query = "SELECT * FROM training_list WHERE 1=1 $searchCondition $orderBy LIMIT $start, $length";
				$result = $GLOBALS['conn']->query($query);

				$data = [];
				if ($result->num_rows > 0) {
				    while ($row = $result->fetch_assoc()) {
				        $data[] = $row;
				    }
				}

				$response = [
				    'draw' => intval($_POST['draw']),
				    'recordsTotal' => $totalRecords,
				    'recordsFiltered' => $totalRecords,
				    'data' => $data
				];

				echo json_encode($response);
				exit();
			} else {
				$result['msg'] = 'Invalid endpoint';
				echo json_encode($result);
				exit();
			}
            // Return the result as a JSON response
			echo json_encode($result);

			exit();

		} 
        // GET (single)
        else if($_GET['action'] == 'get') {
            if($_GET['endpoint'] == 'trainers') {
                $id = isset($_POST['id']) ? $_POST['id'] : null;
                $result = [ 'error' => true, 'msg' => '', 'data' => null ];
                if(!$id) {
                    $result['msg'] = 'Missing trainer ID';
                    echo json_encode($result); exit();
                }
                $trainer = get_data('trainers', ['id' => $id]);
                if($trainer) {
                    $result['data'] = $trainer[0];
                    $result['error'] = false;
                } else {
                    $result['msg'] = 'Trainer not found';
                }
                echo json_encode($result); exit();
            } else if($_GET['endpoint'] == 'training') {
                $id = isset($_POST['id']) ? $_POST['id'] : null;
                $result = ['error' => true, 'msg' => '', 'data' => null];
                if(!$id) {
                    $result['msg'] = 'Missing training ID';
                    echo json_encode($result); exit();
                }
                $training = $trainingListClass->get(['id' => $id]);
                if($training) {
                    $result['data'] = $training;
                    $result['error'] = false;
                } else {
                    $result['msg'] = 'Training not found';
                }
                echo json_encode($result); exit();
            } else {
                $result['msg'] = 'Invalid endpoint';
                echo json_encode($result); exit();
            }
        }
        // Search
        else if($_GET['action'] == 'search') {
            if ($_GET['endpoint'] === 'employee4Training') {
				$searchFor = isset($_POST['searchFor']) ? $_POST['searchFor'] : '';
				$search = isset($_POST['search']) ? $_POST['search'] : '';

				$options = '';
				$response = [];
				$response['error'] = true;
				if($search) {
					$query = "SELECT * FROM `employees` WHERE `status` = 'Active' AND (`full_name` LIKE '$search%' OR `phone_number` LIKE '$search%' OR `email` LIKE '$search%') ORDER BY `full_name` ASC LIMIT 10";
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
					$query = "SELECT * FROM `employees` WHERE `status` = 'Active' ORDER BY `full_name` ASC LIMIT 10";
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
			}
        }
        // DELETE
        else if($_GET['action'] == 'delete') {
            if($_GET['endpoint'] == 'trainers') {
                $id = isset($_POST['id']) ? $_POST['id'] : null;
                $result = [ 'error' => true, 'msg' => '' ];
                if(!$id) {
                    $result['msg'] = 'Missing trainer ID';
                    echo json_encode($result); exit();
                }
                check_auth('delete_trainers');
                $deleted = $trainersClass->delete(['id' => $id]);
                if($deleted) {
                    $result['msg'] = 'Trainer deleted successfully';
                    $result['error'] = false;
                } else {
                    $result['msg'] = 'Failed to delete trainer';
                }
                echo json_encode($result); exit();
            } else if($_GET['endpoint'] == 'training') {
                $id = isset($_POST['id']) ? $_POST['id'] : null;
                $result = [ 'error' => true, 'msg' => '' ];
                if(!$id) {
                    $result['msg'] = 'Missing training ID';
                    echo json_encode($result); exit();
                }
                check_auth('delete_training');
                $deleted = $trainingListClass->delete(['id' => $id]);
                if($deleted) {
                    $result['msg'] = 'Training deleted successfully';
                    $result['error'] = false;
                } else {
                    $result['msg'] = 'Failed to delete training';
                }
                echo json_encode($result); exit();
            } else {
                $result['msg'] = 'Invalid endpoint';
                echo json_encode($result); exit();
            }
        }
    }
}
