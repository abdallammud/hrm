# Attendance Features Documentation

## Overview

The attendance management system in the HRM application provides comprehensive functionality for tracking employee attendance, managing leave requests, and recording time-based data. The system supports multiple attendance recording methods, automated leave integration, and flexible reporting capabilities.

## Core Features

### 1. Attendance Recording

#### Individual Employee Attendance
The system allows recording attendance for individual employees with the following capabilities:

**User Story**: As an HR administrator, I want to record attendance for individual employees, so that I can track their daily presence and absence patterns.

**Key Features**:
- Single employee attendance recording
- Multiple attendance status options (Present, Absent, Late, Leave)
- Automatic leave status detection from approved leave requests
- Date-specific attendance tracking
- Integration with employee organizational assignments

**Code Example - Individual Attendance Recording**:
```php
// From atten_controller.php - Individual attendance creation
$data = array(
    'ref' => 'Employee',           // Reference type
    'ref_id' => $employee_id,      // Employee ID
    'ref_name' => $employee_name,  // Employee name
    'atten_date' => $post['atten_date'], 
    'added_by' => $_SESSION['user_id']
);

$result['id'] = $attendanceClass->create($data);
```

#### Bulk Attendance Recording
The system supports bulk attendance recording for departments and locations:

**User Story**: As an HR administrator, I want to record attendance for entire departments or locations, so that I can efficiently manage attendance for large groups of employees.

**Key Features**:
- Department-wide attendance recording
- Location-based attendance recording
- Automatic employee filtering based on organizational structure
- Batch processing with database transactions
- Error handling and rollback capabilities

**Code Example - Bulk Attendance Logic**:
```php
// From atten_controller.php - Employee filtering for bulk attendance
$get_employees = "SELECT * FROM `employees` WHERE `status` = 'active'";
if($post['ref'] == 'Employee') {
    $get_employees .= " AND `employee_id` = '$ref_id'";
} else if($post['ref'] == 'Department') {
    $get_employees .= " AND `branch_id` = '$ref_id'";
} else if($post['ref'] == 'Location') {
    $get_employees .= " AND `location_id` = '$ref_id'";
}

$empSet = $GLOBALS['conn']->query($get_employees);
```

#### CSV Upload Attendance
The system provides CSV-based bulk attendance upload functionality:

**User Story**: As an HR administrator, I want to upload attendance data via CSV files, so that I can efficiently process large volumes of attendance data from external systems.

**Key Features**:
- CSV file validation and processing
- Row-by-row error handling
- Batch processing with transaction management
- Template generation for consistent data format
- Comprehensive error reporting

**Code Example - CSV Upload Validation**:
```php
// From atten_controller.php - CSV file validation
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'];
    $fileType = $_FILES['file']['type'];

    // Validate file type
    if ($fileType != 'text/csv') {
        $result['error'] = true;
        $result['msg'] = "Invalid file type. Please upload a valid CSV file.";
        echo json_encode($result);
        exit();
    }
}
```

### 2. Leave Management

#### Leave Type Configuration
The system allows administrators to configure different types of leave:

**User Story**: As an HR administrator, I want to configure different leave types, so that I can manage various leave policies and their payment implications.

**Key Features**:
- Paid and unpaid leave type definitions
- Leave type name uniqueness validation
- Administrative control over leave type creation
- Integration with employee leave requests

**Code Example - Leave Type Creation**:
```php
// From atten_controller.php - Leave type creation
$data = array(
    'name' => $post['name'], 
    'paid_type' => $post['paid_type'], 
    'added_by' => $_SESSION['user_id']
);

check_exists('leave_types', ['name' => $post['name']]);
check_auth('create_leave_types');

$result['id'] = $leaveTypesClass->create($data);
```

#### Employee Leave Requests
The system manages employee leave requests with automatic calculations:

**User Story**: As an employee, I want to submit leave requests, so that my absence can be properly recorded and approved through the system.

