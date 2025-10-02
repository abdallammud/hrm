# Timesheet Management Documentation

## Overview

The timesheet management system provides detailed time tracking capabilities that complement the attendance system. It records precise work hours, manages time-in and time-out data, and integrates directly with payroll calculations to ensure accurate compensation based on actual hours worked.

## Core Timesheet Features

### 1. Time Recording System

#### Individual Timesheet Entry
The system allows recording detailed time information for individual employees:

**User Story**: As an HR administrator, I want to record precise work hours for employees, so that payroll calculations can be based on actual time worked rather than just attendance status.

**Key Features**:
- Time-in and time-out recording
- Date-specific timesheet entries
- Employee-specific time tracking
- Status validation for time entries
- Integration with attendance data

**Technical Implementation**:
The timesheet system uses a master-detail relationship similar to attendance, with `timesheet` as the master table and `timesheet_details` containing individual employee time records.

**Code Example - Timesheet Creation Structure**:
```php
// From AttendanceClass.php - Timesheet model classes
class Timesheet extends Model {
    public function __construct() {
        parent::__construct('timesheet');
    }
}

class TimesheetDetails extends Model {
    public function __construct() {
        parent::__construct('timesheet_details');
    }
}

// Global instances for system-wide access
$GLOBALS['timesheetClass'] = $timesheetClass = new Timesheet();
$GLOBALS['timesheetDetailsClass'] = $timesheetDetailsClass = new TimesheetDetails();
```

#### Bulk Timesheet Recording
The system supports bulk timesheet entry for multiple employees:

**User Story**: As an HR administrator, I want to record timesheet data for multiple employees simultaneously, so that I can efficiently process time data for entire departments or shifts.

**Key Features**:
- Department-wide timesheet recording
- Location-based timesheet entry
- Batch processing with transaction management
- Automatic employee filtering based on organizational structure
- Error handling and data validation

### 2. Time Calculation and Validation

#### Work Hours Calculation
The system automatically calculates work hours based on time-in and time-out entries:

**Calculation Logic**:
- **Regular Hours**: Standard 8-hour workday calculation
- **Overtime Hours**: Hours worked beyond standard work time
- **Total Hours**: Complete time worked for the day
- **Break Time**: Automatic deduction for standard breaks

**SQL Example - Hours Calculation**:
```sql
-- Calculate hours worked and overtime
SELECT 
    emp_id,
    full_name,
    time_in,
    time_out,
    TIMEDIFF(time_out, time_in) as hours_worked,
    CASE 
        WHEN TIMEDIFF(time_out, time_in) > '08:00:00' 
        THEN TIMEDIFF(TIMEDIFF(time_out, time_in), '08:00:00') 
        ELSE '00:00:00' 
    END as overtime_hours
FROM timesheet_details 
WHERE ts_date = '2024-01-15' AND status = 'P';
```

#### Time Validation Rules
The system implements comprehensive time validation:

**Validation Rules**:
- Time-out must be after time-in
- Maximum daily hours validation (e.g., 24 hours)
- Minimum work period validation
- Break time consideration
- Shift pattern validation

### 3. CSV Upload and Bulk Processing

#### Timesheet CSV Upload
The system provides CSV-based bulk timesheet upload functionality:

**User Story**: As an HR administrator, I want to upload timesheet data via CSV files, so that I can process time data from external time tracking systems or manual records.

**Key Features**:
- CSV file format validation
- Time format validation (HH:MM:SS)
- Employee identification and validation
- Batch processing with error handling
- Template generation for consistent formatting

**Code Example - CSV Processing Logic**:
```php
// From atten_controller.php - CSV upload processing structure
if($_GET['endpoint'] == 'upload_timesheet') {
    try {
        $GLOBALS['conn']->begin_transaction();
        
        check_auth('create_timesheet');
        
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileType = $_FILES['file']['type'];
            
            // Validate file type
            if ($fileType != 'text/csv') {
                throw new Exception("Invalid file type. Please upload a valid CSV file.");
            }
            
            // Process CSV rows
            if (($file = fopen($fileTmpPath, 'r')) !== false) {
                while (($data = fgetcsv($file, 1000, ",")) !== false) {
                    // Process each row with time validation
                    // Create timesheet_details records
                }
                fclose($file);
            }
        }
        
        $GLOBALS['conn']->commit();
    } catch (Exception $e) {
        $GLOBALS['conn']->rollback();
        $result['error'] = true;
        $result['msg'] = 'Error: ' . $e->getMessage();
    }
}
```

