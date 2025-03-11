<?php
require('./app/init.php');
if (!authenticate()) {
    header("Location: ".baseUri()."/login ");
    exit; // Important to exit to prevent further execution
}
// Include database connection
// Ensure $GLOBALS['conn'] is properly set before using this code

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=employees.csv');

// Open a file pointer for output
$output = fopen('php://output', 'w');

// Write the CSV headers
fputcsv($output, [
    'Employee ID', 'Staff No', 'Full Name', 'Email', 'Phone Number', 
    'National ID', 'Gender', 'Date of Birth', 'Avatar', 'State ID', 
    'State', 'City', 'Address', 'Branch ID', 'Branch', 
    'Location ID', 'Location Name', 'Position', 'Project ID', 'Project',
    'Designation', 'Hire Date', 'Contract Start', 'Contract End', 'Work Days', 
    'Work Hours', 'Contract Type', 'Salary', 'Budget Code', 'MOH Contract', 
    'Payment Bank', 'Payment Account', 'Grade', 'Tax Exempt', 'Seniority', 
    'Status', 'Added Date', 'Updated Date', 'Added By', 'Updated By'
]);

// Fetch data from employees table
$sql = "SELECT * FROM employees";
$result = $GLOBALS['conn']->query($sql);

// Check if query is successful
if ($result && $result->num_rows > 0) {
    // Loop through the rows and output them as CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} else {
    // If no data, write a message
    fputcsv($output, ['No data found']);
}

// Close the file pointer
fclose($output);

// End the script to prevent extra output
exit;
?>
