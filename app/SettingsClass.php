<?php 
class Settings extends Model {
    public function __construct() {
        parent::__construct('sys_settings', 'type');
    }

}

$GLOBALS['settingsClass']     = $settingsClass = new Settings();