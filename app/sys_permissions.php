<?php 
class Sys_Permissions extends Model {
    public function __construct() {
        parent::__construct('sys_permissions');
    }

    public function get($id) {
        return $this->read($id);
    }

    public function get_all() {
        $all_sys_permissions = [];
        $sysPermissions = $GLOBALS['sys_permissions']->read_all();
        foreach($sysPermissions as $permission) {
            $actions = json_decode($permission['actions']);
            foreach ($actions as $action_name => $action_code) {
                $all_sys_permissions[] = $action_code->code;
            }
        }

        return $all_sys_permissions;
    }
}

class Sys_roles extends Model {
    public function __construct() {
        parent::__construct('sys_roles');
    }

    public function get($id) {
        return $this->read($id);
    }
}

class Sys_role_permissions extends Model {
    public function __construct() {
        parent::__construct('sys_role_permissions');
    }

    public function get($id) {
        return $this->read($id);
    }

    public function get_permissions($role_id) {
        $allPermissions = [];
        $permissions = get_data2('sys_role_permissions', ['role_id' => $role_id]);
        foreach ($permissions as $permission) {
            $allPermissions[] = $permission['permission'];
        }
        return $allPermissions;
    }
}

function get_data2($table, array $fields) {
    // Ensure the table name is safe
    // $allowedTables = ['company', 'branches', 'states']; // Define allowed tables
    // if (!in_array($table, $allowedTables)) {
    //     return false; // Prevent SQL injection by checking allowed tables
    // }

    // Start building the query
    $query = "SELECT * FROM `$table` WHERE ";
    $conditions = [];
    $params = [];

    // Build conditions based on the provided fields
    foreach ($fields as $key => $value) {
        $conditions[] = "`$key` = ?";
        $params[] = $value; // Store the value for binding
    }

    // Combine conditions into the query
    $query .= implode(' AND ', $conditions);

    // Prepare the statement
    if ($stmt = $GLOBALS['conn']->prepare($query)) {
        // Bind parameters dynamically
        $types = str_repeat('s', count($params)); // Assuming all values are strings; adjust if needed
        $stmt->bind_param($types, ...$params);
        
        // Execute the query
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        // Fetch data
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    return false; // Return false if no records are found or if an error occurs
}

$GLOBALS['sys_permissions']  = $sys_permissions = new Sys_Permissions();
$GLOBALS['sys_roles']  = $sys_roles = new Sys_roles();
$GLOBALS['sys_role_permissions']  = $sys_role_permissions = new Sys_role_permissions();