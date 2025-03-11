<?php
require('init.php');

if(isset($_GET['action'])) {
	if(isset($_GET['endpoint'])) {
		// Save data
		if($_GET['action'] == 'get') {
			if($_GET['endpoint'] == 'cards') {
				$company_balance = 0;
				// Get balance 
				$banks = $GLOBALS['conn']->query("SELECT SUM(`balance`) AS 'balance' FROM `bank_accounts` WHERE `status` = 'Active'");
				if($banks) {
					$company_balance = $banks->fetch_assoc()['balance'];
				}

				// Expenses
				$currentMonth = date("Y-m");
				$nextMonth = date('Y-m', strtotime("+1 month"));

				$total_expenses = 0;
				$coming_date = date('Y-m-d', strtotime("28th next month"));
				$expenses = $GLOBALS['conn']->query("SELECT `id`, `added_date`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `month` LIKE '$currentMonth%' AND `status` = 'Paid'");
				// var_dump($expenses);
				if($expenses) {
					while($row = $expenses->fetch_assoc()) {
						$total_expenses += $net_salary = $row['net_salary'];
						$added_date = $row['added_date']; 
						$coming_date = date('Y-m-d', strtotime('+1 month', strtotime($added_date))); 
					}
				}

				$upcoming_salary = 0;
				// Get balance 
				$banks = $GLOBALS['conn']->query("SELECT SUM(`salary`) AS 'salary' FROM `employees` WHERE `status` = 'Active'");
				if($banks) {
					$upcoming_salary = $banks->fetch_assoc()['salary'];
				}

				$coming_date = new DateTime($coming_date);
				$coming_date = $coming_date->format('F d, Y');
			    
				echo json_encode(['company_balance' => $company_balance, 'total_expenses' => $total_expenses, 'upcoming_salary' => $upcoming_salary, 'coming_date' => $coming_date]);
			} else if($_GET['endpoint'] == 'chart_bar') {
				$currentYear = date('Y');
				$months = ['01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0, '10' => 0, '11' => 0, '12' => 0];
				$query = "SELECT `month`, SUM(`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `month` LIKE '$currentYear%' AND `status` = 'Paid' GROUP BY `month`";
				$dataset = $GLOBALS['conn']->query($query);
				while($row = $dataset->fetch_assoc()) {
					$month = date('m', strtotime($row['month']));
					$months[$month] = $row['net_salary'];
				}
				echo json_encode(array_values($months));
				
			}
		} 


	}
}

?>