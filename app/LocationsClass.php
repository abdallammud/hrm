<?php 
class Locations extends Model {
    public function __construct() {
        parent::__construct('locations');
    }
}

$GLOBALS['locationsClass'] = $locationsClass = new Locations();