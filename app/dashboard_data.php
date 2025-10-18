<?php
require('init.php');

if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		// Save data
		if($_GET['action'] == 'get') {
			if($_GET['endpoint'] == 'cards') {
				$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
				$month = date('Y-m', strtotime($month));

				$data = [];

				// Total employees
				$employees = $GLOBALS['conn']->query("SELECT COUNT(`employee_id`) AS 'count' FROM `employees` WHERE `status` = 'Active'");
				if($employees) {
					$data['employees'] = $employees->fetch_assoc()['count'];
				}

				// Total new employees
				$new_employees = $GLOBALS['conn']->query("SELECT COUNT(`employee_id`) AS 'count' FROM `employees` WHERE `added_date` LIKE '$month%' AND `status` = 'Active'");
				if($new_employees) {
					$data['new_employees'] = $new_employees->fetch_assoc()['count'];
				}

				// Total approved leave
				$approved_leave = $GLOBALS['conn']->query("SELECT COUNT(`emp_id`) AS 'count' FROM `employee_leave` WHERE `status` = 'Approved' AND (`date_from` LIKE '$month%' OR `date_to` LIKE '$month%')");
				if($approved_leave) {
					$data['approved_leave'] = $approved_leave->fetch_assoc()['count'];
				}

				// Expenses
				// SUM (AMOUTN) FROM fn_transactions WHERE TYPE == 'Expenses'
				$expenses = $GLOBALS['conn']->query("SELECT SUM(`amount`) AS 'amount' FROM `fn_transactions` WHERE `type` = 'Expense' AND `added_date` LIKE '$month%' AND `status` = 'Active'");
				if($expenses) {
					$data['expenses'] = $expenses->fetch_assoc()['amount'];
				}

				// This month salary
				$total_salary = 0;
				$thisMonthSalary = $GLOBALS['conn']->query("SELECT `id`, `added_date`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `month` LIKE '$month%' AND `status` IN ('Paid', 'Pending', 'Reviewed', 'Validated', 'Approved')");
				// var_dump($expenses);
				if($thisMonthSalary) {
					while($row = $thisMonthSalary->fetch_assoc()) {
						$total_salary += $net_salary = $row['net_salary'];
					}
				}
				$data['thisMonthSalary'] = $total_salary;

				// Charts
				// Employees gender pie chart male and female
				$gender = $GLOBALS['conn']->query("SELECT `gender`, COUNT(`gender`) AS 'count' FROM `employees` WHERE `status` = 'Active' GROUP BY `gender`");
				if($gender) {
					$data['gender'] = $gender->fetch_all(MYSQLI_ASSOC);
				}

				// Employee by department
				$sql = "SELECT b.name AS department, COUNT(e.branch_id) AS employee_count FROM employees e INNER JOIN branches b ON e.branch_id = b.id WHERE e.status = 'Active' GROUP BY e.branch_id, b.name ORDER BY b.name";
				$result = $GLOBALS['conn']->query($sql);
				$employeeCountsByBranch = [];
				if ($result) {
					$employeeCountsByBranch = $result->fetch_all(MYSQLI_ASSOC);
					$data['employeeByDepartment'] = $employeeCountsByBranch; 
					$result->free();
				}

				// Get total payroll for the last 5 months
$last5Months = [];

$query = "
    SELECT 
        CASE 
            WHEN `month` REGEXP '^[0-9]{4}-[0-9]{2}' THEN LEFT(`month`, 7)
            ELSE DATE_FORMAT(`month`, '%Y-%m')
        END AS month_label,
        SUM((`base_salary` + (`allowance` + `bonus` + `commission`) 
             - (`loan` + `advance` + `deductions`) - `tax`)) AS total_salary
    FROM `payroll_details`
    WHERE `status` IN ('Paid', 'Pending', 'Reviewed', 'Validated', 'Approved')
    GROUP BY month_label
    ORDER BY month_label DESC
    LIMIT 5
";

$result = $GLOBALS['conn']->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['month_label'])) {
            $last5Months[] = [
                'month' => $row['month_label'],
                'total_salary' => (float)$row['total_salary']
            ];
        }
    }
}

// Reverse so it shows oldest → newest
$last5Months = array_reverse($last5Months);

$data['last5Months'] = $last5Months;

				
				


				$company_balance = 0;
				// Get balance 
				$banks = $GLOBALS['conn']->query("SELECT SUM(`balance`) AS 'balance' FROM `bank_accounts` WHERE `status` = 'Active'");
				if($banks) {
					$data['company_balance'] = $banks->fetch_assoc()['balance'];
				}
			    
				echo json_encode($data);
			} 
		} 


	}
}

?>