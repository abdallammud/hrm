<?php
$month = $_GET['month'];
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

// Create new PDF document
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Author Name');
$pdf->SetTitle('Attendance Report');
$pdf->SetSubject('Attendance Report');

// Disable default header and footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(10, 10, 10); // left, top, right
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(20);

$pdf->SetAutoPageBreak(TRUE, 15);

// Add a page
$pdf->AddPage();

$pdf->SetFont('aefurat', '', 12);
// $pdf->Image('./assets/images/banner.png', 0, 0, 280, 40); // Adjust size as needed
$pdf->Image('./assets/images/logo.png', 85, 10, 40);
// Set header rectangle
$pdf->SetFillColor(80, 184, 72);
$pdf->SetDrawColor(80, 184, 72);
$pdf->Rect(10, 40, 192, 0.2); // Adjusted position and width for landscape

$pdf->SetFont('aefurat', 'B', 13);
$pdf->SetXY(10, 45);
$pdf->Cell(0, 10, "Attendance Report  ", 0, 1, 'C');

$pdf->SetFont('aefurat', '', 10);
$pdf->SetXY(10, 50);
$pdf->Cell(0, 10,  date('F Y', strtotime($month)), 0, 1, 'C');

$pdf->SetFont('aefurat', '', 10);
$pdf->SetXY(10, 55);
$pdf->Cell(0, 10, "Print date  " . date('F d, Y h:i:s A'), 0, 1, 'C');

$y = 65;

// Table Header
$pdf->SetFont('aefurat', 'B', 10);
$pdf->SetXY(10, $y);
$pdf->Cell(15, 7, "Staff No.", 1, 0, 'L', true);
$pdf->Cell(60, 7, "Full name", 1, 0, 'L', true);
$pdf->Cell(23, 7, "Days worked", 1, 0, 'L', true);
$pdf->Cell(23, 7, "Paid Leave", 1, 0, 'L', true);
$pdf->Cell(23, 7, "Un-paid Leave", 1, 0, 'L', true);
$pdf->Cell(23, 7, "Not hired days", 1, 0, 'L', true);
$pdf->Cell(23, 7, "Holidays", 1, 1, 'L', true);

$y += 7;

$query = "SELECT emp_id, staff_no, full_name, SUM(CASE WHEN status = 'P' THEN 1 ELSE 0 END) AS present_count, SUM(CASE WHEN status = 'PL' THEN 1 ELSE 0 END) AS paid_leave_count, SUM(CASE WHEN status = 'UPL' THEN 1 ELSE 0 END) AS unpaid_leave_count, SUM(CASE WHEN status = 'NH' THEN 1 ELSE 0 END) AS not_hired_count, SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) AS holiday_count, SUM(CASE WHEN status = 'N' THEN 1 ELSE 0 END) AS no_show_count FROM  atten_details  WHERE `atten_date` LIKE '$month%' GROUP BY `emp_id` ";
$employees = $GLOBALS['conn']->query($query);
$num = 1;

if ($employees->num_rows > 0) {
    while ($row = $employees->fetch_assoc()) {
        $emp_id = $row['emp_id'];
        $staff_no = $row['staff_no'];
        $full_name = $row['full_name'];
    	$paid_leave_count = $row['paid_leave_count'];
    	$unpaid_leave_count = $row['unpaid_leave_count'];
    	$not_hired_count = $row['not_hired_count'];
    	$holiday_count = $row['holiday_count'];
    	$required_days = $worked_days = 0;
    	$employeeInfo = get_data('employees', ['employee_id' => $emp_id]);
    	if($employeeInfo) {
    		$employeeInfo = $employeeInfo[0];
    		$work_days = $employeeInfo['work_days'];
    		$required_days = getWorkdaysInMonth($month, $work_days = 0);
    		$required_days -= $not_hired_count + $holiday_count;
    		$worked_days = $required_days - $paid_leave_count - $unpaid_leave_count;
    	}
    	$row['required_days'] =$required_days;
    	$row['worked_days'] =$worked_days;
        $result['data'][] = $row;

        // Check if we need to add a new page
        if ($y + 7 > 180) { // Adjust this value based on your layout
            $pdf->AddPage();
            $pdf->SetFont('aefurat', '', 10);
            $y = 10; // Reset Y position after adding a new page

            // Re-add table header on the new page
            $pdf->SetFont('aefurat', 'B', 10);
            $pdf->SetXY(10, $y);
			$pdf->Cell(15, 7, "Staff No.", 1, 0, 'L', true);
			$pdf->Cell(60, 7, "Full name", 1, 0, 'L', true);
			$pdf->Cell(23, 7, "Days worked", 1, 0, 'L', true);
			$pdf->Cell(23, 7, "Paid Leave", 1, 0, 'L', true);
			$pdf->Cell(23, 7, "Un-paid Leave", 1, 0, 'L', true);
			$pdf->Cell(23, 7, "Not hired days", 1, 0, 'L', true);
			$pdf->Cell(23, 7, "Holidays", 1, 1, 'L', true);

            $y += 7;
        }

        // Add row
        $pdf->SetFont('aefurat', '', 10);
        $pdf->SetXY(10, $y);
		$pdf->Cell(15, 7, "$staff_no", 1, 0, 'L', 0);
		$pdf->Cell(60, 7, "$full_name", 1, 0, 'L', 0);
		$pdf->Cell(23, 7, $worked_days . "/" . $required_days, 1, 0, 'L', 0);
		$pdf->Cell(23, 7, $paid_leave_count, 1, 0, 'L', 0);
		$pdf->Cell(23, 7, $unpaid_leave_count, 1, 0, 'L', 0);
		$pdf->Cell(23, 7, $not_hired_count, 1, 0, 'L', 0);
		$pdf->Cell(23, 7, $holiday_count, 1, 1, 'L', 0);


       

        $num++;
        $y += 7;
    }

    // Draw footer line on the last page
    $pdf->Rect(10, $y, 190, 0.1);
}

// Output the PDF
$pdf->Output("Attendance Report" . date('F Y', strtotime($month)) .".pdf", 'I');
?>
