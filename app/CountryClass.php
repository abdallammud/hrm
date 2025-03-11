<?php 
class Country extends Model {
    public function __construct() {
        parent::__construct('countries', 'country_id');
    }
}

$GLOBALS['countryClass']    = $countryClass = new Country();