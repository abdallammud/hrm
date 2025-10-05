<?php 
require('db.php');
require('utilities.php');
require('config.php');
require('helpers.php');

require('auth.php');
// require('Model.php');
require('autoload.php');
require('vendor/autoload.php');
require('myEmail.php');


require('CompanyClass.php');
require('BranchClass.php');
require('CountryClass.php');
require('EmployeeClass.php');
require('SalaryClass.php');
require('EducationClass.php');
require('StatesClass.php');
require('LocationsClass.php');
require('MiscClass.php');
require('SettingsClass.php');
require('AttendanceClass.php');
require('PayrollClass.php');
require('performanceClass.php');
require('financeClass.php');
require('trainingClass.php');
require('management_classes.php');



$GLOBALS['logoPath'] = baseUri() .'/assets/images/'.return_setting('system_logo');







?>