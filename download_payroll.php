<?php
require('./app/init.php');

$payroll_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$monthRaw   = $_GET['month'] ?? '';

// quick guard
if ($payroll_id <= 0) {
    header('Location: '.baseUri().'/payroll/'.$payroll_id);
    exit;
}

// Single query: payroll_details joined to employees to read authoritative employee info
$sql = "
SELECT
  pd.id,
  pd.payroll_id,
  pd.emp_id,
  pd.staff_no,
  
  pd.contract_type,
  pd.job_title,
  pd.month                AS payroll_month,
  pd.required_days,
  pd.days_worked,
  pd.status               AS payroll_status,
  COALESCE(pd.base_salary, 0) AS base_salary,
  pd.bank_name,
  pd.bank_number,
  (COALESCE(pd.allowance,0) + COALESCE(pd.bonus,0) + COALESCE(pd.commission,0)) AS earnings,
  (COALESCE(pd.loan,0) + COALESCE(pd.advance,0) + COALESCE(pd.deductions,0))    AS total_deductions,
  pd.tax,
  (COALESCE(pd.base_salary,0) + (COALESCE(pd.allowance,0) + COALESCE(pd.bonus,0) + COALESCE(pd.commission,0)) - (COALESCE(pd.loan,0) + COALESCE(pd.advance,0) + COALESCE(pd.deductions,0)) - COALESCE(pd.tax,0)) AS net_salary,

  -- employee authoritative fields
  e.employee_id,
  e.state_id             AS emp_state_id,
  e.full_name            AS pd_full_name,
  e.state                AS emp_state,
  e.location_name        AS emp_location_name,
  e.designation          AS emp_designation,
  e.status               AS emp_status,
  e.gender,
  e.avatar
FROM payroll_details pd
LEFT JOIN employees e ON pd.emp_id = e.employee_id
WHERE pd.payroll_id = ?
";

// optionally filter by month (keeps original behavior that used LIKE)
if ($monthRaw !== '') {
    $sql .= " AND pd.month LIKE ?";
}

$sql .= " ORDER BY pd.id ASC";

$stmt = $GLOBALS['conn']->prepare($sql);
if ($stmt === false) {
    // handle prepare error (simple fallback)
    die('Database error: ' . $GLOBALS['conn']->error);
}

if ($monthRaw !== '') {
    // keep the original LIKE behavior — the caller may provide wildcards if intended
    $stmt->bind_param('is', $payroll_id, $monthRaw);
} else {
    $stmt->bind_param('i', $payroll_id);
}

$stmt->execute();
$res = $stmt->get_result();

// if no rows found, redirect to payroll page (same behaviour as before)
if ($res === false || $res->num_rows === 0) {
    $stmt->close();
    header('Location: '.baseUri().'/payroll/'.$payroll_id);
    exit;
}

// Stream CSV to client (headers first, then rows as they are fetched)
$filename = "Payroll_details_" . date('Ymd') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// CSV header row (same columns you used)
$headers = [
    'Staff No.', 'Status', 'Full Name', 'Designation', 'Duty Station', 'State',
    'Contract Type', 'Payroll Month', 'Required Days', 'Days Worked',
    'Gross Salary', 'Earnings', 'Deductions', 'Tax', 'Net Salary', 'Bank Name', 'Account Number'
];
fputcsv($output, $headers);

// Stream rows
while ($row = $res->fetch_assoc()) {
    // Prefer employee-table values (authoritative) where available, fallback to payroll_details
    $staff_no       = $row['staff_no'];
    $status         = $row['emp_status'] ?? $row['payroll_status'];
    $full_name      = $row['pd_full_name'];
    $designation    = $row['emp_designation'] ?? $row['job_title'];
    $location_name  = $row['emp_location_name'] ?? '';
    $state          = $row['emp_state'] ?? '';
    $contract_type  = $row['contract_type'];
    $payrollMonthRaw = $row['payroll_month'];
    $monthFormatted = $payrollMonthRaw ? @date('F Y', strtotime($payrollMonthRaw)) : '';
    $required_days  = $row['required_days'];
    $days_worked    = $row['days_worked'];
    $base_salary    = (float) $row['base_salary'];
    $earnings       = (float) $row['earnings'];
    $total_deductions = (float) $row['total_deductions'];
    $net_salary     = (float) $row['net_salary'];
    $bank_name      = $row['bank_name'];
    $bank_number    = $row['bank_number'];

    // compute tax percentage using employee's state_id (falls back to 0 if missing)
    $taxPercentage = getTaxPercentage($base_salary, (int)($row['emp_state_id'] ?? 0));
    $taxFormatted = formatMoney($row['tax']) . " (" . $taxPercentage . "%)";

    $data = [
        $staff_no,
        $status,
        $full_name,
        $designation,
        $location_name,
        $state,
        $contract_type,
        $monthFormatted,
        $required_days,
        $days_worked,
        formatMoney($base_salary),
        formatMoney($earnings),
        formatMoney($total_deductions),
        $taxFormatted,
        formatMoney($net_salary),
        $bank_name,
        $bank_number
    ];

    fputcsv($output, $data);
}

// cleanup
fclose($output);
$stmt->close();
exit;
?>