**Key Features**:
- Leave request submission with date ranges
- Automatic leave day calculation
- Leave type integration with payment implications
- Leave status tracking (Pending, Approved, Rejected, Cancelled)
- Duplicate leave request prevention

**Code Example - Employee Leave Creation**:
```php
// From atten_controller.php - Employee leave request processing
$leave_typeInfo = get_data('leave_types', array('id' => $post['leave_id']))[0];
$data = array(
    'emp_id' => $post['emp_id'], 
    'leave_id' => $post['leave_id'], 
    'paid_type' => $leave_typeInfo['paid_type'], 
    'date_from' => $post['date_from'], 
    'date_to' => $post['date_to'], 
    'days_num' => getDateDifference($post['date_from'], $post['date_to'])['totalDays'],
    'added_by' => $_SESSION['user_id']
);

check_exists('employee_leave', [
    'leave_id' => $post['leave_id'], 
    'emp_id' => $post['emp_id'], 
    'date_from' => $post['date_from']
]);
```

#### Automatic Leave Integration
The system automatically integrates approved leave with attendance recording:

**User Story**: As an HR administrator, I want the system to automatically apply leave status during attendance recording, so that employees on approved leave are correctly marked without manual intervention.

**Key Features**:
- Real-time leave status checking during attendance recording
- Automatic status assignment (PL for Paid Leave, UL for Unpaid Leave)
- Date range validation for leave periods
- Leave status override in attendance records

**Code Example - Automatic Leave Detection**:
```php
// From atten_controller.php - Automatic leave status detection
$leaveType = '';
$atten_date = $post['atten_date'];
$check_leave = $GLOBALS['conn']->query("SELECT * FROM `employee_leave` 
    WHERE `emp_id` = '$employee_id' 
    AND `status` <> 'Cancelled' 
    AND '$atten_date' BETWEEN `date_from` AND `date_to`");

if($check_leave->num_rows > 0) {
    while($leaveRow = $check_leave->fetch_assoc()) {
        $paid_type = $leaveRow['paid_type'];
        if($paid_type == 'Unpaid') {
            $leaveType = 'UL';
        } else {
            $leaveType = 'PL';
        }
    }
}

if($leaveType) $post['atten_status'] = $leaveType;
```

### 3. Attendance Status Management

#### Status Code System
The system uses a comprehensive status code system for attendance tracking:

**Status Codes**:
- **P**: Present - Employee was present for the full day
- **A**: Absent - Employee was absent without leave
- **L**: Late - Employee arrived late
- **PL**: Paid Leave - Employee on approved paid leave
- **UL**: Unpaid Leave - Employee on approved unpaid leave
- **S**: Sick - Employee was sick
- **H**: Holiday - Public or company holiday
- **NH**: Not Hired Day - Employee not yet hired
- **N**: Absent - General absence

#### Status Assignment Logic
The system automatically assigns appropriate status codes based on various conditions:

**User Story**: As an HR administrator, I want the system to automatically assign appropriate attendance status codes, so that attendance records accurately reflect employee situations without manual status determination.

**Key Features**:
- Automatic leave status assignment from approved leave requests
- Manual status override capabilities
- Status validation and consistency checking
- Historical status tracking and reporting

### 4. Resource Allocation Integration

#### Project and Budget Allocation
The system includes resource allocation functionality for project and budget tracking:

**User Story**: As a project manager, I want to allocate employee time to specific projects and budgets, so that I can track resource utilization and project costs.

**Key Features**:
- JSON-based allocation data storage
- Project and budget time allocation
- Supervisor assignment and tracking
- Monthly allocation periods
- Total time calculation from allocation data

