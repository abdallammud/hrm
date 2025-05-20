<?php 
class Salary extends Model {
    public function __construct() {
        parent::__construct('employee_salaries', 'salary_id');
    }
}

$GLOBALS['salaryClass']     = $salaryClass = new Salary();