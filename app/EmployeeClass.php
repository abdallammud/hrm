<?php 
class Employee extends Model {
    public function __construct() {
        parent::__construct('employees', 'employee_id');
    }

    public function get_user($employee_id) {
       return get_data('users', ['emp_id' => $employee_id]);
    }

    public function get_education($employee_id) {
       return get_data('employee_education', ['employee_id' => $employee_id]);
    }

    public function get($employee_id) {
        return $this->read($employee_id);
    }
}

class EmpDoc extends Model {
    public function __construct() {
        parent::__construct('employee_docs');
    }
}
$GLOBALS['employeeClass']   = $employeeClass = new Employee();
$GLOBALS['empDocClass']   = $empDocClass = new EmpDoc();