**Code Example - Resource Allocation Time Calculation**:
```php
// From AttendanceClass.php - Total time calculation from JSON allocation
public function getTotalTime($jsonData) {
    $data = json_decode($jsonData, true);
    $totalTime = 0;

    // Check budgets allocation
    if (isset($data['budgets']) && is_array($data['budgets'])) {
        foreach ($data['budgets'] as $budget) {
            if (isset($budget['time']) && is_numeric($budget['time'])) {
                $totalTime += $budget['time'];
            }
        }
    }

    // Check projects allocation
    if (isset($data['projects']) && is_array($data['projects'])) {
        foreach ($data['projects'] as $project) {
            if (isset($project['time']) && is_numeric($project['time'])) {
                $totalTime += $project['time'];
            }
        }
    }

    return $totalTime;
}
```

## Technical Implementation

### Database Transaction Management
The system uses database transactions to ensure data integrity during complex operations:

**Code Example - Transaction Management**:
```php
// From atten_controller.php - Transaction handling
try {
    $GLOBALS['conn']->begin_transaction();
    
    // Create attendance record
    $result['id'] = $attendanceClass->create($data);
    
    // Create attendance details for each employee
    foreach($employees as $employee) {
        $detailData = [
            'atten_id' => $atten_id,
            'emp_id' => $employee_id,
            'full_name' => $full_name,
            'phone_number' => $phone_number,
            'email' => $email,
            'staff_no' => $staff_no,
            'status' => $post['atten_status'],
            'atten_date' => $post['atten_date'], 
            'added_by' => $_SESSION['user_id']
        ];
        $result['id'] = $attenDetailsClass->create($detailData);
    }
    
    $GLOBALS['conn']->commit();
} catch (Exception $e) {
    $GLOBALS['conn']->rollback();
    $result['error'] = true;
    $result['msg'] = 'Error: ' . $e->getMessage();
}
```

### Security and Authorization
The system implements comprehensive security measures:

**Authorization Checks**:
- `create_leave_types` - Permission to create leave types
- `create_leave` - Permission to create employee leave requests
- `create_attendance` - Permission to record attendance
- `create_timesheet` - Permission to record timesheets
- `create_allocation` - Permission to manage resource allocation

**Data Validation**:
- Input sanitization using `escapePostData()` and `escapeStr()`
- Uniqueness validation for critical records
- File type validation for uploads
- Date range validation for leave requests

### Error Handling and Logging
The system provides comprehensive error handling:

**Features**:
- Exception-based error handling
- Database transaction rollback on errors
- Detailed error messages for debugging
- JSON response format for API consistency
- File upload error handling

## Integration with Other Systems

### Payroll Integration
Attendance data directly affects payroll calculations:

**Integration Points**:
- Present days count for salary calculations
- Leave days impact on salary deductions
- Overtime calculation from timesheet data
- Leave type (paid/unpaid) affects payroll processing

### Employee Management Integration
The attendance system is tightly integrated with employee management:

**Integration Features**:
- Employee status filtering (active/inactive)
- Organizational structure integration (branches, locations)
- Employee data synchronization in attendance records
- Employee hierarchy consideration in reporting

### Reporting Integration
Attendance data feeds into various reporting systems:

**Report Types**:
- Daily attendance summaries
- Monthly attendance reports
- Leave utilization reports
- Employee attendance history
- Department-wise attendance statistics

## User Interface Features

### Attendance Recording Interface
The system provides user-friendly interfaces for attendance recording:

**Features**:
- Employee selection with search and filtering
- Date picker for attendance date selection
- Status dropdown with predefined options
- Bulk selection capabilities for multiple employees
- Real-time validation and feedback

### Leave Management Interface
The leave management interface supports:

**Features**:
- Leave type selection with paid/unpaid indicators
- Date range picker for leave periods
- Automatic day calculation display
- Leave status tracking and updates
- Leave history viewing and management

### CSV Upload Interface
The CSV upload functionality includes:

**Features**:
- Template download for consistent formatting
- File validation and error reporting
- Progress indicators for large file processing
- Error summary with row-specific details
- Preview functionality before final processing

This comprehensive attendance management system provides the foundation for accurate time tracking, leave management, and integration with payroll and reporting systems throughout the HRM application.