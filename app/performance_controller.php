<?php
require('init.php');

if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		// Save data
		if($_GET['action'] == 'save') {
			// Save indicators
			if($_GET['endpoint'] == 'indicators') {
				$department_id = $_POST['department_id'];
				$designation_id = $_POST['designation_id'];
				$department = $_POST['department'];
				$designation = $_POST['designation'];
				$added_by = $_SESSION['user_id'];
				
				// Build attributes JSON
				$attributes = [
					'Behavioural Competencies' => [
						['name' => 'Business Process', 'rating' => isset($_POST['business_pro']) ? (int)$_POST['business_pro'] : 0],
						['name' => 'Oral Communication', 'rating' => isset($_POST['oral_com']) ? (int)$_POST['oral_com'] : 0]
					],
					'Organizational Competencies' => [
						['name' => 'Leadership', 'rating' => isset($_POST['leadership']) ? (int)$_POST['leadership'] : 0],
						['name' => 'Project Management', 'rating' => isset($_POST['project_mgt']) ? (int)$_POST['project_mgt'] : 0]
					],
					'Technical Competencies' => [
						['name' => 'Allocating Resources', 'rating' => isset($_POST['res_allocating']) ? (int)$_POST['res_allocating'] : 0]
					]
				];
				
				$attributes_json = json_encode($attributes);
				
				$sql = "INSERT INTO indicators (department_id, designation_id, department, designation, attributes, added_by) 
						VALUES ('$department_id', '$designation_id', '$department', '$designation', '$attributes_json', '$added_by')";
				
				$result = [
					'status' => 201,
					'error' => true,
					'msg' => 'Failed to add indicator'
				];
				
				if($conn->query($sql)) {
					$result['error'] = false;
					$result['msg'] = 'Indicator added successfully';
				}
				
				echo json_encode($result);
			}

			// Save appraisals
			if($_GET['endpoint'] == 'appraisal') {
				$emp_id = $_POST['emp_id'];
				$department_id = $_POST['department_id'];
				$designation_id = $_POST['designation_id'];
				$department = $_POST['department'];
				$designation = $_POST['designation'];
				$indicator_rating = $_POST['indicator_rating'];
				$appraisal_rating = $_POST['appraisal_rating'];
				$month = $_POST['month'];
				$remarks = $_POST['remarks'];
				$added_by = $_SESSION['user_id'];
				
				// Get employee details
				$emp_query = "SELECT full_name, phone_number, email, staff_no FROM employees WHERE employee_id = '$emp_id'";
				$emp_result = $conn->query($emp_query);
				$emp_data = $emp_result->fetch_assoc();
				
				$sql = "INSERT INTO employee_performance (
							emp_id, full_name, phone_number, email, staff_no,
							department_id, designation_id, department, desgination,
							indicator_rating, appraisal_rating, month, remarks, added_by
						) VALUES (
							'$emp_id', '{$emp_data['full_name']}', '{$emp_data['phone_number']}', 
							'{$emp_data['email']}', '{$emp_data['staff_no']}', '$department_id', 
							'$designation_id', '$department', '$designation', '$indicator_rating', 
							'$appraisal_rating', '$month', '$remarks', '$added_by'
						)";
				
				$result = [
					'status' => 201,
					'error' => true,
					'msg' => 'Failed to add appraisal'
				];
				
				if($conn->query($sql)) {
					$result['error'] = false;
					$result['msg'] = 'Appraisal added successfully';
				}
				
				echo json_encode($result);
			}

			// Save goal tracking
			if($_GET['endpoint'] == 'goal_tracking') {
				$department_id = $_POST['department_id'];
				$type_id = $_POST['type_id'];
				$department = $_POST['department'];
				$type = $_POST['type'];
				$subject = $_POST['subject'];
				$target = $_POST['target'];
				$description = $_POST['description'];
				$start_date = $_POST['start_date'];
				$end_date = $_POST['end_date'];
				$progress = $_POST['progress'];
				$status = $_POST['status'];
				$added_by = $_SESSION['user_id'];
				
				$sql = "INSERT INTO goal_tracking (
							department_id, type_id, department, type,
							subject, target, description, start_date,
							end_date, progress, status, added_by
						) VALUES (
							'$department_id', '$type_id', '$department', '$type',
							'$subject', '$target', '$description', '$start_date',
							'$end_date', '$progress', '$status', '$added_by'
						)";
				
				$result = [
					'status' => 201,
					'error' => true,
					'msg' => 'Failed to add goal tracking'
				];
				
				if($conn->query($sql)) {
					$result['error'] = false;
					$result['msg'] = 'Goal tracking added successfully';
				}
				
				echo json_encode($result);
				exit();
			}

			exit();
		} 


		// Update data
		else if($_GET['action'] == 'update') {
			$updated_date = date('Y-m-d H:i:s');
			
			// Update indicators
			if($_GET['endpoint'] == 'indicators') {
				$id = $_POST['id'];
				$department_id = $_POST['department_id'];
				$designation_id = $_POST['designation_id'];
				$department = $_POST['department'];
				$designation = $_POST['designation'];
				$updated_by = $_SESSION['user_id'];
				
				// Build attributes JSON
				$attributes = [
					'Behavioural Competencies' => [
						['name' => 'Business Process', 'rating' => isset($_POST['business_pro']) ? (int)$_POST['business_pro'] : 0],
						['name' => 'Oral Communication', 'rating' => isset($_POST['oral_com']) ? (int)$_POST['oral_com'] : 0]
					],
					'Organizational Competencies' => [
						['name' => 'Leadership', 'rating' => isset($_POST['leadership']) ? (int)$_POST['leadership'] : 0],
						['name' => 'Project Management', 'rating' => isset($_POST['project_mgt']) ? (int)$_POST['project_mgt'] : 0]
					],
					'Technical Competencies' => [
						['name' => 'Allocating Resources', 'rating' => isset($_POST['res_allocating']) ? (int)$_POST['res_allocating'] : 0]
					]
				];
				
				$attributes_json = json_encode($attributes);
				
				$sql = "UPDATE indicators SET 
						department_id = '$department_id', 
						designation_id = '$designation_id', 
						department = '$department', 
						designation = '$designation', 
						attributes = '$attributes_json', 
						updated_by = '$updated_by', 
						updated_date = '$updated_date' 
						WHERE id = '$id'";
				
				$result = [
					'status' => 201,
					'error' => true,
					'msg' => 'Failed to update indicator'
				];
				
				if($conn->query($sql)) {
					$result['error'] = false;
					$result['msg'] = 'Indicator updated successfully';
				}
				
				echo json_encode($result);
				exit();
			}

			// Update appraisals
			if($_GET['endpoint'] == 'appraisal') {
				$id = $_POST['id'];
				$department_id = $_POST['department_id'];
				$designation_id = $_POST['designation_id'];
				$department = $_POST['department'];
				$designation = $_POST['designation'];
				$indicator_rating = $_POST['indicator_rating'];
				$appraisal_rating = $_POST['appraisal_rating'];
				$month = $_POST['month'];
				$remarks = $_POST['remarks'];
				$updated_by = $_SESSION['user_id'];
				$updated_date = date('Y-m-d H:i:s');
				
				$sql = "UPDATE employee_performance SET 
						department_id = '$department_id', 
						designation_id = '$designation_id', 
						department = '$department', 
						desgination = '$designation', 
						indicator_rating = '$indicator_rating', 
						appraisal_rating = '$appraisal_rating', 
						month = '$month', 
						remarks = '$remarks', 
						updated_by = '$updated_by', 
						updated_date = '$updated_date' 
						WHERE id = '$id'";
				
				$result = [
					'status' => 201,
					'error' => true,
					'msg' => 'Failed to update appraisal'
				];
				
				if($conn->query($sql)) {
					$result['error'] = false;
					$result['msg'] = 'Appraisal updated successfully';
				}
				
				echo json_encode($result);
				exit();
			}

			// Update goal tracking
			if($_GET['endpoint'] == 'goal_tracking') {
				$id = $_POST['id'];
				$department_id = $_POST['department_id'];
				$type_id = $_POST['type_id'];
				$department = $_POST['department'];
				$type = $_POST['type'];
				$subject = $_POST['subject'];
				$target = $_POST['target'];
				$description = $_POST['description'];
				$start_date = $_POST['start_date'];
				$end_date = $_POST['end_date'];
				$progress = $_POST['progress'];
				$updated_by = $_SESSION['user_id'];
				$updated_date = date('Y-m-d H:i:s');
				
				$sql = "UPDATE goal_tracking SET 
						department_id = '$department_id',
						type_id = '$type_id',
						department = '$department',
						type = '$type',
						subject = '$subject',
						target = '$target',
						description = '$description',
						start_date = '$start_date',
						end_date = '$end_date',
						progress = '$progress',
						updated_by = '$updated_by',
						updated_date = '$updated_date'
						WHERE id = '$id'";
				
				$result = [
					'status' => 201,
					'error' => true,
					'msg' => 'Failed to update goal tracking'
				];
				
				if($conn->query($sql)) {
					$result['error'] = false;
					$result['msg'] = 'Goal tracking updated successfully';
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
			
			// Load indicators
			if($_GET['endpoint'] == 'indicators') {
				$whereClause = "1=1";
				
				if(!empty($searchParam)) {
					$whereClause .= " AND (department LIKE '%$searchParam%' OR designation LIKE '%$searchParam%')";
				}
				
				// Count total records
				$countSql = "SELECT COUNT(*) as total FROM indicators WHERE $whereClause";
				$countResult = $conn->query($countSql);
				$totalRecords = $countResult->fetch_assoc()['total'];
				
				// Get paginated data
				$sql = "SELECT * FROM indicators WHERE $whereClause ORDER BY id DESC LIMIT $start, $length";
				$query = $conn->query($sql);
				
				$data = [];
				while($row = $query->fetch_assoc()) {
					// Calculate overall rating
					$attributes = json_decode($row['attributes'], true);
					$totalRating = 0;
					$ratingCount = 0;
					
					foreach($attributes as $category => $items) {
						foreach($items as $item) {
							$totalRating += $item['rating'];
							$ratingCount++;
						}
					}
					
					$overallRating = $ratingCount > 0 ? round($totalRating / $ratingCount, 1) : 0;
					
					// Format date
					$added_date = date('d M Y', strtotime($row['added_date']));
					
					// Actions buttons
					$actions = '<div class="btn-group">
						<button type="button" class="btn smr-10 btn-sm btn-primary edit_indicator" data-recid="'.$row['id'].'">
							<i class="bi bi-pencil-square"></i>
						</button>
						<button type="button" class="btn btn-sm btn-danger delete_indicator" data-recid="'.$row['id'].'">
							<i class="bi bi-trash"></i>
						</button>
					</div>';
					
					$data[] = [
						'id' => $row['id'],
						'department' => $row['department'],
						'designation' => $row['designation'],
						'overall_rating' => $overallRating,
						'added_date' => $added_date,
						'actions' => $actions
					];
				}
				
				$result['data'] = $data;
				$result['iTotalRecords'] = $totalRecords;
				$result['iTotalDisplayRecords'] = $totalRecords;
			}

			// Load appraisals
			if($_GET['endpoint'] == 'appraisals') {
				$whereClause = "status = 'Active'";
				
				if(!empty($searchParam)) {
					$whereClause .= " AND (department LIKE '%$searchParam%' OR full_name LIKE '%$searchParam%')";
				}
				
				// Count total records
				$countSql = "SELECT COUNT(*) as total FROM employee_performance WHERE $whereClause";
				$countResult = $conn->query($countSql);
				$totalRecords = $countResult->fetch_assoc()['total'];
				
				// Get paginated data
				$sql = "SELECT * FROM employee_performance WHERE $whereClause ORDER BY added_date DESC LIMIT $start, $length";
				$query = $conn->query($sql);
				
				$data = [];
				while($row = $query->fetch_assoc()) {
					// Format date
					$added_date = date('d M Y', strtotime($row['added_date']));
					// <button type="button" class="btn smr-10 btn-sm btn-primary edit_appraisal" data-recid="'.$row['id'].'">
					// 		<i class="bi bi-pencil-square"></i>
					// 	</button>
					// Actions buttons
					$actions = '<div class="btn-group">
						
						<button type="button" class="btn btn-sm btn-danger delete_appraisal" data-recid="'.$row['id'].'">
							<i class="bi bi-trash"></i>
						</button>
					</div>';
					
					$data[] = [
						'id' => $row['id'],
						'department' => $row['department'],
						'emp_id' => $row['emp_id'],
						'staff_no' => $row['staff_no'],
						'full_name' => $row['full_name'],
						'indicator_rating' => $row['indicator_rating'],
						'appraisal_rating' => $row['appraisal_rating'],
						'month' => $row['month'],
						'added_date' => $added_date,
						'actions' => $actions
					];
				}
				
				$result['data'] = $data;
				$result['iTotalRecords'] = $totalRecords;
				$result['iTotalDisplayRecords'] = $totalRecords;
			}

			// Load goal tracking
			if($_GET['endpoint'] == 'goal_tracking') {
				$whereClause = "status = 'Active'";
				
				if(!empty($searchParam)) {
					$whereClause .= " AND (department LIKE '%$searchParam%' OR type LIKE '%$searchParam%' OR subject LIKE '%$searchParam%')";
				}
				
				// Count total records
				$countSql = "SELECT COUNT(*) as total FROM goal_tracking WHERE $whereClause";
				$countResult = $conn->query($countSql);
				$totalRecords = $countResult->fetch_assoc()['total'];
				
				// Get paginated data
				$sql = "SELECT * FROM goal_tracking WHERE $whereClause ORDER BY added_date DESC LIMIT $start, $length";
				$query = $conn->query($sql);
				
				$data = [];
				while($row = $query->fetch_assoc()) {
					// Format date
					$added_date = date('d M Y', strtotime($row['added_date']));
					
					// Actions buttons
					$actions = '<div class="btn-group">
						<button type="button" class="btn smr-10 btn-sm btn-primary edit_goal_tracking" data-recid="'.$row['id'].'">
							<i class="bi bi-pencil-square"></i>
						</button>
						<button type="button" class="btn btn-sm btn-danger delete_goal_tracking" data-recid="'.$row['id'].'">
							<i class="bi bi-trash"></i>
						</button>
					</div>';

					$start_date = date('d M Y', strtotime($row['start_date']));
					$end_date = date('d M Y', strtotime($row['end_date']));

					// Format date F d, Y
					$added_date = date('F d, Y', strtotime($row['added_date']));
					$start_date = date('F d, Y', strtotime($row['start_date']));
					$end_date = date('F d, Y', strtotime($row['end_date']));
					
					$data[] = [
						'id' => $row['id'],
						'department' => $row['department'],
						'type' => $row['type'],
						'subject' => $row['subject'],
						'target' => $row['target'],
						'progress' => $row['progress'],
						'status' => $row['status'],
						'start_date' => $start_date,
						'end_date' => $end_date,
						'added_date' => $added_date,
						'actions' => $actions
					];
				}
				
				$result['data'] = $data;
				$result['iTotalRecords'] = $totalRecords;
				$result['iTotalDisplayRecords'] = $totalRecords;
			}

			echo json_encode($result);
			exit();
		} 


		// Get data
		else if($_GET['action'] == 'get') {
			// Get indicator by ID
			if($_GET['endpoint'] == 'indicator') {
				$id = $_POST['id'];
				
				$sql = "SELECT * FROM indicators WHERE id = '$id'";
				$query = $conn->query($sql);
				
				$result = [
					'status' => 201,
					'error' => true,
					'data' => null,
					'msg' => 'Indicator not found'
				];
				
				if($row = $query->fetch_assoc()) {
					$result['error'] = false;
					$result['data'] = $row;
				}
				
				echo json_encode($result);
				exit();
			} else if ($_GET['endpoint'] == 'indicator4Appraisals') {
				$department_id = isset($_POST['department_id']) ? intval($_POST['department_id']) : 0; // Use intval for safety
				$designation_id = isset($_POST['designation_id']) ? intval($_POST['designation_id']) : 0; // Also use designation_id if relevant for indicators

				// echo 'department_id: '.$department_id.'<br>';
		
				$result = [
					'status' => 200,
					'error' => true,
					'msg' => 'No indicators found for this department/designation.',
					'data' => null
				];
		
				// Ensure department_id is valid
				if ($department_id > 0) {
					// Query to find indicators for the department (and potentially designation if applicable)
					// You might need to adjust your SQL query based on how indicators are linked to designations.
					// For simplicity, I'm assuming indicators are primarily linked to departments for now.
					// If designation affects indicators, you might need to adjust the WHERE clause.
					$sql = "SELECT attributes FROM indicators WHERE department_id = ? AND designation_id = ? ORDER BY id DESC LIMIT 1";
		
					// Prepare and bind for security (prevents SQL injection)
					$stmt = $conn->prepare($sql);
					if ($stmt) {
						$stmt->bind_param("ii", $department_id, $designation_id); // 'i' for integer
						$stmt->execute();
						$query_result = $stmt->get_result();
		
						if ($query_result && $query_result->num_rows > 0) {
							$indicator = $query_result->fetch_assoc();
		
							// Convert attributes JSON to a simplified format for the frontend
							$attributes_array = json_decode($indicator['attributes'], true);
		
							$simplified_attributes = [
								'business_pro' => 0,
								'oral_com' => 0,
								'leadership' => 0,
								'project_mgt' => 0,
								'res_allocating' => 0 // This needs to match the HTML naming 'indicator_res_allocating'
							];
		
							// Map the JSON data to simplified_attributes
							if ($attributes_array) {
								foreach ($attributes_array as $category => $competencies) {
									foreach ($competencies as $competency) {
										// Normalize competency name for mapping
										$name = strtolower(str_replace(' ', '_', $competency['name']));
										$rating = intval($competency['rating']); // Ensure rating is an integer
		
										if ($name === 'business_process') {
											$simplified_attributes['business_pro'] = $rating;
										} elseif ($name === 'oral_communication') {
											$simplified_attributes['oral_com'] = $rating;
										} elseif ($name === 'leadership') {
											$simplified_attributes['leadership'] = $rating;
										} elseif ($name === 'project_management') {
											$simplified_attributes['project_mgt'] = $rating;
										} elseif ($name === 'allocating_resources') { // From your JSON 'Allocating Resources'
											$simplified_attributes['res_allocating'] = $rating; // Maps to 'res_allocating' in your simplified structure
										}
									}
								}
							}
		
							$result['error'] = false;
							$result['msg'] = 'Indicators found';
							$result['data'] = $simplified_attributes; // Send the simplified data to the frontend
						} else {
							$result['msg'] = 'No indicators found for the selected department.';
						}
						$stmt->close();
					} else {
						$result['msg'] = 'Database query preparation failed: ' . $conn->error;
					}
				} else {
					$result['msg'] = 'Invalid department ID provided.';
				}
		
				header('Content-Type: application/json');
				echo json_encode($result);
				exit();
			}

			// Get appraisal by ID
			if($_GET['endpoint'] == 'appraisal') {
				$id = $_POST['id'];
				
				$sql = "SELECT * FROM employee_performance WHERE id = '$id' AND status = 'Active'";
				$query = $conn->query($sql);
				
				$result = [
					'status' => 201,
					'error' => true,
					'data' => null,
					'msg' => 'Appraisal not found'
				];
				
				if($row = $query->fetch_assoc()) {
					$result['error'] = false;
					$result['data'] = $row;
				}
				
				echo json_encode($result);
				exit();
			}

			// Get goal tracking by ID
			if($_GET['endpoint'] == 'goal_tracking') {
				$id = $_POST['id'];
				
				$sql = "SELECT * FROM goal_tracking WHERE id = '$id' AND status = 'Active'";
				$query = $conn->query($sql);
				
				$result = [
					'status' => 201,
					'error' => true,
					'data' => null,
					'msg' => 'Goal tracking not found'
				];
				
				if($row = $query->fetch_assoc()) {
					$result['error'] = false;
					$row['start_date'] = date('Y-m-d', strtotime($row['start_date']));
					$row['end_date'] = date('Y-m-d', strtotime($row['end_date']));
					$result['data'] = $row;
				}
				
				echo json_encode($result);
				exit();
			}
		}

		else if($_GET['action'] == 'search') {
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

							$text = $full_name;

							if($phone_number) {
								$text .= ", ".$phone_number;
							}

                    		$options .=  '<option value="'.$employee_id.'">'.$text.'</option>';
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
			}
		}


		// Delete data
		else if($_GET['action'] == 'delete') {
			// Delete indicator
			if($_GET['endpoint'] == 'indicator') {
				$id = $_POST['id'];
				
				$sql = "DELETE FROM indicators WHERE id = '$id'";
				
				$result = [
					'status' => 201,
					'error' => true,
					'msg' => 'Failed to delete indicator'
				];
				
				if($conn->query($sql)) {
					$result['error'] = false;
					$result['msg'] = 'Indicator deleted successfully';
				}
				
				echo json_encode($result);
				exit();
			}

			// Delete appraisal
			if($_GET['endpoint'] == 'appraisal') {
				$id = $_POST['id'];
				$updated_by = $_SESSION['user_id'];
				$updated_date = date('Y-m-d H:i:s');
				
				$sql = "DELETE FROM employee_performance WHERE id = '$id'";
				
				$result = [
					'status' => 201,
					'error' => true,
					'msg' => 'Failed to delete appraisal'
				];
				
				if($conn->query($sql)) {
					$result['error'] = false;
					$result['msg'] = 'Appraisal deleted successfully';
				}
				
				echo json_encode($result);
				exit();
			}

			// Delete goal tracking
			if($_GET['endpoint'] == 'goal_tracking') {
				$id = $_POST['id'];
				
				$sql = "DELETE FROM goal_tracking WHERE id = '$id'";
				
				$result = [
					'status' => 201,
					'error' => true,
					'msg' => 'Failed to delete goal tracking'
				];
				
				if($conn->query($sql)) {
					$result['error'] = false;
					$result['msg'] = 'Goal tracking deleted successfully';
				}
				
				echo json_encode($result);
				exit();
			}
		}
	}
}

?>