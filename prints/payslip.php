<?php 
// Extend the TCPDF class to create a custom footer
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
$payrollDetId = $_GET['rec_id'] ?? 0;
$query = $GLOBALS['conn']->query("SELECT * FROM `payroll_details` WHERE `id` = '$payrollDetId'");
$rec = [];
$emp = [];
if($query) {
	while($row = $query->fetch_assoc()) {
		$rec = $row;
		$full_name = $row['full_name'];
		$emp_id = $row['emp_id'];
		$month1 = $row['month'];
		$month = $row['month'];
		$base_salary = $row['base_salary'];
		$added_date = $row['added_date'];
		$month = date('F Y', strtotime($month));
		$added_date = date('F d, Y', strtotime($added_date));

		$attenInfo = calculateAttendanceStats($emp_id, $month1);



		$emp = $employee = $GLOBALS['employeeClass']->read($emp_id);
		$state_id = $employee['state_id'];
		$taxPercentage = getTaxPercentage($base_salary, $state_id);
		// var_dump($employee);
		if(!$employee['avatar']) {
			if(strtolower($employee['gender']) == 'female')  {
				$employee['avatar'] = 'female_avatar.png';
			} else {
				$employee['avatar'] = 'male_avatar.png';
			}
		}

		$avatar = $employee['avatar'];
	}
}

// Create new PDF document
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Hawlkar it solutions');
$pdf->SetTitle('Payslip');
$pdf->SetSubject('Payslip');

// Disable default header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(10, 10, 10); // left, top, right
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(20);

$pdf->SetAutoPageBreak(TRUE, 15);

// Add a page
$pdf->AddPage();

$pdf->SetFont('dejavusans', '', 12);
$pdf->Image('./assets/images/logo.png', 15, 10, 40); // Adjust size as needed

$y = 40;
$pdf->SetFillColor(80, 184, 72);
$pdf->Rect(10, $y, 190, 0.8, "F");

$pdf->SetFont('dejavusans', 'B', 20);
$pdf->SetXY(15, $y-25);
$pdf->Cell(0, 10, strtoupper("AAH Somalia"), 0, 1, 'C');

$pdf->SetFont('dejavusans', 'B', 10);
$pdf->SetXY(15, $y-17);
$pdf->Cell(0, 10, strtoupper($employee['location_name']), 0, 1, 'C');

$y += 5;
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->SetXY(10, $y);
$pdf->Cell(0, 7, strtoupper("monthly payslip"), "LBRT", 1, 'C');

$y += 7.5;
$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(10, $y);
$pdf->Cell(0, 7, ucwords($month), "LBRT", 1, 'C');

$y += 7.5;
$Y = $y;
$pdf->Rect(10, $y, 190, 60);

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "STAFF ID CODE", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(55, $y);
$pdf->Cell(40, 7, strtoupper($employee['staff_no']), "", 1, 'L');

$y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "NAME", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(55, $y);
$pdf->Cell(40, 7, strtoupper($employee['full_name']), "", 1, 'L');

$y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "ROLE", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(55, $y);
$pdf->Cell(40, 7, strtoupper($employee['position']), "", 1, 'L');

$y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "GRADE", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(55, $y);
$pdf->Cell(40, 7, strtoupper($employee['grade']), "", 1, 'L');

$y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "TAX EXEMPTION", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(55, $y);
$pdf->Cell(40, 7, strtoupper($employee['tax_exempt']), "", 1, 'L');

$y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "STATE", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(55, $y);
$pdf->Cell(40, 7, strtoupper($employee['state']), "", 1, 'L');

$y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "DEPARTMENT", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(55, $y);
$pdf->Cell(40, 7, strtoupper($employee['branch']), "", 1, 'L');

$y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "SENIORITY IN YEARS", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(55, $y);
$pdf->Cell(40, 7, strtoupper($employee['seniority']), "", 1, 'L');

