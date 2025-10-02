<?php  

class Users extends Model {
    public function __construct() {
        parent::__construct('users', 'user_id');
    }

    /* public function getPermissions($userId) {
        $userPermissions = [];
        $sql = "SELECT `permission_id` FROM `user_permissions` WHERE `user_id` = ?";
        $params = [$userId];
        $types = 'i'; // 'i' for integer

        $permissions = $this->query($sql, $params, $types);

        foreach ($permissions as $permission) {
            $permissionName = $GLOBALS['permissionsClass']->get($permission['permission_id'])['name'];
            array_push($userPermissions, $permissionName);
        }

        return $userPermissions;
    } */

    public function getPermissions($userId) {
        $userPermissions = [];
        $role_id = $this->read($userId)['role'];
        return $role_permissions = $GLOBALS['sys_role_permissions']->get_permissions($role_id);
    }

    public function getPermission_ids($userId) {
        $userPermissions = [];
        $sql = "SELECT `permission_id` FROM `user_permissions` WHERE `user_id` = ?";
        $params = [$userId];
        $types = 'i'; // 'i' for integer

        $permissions = $this->query($sql, $params, $types);

        foreach ($permissions as $permission) {
            array_push($userPermissions, $permission['permission_id']);
        }

        return $userPermissions;
    }

    public function get($user_id) {
        return $this->read($user_id);
    }

    public function get_emp($emp_id) {
        $conn = $GLOBALS['conn'];
        $return = [];
        if($emp_id) {
            $query = $conn->query("SELECT * FROM `employees` WHERE `employee_id` = $emp_id");
            if($query->num_rows > 0) {
                $return = $query->fetch_assoc();
            }
        }

        return $return;;
    }

    public function get_roleName($user_id) {
        $user = $this->get($user_id);
        // var_dump($user);
        $role_id = $user['role'];
        return $GLOBALS['sys_roles']->read($role_id)['name'];
    }

    public function get_reportsTo($user_id) {
        $user = $this->read($user_id);
        $reportsTo = $user['reports_to'];
        return $GLOBALS['sys_roles']->get($reportsTo)['name'];
    }

    
}

class Permissios extends Model {
    public function __construct() {
        parent::__construct('permissions');
    }

    public function get($id) {
        return $this->read($id);
    }
}

class UserPermissions extends Model {
    public function __construct() {
        parent::__construct('user_permissions');
    }
}

class SysRoles extends Model {
    public function __construct() {
        parent::__construct('sys_roles');
    }
}

$GLOBALS['userClass']  = $userClass = new Users();
$GLOBALS['permissionsClass']  = $permissionsClass = new Permissios;
$GLOBALS['userPermissionsClass']  = $userPermissionsClass = new UserPermissions();
$GLOBALS['sys_roles']  = $sys_roles = new SysRoles();