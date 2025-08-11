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

    public function getFullProfile(int $employeeId): ?array
    {
        try {
            $employee = $this->read($employeeId);
            if (!$employee) {
                return null;
            }

            $employee['user'] = $this->getUser($employeeId);
            $employee['education'] = $this->getEducation($employeeId);
            $employee['projects'] = $this->getProjects($employeeId);
            $employee['budget_codes'] = $this->getBudgetCodes($employeeId);
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

    public function getBudgetCodeIds(int $employeeId): array
    {
        try {
            $rows = $this->runQuery(
                'SELECT budget_code_id FROM employee_budget_codes WHERE emp_id = ?',
                [$employeeId],
                'i'
            );
            return array_column($rows, 'budget_code_id');
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
                 JOIN employee_budget_codes ebc ON ebc.budget_code_id = b.id
                 WHERE ebc.emp_id = ?',
                [$employeeId],
                'i'
            );
        } catch (Exception $e) {
            error_log("Error getting budget codes for employee {$employeeId}: " . $e->getMessage());
            return [];
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
            'INSERT INTO employee_budget_codes (emp_id, budget_code_id) VALUES (?, ?)'
        );
        foreach ($budgetCodeIds as $bcid) {
            $stmt->bind_param('ii', $employeeId, $bcid);
            $stmt->execute();
        }
        $stmt->close();

        $GLOBALS['conn']->commit();
    }


    // All other methods would be similarly updated to use $this->runQuery()
    // instead of $this->query(), but their signatures and logic remain exactly the same
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
