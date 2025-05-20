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


// Resource Allocation
class Allocation extends Model {
    public function __construct() {
        parent::__construct('res_allocation');
    }

    public function getTotalTime($jsonData) {
        // Decode the JSON data into an associative array
        $data = json_decode($jsonData, true);

        // Initialize total time
        $totalTime = 0;

        // Check if 'budgets' exists and is an array
        if (isset($data['budgets']) && is_array($data['budgets'])) {
            foreach ($data['budgets'] as $budget) {
                if (isset($budget['time']) && is_numeric($budget['time'])) {
                    $totalTime += $budget['time'];
                }
            }
        }

        // Check if 'projects' exists and is an array
        if (isset($data['projects']) && is_array($data['projects'])) {
            foreach ($data['projects'] as $project) {
                if (isset($project['time']) && is_numeric($project['time'])) {
                    $totalTime += $project['time'];
                }
            }
        }

        return $totalTime;
    }


    // Create function to handle json data
}
$GLOBALS['allocationClass'] =  $allocationClass = new Allocation();