### 4. Integration with Payroll System

#### Payroll Data Integration
Timesheet data directly feeds into payroll calculations:

**User Story**: As a payroll administrator, I want timesheet data to automatically integrate with payroll calculations, so that employee compensation accurately reflects hours worked.

**Integration Points**:
- **Regular Hours**: Base salary calculation based on standard work hours
- **Overtime Hours**: Overtime rate application for excess hours
- **Absence Deductions**: Salary deductions for missing time entries
- **Leave Integration**: Coordination with leave records for accurate calculations

**Payroll Integration Logic**:
```sql
-- Timesheet data for payroll calculation
SELECT 
    td.emp_id,
    td.full_name,
    COUNT(td.id) as days_worked,
    SUM(CASE 
        WHEN td.time_in IS NOT NULL AND td.time_out IS NOT NULL 
        THEN TIME_TO_SEC(TIMEDIFF(td.time_out, td.time_in))/3600 
        ELSE 0 
    END) as total_hours,
    SUM(CASE 
        WHEN TIMEDIFF(td.time_out, td.time_in) > '08:00:00'
        THEN TIME_TO_SEC(TIMEDIFF(TIMEDIFF(td.time_out, td.time_in), '08:00:00'))/3600
        ELSE 0 
    END) as overtime_hours
FROM timesheet_details td
WHERE td.ts_date BETWEEN '2024-01-01' AND '2024-01-31'
AND td.status = 'P'
GROUP BY td.emp_id, td.full_name;
```

#### Salary Calculation Impact
Timesheet data affects various aspects of salary calculation:

**Calculation Components**:
- **Base Salary**: Prorated based on actual hours worked vs. standard hours
- **Overtime Pay**: Additional compensation for hours beyond standard work time
- **Attendance Bonus**: Bonuses based on consistent time tracking
- **Deductions**: Penalties for insufficient work hours or late arrivals

### 5. Time Tracking Workflows

#### Daily Timesheet Workflow
The standard daily timesheet process follows these steps:

**Workflow Steps**:
1. **Timesheet Creation**: Create master timesheet record for the date
2. **Employee Selection**: Identify employees for time tracking
3. **Time Entry**: Record time-in and time-out for each employee
4. **Validation**: Validate time entries for consistency and accuracy
5. **Processing**: Calculate work hours and overtime
6. **Integration**: Feed data to payroll and reporting systems

**Code Example - Timesheet Detail Creation**:
```php
// Timesheet detail record structure
$detailData = [
    'ts_id' => $timesheet_id,           // Foreign key to timesheet table
    'emp_id' => $employee_id,           // Employee identifier
    'full_name' => $full_name,          // Employee name
    'phone_number' => $phone_number,    // Contact information
    'email' => $email,                  // Email address
    'staff_no' => $staff_no,            // Staff number
    'ts_date' => $timesheet_date,       // Timesheet date
    'time_in' => $time_in,              // Check-in time
    'time_out' => $time_out,            // Check-out time
    'status' => 'P',                    // Present status
    'added_by' => $_SESSION['user_id']  // User who created record
];

$result = $timesheetDetailsClass->create($detailData);
```

#### Bulk Timesheet Workflow
For processing multiple employees simultaneously:

**Workflow Steps**:
1. **Department/Location Selection**: Choose organizational unit
2. **Employee Filtering**: Get active employees for the selected unit
3. **Bulk Time Entry**: Record time data for all selected employees
4. **Batch Validation**: Validate all entries before processing
5. **Transaction Processing**: Use database transactions for data integrity
6. **Error Handling**: Handle individual record errors without affecting others

### 6. Reporting and Analytics

#### Timesheet Reports
The system generates various timesheet-based reports:

**Report Types**:
- **Daily Timesheet Summary**: Hours worked by all employees for a specific date
- **Employee Time History**: Individual employee time tracking over periods
- **Department Time Analysis**: Time tracking statistics by department
- **Overtime Reports**: Overtime hours and costs by employee and period
- **Attendance vs. Timesheet Comparison**: Comparison between attendance and actual hours

