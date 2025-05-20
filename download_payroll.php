<?php 
require('./app/init.php');
$payroll_id = $_GET['id'] ?? 0;
$month = $_GET['month'] ?? '';
$payrollInfo = get_data('payroll', ['id' => $payroll_id]);

// Fetch data from the database
$sql = "SELECT `id`, `payroll_id`, `emp_id`, `staff_no`, `full_name`, `contract_type`, `job_title`, `month`, `required_days`, `days_worked`, `status`, `base_salary`, `bank_name`, `bank_number`, (`allowance` + `bonus` + `commission`) AS earnings, (`loan` + `advance` + `deductions`) AS `total_deductions`, `tax`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE `payroll_id` = $payroll_id AND `month` LIKE '$month'";
$query = $GLOBALS['conn']->query($sql);


if ($query->num_rows > 0) {
    $result = [];
    $result['data'] = []; // Initialize as an empty array for storing rows

    // Add header row as the first entry
    $result['data'][] = [
        'Staff No.', 'Status', 'Full Name', 'Designation', 'Duty Station', 'State',  'Contract Type', 'Payroll Month', 'Required Days', 'Days Worked', 'Gross Salary', 'Earnings', 'Deductions', 'Tax', 'Net Salary', 'Bank Name', 'Account Number'
    ];

    while ($row = $query->fetch_assoc()) {
        $full_name = $row['full_name'];
        $emp_id = $row['emp_id'];
        $staff_no = $row['staff_no'];
        $month = $row['month'];
        $status = $row['status'];
        $earnings = $row['earnings'];
        $total_deductions = $row['total_deductions'];
        $net_salary = $row['net_salary'];
        $contract_type = $row['contract_type'];
        $job_title = $row['job_title'];
        $required_days = $row['required_days'];
        $days_worked = $row['days_worked'];
        $base_salary = $row['base_salary'];
        $bank_name = $row['bank_name'];
        $bank_number = $row['bank_number'];
        $month = date('F Y', strtotime($month));



        $employee = $GLOBALS['employeeClass']->read($emp_id);
        $state_id = $employee['state_id'];
        $taxPercentage = getTaxPercentage($base_salary, $state_id);
        
        if (!$employee['avatar']) {
            $employee['avatar'] = strtolower($employee['gender']) == 'female' ? 'female_avatar.png' : 'male_avatar.png';
        }

        $status = $employee['status'];
        $designation = $employee['designation'];

        $tax = formatMoney($row['tax']) . " (" . $taxPercentage . "%)";
        $data = [
            $staff_no, $status, $full_name, $designation, $employee['location_name'], $employee['state'], $contract_type, $month, 
            $required_days, $days_worked, formatMoney($base_salary), formatMoney($earnings), 
            formatMoney($total_deductions), $tax, formatMoney($net_salary), 
            $bank_name, $bank_number
        ];

        $result['data'][] = $data;
    }
     // var_dump($result);
    // Generate CSV
    $filename = "Payroll details " . date('Ymd') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $filename);

    $output = fopen('php://output', 'w');

    foreach ($result['data'] as $row) {
        fputcsv($output, $row);
    }

    fclose($output);

    // Redirect after download
    // header('Location: '.baseUri().'/payroll/'.$payroll_id.' ');
    exit;
} else {
    // Redirect to payroll page if no records found
    header('Location: '.baseUri().'/payroll/'.$payroll_id.' ');
    exit;
}



?>