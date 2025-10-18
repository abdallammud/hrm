
      <div class="page-breadcrumb mt-4 d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Dashboard</div>
        <div class="ps-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
              <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
              </li>
              <!-- <li class="breadcrumb-item active" aria-current="page">HRM Dashboard</li> -->
            </ol>
          </nav>
        </div>
        
      </div>
      <!--end breadcrumb-->


    <div class="row dashboard-cards">

        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
            <div class="card rounded-4 w-100 card-1">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="wh-42 icon-circle bg-primary text-white">
                    <span class="bi bi-people"></span>
                </div>
                </div>
                <div>
                <h4 class="mb-0 total_employees">00</h4>
                <p class="mb-3">Total Employees</p>
                </div>
            </div>
            </div>
        </div>

        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
            <div class="card rounded-4 w-100 card-2">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="wh-42 icon-circle bg-success text-white">
                    <span class="bi bi-person-plus"></span>
                </div>
                </div>
                <div>
                <h4 class="mb-0 total_new_employees">00</h4>
                <p class="mb-3">New Employees</p>
                </div>
            </div>
            </div>
        </div>

        <!-- <div class="col-12 col-lg-2 col-xxl-2 d-flex">
            <div class="card rounded-4 w-100 card-3">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="wh-42 icon-circle bg-info text-white">
                    <span class="bi bi-ticket"></span>
                </div>
                </div>
                <div>
                <h4 class="mb-0 on_leave">00</h4>
                <p class="mb-3">Employees on Leave</p>
                </div>
            </div>
            </div>
        </div> -->

        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
            <div class="card rounded-4 w-100 card-4">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="wh-42 icon-circle bg-warning text-white">
                    <span class="bi bi-cash-stack"></span>
                </div>
                </div>
                <div>
                <h4 class="mb-0 operational_funds">$00</h4>
                <p class="mb-3">Operational Funds</p>
                </div>
            </div>
            </div>
        </div>

        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
            <div class="card rounded-4 w-100 card-6">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="wh-42 icon-circle bg-purple text-white">
                    <span class="bi bi-wallet2"></span>
                </div>
                </div>
                <div>
                <h4 class="mb-0 this_month_salary">$00</h4>
                <p class="mb-3">This Month Salary</p>
                </div>
            </div>
            </div>
        </div>

        <!-- <div class="col-12 col-lg-2 col-xxl-2 d-flex">
            <div class="card rounded-4 w-100 card-6">
            <div class="card-body">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                <div class="wh-42 icon-circle bg-purple text-white">
                    <span class="bi bi-credit-card"></span>
                </div>
                </div>
                <div>
                <h4 class="mb-0 expenses">$00</h4>
                <p class="mb-3">Expenses</p>
                </div>
            </div>
            </div>
        </div> -->

    </div><!--end row-->


      <div class="row">
        <div class="col-12 col-xl-4">
            <div class="card rounded-4 gender-chart-card">
                <div class="card-body text-center">
                    <h5 class="chart-title mb-3">Employee Gender Distribution</h5>
                    <div class="chart-container">
                    <canvas id="employeeGenderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card rounded-4 gender-chart-card">
                <div class="card-body text-center">
                    <h5 class="chart-title mb-3">Employee Department Distribution</h5>
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="">
                        <h5 class="mb-0"></h5>
                    </div>
                    
                </div>
                    <div class="chart-container">
                    <canvas id="employeeDepartmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card rounded-4 gender-chart-card">
                <div class="card-body text-center">
                    <h5 class="chart-title mb-3">Last 5 Months Payroll</h5>
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="">
                        <h5 class="mb-0"></h5>
                    </div>
                    
                </div>
                    <div class="chart-container">
                    <canvas id="last5MonthsPayrollChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
      </div><!--end row-->


      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <p>Lates payroll transaction</p>
            <table id="payrollDT" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr role="row">
                  <th>Staff No. </th>
                  <th>Full name </th>
                  <th>Gross salary </th>
                  <th>Earnings </th>
                  <th>Deductions </th>
                  <th>Tax </th>
                  <th>Net salary </th>
                  <th>Status </th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $month = date('Y-m');
                $query = "SELECT `id`, `payroll_id`, `emp_id`, `staff_no`, `full_name`, `status`, `base_salary`, (`allowance` + `bonus` + `commission`) AS earnings, (`loan` + `advance` + `deductions`) AS `total_deductions`, `tax`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE  `status` IN ('Approved', 'Pending') AND `month` LIKE '$month%' LIMIT 10";
                $data = $GLOBALS['conn']->query($query);
                while($row = $data->fetch_assoc()) { ?>

                  <tr role="row">
                    <td><?=$row['staff_no'];?> </td>
                    <td><?=$row['full_name'];?></td>
                    <td><?=formatMoney($row['base_salary']);?> </td>
                    <td><?=formatMoney($row['earnings']);?> </td>
                    <td><?=formatMoney($row['total_deductions']);?> </td>
                    <td><?=formatMoney($row['tax']);?> </td>
                    <td><?=formatMoney($row['net_salary']);?> </td>
                    <td><?=$row['status'];?> </td>
                  </tr>

               <?php  }

                ?>
              </tbody>
            </table> 
          </div>
        </div>
      </div>
    
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script src="<?=baseUri();?>/assets/js/modules/dashboard.js"></script>
      
      <script type="text/javascript">
        
      </script>


