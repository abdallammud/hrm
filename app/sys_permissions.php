<?php 
class Sys_Permissions extends Model {
    public function __construct() {
        parent::__construct('sys_permissions');
    }

    public function get($id) {
        return $this->read($id);
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
        return get_data('sys_role_permissions', ['role_id' => $role_id]);
    }
}

$GLOBALS['sys_permissions']  = $sys_permissions = new Sys_Permissions();
$GLOBALS['sys_roles']  = $sys_roles = new Sys_roles();
$GLOBALS['sys_role_permissions']  = $sys_role_permissions = new Sys_role_permissions();