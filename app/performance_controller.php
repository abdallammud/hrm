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
			} else if($_GET['endpoint'] == 'indicator4Appraisals') {
				$department_id = isset($_POST['department_id']) ? $_POST['department_id'] : 0;
				
				$result = [
					'status' => 200,
					'error' => true,
					'msg' => 'No indicators found for this department',
					'data' => null
				];
				
				// Query to find indicators for the department
				$sql = "SELECT * FROM indicators WHERE department_id = '$department_id' ORDER BY id DESC LIMIT 1";
				$query = $conn->query($sql);
				
				if($query && $query->num_rows > 0) {
					$indicator = $query->fetch_assoc();
					
					// Convert attributes JSON to a simplified format for the frontend
					$attributes_array = json_decode($indicator['attributes'], true);
					$simplified_attributes = [
						'business_pro' => 0,
						'oral_com' => 0,
						'leadership' => 0,
						'project_mgt' => 0,
						'res_allocating' => 0
					];
					
					// Extract ratings from the attributes
					foreach($attributes_array as $category => $items) {
						foreach($items as $item) {
							if($item['name'] == 'Business Process') {
								$simplified_attributes['business_pro'] = $item['rating'];
							} else if($item['name'] == 'Oral Communication') {
								$simplified_attributes['oral_com'] = $item['rating'];
							} else if($item['name'] == 'Leadership') {
								$simplified_attributes['leadership'] = $item['rating'];
							} else if($item['name'] == 'Project Management') {
								$simplified_attributes['project_mgt'] = $item['rating'];
							} else if($item['name'] == 'Allocating Resources') {
								$simplified_attributes['res_allocating'] = $item['rating'];
							}
						}
					}
					
					// Update the indicator with simplified attributes
					$indicator['attributes'] = json_encode($simplified_attributes);
					
					$result['error'] = false;
					$result['msg'] = 'Indicators found';
					$result['data'] = $indicator;
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
		}
	}
}

?>