<style>
    .dashboard-cards {
  /* gap: 1rem; */
}

.dashboard-cards .card {
  border: none;
  color: #222;
  transition: all 0.3s ease-in-out;
  box-shadow: 0 3px 10px rgba(0,0,0,0.05);
  background: #fff;
  cursor: pointer;
}
.card {
  transition: all 0.3s ease-in-out;
  box-shadow: 0 3px 10px rgba(0,0,0,0.05);
  cursor: pointer;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.dashboard-cards .icon-circle {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-size: 18px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

/* Unique gradient themes per card */
.dashboard-cards .card-1 {
  background: linear-gradient(135deg, #007bff1a, #007bff0d);
  border-top: 4px solid #007bff;
}
.dashboard-cards .card-2 {
  background: linear-gradient(135deg, #28a7451a, #28a7450d);
  border-top: 4px solid #28a745;
}
.dashboard-cards .card-3 {
  background: linear-gradient(135deg, #17a2b81a, #17a2b80d);
  border-top: 4px solid #17a2b8;
}
.dashboard-cards .card-4 {
  background: linear-gradient(135deg, #ffc1071a, #ffc1070d);
  border-top: 4px solid #ffc107;
}
.dashboard-cards .card-5 {
  background: linear-gradient(135deg, #dc35451a, #dc35450d);
  border-top: 4px solid #dc3545;
}
.dashboard-cards .card-6 {
  background: linear-gradient(135deg, #6f42c11a, #6f42c10d);
  border-top: 4px solid #6f42c1;
}

/* Typography tweaks */
.dashboard-cards .card h4 {
  font-weight: 700;
  font-size: 1.6rem;
  color: var(--bs-body-color);
}

.dashboard-cards .card p {
  color: var(--bs-body-color);
  margin-bottom: 0;
  font-size: 0.9rem;
}

/* Optional: responsive spacing for smaller screens */
@media (max-width: 991px) {
  .dashboard-cards {
    /* gap: 0.5rem; */
  }
  .dashboard-cards .card {
    margin-bottom: 1rem;
  }
}

.gender-chart-card {
  border: none;
  background: linear-gradient(135deg, #f8f9fa, #ffffff);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.gender-chart-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.chart-title {
  font-weight: 600;
  color: #333;
  letter-spacing: 0.5px;
}

.chart-container {
  position: relative;
  height: 280px;
  width: 280px;
  margin: 0 auto;
}

canvas {
  transition: all 0.3s ease;
}

@media (max-width: 768px) {
  .chart-container {
    height: 220px;
    width: 220px;
  }
}

</style>