**Example Report Query**:
```sql
-- Monthly timesheet summary by employee
SELECT 
    td.emp_id,
    td.full_name,
    COUNT(*) as days_with_timesheet,
    AVG(TIME_TO_SEC(TIMEDIFF(td.time_out, td.time_in))/3600) as avg_daily_hours,
    SUM(TIME_TO_SEC(TIMEDIFF(td.time_out, td.time_in))/3600) as total_hours,
    SUM(CASE 
        WHEN TIMEDIFF(td.time_out, td.time_in) > '08:00:00'
        THEN TIME_TO_SEC(TIMEDIFF(TIMEDIFF(td.time_out, td.time_in), '08:00:00'))/3600
        ELSE 0 
    END) as total_overtime
FROM timesheet_details td
WHERE td.ts_date BETWEEN '2024-01-01' AND '2024-01-31'
AND td.time_in IS NOT NULL AND td.time_out IS NOT NULL
GROUP BY td.emp_id, td.full_name
ORDER BY total_hours DESC;
```

### 7. Data Validation and Quality Control

#### Time Entry Validation
The system implements comprehensive validation for time entries:

**Validation Rules**:
- **Time Format**: Ensure proper time format (HH:MM:SS)
- **Logical Sequence**: Time-out must be after time-in
- **Date Consistency**: Time entries must match timesheet date
- **Employee Validation**: Verify employee exists and is active
- **Duplicate Prevention**: Prevent duplicate time entries for same employee/date

#### Data Quality Checks
Regular data quality checks ensure timesheet accuracy:

**Quality Checks**:
- **Missing Time Entries**: Identify employees with incomplete time data
- **Excessive Hours**: Flag unusually long work hours for review
- **Time Gaps**: Identify potential data entry errors or missing breaks
- **Consistency Checks**: Compare timesheet data with attendance records

### 8. System Integration Points

#### Attendance System Integration
Timesheet data complements attendance information:

**Integration Features**:
- **Status Synchronization**: Align timesheet status with attendance status
- **Leave Integration**: Handle leave days in timesheet processing
- **Data Consistency**: Ensure timesheet and attendance data consistency
- **Reporting Coordination**: Combine attendance and timesheet data in reports

#### Employee Management Integration
Timesheet system integrates with employee management:

**Integration Points**:
- **Employee Data**: Synchronize employee information in timesheet records
- **Organizational Structure**: Use department and location data for filtering
- **Employee Status**: Consider active/inactive status in timesheet processing
- **Role-Based Access**: Apply employee roles to timesheet access permissions

#### Payroll System Integration
Direct integration with payroll processing:

**Integration Features**:
- **Hours Calculation**: Provide accurate work hours for salary calculation
- **Overtime Processing**: Calculate overtime hours and rates
- **Deduction Calculation**: Support salary deductions based on time data
- **Payroll Validation**: Validate payroll calculations against timesheet data

## Technical Architecture

### Database Design
The timesheet system uses a master-detail database design:

**Master Table (timesheet)**:
- Groups timesheet entries by date
- Tracks creation and modification metadata
- Provides reference point for detail records

**Detail Table (timesheet_details)**:
- Stores individual employee time records
- Contains complete employee information for reporting
- Includes time-in, time-out, and calculated fields

### Transaction Management
The system uses database transactions for data integrity:

**Transaction Features**:
- **Atomic Operations**: Ensure all related records are created together
- **Rollback Capability**: Undo changes if any part of the operation fails
- **Error Handling**: Comprehensive error handling with transaction rollback
- **Data Consistency**: Maintain data consistency across related tables

### Security and Authorization
Timesheet system implements security measures:

**Security Features**:
- **Permission-Based Access**: Role-based permissions for timesheet operations
- **Data Validation**: Input sanitization and validation
- **Audit Trail**: Track all timesheet modifications with user information
- **Access Control**: Restrict timesheet access based on organizational hierarchy

This comprehensive timesheet management system provides accurate time tracking, seamless payroll integration, and detailed reporting capabilities that enhance the overall effectiveness of the HRM application's time and attendance management.