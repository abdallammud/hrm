<?php 
$payroll_id = $_GET['id'] ?? 0;

$allColumns = [
    'employee_id' => 'Employee ID', 'staff_no' => 'Staff No', 'full_name' => 'Full Name', 'email' => 'Email',
    'contract_type' => 'Contract Type', 'job_title' => 'Job Title', 'month' => 'Payroll Month',
    'required_days' => 'Required Days', 'days_worked' => 'Days Worked', 'gross_salary' => 'Gross Salary',
    'net_salary' => 'Net Salary', 'earnings' => 'Earnings', 'deductions' => 'Deductions', 'tax' => 'Tax',
    'unpaid_days' => 'Unpaid Days', 'unpaid_hours' => 'Unpaid Hours', 'status' => 'Status',
    'bank_name' => 'Bank Name', 'bank_number' => 'Bank Number', 'pay_date' => 'Pay Date',
    'paid_by' => 'Paid By', 'paid_through' => 'Paid Through', 'action' => 'Action'
];

$payrollInfo = get_data('payroll', ['id' => $payroll_id]);
$payrollInfo = $payrollInfo ? $payrollInfo[0] : ['month' => '', 'ref' => '', 'ref_name' => '', 'added_date' => '', 'status' => ''];
$payrollInfo['month'] = explode(",", $payrollInfo['month']);

$month = '2025-01';
$monthName = (new DateTime($month))->format('F Y');

$showColumns = get_columns('payroll_pdf', 'show_columns');

// Fetch payroll data
$sql = "SELECT `id`, `payroll_id`, `emp_id`, `staff_no`, `full_name`, `contract_type`, `job_title`, `month`, 
    `required_days`, `days_worked`, `base_salary`, (`allowance` + `bonus` + `commission`) AS earnings, 
    (`loan` + `advance` + `deductions`) AS `total_deductions`, `tax`, 
    (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary 
    FROM `payroll_details` WHERE `payroll_id` = $payroll_id AND `month` LIKE '$month'";

$query = $GLOBALS['conn']->query($sql);
$data = [];
while ($row = $query->fetch_assoc()) {
    $data[] = $row;
}

$companyInfo = get_data('company', ['id' => 1])[0];

class MYPDF extends TCPDF {
    // Page footer
    public function Footer() {
        // Set position to 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Author Name');
$pdf->SetTitle('All Employees Report');
$pdf->SetSubject('Employees Report');

// Disable default header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(10, 10, 10); // left, top, right
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(20);

$pdf->SetAutoPageBreak(TRUE, 15);

// Add a page
$pdf->AddPage();

$primary_color = return_setting('primary_color');
$secondary_color = return_setting('secondary_color');

$primary_color = explode(",", hexToRgb($primary_color));
$secondary_color =explode(",", hexToRgb($secondary_color));

$logo = get_logo_name_from_url();
$pdf->Image('./assets/images/'.$logo, 10, 10, 30);

$pdf->SetFont('helvetica', 'B', 16);
$y = 10;
$pdf->SetTextColor($primary_color[0], $primary_color[1], $primary_color[2]);
$pdf->SetXY(158, $y);
$pdf->Cell(130, 7, strtoupper($companyInfo['name']), 0, 0, 'R');
$pdf->SetTextColor(000, 000, 000);

$pdf->SetFont('helvetica', '', 10);
$y += 8;
$pdf->SetXY(158, $y);
$pdf->Cell(130, 7, $companyInfo['contact_phone'], 0, 0, 'R');

$y += 5;
$pdf->SetXY(158, $y);
$pdf->Cell(130, 7, $companyInfo['contact_email'], 0, 0, 'R');

$pdf->SetFont('helvetica', 'B', 12);
$y += 6;
$pdf->SetXY(158, $y);
$pdf->Cell(130, 7, strtoupper($monthName . " payroll report"), 0, 0, 'R');

$pdf->SetFont('helvetica', '', 10);
$y += 6;
$pdf->SetXY(158, $y);
$pdf->Cell(130, 7, "Print date " . date("F d Y h:i:s A"), 0, 0, 'R');

$pdf->SetFillColor($primary_color[0], $primary_color[1], $primary_color[2]);
$pdf->SetDrawColor($primary_color[0], $primary_color[1], $primary_color[2]);

$pdf->Rect(10, 45, 278, 0.2);
$pdf->setY(50);

$pdf->SetDrawColor(000, 000, 000);

$pdf->SetFont('helvetica', 'B', 10);
// Column Width Calculation
$pageWidth = 278;  // Total page width in A4 landscape (approx)
$fullNameWidth = 70;  // Fixed width for Full Name column
$remainingColumns = array_diff($showColumns, ['full_name']);  // Other columns
$remainingWidth = $pageWidth - $fullNameWidth;  // Remaining space for other columns
$dynamicWidth = count($remainingColumns) > 0 ? $remainingWidth / count($remainingColumns) : $remainingWidth;

// Table Header
foreach ($showColumns as $col) {
    $width = ($col == 'full_name') ? $fullNameWidth : $dynamicWidth;
    $pdf->Cell($width, 8, $allColumns[$col], 1, 0, 'L', 1);
}
$pdf->Ln();

$pdf->SetFillColor(120, 120, 120);
$pdf->SetDrawColor(120, 120, 120);
// Table Data
$pdf->SetFont('aefuratB', '', 10);
foreach ($data as $row) {
    foreach ($showColumns as $col) {
        $width = ($col == 'full_name') ? $fullNameWidth : $dynamicWidth;
        $moneyColumns = ['gross_salary', 'net_salary', 'earnings', 'deductions', 'tax'];
        if(in_array($col, $moneyColumns)) {
        	$pdf->Cell($width, 7, isset($row[$col]) ? formatMoney($row[$col]) : formatMoney(0), 1, 0, 'L');
        } else {
        	if($col == 'full_name') $row[$col] = ucwords(strtolower($row[$col]));
        	$pdf->Cell($width, 7, isset($row[$col]) ? $row[$col] : '-', 1, 0, 'L');
        }
        
    }
    $pdf->Ln();
}

$pdf->Output("$monthName payroll report.pdf", 'I');
 ?>