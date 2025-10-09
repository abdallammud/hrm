<?php 
$report = $_GET['report'] ?? '';

$reportTitles = [
    'employees'   => 'All Employees Reports',
    'absence'     => 'Absence and Leave Reports',
    'attendance'  => 'Timesheet and Attendance Reports',
    'componsation'=> 'Compensation and Benefits Reports',
    'deductions'  => 'Deductions Reports',
    'payroll'     => 'Payroll Reports',
    'taxation'    => 'Taxation Reports',
];

$reportTitle = $reportTitles[$report] ?? 'Reports';

// 🔹 Define filter sets (each block reusable)
function filter_gender() { ?>
    <div class="col-md-12 col-lg-2">
        <div class="form-group">
            <label for="slcGender">Gender</label>
            <select id="slcGender" class="form-control">
                <option value="">All</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
    </div>
<?php }

function filter_state() { ?>
    <div class="col-md-12 col-lg-2">
        <div class="form-group">
            <label for="slcState">State</label>
            <select id="slcState" class="form-control">
                <option value="">All</option>
                <?php select_all('states'); ?>
            </select>
        </div>
    </div>
<?php }

function filter_department() { ?>
    <div class="col-md-12 col-lg-3">
        <div class="form-group">
            <label for="slcDepartment">Department</label>
            <select id="slcDepartment" class="form-control">
                <option value="">All</option>
                <?php select_all('branches'); ?>
            </select>
        </div>
    </div>
<?php }

function filter_location() { ?>
    <div class="col-md-12 col-lg-2">
        <div class="form-group">
            <label for="slcLocation">Duty Location</label>
            <select id="slcLocation" class="form-control">
                <option value="">All</option>
                <?php select_all('locations'); ?>
            </select>
        </div>
    </div>
<?php }

function filter_salary_range() { ?>
    <div class="col-md-12 col-lg-2">
        <div class="form-group">
            <label>Salary range from</label>
            <input type="text" class="form-control" id="salary_range_start">
        </div>
    </div>
    <div class="col-md-12 col-lg-1">
        <div class="form-group">
            <label>&nbsp;</label>
            <input type="text" class="form-control" id="salary_range_end">
        </div>
    </div>
<?php }

function filter_month() { ?>
    <div class="col-md-12 col-lg-2">
        <div class="form-group">
            <label for="month">Month</label>
            <input type="month" class="form-control monthPicker cursor" id="month" 
                   value="<?php echo date('Y-m'); ?>" readonly>
        </div>
    </div>
<?php }

function filter_date_range() { ?>
    <div class="col-md-12 col-lg-2">
        <div class="form-group">
            <label for="date_range_start">Date from</label>
            <input type="date" class="form-control datepicker cursor" id="date_range_start" 
                   value="<?php echo date('Y-m-d'); ?>" readonly>
        </div>
    </div>
    <div class="col-md-12 col-lg-2">
        <div class="form-group">
            <label for="date_range_end">Date to</label>
            <input type="date" class="form-control datepicker cursor" id="date_range_end" 
                   value="<?php echo date('Y-m-d'); ?>" readonly>
        </div>
    </div>
<?php }

// 🔹 Map reports to their filters
$reportFilters = [
    'employees'   => ['gender', 'state', 'department', 'location', 'salary_range'],
    'payroll'     => ['department', 'state', 'location', 'salary_range', 'month'],
    'taxation'    => ['department', 'state', 'location', 'salary_range', 'month'],
    'absence'     => ['month'],
    'componsation'=> ['month'],
    'deductions'  => ['month'],
    'other'       => ['date_range'],
];
?>

<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="page content reportShow">
            <input type="hidden" id="report" value="<?=$report;?>">
            <div class="d-flex align-items-center text-primary">
                <h5><?=$reportTitle;?></h5>
            </div>
            <hr>

            <!-- 🔹 Dynamic Filters -->
            <div class="row filter">
                <?php 
                if(isset($reportFilters[$report])) {
                    foreach($reportFilters[$report] as $filter) {
                        $func = "filter_{$filter}";
                        if(function_exists($func)) $func();
                    }
                }
                ?>
            </div>

            <div class="card smt-10">
                <div class="card-body">
                    <div class="sflex sjend">
                        <a onclick="display_report(true)" class="cursor smr-20" target="_blank">
                            <i class="fa fa-refresh"></i> Set and filter
                        </a>
                        <a id="printTag" class="cursor" target="_blank">
                            <i class="fa fa-print"></i> Print
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="reportDataTable" class="table table-striped table-bordered" style="width:100%"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    display_report();
});
</script>