$y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(130, $Y);
$pdf->Cell(40, 7, "NO. WORKING DAYS", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(180, $Y);
$pdf->Cell(40, 7, strtoupper($rec['required_days']), "", 1, 'L');

$Y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(130, $Y);
$pdf->Cell(40, 7, "NO. MONTHLY HOURS", "", 1, 'L');

$hours = $rec['required_days'] * $employee['work_hours'];
if(!$hours || !is_numeric($hours)) $hours = 0;
$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(180, $Y);
$pdf->Cell(40, 7, strtoupper($hours), "", 1, 'L');


$Y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(130, $Y);
$pdf->Cell(40, 7, "NO. DAYS WORKED", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(180, $Y);
$pdf->Cell(40, 7, strtoupper($rec['days_worked']), "", 1, 'L');

$Y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(130, $Y);
$pdf->Cell(40, 7, "MONTHLY HOURS WORKED", "", 1, 'L');

$worked_hours = $rec['days_worked'] * $employee['work_hours'];

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(180, $Y);
$pdf->Cell(40, 7, strtoupper($worked_hours), "", 1, 'L');

$Y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(130, $Y);
$pdf->Cell(40, 7, "ABSENCE DAYS", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(180, $Y);
$pdf->Cell(40, 7, strtoupper($attenInfo['unpaid_leave_days'] + $attenInfo['paid_leave_days'] + $attenInfo['sick_days'] + $attenInfo['no_show_days']), "", 1, 'L');

$Y += 7.5;

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(130, $Y);
$pdf->Cell(40, 7, "DAYS NOT HIRED", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(180, $Y);
$pdf->Cell(40, 7, strtoupper($attenInfo['not_hired_days']), "", 1, 'L');


$y += 1;
$Y = $y;
$pdf->Rect(10, $y, 190, 150);

$y += 1;
$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "Base salary", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(110, $y);
$pdf->Cell(40, 7, " + ", "", 1, 'L');

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(120, $y);
$pdf->Cell(40, 7, formatMoney($rec['base_salary']), "", 1, 'L');

$gross_salary = $rec['base_salary'];

$allowanceTypes = $GLOBALS['conn']->query("SELECT * FROM `trans_subtypes` WHERE `type` IN ('Allowance', 'Bonus')");
if($allowanceTypes->num_rows > 0) {
	while($atRow = $allowanceTypes->fetch_assoc()) {
		$name = $atRow['name'];
		$atAmount = 0;

		$get_amount = $GLOBALS['conn']->query("SELECT * FROM `employee_transactions` WHERE `transaction_subtype` = '$name' AND `emp_id` = $emp_id AND `date` LIKE '$month%'");
		if($get_amount->num_rows > 0) {
			$atAmount = $get_amount->fetch_assoc()['amount'];
		}

		$y += 7.5;
		$pdf->SetFont('dejavusans', '', 9);
		$pdf->SetXY(13, $y);
		$pdf->Cell(40, 7, "$name", "", 1, 'L');

		$pdf->SetFont('dejavusans', '', 9);
		$pdf->SetXY(110, $y);
		$pdf->Cell(40, 7, " + ", "", 1, 'L');

		$pdf->SetFont('dejavusans', '', 9);
		$pdf->SetXY(120, $y);
		$pdf->Cell(40, 7, formatMoney($atAmount), "", 1, 'L');

		$gross_salary += $atAmount;
	}
}

$y += 7.5;
$pdf->SetFillColor(200, 200, 200);
$pdf->Rect(13, $y-0.7, 186, 8, "F");

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "Gross salary", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(110, $y);
$pdf->Cell(40, 7, " = ", "", 1, 'L');

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(120, $y);
$pdf->Cell(40, 7, formatMoney($gross_salary), "", 1, 'L');


$y += 7.5;
$pdf->SetFillColor(255, 255, 255);
$pdf->Rect(13, $y-0.7, 186, 8, "F");

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "Taxes", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(110, $y);
$pdf->Cell(40, 7, " - ", "", 1, 'L');

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(120, $y);
$pdf->Cell(40, 7, formatMoney($rec['tax']), "", 1, 'L');


$y += 7.5;
$pdf->SetFillColor(255, 255, 255);
$pdf->Rect(13, $y-0.7, 186, 8, "F");

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "Salary advance", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(110, $y);
$pdf->Cell(40, 7, " - ", "", 1, 'L');

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(120, $y);
$pdf->Cell(40, 7, formatMoney($rec['advance']), "", 1, 'L');


$y += 7.5;
$pdf->SetFillColor(255, 255, 255);
$pdf->Rect(13, $y-0.7, 186, 8, "F");

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "Total deductions", "", 1, 'L');
$total_deductions = $rec['advance']+$rec['loan'] + $rec['deductions'] + $rec['unpaid_days'] + $rec['unpaid_hours']+$rec['tax'];
$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(110, $y);
$pdf->Cell(40, 7, " = ", "", 1, 'L');

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(120, $y);
$pdf->Cell(40, 7, formatMoney($total_deductions), "", 1, 'L');

$total_earnings = $rec['base_salary']+$rec['allowance'] + $rec['bonus'] + $rec['extra_hours'] + $rec['commission'];

$y += 7.5;
$net_salary = $total_earnings - $total_deductions;
$pdf->SetFillColor(200, 200, 200);
$pdf->Rect(13, $y-0.7, 186, 8, "F");

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "Net salary rounded", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(110, $y);
$pdf->Cell(40, 7, " = ", "", 1, 'L');

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(120, $y);
$pdf->Cell(40, 7, formatMoney($net_salary), "", 1, 'L');



$y += 10;
$pdf->SetFillColor(200, 200, 200);
$pdf->Rect(13, $y-0.7, 186, 8, "F");

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(13, $y);
$pdf->Cell(40, 7, "Total to be paid", "", 1, 'L');

$pdf->SetFont('dejavusans', '', 9);
$pdf->SetXY(110, $y);
$pdf->Cell(40, 7, " = ", "", 1, 'L');

$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetXY(120, $y);
$pdf->Cell(40, 7, formatMoney($net_salary), "", 1, 'L');


// Output the PDF
$pdf->Output("Payslip.pdf", 'I');
?>