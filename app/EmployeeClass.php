<?php
/*-------------------------------------------------------------
 *  EmployeeClass.php – Enhanced Employee & Relation helpers
 *  Using $GLOBALS['conn'] for all DB queries
 *-----------------------------------------------------------*/

class Employee extends Model
{
    public function __construct()
    {
        parent::__construct('employees', 'employee_id');
    }

    private function runQuery(string $sql, array $params = [], string $types = '')
    {
        $stmt = $GLOBALS['conn']->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $GLOBALS['conn']->error);
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $data;
    }

    public function getUser(int $employeeId): ?array
    {
        try {
            $result = $this->runQuery(
                'SELECT * FROM users WHERE emp_id = ? LIMIT 1',
                [$employeeId],
                'i'
            );
            return $result[0] ?? null;
        } catch (Exception $e) {
            error_log("Error getting user for employee {$employeeId}: " . $e->getMessage());
            return null;
        }
    }

    public function getEducation(int $employeeId): array
    {
        try {
            return $this->runQuery(
                'SELECT * FROM employee_education WHERE employee_id = ? ORDER BY start_year DESC',
                [$employeeId],
                'i'
            );
        } catch (Exception $e) {
            error_log("Error getting education for employee {$employeeId}: " . $e->getMessage());
            return [];
        }
    }

    public function getByStaffNo(string $staffNo): ?array
    {
        try {
            $result = $this->runQuery(
                'SELECT * FROM employees WHERE staff_no = ? LIMIT 1',
                [$staffNo],
                's'
            );
            return $result[0] ?? null;
        } catch (Exception $e) {
            error_log("Error getting employee by staff number {$staffNo}: " . $e->getMessage());
            return null;
        }
    }

    public function getByEmail(string $email): ?array
    {
        try {
            $result = $this->runQuery(
                'SELECT * FROM employees WHERE email = ? LIMIT 1',
                [$email],
                's'
            );
            return $result[0] ?? null;
        } catch (Exception $e) {
            error_log("Error getting employee by email {$email}: " . $e->getMessage());
            return null;
        }
    }

    public function getFullProfile2(int $employeeId): ?array
    {
        try {
            // 1. Get full employee info by joining related tables
            $employeeData = $this->runQuery(
                'SELECT e.*,
                        s.name AS state_name,
                        s.country_name,
                        l.name AS location_name,
                        l.city_name AS location_city,
                        b.name AS branch_name,
                        b.address AS branch_address,
                        b.contact_email AS branch_email,
                        b.contact_phone AS branch_phone
                FROM employees e
                LEFT JOIN states s ON s.id = e.state_id
                LEFT JOIN locations l ON l.id = e.location_id
                LEFT JOIN branches b ON b.id = e.branch_id
                WHERE e.employee_id = ?',
                [$employeeId],
                'i'
            );

            if (!$employeeData) {
                return null;
            }

            $employee = $employeeData[0]; // single row

            // 2. Related user account
            $employee['user'] = $this->getUser($employeeId);

            // 3. Education history
            $employee['education'] = $this->getEducation($employeeId);

            // 4. Projects (comma-separated string)
            $projects = $this->runQuery(
                'SELECT p.name
                FROM projects p
                JOIN employee_projects ep ON ep.project_id = p.id
                WHERE ep.emp_id = ?',
                [$employeeId],
                'i'
            );
            $employee['projects'] = implode(', ', array_column($projects, 'name'));

            // 5. Budget Codes (comma-separated string)
            $budgetCodes = $this->runQuery(
                'SELECT b.name
                FROM budget_codes b
                JOIN employee_budget_codes ebc ON ebc.code_id = b.id
                WHERE ebc.emp_id = ?',
                [$employeeId],
                'i'
            );
            $employee['budget_codes'] = implode(', ', array_column($budgetCodes, 'name'));

            // 6. Documents
            $employee['documents'] = $this->getDocuments($employeeId);

            return $employee;

        } catch (Exception $e) {
            error_log("Error getting full profile for employee {$employeeId}: " . $e->getMessage());
            return null;
        }
    }

     public function getFullProfile(int $employeeId): ?array
    {
        try {
            $rows = $this->runQuery(
                'SELECT 
                    e.*,
                    s.name AS state_name,
                    s.country_name,
                    l.name AS location_name,
                    l.city_name AS location_city,
                    b.name AS branch_name,
                    b.address AS branch_address,
                    b.contact_email AS branch_email,
                    b.contact_phone AS branch_phone,
                    GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ", ") AS projects,
                    GROUP_CONCAT(DISTINCT bc.name ORDER BY bc.name SEPARATOR ", ") AS budget_codes
                FROM employees e
                LEFT JOIN states s ON s.id = e.state_id
                LEFT JOIN locations l ON l.id = e.location_id
                LEFT JOIN branches b ON b.id = e.branch_id
                LEFT JOIN employee_projects ep ON ep.emp_id = e.employee_id
                LEFT JOIN projects p ON p.id = ep.project_id
                LEFT JOIN employee_budget_codes ebc ON ebc.emp_id = e.employee_id
                LEFT JOIN budget_codes bc ON bc.id = ebc.code_id
                WHERE e.employee_id = ?
                GROUP BY e.employee_id',
                [$employeeId],
                'i'
            );

            if (!$rows) {
                return null;
            }

            $employee = $rows[0];

            // Still append separately fetched data that cannot be aggregated in the main query
            $employee['user'] = $this->getUser($employeeId);
            $employee['education'] = $this->getEducation($employeeId);
            $employee['documents'] = $this->getDocuments($employeeId);

            return $employee;

        } catch (Exception $e) {
            error_log("Error getting full profile for employee {$employeeId}: " . $e->getMessage());
            return null;
        }
    }


    public function getProjectIds(int $employeeId): array
    {
        try {
            $rows = $this->runQuery(
                'SELECT project_id FROM employee_projects WHERE emp_id = ?',
                [$employeeId],
                'i'
            );
            return array_column($rows, 'project_id');
        } catch (Exception $e) {
            error_log("Error getting project IDs for employee {$employeeId}: " . $e->getMessage());
            return [];
        }
    }

    public function getProjects(int $employeeId): array
    {
        try {
            return $this->runQuery(
                'SELECT p.* 
                 FROM projects p
                 JOIN employee_projects ep ON ep.project_id = p.id
                 WHERE ep.emp_id = ?',
                [$employeeId],
                'i'
            );
        } catch (Exception $e) {
            error_log("Error getting projects for employee {$employeeId}: " . $e->getMessage());
            return [];
        }
    }

    public function getProjectsAsString(int $employeeId): string
    {
        try {
            $rows = $this->runQuery(
                'SELECT p.name 
                FROM projects p
                JOIN employee_projects ep ON ep.project_id = p.id
                WHERE ep.emp_id = ?',
                [$employeeId],
                'i'
            );
            return implode(', ', array_column($rows, 'name'));
        } catch (Exception $e) {
            error_log("Error getting projects for employee {$employeeId}: " . $e->getMessage());
            return '';
        }
    }

    public function getBudgetCodeIds(int $employeeId): array
    {
        try {
            $rows = $this->runQuery(
                'SELECT code_id FROM employee_budget_codes WHERE emp_id = ?',
                [$employeeId],
                'i'
            );
            return array_column($rows, 'code_id');
        } catch (Exception $e) {
            error_log("Error getting budget code IDs for employee {$employeeId}: " . $e->getMessage());
            return [];
        }
    }

    public function getBudgetCodeNames(int $employeeId): array
    {
        try {
            $rows = $this->runQuery(
                'SELECT b.* 
                 FROM budget_codes b
                 JOIN employee_budget_codes ebc ON ebc.code_id = b.id
                 WHERE ebc.emp_id = ?',
                [$employeeId],
                'i'
            );
            return array_column($rows, 'name');
        } catch (Exception $e) {
            error_log("Error getting budget code IDs for employee {$employeeId}: " . $e->getMessage());
            return [];
        }
    }

    public function getBudgetCodes(int $employeeId): array
    {
        try {
            return $this->runQuery(
                'SELECT b.* 
                 FROM budget_codes b
                 JOIN employee_budget_codes ebc ON ebc.code_id = b.id
                 WHERE ebc.emp_id = ?',
                [$employeeId],
                'i'
            );
        } catch (Exception $e) {
            error_log("Error getting budget codes for employee {$employeeId}: " . $e->getMessage());
            return [];
        }
    }

    public function getBudgetCodeNamesAsString(int $employeeId): string
    {
        try {
            $rows = $this->runQuery(
                'SELECT b.name 
                FROM budget_codes b
                JOIN employee_budget_codes ebc ON ebc.code_id = b.id
                WHERE ebc.emp_id = ?',
                [$employeeId],
                'i'
            );
            return implode(', ', array_column($rows, 'name'));
        } catch (Exception $e) {
            error_log("Error getting budget code names for employee {$employeeId}: " . $e->getMessage());
            return '';
        }
    }

    public function getDocuments(int $employeeId): array
    {
        try {
            return $this->runQuery(
                'SELECT * FROM employee_docs WHERE employee_id = ? ORDER BY created_at DESC',
                [$employeeId],
                'i'
            );
        } catch (Exception $e) {
            error_log("Error getting documents for employee {$employeeId}: " . $e->getMessage());
            return [];
        }
    }

    public function assignProjects(int $employeeId, array $projectIds): void
    {
        if (!$projectIds) return;

        $GLOBALS['conn']->begin_transaction();

        $stmt = $GLOBALS['conn']->prepare('DELETE FROM employee_projects WHERE emp_id = ?');
        $stmt->bind_param('i', $employeeId);
        $stmt->execute();
        $stmt->close();

        $stmt = $GLOBALS['conn']->prepare('INSERT INTO employee_projects (emp_id, project_id) VALUES (?, ?)');
        foreach ($projectIds as $pid) {
            $stmt->bind_param('ii', $employeeId, $pid);
            $stmt->execute();
        }
        $stmt->close();

        $GLOBALS['conn']->commit();
    }

    public function assignBudgetCodes(int $employeeId, array $budgetCodeIds): void
    {
        if (!$budgetCodeIds) return;

        $GLOBALS['conn']->begin_transaction();

        $stmt = $GLOBALS['conn']->prepare('DELETE FROM employee_budget_codes WHERE emp_id = ?');
        $stmt->bind_param('i', $employeeId);
        $stmt->execute();
        $stmt->close();

        $stmt = $GLOBALS['conn']->prepare(
            'INSERT INTO employee_budget_codes (emp_id, code_id) VALUES (?, ?)'
        );
        foreach ($budgetCodeIds as $bcid) {
            $stmt->bind_param('ii', $employeeId, $bcid);
            $stmt->execute();
        }
        $stmt->close();

        $GLOBALS['conn']->commit();
    }

    public function getEmployeesByBranch(int $branchId): array
    {
        try {
            return $this->runQuery(
                'SELECT e.*
                FROM employees e
                WHERE e.branch_id = ?',
                [$branchId],
                'i'
            );
        } catch (Exception $e) {
            error_log("Error getting employees for branch {$branchId}: " . $e->getMessage());
            return [];
        }
    }

    public function getEmployeesByState(int $stateId): array
    {
        try {
            return $this->runQuery(
                'SELECT e.*
                FROM employees e
                WHERE e.state_id = ?',
                [$stateId],
                'i'
            );
        } catch (Exception $e) {
            error_log("Error getting employees for state {$stateId}: " . $e->getMessage());
            return [];
        }
    }

    public function getEmployeeBranchName(int $employeeId): string
    {
        try {
            $rows = $this->runQuery(
                'SELECT b.name
                FROM employees e
                JOIN branches b ON b.id = e.branch_id
                WHERE e.employee_id = ?',
                [$employeeId],
                'i'
            );
            return $rows[0]['name'] ?? '';
        } catch (Exception $e) {
            error_log("Error getting branch name for employee {$employeeId}: " . $e->getMessage());
            return '';
        }
    }


    public function getEmployeeStateName(int $employeeId): string
    {
        try {
            $rows = $this->runQuery(
                'SELECT s.name
                FROM employees e
                JOIN states s ON s.id = e.state_id
                WHERE e.employee_id = ?',
                [$employeeId],
                'i'
            );
            return $rows[0]['name'] ?? '';
        } catch (Exception $e) {
            error_log("Error getting state name for employee {$employeeId}: " . $e->getMessage());
            return '';
        }
    }

    public function getEmployeesByLocation(int $locationId): array
    {
        try {
            return $this->runQuery(
                'SELECT e.*
                FROM employees e
                WHERE e.location_id = ?',
                [$locationId],
                'i'
            );
        } catch (Exception $e) {
            error_log("Error getting employees for location {$locationId}: " . $e->getMessage());
            return [];
        }
    }

    public function deleteEmployee(int $employeeId): bool
    {
        // List of related tables to clear before deleting the main employee record.
        $tables = [
            'atten_details',
            'employee_budget_codes',
            'employee_docs',
            // 'employee_education',
            'employee_leave',
            'employee_performance',
            'employee_projects',
            'employee_transactions',
            'payroll_details',
            // 'resignations',
            'training_list'
        ];

        try {
            // Start a database transaction to ensure atomicity.
            $GLOBALS['conn']->begin_transaction();

            // Delete records from related tables first.
            foreach ($tables as $table) {
                $this->runQuery(
                    "DELETE FROM {$table} WHERE emp_id = ?",
                    [$employeeId],
                    'i'
                );
            }

            // Finally, delete the main employee record.
            $this->runQuery(
                "DELETE FROM employees WHERE employee_id = ?",
                [$employeeId],
                'i'
            );

            // Commit the transaction only if all queries were successful.
            $GLOBALS['conn']->commit();
            return true;

        } catch (Exception $e) {
            $GLOBALS['conn']->rollback();
            // Log the error with the specific table name and employee ID
            error_log("Error deleting from table '{$table}' for employee {$employeeId}: " . $e->getMessage());
            // Re-throw a new exception with more specific information
            throw new Exception("Error deleting from table '{$table}' for employee {$employeeId}.");
        }
    }




    
}

/*===========================================================
 *  Tiny relation classes – also updated to use $GLOBALS['conn']
 *==========================================================*/
class EmployeeProjects extends Model
{
    public function __construct()
    {
        parent::__construct('employee_projects');
    }
}

class EmployeeBudgetCodes extends Model
{
    public function __construct()
    {
        parent::__construct('employee_budget_codes');
    }
}

class EmpDoc extends Model
{
    public function __construct()
    {
        parent::__construct('employee_docs');
    }
}

/* Globals (kept for legacy code) */
$GLOBALS['employeeClass'] = $employeeClass = new Employee();
$GLOBALS['empDocClass'] = $empDocClass = new EmpDoc();
$GLOBALS['employeeProjectClass'] = $employeeProjectClass = new EmployeeProjects();
$GLOBALS['employeeBudgetCodeClass'] = $employeeBudgetCodeClass = new EmployeeBudgetCodes();
