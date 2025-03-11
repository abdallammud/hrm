<?php 
class LeaveTypes extends Model {
    public function __construct() {
        parent::__construct('leave_types');
    }
}

$GLOBALS['leaveTypesClass'] = $leaveTypesClass = new LeaveTypes();


// Employee leave
class EmployeeLeave extends Model {
    public function __construct() {
        parent::__construct('employee_leave');
    }
}
$GLOBALS['employeeLeaveClass'] = $employeeLeaveClass = new EmployeeLeave();



// Attendance
class Attendance extends Model {
    public function __construct() {
        parent::__construct('attendance');
    }
}
$GLOBALS['attendanceClass'] = $attendanceClass = new Attendance();



// Attendance details
class AttenDetails extends Model {
    public function __construct() {
        parent::__construct('atten_details');
    }
}
$GLOBALS['attenDetailsClass'] = $attenDetailsClass = new AttenDetails();









// Attendance
class Timesheet extends Model {
    public function __construct() {
        parent::__construct('timesheet');
    }
}
$GLOBALS['timesheetClass'] = $timesheetClass = new Timesheet();



// Attendance details
class TimesheetDetails extends Model {
    public function __construct() {
        parent::__construct('timesheet_details');
    }
}
$GLOBALS['timesheetDetailsClass'] =  $timesheetDetailsClass = new TimesheetDetails();